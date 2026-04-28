# CMSmsMetadata

A content metadata overview module for **CMS Made Simple 2.x**.

Displays all content pages and their metadata fields — tab index, title attributes, access keys, meta descriptions, and any extra content properties — in a single scrollable table, directly in the **Content** section of the admin panel.

---

## Requirements

- CMS Made Simple 2.0 or later
- PHP 7.2 or later

---

## Installation

1. Upload the `CMSmsMetadata/` folder into your CMS `modules/` directory.
2. In the CMSMS admin panel, go to **Extensions → Module Manager** and install the module.
3. Navigate to **Content → Content Metadata** to view the overview.

---

## Features

### Metadata Overview Table
Displays every content page with these fixed columns:

| Column | Source | Description |
|---|---|---|
| **Page** | `cms_content.content_name` | Page title, indented by hierarchy depth, with URL alias shown below |
| **Active** | `cms_content.active` | Active/published status indicator |
| **Tab Index** | `cms_content.tabindex` | Keyboard tab-order value (accessibility) |
| **Access Key** | `cms_content.accesskey` | Keyboard shortcut character (accessibility) |
| **Title Attribute** | `cms_content.titleattribute` | HTML `title` tooltip text |
| **Meta Description** | `cms_content.metadata` | SEO meta description (HTML stripped, truncated to 90 chars) |

### Auto-discovered Extra Columns
Any additional properties stored in the `content_props` table (e.g. `page_image`, `extra1`, `extra2`, or custom template properties) are automatically detected and appear as additional columns. Body-content fields (`content`, `layout`, `styles`, locale-based props like `content_en_US`) are excluded.

### Summary Statistics
A statistics bar at the top shows:
- Total pages
- Active pages
- Pages with / without a meta description

### Hierarchy Indentation
Pages are listed in their natural tree order with visual indentation matching the page hierarchy, mirroring the look of the standard Content Manager.

### Visual Cues
- Missing values are shown with a muted "–" indicator
- Cells without a meta description are highlighted with a subtle warm tint
- Inactive pages are dimmed

---

## Roadmap (future versions)

- **Column filtering** – show/hide individual prop columns
- **Search / highlight** – filter by page name or field content
- **Inline editing** – edit metadata fields directly in the table without opening the page editor

---

## License

GPL-2.0-or-later — see `modules/CMSmsMetadata/LICENSE` or https://www.gnu.org/licenses/gpl-2.0.html
