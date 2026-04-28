# Changelog – CMSmsMetadata

All notable changes to this project will be documented in this file.
Format loosely follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

---

## [1.1.0] – 2026-04-28

### Added
- **Column visibility picker**: chip-based token widget to show/hide individual
  metadata columns. Selection is saved in the CMS preferences table per installation.
- **Filter bar**: clickable status pills (All / Active / Inactive) and a custom
  field + operator + value filter. Operators: `contains`, `==`, `!=`, `is empty`,
  `is not empty`, `>`, `<`. Filter state is saved in CMS preferences.
- **Inline cell editing**: double-click any editable cell (Tab Index, Access Key,
  Title Attribute, Meta Description, or extra property columns) to edit the value
  in place. Enter or blur saves; Escape cancels without saving. Changes write
  directly to the database and flush the CMS content cache.
- German (`de_DE`) translations for all new UI strings.

### Fixed
- Prop column key mismatch: prop names containing hyphens or dots (e.g. `page-image`)
  are now handled correctly end-to-end — column picker, filter, and inline save all
  reference the same unmodified key.
- Inline save validation now checks the submitted prop name against the actual
  prop names present in the database, preventing writes to arbitrary columns.
- JS lang strings that were embedded in single-quoted string literals are now
  JSON-encoded from PHP, safe against apostrophes in translations.
- Chip remove button `title` and `aria-label` are now taken from the lang system
  instead of being hardcoded in English.

### Changed
- `module_help` text updated in EN and DE to reflect all implemented features
  (column picker, filter bar, inline editing); removed "read-only" description
  and "future version" placeholder text.

### Removed
- Dead CSS rules for `.meta-stats-bar`, `.meta-stat`, `.meta-stat-ok`,
  `.meta-stat-warn` (stats bar was replaced by the filter bar in v1.1.0).

---

## [1.0.0] – 2026-04-27

### Added
- Initial release.
- **Metadata overview table** showing all content pages with their tab index,
  access key, title attribute, and meta description in a single admin view
  (Content section).
- **Auto-discovered extra columns**: any non-body properties present in the
  `content_props` table (e.g. `page_image`, `extra1`, `extra2`) are detected
  and shown automatically.
- **Hierarchy indentation**: pages listed in natural tree order with depth-based
  CSS indentation, mirroring the content manager layout.
- **Summary statistics bar**: total pages, active pages, pages with/without
  a meta description.
- **Visual empty-cell indicators**: missing values shown as muted "–";
  cells without a meta description receive a subtle warm tint.
- **Horizontal scroll** with sticky first column (page name) for wide tables.
- English (`en_US`) and German (`de_DE`) language files.
- Full `module_help` text available via the standard CMSMS help system.
