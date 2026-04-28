<?php
/**
 * German language strings for CMSmsMetadata.
 *
 * Placed in lang/ext/ so site administrators can override individual
 * strings without modifying the core language file.
 *
 * @package CMSmsMetadata
 */

$lang = [
    // ── Module meta ──────────────────────────────────────────────────────────
    'friendly_name'      => 'Inhalts-Metadaten',
    'module_description' => 'Übersicht der Metadaten-Felder aller Inhaltselemente: Tab-Index, Titelattribute, Tastenkürzel, Meta-Beschreibungen und benutzerdefinierte Seiteneigenschaften – alles in einer Ansicht.',
    'module_help'        =>
        '<h3>Inhalts-Metadaten – Übersicht</h3>'
      . '<p>CMSmsMetadata zeigt alle Inhaltsseiten Ihrer CMS Made Simple-Installation in einer kompakten Übersichtstabelle. '
      . 'Die Metadaten-Felder, die normalerweise nur beim Öffnen einzelner Seiten im Inhalts-Editor sichtbar sind, '
      . 'werden hier auf einen Blick dargestellt.</p>'
      . '<h4>Feste Spalten</h4>'
      . '<ul>'
      . '<li><strong>Seite</strong> – Seitentitel und URL-Alias, eingerückt entsprechend der Seitenhierarchie.</li>'
      . '<li><strong>Aktiv</strong> – ob die Seite aktuell aktiv (veröffentlicht) ist.</li>'
      . '<li><strong>Tab-Index</strong> – der auf der Seite eingestellte <code>tabindex</code>-Wert für die Tastaturnavigation.</li>'
      . '<li><strong>Tastenkürzel</strong> – das auf der Seite eingestellte <code>accesskey</code>-Zeichen.</li>'
      . '<li><strong>Titelattribut</strong> – der HTML-<code>title</code>-Attributwert (Tooltip-Text) der Seite.</li>'
      . '<li><strong>Meta-Beschreibung</strong> – der Text im Metadaten-Feld der Seite, der typischerweise als Suchmaschinen-Beschreibung verwendet wird.</li>'
      . '</ul>'
      . '<h4>Zusätzliche Eigenschaftsspalten</h4>'
      . '<p>Alle weiteren Eigenschaften aus der <code>content_props</code>-Tabelle (z.B. <code>page_image</code>, '
      . '<code>extra1</code>, <code>extra2</code> oder benutzerdefinierte Template-Eigenschaften) werden automatisch erkannt '
      . 'und erscheinen als zusätzliche Spalten rechts. Textkörper-Felder werden automatisch ausgeblendet.</p>'
      . '<h4>Farbliche Kennzeichnung</h4>'
      . '<p>Zellen ohne Inhalt werden gedämpft dargestellt. Die Statistikleiste oben zeigt, '
      . 'wie viele Seiten keine Meta-Beschreibung haben.</p>'
      . '<h4>Spaltensichtbarkeit &amp; Filter</h4>'
      . '<p>Mit der Spaltenauswahl können einzelne Spalten ein- oder ausgeblendet werden. Die Filterleiste ermöglicht '
      . 'das Einschränken der Zeilen nach Seitenstatus (Alle / Aktiv / Inaktiv) und nach Feldwerten mit Operatoren '
      . 'wie enthält, gleich, ungleich, leer u. v. m. Spaltenauswahl und Filterzustand werden sitzungsübergreifend gespeichert.</p>'
      . '<h4>Direktbearbeitung</h4>'
      . '<p>Durch Doppelklick auf eine Zelle in den Spalten Tab-Index, Tastenkürzel, Titelattribut, Meta-Beschreibung oder '
      . 'zusätzliche Eigenschaften kann der Wert direkt bearbeitet werden. Änderungen werden sofort in der Datenbank gespeichert. '
      . 'Mit Escape wird die Bearbeitung ohne Speicherung abgebrochen.</p>',

    // ── Install / uninstall ───────────────────────────────────────────────────
    'postinstall'        => 'CMSmsMetadata wurde installiert. Gehen Sie zu <em>Inhalt → Inhalts-Metadaten</em>, um die Metadaten-Übersicht anzuzeigen.',
    'confirm_uninstall'  => 'Das Modul CMSmsMetadata wird entfernt. Es werden keine Inhaltsdaten gelöscht. Sind Sie sicher?',

    // ── Page title ────────────────────────────────────────────────────────────
    'page_title'         => 'Inhalts-Metadaten – Übersicht',

    // ── Summary statistics ────────────────────────────────────────────────────
    'stat_label_pages'      => 'Seiten gesamt',
    'stat_label_active'     => 'aktiv',
    'stat_label_with_meta'  => 'mit Meta-Beschreibung',
    'stat_label_missing'    => 'ohne Meta-Beschreibung',

    // ── Table column headings ─────────────────────────────────────────────────
    'col_page'           => 'Seite',
    'col_active'         => 'Aktiv',
    'col_alias'          => 'Alias',
    'col_tabindex'       => 'Tab-Index',
    'col_titleattr'      => 'Titelattribut',
    'col_accesskey'      => 'Tastenkürzel',
    'col_metadata'       => 'Meta-Beschreibung',

    // ── Empty / null indicator ────────────────────────────────────────────────
    'empty_value'        => '–',
    'yes'                => 'Ja',
    'no'                 => 'Nein',

    // ── Section labels ────────────────────────────────────────────────────────
    'section_standard'   => 'Standard-Metadaten',
    'section_extra'      => 'Seiteneigenschaften',

    // ── Footer hints ──────────────────────────────────────────────────────────
    'hint_extra_props'    => 'Zusätzliche Spalten werden automatisch aus der content_props-Tabelle ermittelt. Textkörper-Felder werden automatisch ausgeblendet.',
    'hint_no_extra_props' => 'Für den aktuellen Inhaltsbestand wurden keine zusätzlichen Seiteneigenschaften in der content_props-Tabelle gefunden.',

    // ── Platzhalter ───────────────────────────────────────────────────────────
    'no_pages'           => 'In der Datenbank wurden keine Inhaltsseiten gefunden.',

    // ── Spaltenauswahl ────────────────────────────────────────────────────────
    'picker_label'        => 'Sichtbare Spalten',
    'picker_placeholder'  => 'Klicken, um eine Spalte hinzuzufügen…',
    'picker_all_selected' => 'Alle Spalten sind sichtbar.',
    'picker_reset'        => 'Alle anzeigen',

    // ── Filterleiste ───────────────────────────────────────────────────────────
    'filter_status_all'      => 'Alle',
    'filter_status_active'   => 'Aktiv',
    'filter_status_inactive' => 'Inaktiv',
    'filter_showing'         => '%shown% von %total% angezeigt',
    'filter_label_field'     => 'Filtern nach Feld:',
    'filter_label_value'     => 'Wert…',
    'filter_op_contains'     => 'enthält',
    'filter_op_equals'       => '==  gleich',
    'filter_op_notequals'    => '!=  ungleich',
    'filter_op_empty'        => 'ist leer',
    'filter_op_notempty'     => 'ist nicht leer',
    'filter_op_gt'           => '>  größer als',
    'filter_op_lt'           => '<  kleiner als',
    'filter_clear'           => 'Filter zurücksetzen',

    // -- Inline-Zellbearbeitung --------------------------------------------------
    'edit_saving'        => 'Wird gespeichert…',
    'edit_error'         => 'Fehler beim Speichern. Doppelklicken zum Wiederholen.',
    'edit_locked'        => 'Diese Seite wird gerade von einem anderen Benutzer bearbeitet.',
    'row_locked'         => 'Diese Seite wird gerade von einem anderen Benutzer bearbeitet und kann hier nicht geändert werden.',
    'chip_remove_label'  => 'Entfernen',
];

