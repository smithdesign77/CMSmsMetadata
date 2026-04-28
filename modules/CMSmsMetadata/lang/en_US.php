<?php
/**
 * Language strings for CMSmsMetadata (English – default).
 *
 * @package CMSmsMetadata
 */

$lang = [
    // ── Module meta ──────────────────────────────────────────────────────────
    'friendly_name'      => 'Content Metadata',
    'module_description' => 'Overview of content metadata fields: tab index, title attributes, access keys, meta descriptions, and custom page properties — all in one admin view.',
    'module_help'        =>
        '<h3>Content Metadata Overview</h3>'
      . '<p>CMSmsMetadata provides a compact, interactive overview of every content page in your CMS Made Simple installation, '
      . 'showing the metadata fields that are normally only visible when you open each page individually in the content editor.</p>'
      . '<h4>Fixed columns</h4>'
      . '<ul>'
      . '<li><strong>Page</strong> – page title and URL alias, indented to reflect the page hierarchy.</li>'
      . '<li><strong>Active</strong> – whether the page is currently active (published).</li>'
      . '<li><strong>Tab Index</strong> – the <code>tabindex</code> value set on the page, used for keyboard navigation accessibility.</li>'
      . '<li><strong>Access Key</strong> – the <code>accesskey</code> shortcut character set on the page.</li>'
      . '<li><strong>Title Attribute</strong> – the HTML <code>title</code> attribute value (tooltip text) set on the page.</li>'
      . '<li><strong>Meta Description</strong> – the text stored in the page\'s Metadata field, typically used for search engine descriptions.</li>'
      . '</ul>'
      . '<h4>Extra Property columns</h4>'
      . '<p>Any additional properties stored in the <code>content_props</code> table (such as <code>page_image</code>, '
      . '<code>extra1</code>, <code>extra2</code>, or custom template properties) are automatically discovered and appear '
      . 'as additional columns to the right. Body-content fields are excluded.</p>'
      . '<h4>Colour coding</h4>'
      . '<p>Cells with a missing value are shown in a muted colour. The summary bar at the top highlights how many pages are missing a meta description.</p>'
      . '<h4>Column visibility &amp; filtering</h4>'
      . '<p>Use the column picker to show or hide individual columns. The filter bar lets you narrow rows by page status '
      . '(All / Active / Inactive) and by any field value using operators such as contains, equals, not equals, '
      . 'is empty, and more. Both the column selection and filter state are saved between sessions.</p>'
      . '<h4>Inline editing</h4>'
      . '<p>Double-click any cell in the Tab Index, Access Key, Title Attribute, Meta Description, or extra property columns '
      . 'to edit the value in place. Changes are saved to the database immediately. Press Escape to cancel without saving.</p>',

    // ── Install / uninstall ───────────────────────────────────────────────────
    'postinstall'        => 'CMSmsMetadata has been installed. Visit <em>Content → Content Metadata</em> to view your metadata overview.',
    'confirm_uninstall'  => 'This will remove the CMSmsMetadata module. No content data will be deleted. Are you sure?',

    // ── Page title ────────────────────────────────────────────────────────────
    'page_title'         => 'Content Metadata Overview',

    // ── Summary statistics ────────────────────────────────────────────────────
    'stat_label_pages'      => 'pages total',
    'stat_label_active'     => 'active',
    'stat_label_with_meta'  => 'with meta description',
    'stat_label_missing'    => 'missing meta description',

    // ── Table column headings ─────────────────────────────────────────────────
    'col_page'           => 'Page',
    'col_active'         => 'Active',
    'col_alias'          => 'Alias',
    'col_tabindex'       => 'Tab Index',
    'col_titleattr'      => 'Title Attribute',
    'col_accesskey'      => 'Access Key',
    'col_metadata'       => 'Meta Description',

    // ── Empty / null indicator ────────────────────────────────────────────────
    'empty_value'        => '–',
    'yes'                => 'Yes',
    'no'                 => 'No',

    // ── Section labels ────────────────────────────────────────────────────────
    'section_standard'   => 'Standard Metadata',
    'section_extra'      => 'Page Properties',

    // ── Footer hints ──────────────────────────────────────────────────────────
    'hint_extra_props'     => 'Extra columns are auto-discovered from the content_props table. Body-content fields are excluded automatically.',
    'hint_no_extra_props'  => 'No extra page properties were found in the content_props table for the current content set.',

    // ── Placeholder strings (for future use) ─────────────────────────────────
    'no_pages'           => 'No content pages were found in the database.',

    // ── Column picker ───────────────────────────────────────────────────────────
    'picker_label'        => 'Visible columns',
    'picker_placeholder'  => 'Click to add a column…',
    'picker_all_selected' => 'All columns are visible.',
    'picker_reset'        => 'Show all',

    // ── Filter bar ─────────────────────────────────────────────────────────────
    'filter_status_all'      => 'All',
    'filter_status_active'   => 'Active',
    'filter_status_inactive' => 'Inactive',
    'filter_showing'         => 'Showing %shown% of %total%',
    'filter_label_field'     => 'Filter by field:',
    'filter_label_value'     => 'value…',
    'filter_op_contains'     => 'contains',
    'filter_op_equals'       => '==  equals',
    'filter_op_notequals'    => '!=  not equals',
    'filter_op_empty'        => 'is empty',
    'filter_op_notempty'     => 'is not empty',
    'filter_op_gt'           => '>  greater than',
    'filter_op_lt'           => '<  less than',
    'filter_clear'           => 'Clear filter',

    // -- Inline cell editor ------------------------------------------------------
    'edit_saving'        => 'Saving…',
    'edit_error'         => 'Error saving. Double-click to retry.',
    'chip_remove_label'  => 'Remove',
];

