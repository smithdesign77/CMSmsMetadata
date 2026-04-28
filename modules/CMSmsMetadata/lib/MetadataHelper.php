<?php
/**
 * MetadataHelper – database query helpers for the CMSmsMetadata module.
 *
 * Responsible for:
 *   - Fetching all content pages ordered by hierarchy
 *   - Discovering extra content_props column names (excluding body-content fields)
 *   - Fetching all prop values indexed by content_id
 *
 * @package  CMSmsMetadata
 * @author   smithdesign77
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('CMS_VERSION')) {
    exit();
}

class CMSmsMetadata_MetadataHelper
{
    /**
     * Prop names that store body / layout content rather than metadata.
     * These are excluded from the dynamic extra columns.
     */
    const EXCLUDE_PROPS = [
        'content',
        'layout',
        'styles',
        'head_content',
        'secure_params',
        'design_id',
        'template_id',
    ];

    // -------------------------------------------------------------------------
    // Public query methods
    // -------------------------------------------------------------------------

    /**
     * Fetch all content pages ordered by their hierarchy field.
     *
     * Adds a computed 'depth' key (0 = top-level) and 'indent_px' (CSS pixels)
     * to each row for use directly in the template.
     *
     * @param  object $db  CMS Made Simple DB connection object.
     * @return array       Array of associative row arrays.
     */
    public static function getPages($db)
    {
        $prefix = cms_db_prefix();
        $sql    = "SELECT content_id, content_name, content_alias, type,
                          parent_id, hierarchy, active, show_in_menu,
                          menu_text, titleattribute, accesskey, tabindex, metadata
                   FROM {$prefix}content
                   ORDER BY hierarchy ASC";

        $rows = $db->GetAll($sql);
        if (!$rows) {
            return [];
        }

        foreach ($rows as &$row) {
            $h             = (string) $row['hierarchy'];
            $depth         = ($h === '') ? 0 : substr_count($h, '.');
            $row['depth']  = $depth;
            // Pre-compute CSS indentation so the template stays free of arithmetic
            $row['indent_px'] = $depth * 16 + 6;
        }
        unset($row);

        return $rows;
    }

    /**
     * Return a sorted array of distinct prop_names present in content_props,
     * with body-content fields filtered out.
     *
     * Filtering rules:
     *   1. Names listed in EXCLUDE_PROPS are dropped.
     *   2. Names matching locale-based body-content patterns
     *      (e.g. content_en_US, content_de_DE) are dropped.
     *
     * @param  object   $db
     * @return string[]
     */
    public static function getExtraPropNames($db)
    {
        $prefix = cms_db_prefix();
        $sql    = "SELECT DISTINCT prop_name
                   FROM {$prefix}content_props
                   ORDER BY prop_name ASC";

        $all = $db->GetCol($sql);
        if (!$all) {
            return [];
        }

        $filtered = [];
        foreach ($all as $name) {
            if (in_array($name, self::EXCLUDE_PROPS, true)) {
                continue;
            }
            // Skip locale-suffixed body content props: content_en_US, content_de_DE …
            if (preg_match('/^content_[a-z]{2}_[A-Z]{2}$/', $name)) {
                continue;
            }
            $filtered[] = $name;
        }

        sort($filtered);
        return $filtered;
    }

    /**
     * Fetch all content_props values for the given prop names and return them
     * indexed as [ content_id (int) => [ prop_name => value ] ].
     *
     * Serialized values are silently replaced with an empty string because they
     * are not human-readable metadata.
     *
     * Uses a parameterised query to prevent SQL injection.
     *
     * @param  object   $db
     * @param  string[] $prop_names  List returned by getExtraPropNames().
     * @return array
     */
    public static function getPropValues($db, array $prop_names)
    {
        if (empty($prop_names)) {
            return [];
        }

        $prefix       = cms_db_prefix();
        $placeholders = implode(',', array_fill(0, count($prop_names), '?'));
        $sql          = "SELECT content_id, prop_name, content AS prop_value
                         FROM {$prefix}content_props
                         WHERE prop_name IN ({$placeholders})
                         ORDER BY content_id, prop_name";

        $rows = $db->GetAll($sql, $prop_names);
        if (!$rows) {
            return [];
        }

        $indexed = [];
        foreach ($rows as $row) {
            $cid  = (int) $row['content_id'];
            $name = $row['prop_name'];
            $val  = (string) $row['prop_value'];

            // Replace PHP-serialized data with an empty string
            if (self::isSerialized($val)) {
                $val = '';
            }

            $indexed[$cid][$name] = $val;
        }

        return $indexed;
    }

    // -------------------------------------------------------------------------
    // Internal helpers
    // -------------------------------------------------------------------------

    /**
     * Quick heuristic: is this string PHP-serialized data?
     *
     * Checks for the common serialisation prefixes used by serialize():
     * a: (array), O: (object), C: (custom), i: (int), d: (float), s: (string).
     * The literal 'N;' (null) is also matched.
     *
     * @param  string $value
     * @return bool
     */
    private static function isSerialized($value)
    {
        if (strlen($value) < 4) {
            return false;
        }
        if ($value === 'N;') {
            return true;
        }
        return (bool) preg_match('/^[aOCids]:/', $value);
    }

    // -------------------------------------------------------------------------
    // Inline editing
    // -------------------------------------------------------------------------

    /**
     * Save a single inline-edited cell value back to the database.
     *
     * Routes the write to the correct table:
     *   - Standard fields  → direct UPDATE on cms_content
     *   - Extra prop fields → INSERT or UPDATE on cms_content_props
     *
     * @param  object $db
     * @param  int    $content_id  Validated, positive content ID.
     * @param  string $field       Validated field key, e.g. 'tabindex', 'prop__foo'.
     * @param  string $value       Sanitised value to store.
     * @return bool   true on success.
     */
    public static function saveCell($db, $content_id, $field, $value)
    {
        $prefix = cms_db_prefix();

        // ── Standard content columns ─────────────────────────────────────────
        $direct_map = [
            'tabindex'  => 'tabindex',
            'accesskey' => 'accesskey',
            'titleattr' => 'titleattribute',
            'metadata'  => 'metadata',
        ];

        if (isset($direct_map[$field])) {
            $col = $direct_map[$field];
            $db->Execute(
                "UPDATE {$prefix}content SET {$col} = ? WHERE content_id = ?",
                [$value, $content_id]
            );
            self::clearContentCache();
            return true;
        }

        // ── Extra content_props ──────────────────────────────────────────────
        if (substr($field, 0, 7) === 'prop__' && strlen($field) > 7) {
            $prop_name = substr($field, 7);
            $exists = (int) $db->GetOne(
                "SELECT COUNT(*) FROM {$prefix}content_props
                  WHERE content_id = ? AND prop_name = ?",
                [$content_id, $prop_name]
            );
            if ($exists > 0) {
                $db->Execute(
                    "UPDATE {$prefix}content_props SET content = ?
                      WHERE content_id = ? AND prop_name = ?",
                    [$value, $content_id, $prop_name]
                );
            } else {
                $db->Execute(
                    "INSERT INTO {$prefix}content_props (content_id, prop_name, content)
                     VALUES (?, ?, ?)",
                    [$content_id, $prop_name, $value]
                );
            }
            self::clearContentCache();
            return true;
        }

        return false;
    }

    /**
     * Invalidate the CMS content cache after a direct database write.
     * Called after every saveCell() to ensure front-end reflects changes.
     */
    private static function clearContentCache()
    {
        if (class_exists('global_cache')) {
            global_cache::clear('content');
            global_cache::clear('content_tree');
        }
    }
}
