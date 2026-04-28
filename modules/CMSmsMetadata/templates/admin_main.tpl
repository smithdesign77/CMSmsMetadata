{*
 * CMSmsMetadata – Admin Overview Template
 *
 * Renders a horizontally-scrollable metadata table for all content pages,
 * styled to match the standard CMS Made Simple admin interface.
 *
 * Variables injected from action.default.php:
 *   $pages             – array of page rows (incl. pre-merged 'props' sub-array)
 *   $prop_names        – array of extra prop column names discovered in DB
 *   $col_defs_json     – JSON object {key: label} for the column picker
 *   $visible_cols_json – JSON array of currently visible column keys
 *   $save_url_json     – JSON-encoded AJAX save URL
 *   $stat_*            – summary statistics
 *   $lang_*            – translated UI strings
 *}

<div id="CMSmsMetadata" class="pagecontainer">

  {* ── Page header ── *}
  <div class="pageheader">
    <h2>{$lang_page_title}</h2>
  </div>

  <div class="pagecontents">

    {* ── Filter bar: quick status pills + custom field filter ──────────────────────── *}
    <div class="meta-filter-wrap" id="meta-filter-wrap">

      {* ── Row 1: status pills + live row counter ── *}
      <div class="meta-filter-bar">
        <div class="meta-filter-pills">
          <button type="button" class="meta-filter-pill" id="pill-all"
                  data-status="all">{$lang_filter_status_all} <span class="meta-pill-count">{$stat_total}</span></button>
          <button type="button" class="meta-filter-pill" id="pill-active"
                  data-status="active">{$lang_filter_status_active} <span class="meta-pill-count">{$stat_active}</span></button>
          <button type="button" class="meta-filter-pill" id="pill-inactive"
                  data-status="inactive">{$lang_filter_status_inactive} <span class="meta-pill-count">{$stat_inactive}</span></button>
        </div>
        <span class="meta-filter-showing" id="meta-filter-showing"></span>
      </div>

      {* ── Row 2: custom field filter ── *}
      <div class="meta-custom-filter" id="meta-custom-filter">
        <label class="meta-cfilter-label" for="cfilter-field">{$lang_filter_label_field}</label>
        <select class="meta-cfilter-select" id="cfilter-field">
          <option value="">&#9776;</option>
        </select>
        <select class="meta-cfilter-select meta-cfilter-op" id="cfilter-op">
          <option value="contains">{$lang_filter_op_contains}</option>
          <option value="==">{$lang_filter_op_equals}</option>
          <option value="!=">{$lang_filter_op_notequals}</option>
          <option value="empty">{$lang_filter_op_empty}</option>
          <option value="notempty">{$lang_filter_op_notempty}</option>
          <option value="&gt;">{$lang_filter_op_gt}</option>
          <option value="&lt;">{$lang_filter_op_lt}</option>
        </select>
        <input type="text" class="meta-cfilter-value" id="cfilter-value"
               placeholder="{$lang_filter_label_value}">
        <button type="button" class="meta-cfilter-clear" id="cfilter-clear">&times; {$lang_filter_clear}</button>
      </div>

    </div>{* /meta-filter-wrap *}

    {* ── Column picker ──────────────────────────────────────────────────────── *}
    {*  The first column (Page) is always visible and excluded from the picker.  *}
    {*  All other columns can be toggled. State is saved to the CMS preferences. *}
    <div class="meta-col-picker" id="meta-col-picker">
      <div class="meta-picker-bar">
        <span class="meta-picker-label">{$lang_picker_label}</span>
        <button type="button" class="meta-picker-reset" id="meta-picker-reset"
                title="{$lang_picker_reset}">{$lang_picker_reset}</button>
      </div>
      <div class="meta-picker-input" id="meta-picker-input"
           tabindex="0" role="combobox" aria-expanded="false"
           aria-haspopup="listbox" aria-label="{$lang_picker_label}">
        <span class="meta-picker-ph" id="meta-picker-ph">{$lang_picker_placeholder}</span>
      </div>
      <div class="meta-picker-dropdown" id="meta-picker-dropdown"
           role="listbox" style="display:none"></div>
    </div>

    {if $pages}

      {* ── Scrollable metadata table ── *}
      <div class="meta-table-wrapper">
        <table class="pagetable meta-table" id="meta-data-table" cellspacing="0">

          <thead>
            <tr>
              {* ── Fixed: Page (always visible, not in picker) ── *}
              <th class="col-page">{$lang_col_page}</th>
              {* ── Optional standard columns ── *}
              <th class="col-center col-active"   data-col="active"    >{$lang_col_active}</th>
              <th class="col-center col-tabindex" data-col="tabindex"  >{$lang_col_tabindex}</th>
              <th class="col-center col-accesskey" data-col="accesskey">{$lang_col_accesskey}</th>
              <th class="col-titleattr"            data-col="titleattr" >{$lang_col_titleattr}</th>
              <th class="col-metadata"             data-col="metadata"  >{$lang_col_metadata}</th>
              {* ── Dynamic extra-prop columns ── *}
              {foreach from=$prop_col_map key=col_key item=pname}
                <th class="col-prop" data-col="{$col_key|escape}">{$pname|escape}</th>
              {/foreach}
            </tr>
          </thead>

          <tbody>
            {foreach from=$pages item=page}
              <tr class="{if not $page.active}row-inactive{/if}{if isset($locked_ids[$page.content_id])} row-locked{/if}" data-cid="{$page.content_id}"{if isset($locked_ids[$page.content_id])} data-locked="1"{/if}>

                {* ── Page name (always visible, filterable by field 'page') ── *}
                <td class="col-page" data-col="page"
                    data-val="{$page.content_name|escape}"
                    style="padding-left:{$page.indent_px}px">
                  {if isset($locked_ids[$page.content_id])}<span class="meta-lock-badge" title="{$lang_row_locked}">&#128274;</span>{/if}<strong>{$page.content_name|escape}</strong>
                  {if $page.content_alias neq ''}
                    <br><span class="meta-alias">/{$page.content_alias|escape}</span>
                  {/if}
                </td>

                {* ── Active badge ── *}
                <td class="col-center col-active" data-col="active"
                    data-val="{if $page.active}1{else}0{/if}">
                  {if $page.active}
                    <span class="meta-dot meta-dot-on" title="{$lang_yes}">&#9679;</span>
                  {else}
                    <span class="meta-dot meta-dot-off" title="{$lang_no}">&#9675;</span>
                  {/if}
                </td>

                {* ── Tab index ── *}
                <td class="col-center col-tabindex" data-col="tabindex"
                    data-val="{$page.tabindex|default:''|escape}" data-editable="1">
                  {if $page.tabindex neq '' and $page.tabindex neq '0' and $page.tabindex neq null}
                    <code>{$page.tabindex|escape}</code>
                  {else}
                    <span class="meta-empty">{$lang_empty_value}</span>
                  {/if}
                </td>

                {* ── Access key ── *}
                <td class="col-center col-accesskey" data-col="accesskey"
                    data-val="{$page.accesskey|default:''|escape}" data-editable="1">
                  {if $page.accesskey neq ''}
                    <code>{$page.accesskey|escape}</code>
                  {else}
                    <span class="meta-empty">{$lang_empty_value}</span>
                  {/if}
                </td>

                {* ── Title attribute ── *}
                <td class="col-titleattr" data-col="titleattr"
                    data-val="{$page.titleattribute|default:''|escape}" data-editable="1">
                  {if $page.titleattribute neq ''}
                    <span class="meta-text" title="{$page.titleattribute|escape}">{$page.titleattribute|escape|truncate:45:'…'}</span>
                  {else}
                    <span class="meta-empty">{$lang_empty_value}</span>
                  {/if}
                </td>

                {* ── Meta description (strips HTML, truncates) ── *}
                {assign var="meta_clean" value=$page.metadata|strip_tags|trim}
                <td class="col-metadata{if $meta_clean eq ''} meta-cell-empty{/if}" data-col="metadata"
                    data-val="{$meta_clean|escape}" data-editable="1">
                  {if $meta_clean neq ''}
                    <span class="meta-text" title="{$meta_clean|escape}">{$meta_clean|truncate:90:'…'|escape}</span>
                  {else}
                    <span class="meta-empty">{$lang_empty_value}</span>
                  {/if}
                </td>

                {* ── Dynamic extra-prop columns ── *}
                {foreach from=$prop_col_map key=col_key item=pname}
                  {assign var="pval" value=$page.props[$pname]|default:''}
                  <td class="col-prop{if $pval eq ''} meta-cell-empty{/if}"
                      data-col="{$col_key|escape}"
                      data-val="{$pval|strip_tags|trim|escape}" data-editable="1">
                    {if $pval neq ''}
                      {assign var="pval_clean" value=$pval|strip_tags|trim}
                      <span class="meta-text" title="{$pval_clean|escape}">{$pval_clean|truncate:45:'…'|escape}</span>
                    {else}
                      <span class="meta-empty">{$lang_empty_value}</span>
                    {/if}
                  </td>
                {/foreach}

              </tr>
            {/foreach}
          </tbody>

        </table>
      </div>{* /meta-table-wrapper *}

    {else}
      <p class="pageinfo"><em>{$lang_no_pages}</em></p>
    {/if}

    {* ── Footer note about extra columns ── *}
    {if $prop_names}
      <p class="meta-footnote">{$lang_hint_extra_props}</p>
    {else}
      <p class="meta-footnote">{$lang_hint_no_extra_props}</p>
    {/if}

  </div>{* /pagecontents *}

</div>{* /CMSmsMetadata *}

{* ── JavaScript: column picker + row filter ─────────────────────────────────── *}
{*  Self-contained, no library dependencies required.                              *}
<script>
/* ════════════════════════════════════════════════════════════════════
   COLUMN PICKER  –  hides/shows table columns
   ════════════════════════════════════════════════════════════════════ */
(function () {
    'use strict';

    var ALL_COLS = {$col_defs_json};
    var selected = {$visible_cols_json};
    var SAVE_URL = {$save_url_json};

    var picker   = document.getElementById('meta-col-picker');
    var inputBox = document.getElementById('meta-picker-input');
    var dropdown = document.getElementById('meta-picker-dropdown');
    var ph       = document.getElementById('meta-picker-ph');
    var resetBtn = document.getElementById('meta-picker-reset');
    var table    = document.getElementById('meta-data-table');

    function applyVisibility() {
        if (!table) { return; }
        var keys = Object.keys(ALL_COLS);
        for (var i = 0; i < keys.length; i++) {
            var key   = keys[i];
            var show  = selected.indexOf(key) !== -1;
            var safe  = key.replace(/\\/g, '\\\\').replace(/"/g, '\\"');
            var cells = table.querySelectorAll('[data-col="' + safe + '"]');
            for (var j = 0; j < cells.length; j++) {
                cells[j].style.display = show ? '' : 'none';
            }
        }
    }

    function renderChips() {
        var old = inputBox.querySelectorAll('.meta-chip');
        for (var i = old.length - 1; i >= 0; i--) { old[i].parentNode.removeChild(old[i]); }
        ph.style.display = (selected.length === 0) ? '' : 'none';
        for (var s = 0; s < selected.length; s++) {
            var key   = selected[s];
            var label = ALL_COLS[key] || key;
            var chip  = document.createElement('span');
            chip.className = 'meta-chip';
            chip.setAttribute('data-col', key);
            var txt = document.createElement('span');
            txt.className   = 'meta-chip-label';
            txt.textContent = label;
            var btn = document.createElement('button');
            btn.type      = 'button';
            btn.className = 'meta-chip-x';
            btn.title = {$js_lang_chip_remove};
            btn.setAttribute('aria-label', {$js_lang_chip_remove} + ' ' + label);
            btn.innerHTML = '&times;';
            (function (k) {
                btn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    deselect(k);
                });
            }(key));
            chip.appendChild(txt);
            chip.appendChild(btn);
            inputBox.insertBefore(chip, ph);
        }
    }

    function renderDropdown() {
        while (dropdown.firstChild) { dropdown.removeChild(dropdown.firstChild); }
        var available = Object.keys(ALL_COLS).filter(function (k) {
            return selected.indexOf(k) === -1;
        });
        if (available.length === 0) {
            var msg = document.createElement('div');
            msg.className   = 'meta-picker-empty';
            msg.textContent = {$js_lang_picker_all_selected};
            dropdown.appendChild(msg);
            return;
        }
        for (var i = 0; i < available.length; i++) {
            var key  = available[i];
            var item = document.createElement('div');
            item.className   = 'meta-picker-item';
            item.setAttribute('role', 'option');
            item.setAttribute('data-col', key);
            item.textContent = ALL_COLS[key] || key;
            (function (k) {
                item.addEventListener('click', function (e) {
                    e.stopPropagation();
                    select(k);
                    closeDD();
                });
            }(key));
            dropdown.appendChild(item);
        }
    }

    function select(key)   { if (selected.indexOf(key) === -1) { selected.push(key); } update(); }
    function deselect(key) { selected = selected.filter(function (k) { return k !== key; }); update(); }
    function resetAll()    { selected = Object.keys(ALL_COLS).slice(); update(); closeDD(); }
    function update()      { renderChips(); applyVisibility(); saveState(); }

    function openDD()  { renderDropdown(); dropdown.style.display = ''; inputBox.classList.add('meta-picker-open'); inputBox.setAttribute('aria-expanded', 'true'); }
    function closeDD() { dropdown.style.display = 'none'; inputBox.classList.remove('meta-picker-open'); inputBox.setAttribute('aria-expanded', 'false'); }

    function saveState() {
        try {
            var fd = new FormData();
            fd.append('cols', JSON.stringify(selected));
            fetch(SAVE_URL, { method: 'POST', body: fd, credentials: 'same-origin', keepalive: true }).catch(function () {});
        } catch (e) {}
    }

    inputBox.addEventListener('click', function (e) {
        if (!e.target.classList.contains('meta-chip-x')) {
            if (dropdown.style.display === 'none') { openDD(); } else { closeDD(); }
        }
    });
    inputBox.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openDD(); }
        if (e.key === 'Escape') { closeDD(); }
    });
    if (resetBtn) { resetBtn.addEventListener('click', function () { resetAll(); }); }
    document.addEventListener('click', function (e) {
        if (picker && !picker.contains(e.target)) { closeDD(); }
    });

    renderChips();
    applyVisibility();
}());

/* ════════════════════════════════════════════════════════════════════
   ROW FILTER  –  status pills + custom field/operator/value filter
   ════════════════════════════════════════════════════════════════════ */
(function () {
    'use strict';

    /* Data injected by PHP */
    var FILTER_FIELDS    = {$filter_fields_json};
    var flt              = {$filter_state_json};
    var SAVE_FILTER_URL  = {$save_filter_url_json};

    /* DOM */
    var table     = document.getElementById('meta-data-table');
    var pillAll   = document.getElementById('pill-all');
    var pillAct   = document.getElementById('pill-active');
    var pillInact = document.getElementById('pill-inactive');
    var showing   = document.getElementById('meta-filter-showing');
    var fieldSel  = document.getElementById('cfilter-field');
    var opSel     = document.getElementById('cfilter-op');
    var valInput  = document.getElementById('cfilter-value');
    var clearBtn  = document.getElementById('cfilter-clear');

    /* Pill list as array-of-pairs to avoid inline {}-object Smarty conflicts */
    var pillPairs = [
        [pillAll,   'all'     ],
        [pillAct,   'active'  ],
        [pillInact, 'inactive']
    ];

    /* Populate field <select> from injected map */
    var fkeys = Object.keys(FILTER_FIELDS);
    for (var fi = 0; fi < fkeys.length; fi++) {
        var opt = document.createElement('option');
        opt.value       = fkeys[fi];
        opt.textContent = FILTER_FIELDS[fkeys[fi]];
        fieldSel.appendChild(opt);
    }

    /* ── Read raw cell value for a field key from a given <tr> ────────────── */
    function getCellVal(row, field) {
        var safe = field.replace(/\\/g, '\\\\').replace(/"/g, '\\"');
        var cell = row.querySelector('[data-col="' + safe + '"]');
        return cell ? (cell.getAttribute('data-val') || '').trim() : '';
    }

    /* ── Match a single value against operator + target ───────────────────── */
    function matchVal(val, op, target) {
        var v = val.toLowerCase(), t = target.toLowerCase();
        if (op === 'contains')  { return v.indexOf(t) !== -1; }
        if (op === '==')        { return v === t; }
        if (op === '!=')        { return v !== t; }
        if (op === 'empty')     { return val === ''; }
        if (op === 'notempty')  { return val !== ''; }
        var vn = parseFloat(val), tn = parseFloat(target);
        if (op === '>')  { return isNaN(vn) ? val > target : vn > tn; }
        if (op === '<')  { return isNaN(vn) ? val < target : vn < tn; }
        return true;
    }

    /* ── Apply all active filters to tbody rows ───────────────────────────── */
    function applyFilters() {
        if (!table) { return; }
        var rows       = table.querySelectorAll('tbody tr');
        var shown      = 0;
        var noValOp    = (flt.op === 'empty' || flt.op === 'notempty');
        var customOn   = (flt.field !== '') && (noValOp || flt.value.length > 0);

        for (var i = 0; i < rows.length; i++) {
            var row      = rows[i];
            var inactive = row.classList.contains('row-inactive');

            var statusOk = true;
            if (flt.status === 'active')   { statusOk = !inactive; }
            if (flt.status === 'inactive') { statusOk =  inactive; }

            var customOk = true;
            if (customOn) {
                customOk = matchVal(getCellVal(row, flt.field), flt.op, flt.value);
            }

            var show = statusOk && customOk;
            row.style.display = show ? '' : 'none';
            if (show) { shown++; }
        }

        if (showing) {
            var total = rows.length;
            showing.textContent = (shown === total) ? '' :
                {$js_lang_filter_showing}.replace('%shown%', shown).replace('%total%', total);
        }
    }

    /* ── Pill highlighting ────────────────────────────────────────────────── */
    function updatePills() {
        for (var i = 0; i < pillPairs.length; i++) {
            if (pillPairs[i][0]) {
                pillPairs[i][0].classList.toggle('meta-pill-selected',
                    flt.status === pillPairs[i][1]);
            }
        }
    }

    /* ── Sync custom-filter UI to flt state ──────────────────────────────── */
    function updateCustomUI() {
        fieldSel.value = flt.field;
        opSel.value    = flt.op;
        valInput.value = flt.value;
        var hasField = (flt.field !== '');
        var noVal    = (flt.op === 'empty' || flt.op === 'notempty');
        opSel.style.display    = hasField ? '' : 'none';
        valInput.style.display = (hasField && !noVal) ? '' : 'none';
        clearBtn.style.display = hasField ? '' : 'none';
    }

    /* ── Persist filter state to DB ──────────────────────────────────────── */
    function saveFilter() {
        try {
            var fd = new FormData();
            fd.append('filter', JSON.stringify(flt));
            fetch(SAVE_FILTER_URL, { method: 'POST', body: fd, credentials: 'same-origin', keepalive: true }).catch(function () {});
        } catch (e) {}
    }

    /* ── Combined update ─────────────────────────────────────────────────── */
    function update(andSave) {
        updatePills();
        updateCustomUI();
        applyFilters();
        if (andSave) { saveFilter(); }
    }

    /* ── Event: status pills ─────────────────────────────────────────────── */
    for (var pi = 0; pi < pillPairs.length; pi++) {
        (function (el, status) {
            if (!el) { return; }
            el.addEventListener('click', function () {
                flt.status = status;
                update(true);
            });
        }(pillPairs[pi][0], pillPairs[pi][1]));
    }

    /* ── Event: field selector ───────────────────────────────────────────── */
    fieldSel.addEventListener('change', function () {
        flt.field = this.value;
        if (!flt.field) { flt.op = 'contains'; flt.value = ''; }
        update(true);
    });

    /* ── Event: operator selector ───────────────────────────────────────── */
    opSel.addEventListener('change', function () {
        flt.op = this.value;
        if (flt.op === 'empty' || flt.op === 'notempty') { flt.value = ''; }
        update(true);
    });

    /* ── Event: value input (filter on every keystroke, save on blur) ───── */
    valInput.addEventListener('keyup', function () {
        flt.value = this.value;
        applyFilters();
    });
    valInput.addEventListener('change', function () {
        flt.value = this.value;
        update(true);
    });

    /* ── Event: clear button ─────────────────────────────────────────────── */
    clearBtn.addEventListener('click', function () {
        flt.field = ''; flt.op = 'contains'; flt.value = '';
        update(true);
    });

    /* ── Initialise ──────────────────────────────────────────────────────── */
    update(false);

}());

/* ---------------------------------------------------------------
   INLINE CELL EDITOR
   Double-click any editable cell to edit its value in place.
   Enter / blur saves; Escape cancels.
   --------------------------------------------------------------- */
(function () {
    'use strict';

    var SAVE_CELL_URL = {$save_cell_url_json};
    var MSG_SAVING  = {$js_lang_edit_saving};
    var MSG_ERROR   = {$js_lang_edit_error};
    var MSG_LOCKED  = {$js_lang_edit_locked};
    var MSG_EMPTY   = {$js_lang_empty_value};

    var active = null;   /* the <td> currently open for editing */

    /* ── Safe HTML entity encoder ──────────────────────────────────── */
    function esc(s) {
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    /* ── Re-render a cell's visible content from data-val ──────────── */
    function renderCell(td) {
        var field = td.getAttribute('data-col');
        var val   = td.getAttribute('data-val') || '';
        var empty = (val === '') || (field === 'tabindex' && val === '0');
        if (empty) {
            td.innerHTML = '<span class="meta-empty">' + MSG_EMPTY + '</span>';
            td.classList.remove('meta-cell-error');
            return;
        }
        var isCode = (field === 'tabindex' || field === 'accesskey');
        var limit  = (field === 'metadata') ? 90 : 45;
        var trunc  = (val.length > limit) ? val.slice(0, limit) + '\u2026' : val;
        td.innerHTML = isCode
            ? '<code>' + esc(val) + '</code>'
            : '<span class="meta-text" title="' + esc(val) + '">' + esc(trunc) + '</span>';
        td.classList.remove('meta-cell-error');
    }

    /* ── Open an inline editor inside a cell ───────────────────────── */
    function openEdit(td) {
        if (active) { cancelEdit(active); }
        active = td;
        var field = td.getAttribute('data-col');
        var val   = td.getAttribute('data-val') || '';
        var multi = (field === 'metadata' || field.indexOf('prop__') === 0);
        var input = document.createElement(multi ? 'textarea' : 'input');
        input.className = 'meta-cell-input';
        if (!multi) { input.type = 'text'; }
        input.value = val;
        td.classList.add('meta-cell-editing');
        td.classList.remove('meta-cell-error');
        td.innerHTML = '';
        td.appendChild(input);
        input.focus();
        if (input.select) { input.select(); }
        input.addEventListener('keydown', function (e) {
            if (!multi && e.key === 'Enter') { e.preventDefault(); commitEdit(td, input); }
            if (e.key === 'Escape')          { cancelEdit(td); }
        });
        input.addEventListener('blur', function () {
            /* slight delay so Escape handler fires before blur triggers save */
            setTimeout(function () { if (active === td) { commitEdit(td, input); } }, 120);
        });
    }

    /* ── Cancel – restore original display, no server call ─────────── */
    function cancelEdit(td) {
        if (!td) { return; }
        td.classList.remove('meta-cell-editing', 'meta-cell-saving', 'meta-cell-error');
        renderCell(td);
        if (active === td) { active = null; }
    }

    /* ── Commit – POST to server, update cell on success ───────────── */
    function commitEdit(td, input) {
        if (active !== td) { return; }   /* already cancelled via Escape */
        active = null;
        var field  = td.getAttribute('data-col');
        var newVal = input.value;
        var oldVal = td.getAttribute('data-val') || '';
        if (newVal === oldVal) { cancelEdit(td); return; }   /* no-op */

        td.classList.remove('meta-cell-editing', 'meta-cell-error');
        td.classList.add('meta-cell-saving');
        td.innerHTML = '<span class="meta-saving-indicator">' + MSG_SAVING + '</span>';

        var cid = td.parentElement ? td.parentElement.getAttribute('data-cid') : '';
        var fd  = new FormData();
        fd.append('content_id', cid);
        fd.append('field',      field);
        fd.append('value',      newVal);

        fetch(SAVE_CELL_URL, { method: 'POST', body: fd, credentials: 'same-origin' })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                td.classList.remove('meta-cell-saving');
                if (data.ok) {
                    td.setAttribute('data-val', newVal);
                    renderCell(td);
                } else {
                    td.classList.add('meta-cell-error');
                    var msg = (data.error === 'locked') ? MSG_LOCKED : MSG_ERROR;
                    td.innerHTML = '<span class="meta-error-indicator" title="' + msg + '">'
                        + esc(newVal) + '</span>';
                }
            })
            .catch(function () {
                td.classList.remove('meta-cell-saving');
                td.classList.add('meta-cell-error');
                td.innerHTML = '<span class="meta-error-indicator" title="' + MSG_ERROR + '">'
                    + esc(newVal) + '</span>';
            });
    }

    /* ── Delegate dblclick on the whole table ──────────────────────── */
    var tbl = document.getElementById('meta-data-table');
    if (tbl) {
        tbl.addEventListener('dblclick', function (e) {
            var td = e.target.closest ? e.target.closest('td[data-editable="1"]') : null;
            if (td && td.parentElement && td.parentElement.getAttribute('data-locked') === '1') { td = null; }
            if (td) { openEdit(td); }
        });
    }

}());
</script>
