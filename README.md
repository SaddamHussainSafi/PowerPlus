<div align="center">

<img src="https://raw.githubusercontent.com/SaddamHussainSafi/PowerKit/main/assets/img/banner.png" alt="PowerKit Banner" width="100%" />

<h1>⚡ PowerKit — Powerful Tools For Your Website</h1>

<p><strong>The complete WordPress toolkit for Elementor.</strong><br/>
Custom auth pages · Page duplicator · Template library · Security hardening · Admin branding</p>

<br/>

[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-0073AA?style=for-the-badge&logo=wordpress&logoColor=white)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Elementor](https://img.shields.io/badge/Elementor-3.5%2B-92003B?style=for-the-badge&logo=elementor&logoColor=white)](https://elementor.com)
[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-green?style=for-the-badge&logo=gnu&logoColor=white)](LICENSE)
[![Version](https://img.shields.io/badge/Version-3.5.5-F05032?style=for-the-badge&logo=git&logoColor=white)](https://github.com/SaddamHussainSafi/PowerKit/releases)

<br/>

[![WP.org](https://img.shields.io/badge/WordPress.org-Submit%20in%20Review-21759B?style=flat-square&logo=wordpress)](https://wordpress.org/plugins/powerkit-powerful-tools-for-your-website/)
[![GitHub stars](https://img.shields.io/github/stars/SaddamHussainSafi/PowerKit?style=flat-square&logo=github)](https://github.com/SaddamHussainSafi/PowerKit/stargazers)
[![GitHub issues](https://img.shields.io/github/issues/SaddamHussainSafi/PowerKit?style=flat-square)](https://github.com/SaddamHussainSafi/PowerKit/issues)
[![GitHub last commit](https://img.shields.io/github/last-commit/SaddamHussainSafi/PowerKit?style=flat-square)](https://github.com/SaddamHussainSafi/PowerKit/commits/main)

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

**PowerKit** replaces the default WordPress login page and gives you a complete suite of power tools — all managed from one clean admin panel. Built for Elementor sites, it lets you design stunning auth pages, duplicate content in one click, harden your site against attacks, and brand the admin area.

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

### 🎨 Admin Branding
Customise the WP admin login logo, background colour, and page styling to match your brand — zero code required.

</td>
<td width="50%">

### ⚙️ Settings Import / Export
Snapshot all plugin settings to JSON. Restore them on any site — perfect for staging → production workflows.

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
2. Upload `powerkit-powerful-tools-for-your-website-X.X.X.zip`
3. Click **Activate**
4. Follow the **PowerKit onboarding wizard**

### From GitHub
```bash
# Download latest release
curl -L https://github.com/SaddamHussainSafi/PowerKit/releases/latest/download/powerkit-powerful-tools-for-your-website.zip \
  -o powerkit.zip

# Unzip into your plugins folder
unzip powerkit.zip -d /path/to/wp-content/plugins/
```

---

## 🚀 Quick Start

```
Step 1 — Activate the plugin
Step 2 — PowerKit → General Settings → assign Login / Register / Password pages
Step 3 — PowerKit → Page Templates → pick a layout → click Import
Step 4 — Open page in Elementor → customise colours, images, text
Step 5 — PowerKit → Security → enable rate limiting + reCAPTCHA
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

PowerKit follows WordPress security best practices throughout:

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
powerkit-powerful-tools-for-your-website/
│
├── powerkit-powerful-tools-for-your-website.php  ← Entry: constants, autoloader, boot
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
│   ├── class-pkwt-security.php            ← Rate limiting & brute-force
│   ├── class-pkwt-redirector.php          ← Login URL redirect logic
│   ├── class-pkwt-page-manager.php        ← Page creation & slug management
│   ├── class-pkwt-settings-repository.php ← Cached options layer
│   ├── class-pkwt-activator.php           ← Activation hook handler
│   ├── class-pkwt-deactivator.php         ← Deactivation hook handler
│   ├── class-pkwt-onboarding.php          ← First-run wizard
│   ├── class-pkwt-compatibility.php       ← Plugin conflict resolution
│   ├── class-pkwt-conflict-detector.php   ← Conflict detection
│   └── class-dpp-*.php                    ← Page duplicator feature
│
└── assets/
    ├── css/  pkwt-admin.css · pkwt-frontend.css · pkwt-templates.css · pkwt-editor-tpl.css
    └── js/   pkwt-admin.js  · pkwt-frontend.js  · pkwt-templates.js  · pkwt-editor-tpl.js
```

**Namespaces:** `PKWT\Includes\` · `PKWT\Admin\` · `PKWT\Elementor\`

---

## 🛠️ Development

```bash
# No build step — pure PHP / CSS / JS

# 1. Install linting tools
composer require --dev squizlabs/php_codesniffer wp-coding-standards/wpcs

# 2. Lint against WordPress Coding Standards
./vendor/bin/phpcs --standard=WordPress .

# 3. Run WP Plugin Check
#    WP Admin → Tools → Plugin Check → Select "PowerKit - Powerful Tools For Your Website"

# 4. Build release zip
STAGING=$(mktemp -d)
mkdir "$STAGING/powerkit-powerful-tools-for-your-website"
rsync -a \
  --exclude='.git' --exclude='.claude' --exclude='vendor' --exclude='node_modules' \
  --exclude='.DS_Store' --exclude='._*' --exclude='.gitignore' \
  --exclude='CLAUDE.md' --exclude='*.zip' --exclude='*.sh' \
  ./ "$STAGING/powerkit-powerful-tools-for-your-website/"
cd "$STAGING"
zip -r powerkit-powerful-tools-for-your-website-X.X.X.zip \
  powerkit-powerful-tools-for-your-website/ -q
```

---

## 📋 Changelog

<details>
<summary><strong>v3.5.5</strong> — Zip packaging fix</summary>

- Fix: Internal zip folder is now `powerkit-powerful-tools-for-your-website` (no version suffix) — eliminates false `TextDomainMismatch` errors in Plugin Check
- Fix: Exclude `.claude` AI directory and hidden files from release archive

</details>

<details>
<summary><strong>v3.5.4</strong> — WP.org review compliance</summary>

- Fix: Rename main file to `powerkit-powerful-tools-for-your-website.php` (slug match)
- Fix: Update `Plugin URI` header to correct WP.org listing URL
- Fix: Extract inline `<style>` → `assets/css/pkwt-templates.css`, enqueued via `wp_enqueue_style()`
- Fix: Extract inline `<script>` → `assets/js/pkwt-templates.js`, PHP values via `wp_localize_script()`
- Fix: Sanitize all `$_POST` fields with `sanitize_text_field()` / `sanitize_email()` after `wp_unslash()`
- Fix: Add inline comments on `wp_signon()` and `wp_create_user()` explaining WP hook chain compliance

</details>

<details>
<summary><strong>v3.5.3</strong> — Initial WP.org submission</summary>

- Full `CLE` → `PKWT` prefix rename across all classes, constants, and hooks
- All Plugin Check errors resolved
- `readme.txt` Stable tag aligned with plugin version

</details>

---

## 📜 License

**GNU General Public License v2.0 or later**

```
PowerKit — Powerful Tools For Your Website
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

[Website](https://saddamhussain.com.np/) · [WordPress.org](https://wordpress.org/plugins/powerkit-powerful-tools-for-your-website/) · [Report a Bug](https://github.com/SaddamHussainSafi/PowerKit/issues) · [Request a Feature](https://github.com/SaddamHussainSafi/PowerKit/issues)

<sub>© 2024–2026 Saddam Hussain Safi · GPL v2 or later</sub>

</div>
