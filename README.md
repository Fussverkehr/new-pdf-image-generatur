# PDF Thumbnail Image Generator

A WordPress plugin for **automatic and editorially controlled thumbnail management** for media files such as **PDFs, videos, and audio files**.

The plugin consistently uses the **attachment's featured image** as the **single visual representation** across the WordPress Media Library — including **Grid View, List View, and Attachment Detail pages**.

---

## ✨ Key Features

### ✅ Single Source of Truth: Featured Image
- The **featured image of an attachment** is the one and only thumbnail
- Displayed consistently in:
  - Media Grid
  - Media List
  - Attachment Detail view

### ✅ Automatic Thumbnail Generation for PDFs
- On PDF upload:
  - The **first page is rendered**
  - Saved as an image
  - Automatically set as the **featured image**

### ✅ Full Editorial Control
- Editors can change the featured image at any time using the **standard WordPress UI**
- Manual decisions are **never overwritten** by the plugin

### ✅ Fallback Button: "Generate Thumbnail"
- Appears **only if no featured image exists**
- Available:
  - On the attachment edit screen
  - As an action in the media list

### ✅ Smart Bulk Synchronization (v2.2.0)
A maintenance tool for existing media libraries:

- Applies to:
  - PDFs
  - Videos
  - Audio files
- If **no featured image** is set:
  - Tries to find an image with the **same base filename**
  - WordPress suffixes (e.g. `-scaled`, `-1024x768`) are handled correctly
  - If multiple images match, the **most recently uploaded image wins**
- If a featured image already exists:
  - It is simply synchronized (idempotent)

### ✅ Stable Media Grid View
- Uses the official `wp_prepare_attachment_for_js` hook
- Overrides **only** the `icon` field
- Does **not** manipulate `sizes`
- Prevents Backbone / JavaScript crashes

---

## 🧠 Design Principle

> **The featured image defines the visual identity of a media item.**

This plugin:
- Uses **only WordPress core APIs**
- Contains **no custom admin JavaScript**
- Avoids icon hacks and MIME overrides
- Is **update-safe, transparent, and reversible**

---

## 📂 Supported Media Types

Automatically or via Bulk Sync:

- ✅ PDFs (`application/pdf`)
- ✅ Videos (`video/*`)
- ✅ Audio files (`audio/*`)

Not affected:
- Images (`image/*`) — native WordPress behavior remains unchanged

---

## 🛠️ Installation

1. Download or clone the plugin
2. Place it in `/wp-content/plugins/`
3. Activate the plugin in the WordPress admin
4. Optional:
   - Go to Media Library → **"Synchronize Media Thumbnails"**

---

## 🔄 Update Notes

When upgrading from older versions, it is recommended to:

1. Deactivate the plugin
2. Delete the plugin folder
3. Upload the new version
4. Activate the plugin
5. Optionally run the bulk synchronization once

---

## 👩‍💼 Editorial Workflow

1. **Upload PDF / Video / Audio**
   - Thumbnail is generated automatically (if possible)

2. **Adjust featured image manually**
   - Via "Edit more details" in the Media Library

3. **Maintain existing media**
   - Use the bulk synchronization button

No special training required — fully native WordPress experience.

---

## 🔐 Security & Stability

- ✅ Nonce-protected admin actions
- ✅ Proper capability checks (`upload_files`)
- ✅ No silent data changes
- ✅ Manual editorial decisions always take precedence

---

## 📦 Requirements

- PHP extension **Imagick** (required)
- WordPress core APIs only (no external libraries)

---

## 🧩 Changelog (Summary)

### 2.2.0
- Extended bulk synchronization
- Filename-based matching for PDFs, videos, and audio files
- Conflict resolution: **newest image wins**
- Stable featured-image icons in Media Grid

### 2.1.x
- Media Grid stability fixes
- Bulk button introduction
- Code cleanup

---

## 📄 License

GPL v2 or later

Free to use, modify, and distribute under the WordPress license model.

---

## 🤝 Contributing

Issues, pull requests, and suggestions are welcome — especially for:

- Cleanup tools
- Dry-run / preview mode for bulk sync
- Accessibility and UX enhancements
