<div align="center">

<img src="https://raw.githubusercontent.com/SaddamHussainSafi/PowerPlus/main/assets/img/banner.png" alt="PowerPlus Banner" width="100%" />

<h1>⚡ PowerPlus — All-in-One Powerful Toolkit</h1>

<p><strong>The complete WordPress toolkit for Elementor.</strong><br/>
Custom auth pages · Page duplicator · Template library · Security hardening · Admin branding</p>

<br/>

[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-0073AA?style=for-the-badge&logo=wordpress&logoColor=white)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Elementor](https://img.shields.io/badge/Elementor-3.5%2B-92003B?style=for-the-badge&logo=elementor&logoColor=white)](https://elementor.com)
[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-green?style=for-the-badge&logo=gnu&logoColor=white)](LICENSE)
[![Version](https://img.shields.io/badge/Version-3.8.0-F05032?style=for-the-badge&logo=git&logoColor=white)](https://github.com/SaddamHussainSafi/PowerPlus/releases)

<br/>

[![WP.org](https://img.shields.io/badge/WordPress.org-Submit%20in%20Review-21759B?style=flat-square&logo=wordpress)](https://wordpress.org/plugins/powerplus-toolkit/)
[![GitHub stars](https://img.shields.io/github/stars/SaddamHussainSafi/PowerPlus?style=flat-square&logo=github)](https://github.com/SaddamHussainSafi/PowerPlus/stargazers)
[![GitHub issues](https://img.shields.io/github/issues/SaddamHussainSafi/PowerPlus?style=flat-square)](https://github.com/SaddamHussainSafi/PowerPlus/issues)
[![GitHub last commit](https://img.shields.io/github/last-commit/SaddamHussainSafi/PowerPlus?style=flat-square)](https://github.com/SaddamHussainSafi/PowerPlus/commits/main)

</div>

---

## 🧭 Table of Contents

- [Overview](#-overview)
- [Features](#-features)
- [Requirements](#-requirements)
- [Installation](#-installation)
- [Quick Start](#-quick-start)
- [Template Library](#-template-library)
- [Security](#-security)
- [Architecture](#-architecture)
- [Development](#-development)
- [Changelog](#-changelog)
- [License](#-license)

---

## 🌟 Overview

**PowerPlus** replaces the default WordPress login page and gives you a complete suite of power tools — all managed from one clean admin panel. Built for Elementor sites, it lets you design stunning auth pages, duplicate content in one click, harden your site against attacks, and brand the admin area.

```
No bloat. No subscriptions. Pure functionality.
```

---

## ✨ Features

<table>
<tr>
<td width="50%">

### 🔐 Custom Auth Pages
Replace `/wp-login.php` with beautiful Elementor-designed pages for login, register, lost password, and reset password. Full AJAX — no page reloads.

</td>
<td width="50%">

### 📐 Template Library
6 premium layout sets with 12+ templates. Import a full-page Elementor design to any auth page in one click — then customise freely.

</td>
</tr>
<tr>
<td width="50%">

### 🧬 Page Duplicator
Duplicate any post, page, or CPT directly from the WP admin list. Copies all content, meta fields, and Elementor data instantly.

</td>
<td width="50%">

### 🛡️ Security Hardening
Rate limiting, brute-force protection, honeypot fields, Google reCAPTCHA v3, and login activity logging — all configurable.

</td>
</tr>
<tr>
<td width="50%">

### 🎨 Branding & White-Label
White-label the **native wp-login.php** (logo, colours, background, welcome/error messages) and the admin chrome (footer text, hide WP version & logo) — zero code.

</td>
<td width="50%">

### ⚙️ Settings Import / Export
Snapshot all plugin settings to JSON. Restore them on any site — perfect for staging → production workflows.

</td>
</tr>
<tr>
<td width="50%">

### ⚛️ Modern React Dashboard
A fast single-page admin built on React 18 — **bundled locally** (no CDNs), light/dark themes, autosave, and a unified sidebar for every module.

</td>
<td width="50%">

### 🖼️ SVG Uploads (hardened)
Allow SVG uploads with a **DOM-allowlist sanitizer** that strips XXE, scripts, event handlers and unsafe URLs — sanitized before the file ever touches disk.

</td>
</tr>
<tr>
<td width="50%">

### 🚀 One-Click Power Tools
Install + activate Elementor from the dashboard in one click, and a flagship **auto-update-all-plugins** switch to keep the whole site current.

</td>
<td width="50%">

### 🕵️ Ghost Mode
Reduce WordPress fingerprints — strip the generator tag and `?ver=` strings, block REST user enumeration, disable XML-RPC, and hide author archives.

</td>
</tr>
</table>

---

## 📋 Requirements

| | Minimum | Recommended |
|---|---|---|
| **WordPress** | 6.0 | Latest stable |
| **PHP** | 8.0 | 8.2+ |
| **Elementor** | 3.5.0 *(template features)* | Latest stable |
| **MySQL** | 5.7 | 8.0+ |

---

## 📦 Installation

### From WordPress Admin *(recommended)*
1. **Plugins → Add New → Upload Plugin**
2. Upload `powerplus-toolkit-X.X.X.zip`
3. Click **Activate**
4. Follow the **PowerPlus onboarding wizard**

### From GitHub
```bash
# Download latest release
curl -L https://github.com/SaddamHussainSafi/PowerPlus/releases/latest/download/powerplus-toolkit.zip \
  -o powerplus-toolkit.zip

# Unzip into your plugins folder
unzip powerplus-toolkit.zip -d /path/to/wp-content/plugins/
```

---

## 🚀 Quick Start

```
Step 1 — Activate the plugin
Step 2 — PowerPlus → General Settings → assign Login / Register / Password pages
Step 3 — PowerPlus → Page Templates → pick a layout → click Import
Step 4 — Open page in Elementor → customise colours, images, text
Step 5 — PowerPlus → Security → enable rate limiting + reCAPTCHA
```

> **Tip:** The onboarding wizard walks you through Steps 1–2 automatically on first activation.

---

## 🖼️ Template Library

Six premium layout sets — each with Login, Register, Forgot Password, and Reset Password variants:

| Layout | Style | Preview |
|--------|-------|---------|
| **Split Left** | Dark panel · Form right | Auth form on the right, branded panel on the left |
| **Centered Card** | Deep blue gradient | Floating card centered on a gradient background |
| **Form Left** | Indigo/violet | Form on the left, decorative panel on the right |
| **Dreamer** | Soft lavender | Light pastel tones, modern and minimal |
| **UXOList** | Navy to electric blue | Bold tech aesthetic, dark and confident |
| **Gradient Hub** | Blue to violet | Vivid gradient hero with bold typography |

Each template is a full Elementor page — fully editable after import.

---

## 🔒 Security

PowerPlus follows WordPress security best practices throughout:

- **Sanitize early** — `sanitize_text_field()`, `sanitize_email()` on all `$_POST` input at assignment
- **Escape late** — `esc_html()`, `esc_attr()`, `esc_url()` on every output
- **Nonces** — verified on every AJAX endpoint before processing
- **Rate limiting** — configurable lockout after N failed attempts
- **Honeypot** — invisible bot-trap field on all forms
- **reCAPTCHA v3** — Google invisible challenge integration
- **Core hooks** — `wp_signon()` and `wp_create_user()` use WordPress's full authenticate/user_register filter chain, so all security plugins (2FA, brute-force blockers) remain compatible

> To report a vulnerability privately, email via [saddamhussain.com.np](https://saddamhussain.com.np/) rather than opening a public issue.

---

## 🗂️ Architecture

```
powerplus-toolkit/
│
├── powerplus-toolkit.php  ← Entry: constants, autoloader, boot
├── readme.txt                                     ← WordPress.org listing
├── uninstall.php                                  ← Cleanup on uninstall
│
├── admin/
│   ├── class-pkwt-admin.php          ← Admin menu & settings page registration
│   ├── class-pkwt-settings.php       ← Form handling & sanitization
│   ├── import-export.php             ← Settings snapshot UI
│   └── views/                        ← Tab view templates (PHP)
│
├── elementor/
│   ├── class-pkwt-widgets-manager.php     ← Registers Elementor widgets
│   └── class-pkwt-template-library.php   ← AJAX template importer
│
├── includes/
│   ├── class-pkwt-plugin.php              ← Core singleton orchestrator
│   ├── class-pkwt-ajax-handler.php        ← Login / register AJAX endpoints
│   ├── class-pkwt-security.php            ← Rate limiting, brute-force, IP allow-list
│   ├── class-pkwt-redirector.php          ← Login URL redirect & wp-login blocking
│   ├── class-pkwt-login-renderer.php      ← Elementor-template-at-secret-URL login mode
│   ├── class-pkwt-branding.php            ← Login + admin white-label
│   ├── class-pkwt-page-manager.php        ← Page creation & slug reconciliation
│   ├── class-pkwt-settings-repository.php ← Cached options layer
│   ├── class-pkwt-activator.php           ← Activation hook handler
│   ├── class-pkwt-deactivator.php         ← Deactivation hook handler
│   ├── class-pkwt-onboarding.php          ← First-run redirect into the dashboard
│   ├── class-pkwt-compatibility.php       ← Plugin conflict resolution
│   ├── class-pkwt-conflict-detector.php   ← Conflict detection
│   └── class-dpp-*.php                    ← Duplicator · SVG · Ghost Mode · Classic Editor
│
└── assets/
    ├── css/     pkwt-dashboard.css · pkwt-tailwind.css (compiled) · pkwt-frontend.css · …
    ├── js/      pkwt-dashboard.js (JSX source) · pkwt-dashboard.min.js (compiled) · …
    ├── vendor/  react.min.js · react-dom.min.js (React 18, bundled — no CDN)
    └── img/     banner.png
```

**Namespaces:** `PKWT\Includes\` · `PKWT\Admin\` · `PKWT\Elementor\`

---

## 🛠️ Development

```bash
# 1. Install linting tools
composer require --dev squizlabs/php_codesniffer wp-coding-standards/wpcs
./vendor/bin/phpcs --standard=WordPress .

# 2. Run WP Plugin Check
#    WP Admin → Tools → Plugin Check → Select "PowerPlus — All-in-One Powerful Toolkit"
```

### Dashboard build

The React admin dashboard ships **precompiled** — React 18 is vendored locally in
`assets/vendor/`, the JSX is transpiled to plain JS, and Tailwind is compiled to a static,
dashboard-scoped stylesheet (no external CDNs). Rebuild only when you edit the JSX source
(`assets/js/pkwt-dashboard.js`):

```bash
npm install @babel/core @babel/cli @babel/preset-react tailwindcss@3.4.17

# JSX -> plain JS (classic runtime; uses the vendored global React)
babel --config-file .build/babelrc.json \
  assets/js/pkwt-dashboard.js -o assets/js/pkwt-dashboard.min.js

# Tailwind -> static CSS (preflight off, scoped to #pkwt-dashboard-root)
tailwindcss -c .build/tailwind.config.js -i in.css -o assets/css/pkwt-tailwind.css --minify
```

See [`.build/BUILD.md`](.build/BUILD.md) for details.

```bash
# Build the release zip (anchor /vendor/ so bundled React in assets/vendor/ is kept)
rsync -a --exclude='.*' --exclude='node_modules/' --exclude='/vendor/' \
  ./ /tmp/powerplus-toolkit/
( cd /tmp && zip -r powerplus-toolkit-X.X.X.zip powerplus-toolkit/ -q )
```

---

## 📋 Changelog

<details open>
<summary><strong>v3.8.0</strong> — Bundling, branding, flagship power tools</summary>

- WP.org compliance: React 18, Babel and Tailwind no longer load from CDNs — React is vendored locally, JSX is precompiled, Tailwind is compiled to a dashboard-scoped stylesheet (preflight off)
- Added: **Branding** module — white-label the native `wp-login.php` (logo, colours, background, welcome/error messages) and the admin chrome (footer text, hide WP version & logo)
- Added: **One-click Install Elementor** (install + activate from the dashboard) and a flagship **Auto-update all plugins** switch
- Added: Autosave across the dashboard; manual save kept
- Fixed: Save failures caused by a nonce-lifetime mismatch; native button/focus borders; one-click flows

</details>

<details>
<summary><strong>v3.6.x – 3.7.0</strong> — Security hardening & market-research integrations</summary>

- Security: fixed a **critical Ghost Mode arbitrary-file-disclosure** (path traversal); rebuilt the **SVG sanitizer** as a DOM allowlist (XXE / scripts / event-handlers / unsafe URLs), sanitizing on the upload prefilter
- Security: brute-force protection now covers **native wp-login.php** (authenticate filter) with an **IP allow-list**; CAPTCHA + throttling on reset/lost-password; registration gating; capability tightening; CAPTCHA secret-key exposure fixed
- Fixed: Elementor **duplication slash corruption** (`_elementor_data`), the custom-login-URL "resets to default" bug, template-import `_elementor_version`, and ~35 missing Elementor control defaults
- Integrations adapted from WPS Hide Login, Yoast Duplicate Post, Limit Login Attempts Reloaded, Safe SVG, LoginPress and White Label CMS

</details>

<details>
<summary><strong>v3.5.6 – 3.5.8</strong> — PowerPlus rename & React dashboard</summary>

- Renamed to **PowerPlus** (slug `powerplus-toolkit`, approved by WP.org); all display strings updated
- Added the modern React admin dashboard with a single unified sidebar, light/dark themes, and real settings persistence

</details>

<details>
<summary><strong>v3.5.5</strong> — Zip packaging fix</summary>

- Fix: Internal zip folder is now `powerplus-toolkit` (no version suffix) — eliminates false `TextDomainMismatch` errors in Plugin Check
- Fix: Exclude `.claude` AI directory and hidden files from release archive

</details>

<details>
<summary><strong>v3.5.4</strong> — WP.org review compliance</summary>

- Fix: Rename main file to `powerplus-toolkit.php` (slug match)
- Fix: Update `Plugin URI` header to correct WP.org listing URL
- Fix: Extract inline `<style>` → `assets/css/pkwt-templates.css`, enqueued via `wp_enqueue_style()`
- Fix: Extract inline `<script>` → `assets/js/pkwt-templates.js`, PHP values via `wp_localize_script()`
- Fix: Sanitize all `$_POST` fields with `sanitize_text_field()` / `sanitize_email()` after `wp_unslash()`
- Fix: Add inline comments on `wp_signon()` and `wp_create_user()` explaining WP hook chain compliance

</details>

<details>
<summary><strong>v3.4.2</strong></summary>

- Fix: Nonce now injected via `wp_add_inline_script` on elementor-editor handle — guaranteed to reach the page even when external JS file is not yet cached on the server

</details>

<details>
<summary><strong>v3.4.1</strong></summary>

- Fix: Nonce now generated fresh on every editor page load via `wp_localize_script` — eliminates "Security check failed" error in widget template picker
- Fix: Dashboard Page Templates tab Import buttons now use AJAX instead of admin-post form — eliminates "The link you followed has expired" error

</details>

<details>
<summary><strong>v3.4.0</strong> — In-widget template import</summary>

- Feature: Template import now works directly inside the Elementor widget panel — click Import on any layout card in the Login, Register, Lost Password, or Reset Password widget
- Removed: Old preset/theme colour system removed from all widgets
- Improvement: Template import uses AJAX — no page reloads or expiring form links
- Improvement: After import, links to open the page in Elementor or view it are shown inline in the widget panel

</details>

<details>
<summary><strong>v3.3.0</strong> — Template library</summary>

- Feature: Full Elementor page template library — 3 complete layout sets (Split Left, Centered Card, Gradient Panel Right), each with Login / Register / Forgot Password / Reset Password variants, importable in one click
- Feature: New "Page Templates" admin tab for browsing and importing templates
- Improvement: Template import writes directly to `_elementor_data` post meta, sets edit mode to builder, and flushes CSS cache automatically

</details>

<details>
<summary><strong>v3.2.0</strong> — Built-in form templates</summary>

- Feature: 6 built-in form templates added to all auth widgets — Default, Midnight, Aurora, Minimal, Corporate, Sunset

</details>

<details>
<summary><strong>v3.1.5</strong></summary>

- Fixed: `?_pkwt_no_cache=1` no longer appended to login/logout/register/lost password URLs — clean URLs everywhere

</details>

<details>
<summary><strong>v3.1.4</strong></summary>

- Improved: Login URL field redesigned as a WordPress permalink-style slug editor
- Changed: Default login page slug is now `wp-admin` (matching WordPress default)

</details>

<details>
<summary><strong>v3.1.3</strong></summary>

- Improved: Custom login URL now automatically renames the login page slug to match — no redirect needed

</details>

<details>
<summary><strong>v3.1.2</strong></summary>

- Fixed: Custom login URL showing 404 — now cleanly redirects to the configured login page

</details>

<details>
<summary><strong>v3.1.1</strong></summary>

- Fixed: `ERR_TOO_MANY_REDIRECTS` loop in custom login URL routing
- Fixed: Path comparison now uses `get_permalink()` instead of cached URL with query strings

</details>

<details>
<summary><strong>v3.1.0</strong></summary>

- Fixed: Custom login URL showing 404 — now redirects to the Elementor login page when configured
- Fixed: Redirect loop between custom URL and login page slug

</details>

<details>
<summary><strong>v3.0.9</strong></summary>

- Fixed: Custom login URL serving raw `wp-login.php` instead of the Elementor login page

</details>

<details>
<summary><strong>v3.0.8</strong></summary>

- Fixed: Elementor preview iframe fails to load on plugin auth pages — redirector was intercepting preview requests
- Fixed: `X-Frame-Options` / CSP `frame-ancestors` headers now suppressed for all Elementor preview contexts

</details>

<details>
<summary><strong>v3.0.7</strong></summary>

- Fixed: HTML entities appearing in Elementor live preview — switched to triple-brace `{{{ }}}` syntax
- Fixed: Frontend CSS and JS not loading on pages containing PowerPlus widgets
- Fixed: Classic Editor module dequeuing `wp-editor` on the Elementor editor screen

</details>

<details>
<summary><strong>v3.0.6</strong></summary>

- Fixed: White screen on all WordPress admin pages — PHP parse error in `capture_change_snapshot()`

</details>

<details>
<summary><strong>v3.0.5</strong></summary>

- Fixed: Fatal `TypeError` — `fix_svg_filetype()` type signature aligned with WordPress core

</details>

<details>
<summary><strong>v3.0.4</strong></summary>

- Fixed: Media uploads broken when plugin active — SVG upload hooks now conditional on feature being enabled
- Fixed: Elementor editor fails to load auth pages — `X-Frame-Options` headers suppressed during editor sessions

</details>

<details>
<summary><strong>v3.0.3</strong></summary>

- Fixed: `phpcs:enable` comment leaking as visible text in admin page footers
- Fixed: "Elementor is not active" conflict notice showing when Elementor is active
- Fixed: SVG upload "server cannot process the image" thumbnail error
- Removed: White Label page and all related functionality
- Improved: Complete widget UI redesign with professional modern design and loading spinner animations

</details>

<details>
<summary><strong>v3.0.2</strong></summary>

- Fixed: Grey oval overlay on settings pages caused by missing CSS class alias

</details>

<details>
<summary><strong>v3.0.1</strong></summary>

- Fixed: Various PHPCS prefix compliance warnings
- Fixed: Post meta key `_cle_page_type` renamed to `_pkwt_page_type`
- Fixed: Export filename updated from `cle-settings-*.json` to `pkwt-settings-*.json`
- Fixed: All admin redirect URLs updated to `page=pkwt-settings`

</details>

<details>
<summary><strong>v3.0.0</strong> — Full PKWT rename & WP.org compliance</summary>

- Fixed: Namespace renamed from `CLE\` to `PKWT\` (4+ character prefix, WP.org compliance)
- Fixed: All option keys, hooks, and transients renamed from `cle_` to `pkwt_` prefix
- Fixed: AJAX endpoint derived via `admin_url()` instead of hardcoded path
- Fixed: `ob_start()` in Ghost Mode paired with `shutdown` action `ob_end_flush()`
- Fixed: Text domain confirmed as `powerplus-toolkit` throughout
- Fixed: Added `== External Services ==` section to readme documenting reCAPTCHA and hCaptcha

</details>

<details>
<summary><strong>v2.9.9</strong></summary>

- Changed: Sidebar top-level menu label fixed to "PowerPlus"
- Changed: Removed user-facing "CLE" wording from all UI

</details>

<details>
<summary><strong>v2.9.8 – v2.9.6</strong> — Plugin Check compliance pass</summary>

- Fixed: Nonce verification warnings resolved
- Fixed: Removed deprecated `load_plugin_textdomain()` for WP.org translation compliance
- Fixed: Frontend script defer behavior uses enqueued script tag modification (no raw output)
- Fixed: Added missing `translators:` comments for placeholder-based strings

</details>

<details>
<summary><strong>v2.9.5</strong></summary>

- Changed: Plugin display name updated to **PowerPlus - Powerful Tools For Your Website**

</details>

<details>
<summary><strong>v2.9.4</strong></summary>

- Fixed: All plugin constants guarded with `defined()` checks — no more activation warnings

</details>

<details>
<summary><strong>v2.9.3</strong></summary>

- Changed: Plugin display name updated for WordPress.org naming compliance

</details>

<details>
<summary><strong>v2.9.2</strong></summary>

- Changed: Compliance hardening pass for WP.org submission prep
- Changed: Ghost plugin-name masking default is now OFF unless explicitly enabled

</details>

<details>
<summary><strong>v2.9.1</strong></summary>

- Fixed: Toggle switch click behavior works directly on the switch body across all settings pages

</details>

<details>
<summary><strong>v2.9.0</strong> — PowerPlus rebrand</summary>

- Changed: Rebranded plugin metadata and admin branding to **PowerPlus - Powerful Tools For Your Website**
- Fixed: Ghost Mode plugin name masking now rewrites plugin folder aliases reliably

</details>

<details>
<summary><strong>v2.8.8</strong></summary>

- Changed: Unified green visual theme across plugin admin UI accents, buttons, links, and save bars

</details>

<details>
<summary><strong>v2.8.7</strong></summary>

- Fixed: Unified all module toggles to use the exact same switch system as Overview
- Added: Runtime conversion of legacy toggle inputs into consistent switch markup

</details>

<details>
<summary><strong>v2.8.6 – v2.8.5</strong></summary>

- Fixed: Overview table forces high-contrast light surface/text colours in dark-mode environments

</details>

<details>
<summary><strong>v2.8.4</strong></summary>

- Fixed: Deactivation now explicitly removes auth URL filters immediately

</details>

<details>
<summary><strong>v2.8.3</strong></summary>

- Added: Dynamic capability mapping for settings groups — selected access roles can save settings
- Fixed: Activity log metadata sanitization handles non-string values safely

</details>

<details>
<summary><strong>v2.8.2</strong> — Security Operations panel</summary>

- Added: Security Operations controls — dashboard toggle, admin-only test mode, settings activity logging
- Added: Role-based plugin access configuration with administrator-safe fallback
- Added: Settings activity log table with clear action in Security page

</details>

<details>
<summary><strong>v2.8.1</strong></summary>

- Added: Deferred loading for admin and module editor scripts
- Added: Dismissible guided setup panel with smart recommendations on Overview
- Changed: Admin option reads now use request-level caching to reduce repeated option fetches

</details>

<details>
<summary><strong>v2.8.0</strong> — Card-based admin redesign</summary>

- Changed: Rebuilt Duplicate, SVG Upload, Ghost Mode, and Classic Editor pages into card-based layouts with top/bottom save bars
- Changed: Overview feature control table now uses a 5-column management layout

</details>

<details>
<summary><strong>v2.7.0</strong> — Premium admin design system</summary>

- Added: New premium card-based design system for all core settings pages
- Added: Top and bottom save controls across settings forms
- Changed: Unified toggle switch UI across all settings pages

</details>

<details>
<summary><strong>v2.6.4</strong></summary>

- Added: Native endpoint blocking toggle for `/wp-login.php` and guest `/wp-admin` (404 response)
- Added: Emergency recovery bypass via `CLE_AUTH_RECOVERY_MODE` constant

</details>

<details>
<summary><strong>v2.6.3</strong></summary>

- Fixed: `register_page_id` and `lost_password_page_id` no longer reset to `0` when saving unrelated settings sections
- Fixed: Conflict scan now auto-heals missing auth page IDs

</details>

<details>
<summary><strong>v2.6.2 – v2.6.0</strong> — Overview redesign</summary>

- Changed: Overview redesigned to a clean minimal toggle table
- Changed: One-click module switch now toggles instantly via card switch control

</details>

<details>
<summary><strong>v2.5.0</strong></summary>

- Added: WordPress left-menu submenu navigation for all plugin sections

</details>

<details>
<summary><strong>v2.4.0</strong></summary>

- Added: Live configuration test actions on Overview (Login URL test + Security Scan)
- Added: Configuration snapshots with one-click rollback restore points
- Added: Module search/filter controls with dependency and impact details

</details>

<details>
<summary><strong>v2.3.0</strong></summary>

- Added: 3-step quick setup wizards for Duplicate, SVG Upload, Ghost Mode, and Classic Editor tabs
- Added: One-click "Apply Recommended Settings" presets for major modules

</details>

<details>
<summary><strong>v2.2.0</strong></summary>

- Added: New dashboard-style Overview tab with module cards, quick actions, and health score
- Added: One-click module enable/disable actions from overview with status feedback

</details>

<details>
<summary><strong>v2.1.1</strong></summary>

- Fixed: Feature toggles no longer reset to OFF when saving a different tab
- Changed: Replaced key ON/OFF checkboxes with slider-style toggle UI

</details>

<details>
<summary><strong>v2.1.0</strong> — Classic Editor module</summary>

- Added: Classic Editor tab with master toggle, scope controls, and per post type selection
- Added: User preference support (Classic/Block) with optional admin bypass
- Added: Gutenberg cleanup controls (widgets, FSE, patterns, block directory)

</details>

<details>
<summary><strong>v2.0.0</strong> — Duplicate, SVG Upload & Ghost Mode</summary>

- Added: Integrated Duplicate, SVG Upload, and Ghost Mode feature modules
- Added: Server-side SVG sanitization, role-based SVG permissions, media scan, and sanitization logs
- Added: Ghost Mode signal controls, endpoint hardening toggles, and detection test tool
- Added: Post-login redirect page selector and custom login URL controls

</details>

<details>
<summary><strong>v1.0.0</strong> — Initial release</summary>

- Login Form widget with full styling controls
- Register Form widget with configurable fields
- Lost Password, Reset Password, Auth Logo, Auth Message, Social Login, Auth Tabs widgets
- CAPTCHA widget with reCAPTCHA v2/v3 and hCaptcha support
- Admin settings panel with General, Redirects, Compatibility, Security, White Label, and Import/Export tabs
- WooCommerce, Multisite, WPML, Polylang compatibility
- WP Rocket and W3 Total Cache auto-exclusion
- Rate limiting, brute force protection, and first-run onboarding wizard

</details>

---

## 📜 License

**GNU General Public License v2.0 or later**

```
PowerPlus — All-in-One Powerful Toolkit
Copyright (C) 2024–2026  Saddam Hussain Safi

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
```

See [LICENSE](LICENSE) for the complete license text.

---

<div align="center">

**Built with ❤️ for the WordPress community**

[Website](https://saddamhussain.com.np/) · [WordPress.org](https://wordpress.org/plugins/powerplus-toolkit/) · [Report a Bug](https://github.com/SaddamHussainSafi/PowerPlus/issues) · [Request a Feature](https://github.com/SaddamHussainSafi/PowerPlus/issues)

<sub>© 2024–2026 Saddam Hussain Safi · GPL v2 or later</sub>

</div>
