# CLAUDE.md — PowerKit: Powerful Tools For Your Website

## Project Overview
WordPress plugin (PHP 8.0+, WP 6.0+) providing a custom login/register/lost-password UI via Elementor widgets, a page duplicator, template library importer, security hardening, and admin branding tools. Hosted on WordPress.org.

## Active Architecture
```
custom-login-elementor.php   # Main entry point — constants, autoloader, boot
readme.txt                   # WP.org listing (Stable tag must match PKWT_VERSION)
uninstall.php                # Cleanup on uninstall
admin/
  class-pkwt-admin.php       # Admin menu, settings page registration
  class-pkwt-settings.php    # Settings form handling & sanitization
  import-export.php          # Settings import/export UI
elementor/
  class-pkwt-widgets-manager.php    # Registers Elementor widgets
  class-pkwt-template-library.php  # AJAX template import handler
includes/
  class-pkwt-plugin.php            # Core orchestrator (singleton)
  class-pkwt-redirector.php        # Login URL redirect logic
  class-pkwt-page-manager.php      # Page creation & slug management
  class-pkwt-settings-repository.php  # get_option('pkwt_settings') cache
  class-pkwt-security.php          # Rate limiting, brute-force protection
  class-pkwt-ajax-handler.php      # AJAX endpoints
  class-pkwt-activator.php         # Activation hook
  class-pkwt-deactivator.php       # Deactivation hook
  class-pkwt-onboarding.php        # First-run onboarding
  class-pkwt-compatibility.php     # Plugin conflict resolution
  class-pkwt-conflict-detector.php # Conflict detection
  class-dpp-*.php                  # Page duplicator feature (dpp namespace)
assets/css/                  # pkwt-admin.css, pkwt-frontend.css, pkwt-editor-tpl.css
assets/js/                   # pkwt-admin.js, pkwt-frontend.js, pkwt-editor-tpl.js
languages/                   # .pot file for translations
```

## Key Constants (defined in main file)
- `PKWT_VERSION` — must match `Version:` header and `readme.txt` Stable tag
- `PKWT_PLUGIN_FILE`, `PKWT_PLUGIN_DIR`, `PKWT_PLUGIN_URL`, `PKWT_PLUGIN_SLUG`
- Namespace: `PKWT\Includes\`, `PKWT\Admin\`, `PKWT\Elementor\`

## Development Commands
```bash
# No build step — pure PHP/CSS/JS plugin
# Lint PHP (install once):
composer require --dev squizlabs/php_codesniffer wp-coding-standards/wpcs
./vendor/bin/phpcs --standard=WordPress .

# WP Plugin Check (in WP admin):
# Tools > Plugin Check > Select "PowerKit - Powerful Tools For Your Website"

# Create release zip (from parent dir of plugin):
cd .. && zip -r powerkit-X.X.X.zip powerkit-powerful-tools-for-your-website/ \
  --exclude "*.DS_Store" --exclude "*/.git/*" --exclude "*/node_modules/*" \
  --exclude "*/vendor/*" --exclude "CLAUDE.md"
```

## Current Backlog (prioritized)
1. **[CRITICAL]** Fix blank wp-admin after plugin activation — delete any WP page with slug `wp-admin` created by old plugin version (SQL: `UPDATE wp_posts SET post_status='trash' WHERE post_name='wp-admin' AND post_type='page'`)
2. **[HIGH]** Elementor widget names changed from `cle-*` → `pkwt-*` — existing saved pages may need rebuilding
3. **[HIGH]** Submit v3.5.3 to WP.org — all Plugin Check errors now resolved
4. **[MEDIUM]** Add changelog entry for 3.5.x in `CHANGELOG.md` and `readme.txt`
5. **[LOW]** Consider adding PHP unit tests (no test suite exists yet)

## WP.org Compliance Status (v3.5.3)
- [x] No short class prefixes (`CLE` removed, all classes use `PKWT`)
- [x] No `WPPOWERKIT_` constants — only `PKWT_` prefix
- [x] No direct `require_once ABSPATH . 'wp-login.php'` — uses `wp_safe_redirect()`
- [x] `Stable tag` matches plugin version
- [x] `translators:` comments on all `sprintf(__(...))` calls
- [x] `save-zip.sh` removed (no application files allowed)
- [x] `load_plugin_textdomain()` removed (auto-loaded by WP 4.6+ on WP.org)

## Compaction Instructions
When compacting context, preserve code architecture, active debugging logs, and test results above all else.

## AI Performance Constraints
- DO NOT scan the entire codebase or read files unrelated to the explicit user prompt.
- DO NOT provide verbose code explanations or repeat unchanged code back to the user.
- If a command fails twice, STOP and ask the user for guidance instead of entering a tool loop.
- Always suggest using the `/clear` command when a specific engineering task is fully completed.
