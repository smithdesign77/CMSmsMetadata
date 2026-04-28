<?php
/**
 * Default admin action – content metadata overview.
 *
 * Reads all content pages from the CMS database and their associated
 * metadata fields (tab index, title attribute, access key, meta description)
 * as well as any extra content properties stored in the content_props table.
 * Renders everything as a single scrollable table, styled like the content manager.
 *
 * @package  CMSmsMetadata
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('CMS_VERSION')) {
    exit();
}

$db  = cmsms()->GetDb();
$sub = isset($params['sub_action']) ? trim($params['sub_action']) : '';

// ── AJAX: save column-picker selection to module preferences ──────────────────
if ($sub === 'savecols') {
    $raw     = isset($_POST['cols']) ? $_POST['cols'] : '[]';
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        $clean = array_values(array_filter($decoded, function ($k) {
            return is_string($k) && preg_match('/^[a-zA-Z0-9_]{1,80}$/', $k);
        }));
        $this->SetPreference('visible_cols', json_encode($clean));
    }
    // Clear any admin-page output buffers so the response is pure JSON
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    header('Content-Type: application/json');
    echo json_encode(['ok' => true]);
    exit();
}

// ── AJAX: save filter state to module preferences ────────────────────────────
if ($sub === 'savefilter') {
    $flt_statuses = ['all', 'active', 'inactive'];
    $flt_ops      = ['contains', '==', '!=', 'empty', 'notempty', '>', '<'];
    $raw     = isset($_POST['filter']) ? $_POST['filter'] : '{}';
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        $clean = [
            'status' => in_array($decoded['status'] ?? '', $flt_statuses)
                            ? $decoded['status'] : 'all',
            'field'  => (isset($decoded['field']) && preg_match('/^[a-zA-Z0-9_]{0,80}$/', $decoded['field']))
                            ? $decoded['field'] : '',
            'op'     => in_array($decoded['op'] ?? '', $flt_ops)
                            ? $decoded['op'] : 'contains',
            'value'  => substr((string) ($decoded['value'] ?? ''), 0, 200),
        ];
        $this->SetPreference('filter_state', json_encode($clean));
    }
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    header('Content-Type: application/json');
    echo json_encode(['ok' => true]);
    exit();
}

// ── AJAX: save a single inline-edited cell value ────────────────────────────
if ($sub === 'savecell') {
    $allowed_fields = ['tabindex', 'accesskey', 'titleattr', 'metadata'];
    $content_id = isset($_POST['content_id']) ? (int) $_POST['content_id'] : 0;
    $field      = isset($_POST['field'])      ? trim((string) $_POST['field'])  : '';
    $value      = isset($_POST['value'])      ? (string) $_POST['value']        : '';

    $field_ok = false;
    if (in_array($field, $allowed_fields, true)) {
        $field_ok = true;
    } elseif (substr($field, 0, 7) === 'prop__' && strlen($field) > 7) {
        $valid_props = CMSmsMetadata_MetadataHelper::getExtraPropNames($db);
        $field_ok    = in_array(substr($field, 7), $valid_props, true);
    }

    if ($content_id <= 0 || !$field_ok) {
        while (ob_get_level() > 0) { ob_end_clean(); }
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'error' => 'invalid']);
        exit();
    }

    // ── Content lock check – reject if another user has the page open ─────────
    $lock_prefix = cms_db_prefix();
    $lock_uid    = $db->GetOne(
        "SELECT uid FROM {$lock_prefix}locks
          WHERE type = 'content' AND oid = ?
            AND (UNIX_TIMESTAMP(modified) + lifetime) > UNIX_TIMESTAMP()",
        [$content_id]
    );
    if ($lock_uid) {
        $current_uid = function_exists('get_userid') ? (int) get_userid(false) : 0;
        if ((int) $lock_uid !== $current_uid) {
            while (ob_get_level() > 0) { ob_end_clean(); }
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'error' => 'locked']);
            exit();
        }
    }

    // Per-field value length caps
    $max_lengths = [
        'tabindex'  => 6,
        'accesskey' => 5,
        'titleattr' => 255,
        'metadata'  => 65535,
    ];
    $max_len = isset($max_lengths[$field]) ? $max_lengths[$field] : 65535;
    $value   = substr($value, 0, $max_len);

    $ok = CMSmsMetadata_MetadataHelper::saveCell($db, $content_id, $field, $value);
    while (ob_get_level() > 0) { ob_end_clean(); }
    header('Content-Type: application/json');
    echo json_encode(['ok' => $ok]);
    exit();
}

// ── Fetch all content pages (ordered by hierarchy) ────────────────────────────
$pages = CMSmsMetadata_MetadataHelper::getPages($db);

// ── Discover extra prop columns from content_props ────────────────────────────
$prop_names = CMSmsMetadata_MetadataHelper::getExtraPropNames($db);

// ── Fetch all prop values indexed by [content_id][prop_name] ─────────────────
$raw_prop_values = CMSmsMetadata_MetadataHelper::getPropValues($db, $prop_names);

// ── Merge prop values into each page record for easier Smarty access ─────────
foreach ($pages as &$page) {
    $cid         = (int) $page['content_id'];
    $page['props'] = isset($raw_prop_values[$cid]) ? $raw_prop_values[$cid] : [];
}
unset($page);

// ── Fetch active content locks held by other users ────────────────────────────
$current_uid = function_exists('get_userid') ? (int) get_userid(false) : 0;
$lprefix     = cms_db_prefix();
$lock_rows   = $db->GetAll(
    "SELECT oid FROM {$lprefix}locks
      WHERE type = 'content'
        AND uid != ?
        AND (UNIX_TIMESTAMP(modified) + lifetime) > UNIX_TIMESTAMP()",
    [$current_uid]
);
$locked_ids = [];
if ($lock_rows) {
    foreach ($lock_rows as $lr) {
        $locked_ids[$lr['oid']] = true;   // string key — matches Smarty array lookup
    }
}

// ── Compute summary statistics ────────────────────────────────────────────────
$stat_total        = count($pages);
$stat_active       = 0;
$stat_with_meta    = 0;
$stat_missing_meta = 0;

foreach ($pages as $p) {
    if ((int) $p['active'] === 1) {
        $stat_active++;
    }
    $meta = trim(strip_tags((string) $p['metadata']));
    if ($meta !== '') {
        $stat_with_meta++;
    } else {
        $stat_missing_meta++;
    }
}

// ── Build column definitions (key → label) for the column picker ──────────────
// Keys used here MUST match the data-col attributes in the template.
$col_defs = [
    'active'    => $this->Lang('col_active'),
    'tabindex'  => $this->Lang('col_tabindex'),
    'accesskey' => $this->Lang('col_accesskey'),
    'titleattr' => $this->Lang('col_titleattr'),
    'metadata'  => $this->Lang('col_metadata'),
];
$prop_col_map = [];
foreach ($prop_names as $pname) {
    $col_key                = 'prop__' . $pname;
    $col_defs[$col_key]     = $pname;
    $prop_col_map[$col_key] = $pname;
}

// ── Load saved column-picker preference ───────────────────────────────────────
$all_col_keys   = array_keys($col_defs);
$saved_json     = $this->GetPreference('visible_cols', '');
if ($saved_json !== '') {
    $saved_cols   = json_decode($saved_json, true);
    // Intersect with currently available columns (props may have been removed)
    $visible_cols = is_array($saved_cols)
        ? array_values(array_intersect($saved_cols, $all_col_keys))
        : $all_col_keys;
    // If the intersect wiped everything out, fall back to all columns
    if (empty($visible_cols)) {
        $visible_cols = $all_col_keys;
    }
} else {
    $visible_cols = $all_col_keys; // default: show every column
}

// JSON-encode for safe injection into the Smarty template <script> block.
// Replace '</' with '<\/' to prevent </script> from appearing inside a script tag.
$col_defs_json    = str_replace('</', '<\/', json_encode($col_defs,    JSON_UNESCAPED_UNICODE));
$visible_cols_json = str_replace('</', '<\/', json_encode($visible_cols, JSON_UNESCAPED_UNICODE));
$save_url_json    = json_encode(
    html_entity_decode(
        $this->CreateLink($id, 'default', $returnid, '', ['sub_action' => 'savecols'], '', true),
        ENT_QUOTES | ENT_HTML5, 'UTF-8'
    )
);

// ── Load saved filter state ────────────────────────────────────────────────────────────
$flt_statuses    = ['all', 'active', 'inactive'];
$flt_ops         = ['contains', '==', '!=', 'empty', 'notempty', '>', '<'];
$default_filter  = ['status' => 'all', 'field' => '', 'op' => 'contains', 'value' => ''];
$saved_flt_raw   = $this->GetPreference('filter_state', '');
if ($saved_flt_raw !== '') {
    $sf = json_decode($saved_flt_raw, true);
    $filter_state = is_array($sf) ? [
        'status' => in_array($sf['status'] ?? '', $flt_statuses) ? $sf['status'] : 'all',
        'field'  => (isset($sf['field']) && preg_match('/^[a-zA-Z0-9_]{0,80}$/', $sf['field']))
                        ? $sf['field'] : '',
        'op'     => in_array($sf['op'] ?? '', $flt_ops) ? $sf['op'] : 'contains',
        'value'  => substr((string) ($sf['value'] ?? ''), 0, 200),
    ] : $default_filter;
} else {
    $filter_state = $default_filter;
}
$stat_inactive        = $stat_total - $stat_active;
$filter_fields        = array_merge(['page' => $this->Lang('col_page')], $col_defs);
$filter_state_json    = str_replace('</', '<\/', json_encode($filter_state,  JSON_UNESCAPED_UNICODE));
$filter_fields_json   = str_replace('</', '<\/', json_encode($filter_fields, JSON_UNESCAPED_UNICODE));
$save_filter_url_json = json_encode(
    html_entity_decode(
        $this->CreateLink($id, 'default', $returnid, '', ['sub_action' => 'savefilter'], '', true),
        ENT_QUOTES | ENT_HTML5, 'UTF-8'
    )
);
$save_cell_url_json = json_encode(
    html_entity_decode(
        $this->CreateLink($id, 'default', $returnid, '', ['sub_action' => 'savecell'], '', true),
        ENT_QUOTES | ENT_HTML5, 'UTF-8'
    )
);

// ── JSON-encode lang strings used directly inside <script> blocks ─────────────────
// Plain Smarty {$lang_*} vars embedded in single-quoted JS strings break if a
// translator ever uses an apostrophe. JSON-encoding guarantees safe embedding.
$js_lang_picker_all_selected = str_replace('</', '<\/', json_encode($this->Lang('picker_all_selected'), JSON_UNESCAPED_UNICODE));
$js_lang_filter_showing      = str_replace('</', '<\/', json_encode($this->Lang('filter_showing'),      JSON_UNESCAPED_UNICODE));
$js_lang_edit_saving         = str_replace('</', '<\/', json_encode($this->Lang('edit_saving'),         JSON_UNESCAPED_UNICODE));
$js_lang_edit_error          = str_replace('</', '<\/', json_encode($this->Lang('edit_error'),          JSON_UNESCAPED_UNICODE));
$js_lang_edit_locked         = str_replace('</', '<\/', json_encode($this->Lang('edit_locked'),         JSON_UNESCAPED_UNICODE));
$js_lang_empty_value         = str_replace('</', '<\/', json_encode($this->Lang('empty_value'),         JSON_UNESCAPED_UNICODE));
$js_lang_chip_remove         = str_replace('</', '<\/', json_encode($this->Lang('chip_remove_label'),   JSON_UNESCAPED_UNICODE));

// ── Build template variable map ───────────────────────────────────────────────
$tpl_vars = [
    'pages'              => $pages,
    'locked_ids'         => $locked_ids,
    'prop_names'         => $prop_names,
    'stat_total'         => $stat_total,
    'stat_active'        => $stat_active,
    'stat_with_meta'     => $stat_with_meta,
    'stat_missing_meta'  => $stat_missing_meta,
    'col_defs_json'        => $col_defs_json,
    'visible_cols_json'    => $visible_cols_json,
    'save_url_json'        => $save_url_json,
    'stat_inactive'        => $stat_inactive,
    'filter_state_json'    => $filter_state_json,
    'filter_fields_json'   => $filter_fields_json,
    'save_filter_url_json' => $save_filter_url_json,
    'save_cell_url_json'            => $save_cell_url_json,
    'prop_col_map'                  => $prop_col_map,
    'js_lang_picker_all_selected'   => $js_lang_picker_all_selected,
    'js_lang_filter_showing'        => $js_lang_filter_showing,
    'js_lang_edit_saving'           => $js_lang_edit_saving,
    'js_lang_edit_error'            => $js_lang_edit_error,
    'js_lang_edit_locked'           => $js_lang_edit_locked,
    'js_lang_empty_value'           => $js_lang_empty_value,
    'js_lang_chip_remove'           => $js_lang_chip_remove,
];

// ── Inject module CSS ─────────────────────────────────────────────────────────
$css_url = $this->GetModuleURLPath() . '/css/admin.css';
echo '<link rel="stylesheet" type="text/css" href="' . htmlspecialchars($css_url, ENT_QUOTES, 'UTF-8') . '">';

// ── Assign template variables ─────────────────────────────────────────────────
$smarty = cmsms()->GetSmarty();
foreach ($tpl_vars as $k => $v) {
    $smarty->assign($k, $v);
}

// Assign all language strings with the 'lang_' prefix convention
$lang_keys = [
    'page_title',
    'col_page',
    'col_active',
    'col_alias',
    'col_tabindex',
    'col_titleattr',
    'col_accesskey',
    'col_metadata',
    'no_pages',
    'yes',
    'no',
    'empty_value',
    'stat_total',
    'stat_active',
    'stat_with_meta',
    'stat_missing_meta',
    'stat_label_pages',
    'stat_label_active',
    'stat_label_with_meta',
    'stat_label_missing',
    'section_standard',
    'section_extra',
    'hint_extra_props',
    'hint_no_extra_props',
    // Column picker
    'picker_label',
    'picker_placeholder',
    'picker_all_selected',
    'picker_reset',
    // Filter bar
    'filter_status_all',
    'filter_status_active',
    'filter_status_inactive',
    'filter_showing',
    'filter_label_field',
    'filter_label_value',
    'filter_op_contains',
    'filter_op_equals',
    'filter_op_notequals',
    'filter_op_empty',
    'filter_op_notempty',
    'filter_op_gt',
    'filter_op_lt',
    'filter_clear',
    // Inline cell editor
    'edit_saving',
    'edit_error',
    'edit_locked',
    'row_locked',
    'chip_remove_label',
];
foreach ($lang_keys as $key) {
    $smarty->assign('lang_' . $key, $this->Lang($key));
}

// ── Render the template ───────────────────────────────────────────────────────
echo $smarty->fetch(__DIR__ . '/templates/admin_main.tpl');
