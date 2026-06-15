=== PowerPlus — All-in-One Powerful Toolkit ===
Contributors: saddamhussainsafi, profilmi
Donate link: https://saddamhussain.com.np/
Tags: login, elementor, custom login, login page, register
Requires at least: 6.0
Tested up to: 6.9
Stable tag: 3.8.0
Requires PHP: 8.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Power Packed Tools - Complete WordPress Management Suite.

== Description ==

**PowerPlus — All-in-One Powerful Toolkit** gives you one dashboard for custom auth routing, post duplication, SVG security controls, Ghost Mode hardening, and Classic Editor control.

No more ugly default WordPress login pages. Every pixel is under your control, and all feature modules are managed from one settings experience.

= Overview =

This plugin now includes four core modules in one package:

* **Custom Auth Builder** — Build and style login/register/lost/reset pages with Elementor widgets
* **Duplicate Post Module** — Duplicate posts, pages, products, and custom post types including meta/ACF and Elementor data regeneration
* **SVG Upload Module** — Safely allow SVG uploads with server-side sanitization, role controls, scan tools, and logs
* **Ghost Mode Module** — Reduce common WordPress fingerprints with configurable source/API hardening and detection testing
* **Classic Editor Module** — Disable Gutenberg with per-post-type control and classic editor management options

= Who is this for? =

* **Agencies** building client sites that need branded auth pages
* **Business owners** who want a professional login experience
* **Developers** who want full control without writing custom CSS

= How it works =

1. Install and activate the plugin
2. The plugin creates custom login, register, and lost password pages automatically
3. Open any page in Elementor and drag the login widgets onto your design
4. Style every detail — fields, buttons, labels, colors, fonts, shadows — from the Elementor panel
5. Save. Your branded login page is live.

= Included Widgets =

* **Login Form** — Username/email, password, remember me, show/hide password toggle
* **Register Form** — Configurable fields, terms checkbox, WooCommerce compatible
* **Lost Password** — Triggers WordPress native password reset flow
* **Reset Password** — Styled confirmation page after email link click
* **Auth Logo** — Displays site logo with full sizing controls
* **Auth Message** — Displays WordPress login errors and success notices with custom styling
* **Social Login** — Google and Facebook login buttons (requires Nextend Social Login)
* **Auth Tabs** — Switch between login and register on a single page
* **CAPTCHA** — Google reCAPTCHA v2/v3 and hCaptcha support
* **Divider with Text** — Styled OR divider between form sections
* **Terms and Privacy** — Configurable legal links below register form
* **Redirect Timer** — Countdown before automatic redirect after login

= Styling Controls =

Every widget includes the same styling controls you find in native Elementor widgets:

* Typography — font family, size, weight, line height, letter spacing
* Colors — text, background, border, placeholder, icon
* Borders — type, width, color, radius per corner
* Box shadows and text shadows
* Spacing — padding and margin with responsive breakpoints
* Responsive controls — desktop, tablet, and mobile breakpoints for every dimension
* Normal, Hover, Focus, and Error state tabs for fields and buttons
* Entrance animations via Elementor's animation system

= Security =

* All form submissions use WordPress nonces
* Rate limiting on login and lost password actions
* No data sent to external servers
* Passwords never logged
* Compatible with two-factor authentication plugins
* CAPTCHA support via Google reCAPTCHA and hCaptcha

= Compatibility =

* Works with Elementor Free (Elementor Pro not required)
* WooCommerce compatible login and register flows
* Works alongside Wordfence, iThemes Security, and other security plugins
* Compatible with WPML and Polylang for multilingual sites
* Compatible with popular caching plugins including WP Rocket and W3 Total Cache
* Multisite compatible

= Privacy =

This plugin does not collect or transmit any user data to external servers. All data stays on your WordPress installation. For details, see the FAQ section.

= Documentation and Support =

* Author website: https://saddamhussain.com.np
* Support: https://inceptastudio.com/

== Installation ==

= Automatic Installation =

1. Log in to your WordPress admin dashboard
2. Go to Plugins > Add New
3. Search for "PowerPlus — All-in-One Powerful Toolkit"
4. Click Install Now then Activate

= Manual Installation =

1. Download the plugin zip file
2. Go to Plugins > Add New > Upload Plugin
3. Upload the zip file and click Install Now
4. Activate the plugin

= After Activation =

1. The plugin will automatically create your custom login, register, and lost password pages
2. Go to PowerPlus — All-in-One Powerful Toolkit > Settings to assign pages and configure options
3. Open any page in Elementor to start designing with the login widgets

= Requirements =

* WordPress 6.0 or higher
* PHP 8.0 or higher
* Elementor (free) 3.5.0 or higher

== Frequently Asked Questions ==

= Does this plugin require Elementor Pro? =

No. All core features work with the free version of Elementor. Elementor Pro is not required.

= Will this break my existing login flow? =

No. The plugin creates separate custom pages and redirects WordPress login traffic to them. The native wp-login.php file is never modified and remains fully functional. Deactivating the plugin instantly restores the default WordPress login behavior.

= Is it compatible with WooCommerce? =

Yes. The plugin includes a WooCommerce mode in settings that hooks into WooCommerce login and register endpoints instead of the WordPress core endpoints. Enable it from PowerPlus — All-in-One Powerful Toolkit > Settings > Compatibility.

= Does it work with two-factor authentication plugins? =

Yes. The plugin uses WordPress's native wp_signon() function for authentication, which means all authentication hooks — including those from 2FA plugins — still fire correctly.

= Is it GDPR compliant? =

Yes. The plugin does not collect, store, or transmit any personal data to external servers. It stores only plugin settings in your WordPress database. It integrates with WordPress's privacy tools including the personal data eraser and exporter. See the Privacy Policy section in settings for suggested policy text.

= What happens if I delete the custom login page by accident? =

The plugin detects missing pages automatically and shows an admin notice with a one-click option to recreate them.

= Can I use it on a multisite installation? =

Yes. Each subsite gets its own set of custom pages. Network admins can manage settings across the network.

= Does it conflict with caching plugins? =

The plugin automatically registers custom auth pages with WP Rocket and W3 Total Cache to exclude them from caching. For other caching plugins, the plugin adds no-cache headers on all auth pages.

= Can I show my own branding instead of the plugin name? =

Yes. The White Label settings tab allows you to rename the plugin in the WordPress admin menu, set a custom support URL, and hide the plugin from the plugins list on client sites.

= Where can I report a security vulnerability? =

Please report security issues directly to the author via the contact form at https://saddamhussain.com.np rather than posting in the public support forum.

== Screenshots ==

1. The Elementor editor showing the Login Form widget with styling controls panel open
2. The Register Form widget with field configuration options
3. A finished branded login page design built with the plugin
4. The plugin settings panel showing the General tab
5. The plugin settings panel showing the Security tab
6. The Elementor widget panel showing all Custom Login widgets in their own category
7. A split-screen login page layout built with the Auth Tabs widget
8. The onboarding wizard shown on first activation

== Changelog ==

= 3.8.0 =
* Fixed: Saving settings no longer fails with a security error. A nonce-lifetime filter shortened the lifetime only during AJAX, so nonces created at page load (24h) didn't match verification (4h) — the filter has been removed and saves now succeed reliably.
* Added: Autosave — toggles, color swatches and the logo selection now save the moment you change them; text fields save when you click away. The manual "Save" button still works for an explicit save-all.
* Fixed: The heavy black border/outline that appeared on toggles and buttons is gone. Disabling Tailwind's global reset (so it can't affect the rest of wp-admin) had removed the dashboard's own button normalization; that is now restored, scoped to the dashboard only.
* Added: One-click "Install Elementor" — installs and activates the latest Elementor directly from the dashboard (no trip to the Plugins screen), then reloads so widgets and templates are immediately available.
* Added: Flagship "Auto-update all plugins" switch on the dashboard — one toggle keeps every plugin on the site updated automatically using WordPress's built-in auto-updater.

= 3.7.0 =
* WP.org compliance: The admin dashboard no longer loads React, Babel, or Tailwind from external CDNs. React 18 is now bundled locally, the JSX is precompiled to plain JS at build time, and Tailwind is compiled to a static stylesheet scoped to the dashboard (with preflight disabled so it can never restyle the rest of wp-admin).
* Added: Branding module — white-label the native wp-login.php screen (logo, link, background, form background, accent color, welcome message, generic error messages) and the admin chrome (custom footer text, hide WordPress version, hide the admin-bar W logo). Purely hook-based; never creates pages. Login styling is skipped automatically when you redirect login to a custom Elementor page. Patterns adapted from LoginPress and White Label CMS.
* Improved: When the custom login URL + endpoint blocking are active, the native wp-login.php now correctly allows password-reset link clicks, password-protected post submissions, logout, and GDPR confirm-action links instead of 404ing them. Adapted from WPS Hide Login's exemption list.

= 3.6.3 =
* Security: Rebuilt the SVG sanitizer as a DOM-based allowlist (was a regex blocklist that could be bypassed). It now strips DOCTYPE/entities (XXE), removes every event handler including unquoted ones, enforces an href/data-URI protocol allowlist, and removes script/foreignObject in all modes. Verified against 10 known bypass vectors. Adapted from the Safe SVG / enshrined approach.
* Security: SVG files are now sanitized on the upload PREFILTER, before WordPress writes them to disk, so a malicious file never lands in a web-accessible location.
* Fixed: SVG MIME detection now runs at the correct priority (75) so SVG uploads aren't silently rejected by WordPress's real-MIME check on modern installs.

= 3.6.2 =
* Fixed: Duplicating an Elementor page no longer corrupts the layout — meta values (notably _elementor_data) are now re-slashed correctly on copy (previously the JSON lost a slash level and broke), stale Elementor CSS caches are skipped and regenerated for the clone, and duplicate-key meta copies 1:1. Adapted from Yoast Duplicate Post's proven approach.
* Added: Brute-force protection now also covers the NATIVE wp-login.php (via the authenticate filter) — previously only the plugin's custom login forms were rate-limited. Locked clients are rejected before the password is checked.
* Added: IP allow-list (Security → IP Allow-list) — list trusted IPs or CIDR ranges that are never rate-limited or locked out, so you can't lock yourself out. Adapted from Limit Login Attempts Reloaded.

= 3.6.1 =
* Fixed: Custom login URL no longer "resets to default" after an https/www switch or site move — the saved slug is now resolved against the current site address, and the login page slug is reconciled with the setting whenever pages are (re)created or settings are imported.
* Fixed: Settings import now re-resolves auth page IDs and the login slug to the local site instead of carrying over the source site's IDs/URLs.
* Fixed: Imported Elementor templates now stamp the Elementor version so modern container templates render correctly (previously a backward-compat flag could break the layout); the root element type is validated before writing.
* Added: Explicit default values for the remaining Elementor form-widget style controls — typography (title/description/field/button), borders, backgrounds, box-shadows, link/message colors, and the divider/social/redirect-timer widgets — all matching the rendered CSS so the editor shows real defaults.

= 3.6.0 =
* Security: Fixed an arbitrary file-disclosure vulnerability in Ghost Mode's aliased-asset server (path traversal could read wp-config.php); added a strict allowlist of static extensions, '..' rejection, and per-base-directory containment.
* Security: Added rate limiting and CAPTCHA enforcement to the lost-password and reset-password endpoints; invalid reset keys now count toward lockout. Added rate limiting to registration.
* Security: CAPTCHA secret keys are no longer printed into admin page source; configuration-changing handlers now require manage_options; settings import now validates is_uploaded_file and caps file size.
* Security: REST user-enumeration blocking now also unsets the /wp/v2/users routes (covers ?rest_route= and other request forms).
* Fixed: The admin menu now uses the real PowerPlus logo as a vector (SVG data URI) so it renders crisply at the correct size and is recolored to match the admin theme — no more oversized/bleeding icon.
* Fixed: Dashboard settings (login URL, security, redirects, modules, ghost/SVG/classic/duplicator) now persist correctly; persistent object caches are invalidated on every settings write.
* Fixed: One-click template import now publishes the page on the Elementor Canvas template and marks it editable; Login Forms and Page Templates act on the site's real auth pages.
* Added: Explicit default values on Elementor form-widget style controls (padding, margins, colors, radii) matching the rendered CSS.
* Improved: Onboarding now opens the modern dashboard instead of a separate unstyled wizard.

= 3.5.8 =
* Added: Premium React dashboard UI with animated stats, quick actions, and module toggles
* Added: Plugin banner and icons on WordPress.org listing
* Added: Lightning bolt SVG admin menu icon
* Improved: Main admin page now loads modern dark-themed dashboard

= 3.5.8 =
* Fixed: All remaining "PowerKit" display strings replaced with "PowerPlus" across admin notices, widget titles, onboarding, conflict detector, and frontend messages
= 3.5.6 =
* Renamed: Plugin slug changed to powerplus-toolkit (approved by WordPress.org)
* Renamed: Plugin display name changed to PowerPlus — All-in-One Powerful Toolkit
* Updated: Text domain updated to powerplus-toolkit across all files
* Updated: Main plugin filename renamed to powerplus-toolkit.php
* Updated: Plugin URI updated to https://wordpress.org/plugins/powerplus-toolkit/

= 3.5.5 =
* Fix: Zip packaging — top-level folder inside archive is now "powerplus-toolkit" (no version suffix), eliminating false TextDomainMismatch errors in Plugin Check
* Fix: Exclude .claude AI directory and all hidden files from release zip

= 3.5.4 =
* Fix: Rename main plugin file to match WP.org slug (powerplus-toolkit.php)
* Fix: Update Plugin URI header to correct WP.org listing URL
* Fix: Move inline <style> block in templates view to enqueued CSS file (pkwt-templates.css)
* Fix: Move inline <script> block in templates view to enqueued JS file (pkwt-templates.js) with wp_localize_script for PHP values
* Fix: Wrap all $_POST username/email/honeypot fields with sanitize_text_field() / sanitize_email() after wp_unslash()
* Fix: Add inline code comments explaining wp_signon() and wp_create_user() use WP core hook chains

= 3.4.2 =
* Fix: Nonce now injected via wp_add_inline_script on elementor-editor handle — guaranteed to reach the page even when external JS file is not yet cached on the server

= 3.4.1 =
* Fix: Nonce now generated fresh on every editor page load via wp_localize_script — eliminates "Security check failed" error in widget template picker
* Fix: Dashboard Page Templates tab Import buttons now use AJAX instead of admin-post form — eliminates "The link you followed has expired" error

= 3.4.0 =
* Feature: Template import now works directly inside the Elementor widget panel — click Import on any layout card in the "Page Templates" section of the Login, Register, Lost Password, or Reset Password widget
* Removed: Old preset/theme colour system (Default, Midnight, Aurora, Minimal, Corporate, Sunset SELECT) removed from all widgets
* Improvement: Template import uses AJAX so it works from within the Elementor editor without any page reloads or expiring form links
* Improvement: After import, links to open the page in Elementor or view it are shown inline in the widget panel

= 3.3.0 =
* Feature: Full Elementor page template library added — 3 complete layout sets (Split Left, Centered Card, Gradient Panel Right), each with Login / Register / Forgot Password / Reset Password variants, importable in one click from the new Page Templates admin tab
* Feature: New "Page Templates" admin tab under PowerPlus settings for browsing and importing templates
* Improvement: Template import writes directly to _elementor_data post meta, sets edit mode to builder, and flushes CSS cache automatically

= 3.2.0 =
* Feature: 6 built-in form templates added to all auth widgets (Login, Register, Lost Password, Reset Password) — choose from Default, Midnight, Aurora, Minimal, Corporate, Sunset directly in the Elementor Content panel

= 3.1.5 =
* Fixed: ?_pkwt_no_cache=1 no longer appended to login, logout, register, or lost password URLs — clean URLs everywhere, cache-busting is handled server-side via HTTP headers only

= 3.1.4 =
* Improved: Login URL field redesigned as a WordPress permalink-style slug editor — type just the slug, the site prefix is shown automatically
* Changed: Default login page slug is now wp-admin (matching WordPress default) instead of pkwt-login

= 3.1.3 =
* Improved: Custom login URL now automatically renames the login page slug to match — no redirect needed, the page is directly accessible at the configured URL with no extra query strings or loop risk

= 3.1.2 =
* Fixed: Custom login URL (/my-login) showing 404 — now cleanly redirects to the configured login page; removed all redirect loop conditions

= 3.1.1 =
* Fixed: ERR_TOO_MANY_REDIRECTS loop — maybe_redirect_legacy_login_page was bouncing pkwt-login → my-login → pkwt-login indefinitely; now skips when custom URL resolves to a WP page
* Fixed: maybe_handle_custom_login_route used get_page_url_by_setting() (which appends ?_pkwt_no_cache) for path comparison, causing loop detection to fail; switched to get_permalink() for clean path comparison

= 3.1.0 =
* Fixed: Custom login URL (/my-login) showing 404 — now redirects to the Elementor login page when one is configured with content
* Fixed: Redirect loop between custom URL and login page slug — maybe_redirect_legacy_login_page now skips when the custom URL resolves to a WordPress page

= 3.0.9 =
* Fixed: Custom login URL serving raw wp-login.php instead of the Elementor login page — maybe_handle_custom_login_route() now skips when a login page with content is configured, letting WordPress render it normally

= 3.0.8 =
* Fixed: Elementor preview iframe fails to load on plugin auth pages — redirector was intercepting the preview request and redirecting it; added Elementor editing detection guard to all redirect methods (maybe_redirect_legacy_login_page, maybe_redirect_wp_login, maybe_block_native_auth_endpoints, maybe_handle_custom_login_route)
* Fixed: X-Frame-Options / CSP frame-ancestors headers now suppressed for all Elementor preview contexts including preview_id, preview_nonce, and WP core preview params

= 3.0.7 =
* Fixed: HTML entities (Don&#039;t, &amp;amp;) appearing in Elementor live preview for text controls — switched content_template() to triple-brace syntax {{{ }}} to prevent double-encoding
* Fixed: Frontend CSS and JS not loading on pages that contain PowerPlus widgets (now detected via Elementor post meta)
* Fixed: Classic Editor module dequeuing wp-editor and wp-blocks on the Elementor editor screen, breaking page editing when Classic Editor was enabled

= 3.0.6 =
* Fixed: White screen on all WordPress admin pages — extra closing brace in capture_change_snapshot() in admin class caused PHP fatal parse error

= 3.0.5 =
* Fixed: Fatal TypeError — fix_svg_filetype() declared $mimes as array but WordPress passes null in some contexts (e.g. plugin zip uploads); type changed to mixed to match WordPress core signature

= 3.0.4 =
* Fixed: Media uploads (images, videos, files) broken when plugin is active — SVG upload hooks were registering unconditionally and interfering with all uploads even when SVG feature was disabled
* Fixed: Elementor editor fails to load login/register pages — X-Frame-Options and CSP frame-ancestors headers were blocking Elementor's preview iframe; headers are now suppressed during editor sessions

= 3.0.3 =
* Fixed: phpcs:enable comment was leaking as visible text in admin page footers
* Fixed: "Elementor is not active" conflict notice showing even when Elementor is active
* Fixed: SVG upload "server cannot process the image" error due to WP thumbnail generation
* Fixed: Footer year hardcoded to 2024 — now uses dynamic current year
* Removed: White Label page and all related functionality
* Fixed: Elementor editor preview not loading (timing issue with elementor/loaded hook)
* Fixed: Elementor widget editor only showed button — all form fields now visible in real-time
* Improved: Complete widget UI redesign — professional modern design with purple CTA button
* Improved: Submit buttons now show loading spinner animation during form submission
* Improved: Login/Register default content looks great out of the box with no extra setup


= 3.0.2 =
* Fixed: Grey oval overlay on settings pages caused by missing CSS class — pkwt-switch now aliased to cle-switch in admin stylesheet

= 3.0.1 =
* Fixed: Added phpcs:ignore for login_message (WP core hook) and wpml_permalink (WPML hook) to suppress false-positive prefix warnings
* Fixed: View template variables suppressed with phpcs:disable scoped to included file context
* Fixed: uninstall.php variables prefixed with pkwt_ for global scope compliance
* Fixed: admin_post_dpp_apply_preset and admin_post_dpp_svg_scan hooks renamed to pkwt_ prefix
* Fixed: All admin redirect URLs updated to page=pkwt-settings and pkwt_notice query param
* Fixed: Post meta key _cle_page_type renamed to _pkwt_page_type; uninstall now cleans this meta
* Fixed: option_page_capability_dpp_settings_group filter renamed to pkwt_dpp_settings_group
* Fixed: Export filename updated from cle-settings-*.json to pkwt-settings-*.json

= 3.0.0 =
* Fixed: Ajax endpoint now derived via admin_url() instead of hardcoded path (WP.org review fix)
* Fixed: Namespace renamed from CLE\ to PKWT\ (4+ character prefix, WP.org compliance)
* Fixed: All option keys, hooks, and transients renamed from cle_/dpp_ to pkwt_/pkwt_dpp_ prefix
* Fixed: ob_start() in Ghost Mode now paired with shutdown action ob_end_flush() for clean buffer close
* Fixed: ABSPATH path concatenation replaced with trailingslashit() for safe path building
* Fixed: require changed to require_once for wp-login.php core file include
* Fixed: Added == External Services == section to readme documenting reCAPTCHA and hCaptcha
* Fixed: Added plugin owner to Contributors list in readme
* Fixed: Text domain confirmed as powerplus-toolkit throughout

= 2.9.9 =
* Changed: Sidebar top-level menu label is now fixed to "PowerPlus"
* Changed: Removed user-facing "CLE" wording from conflict notices, onboarding labels, widget titles, and UI help text
* Changed: Added POWERKIT_AUTH_RECOVERY_MODE constant support (legacy CLE_AUTH_RECOVERY_MODE still works)

= 2.9.8 =
* Fixed: Cleared final Plugin Check nonce-verification warning in custom login route action parsing

= 2.9.7 =
* Fixed: Addressed Plugin Check nonce verification warnings for read-only admin/query access with explicit handling comments
* Fixed: Added profile nonce verification before saving Classic Editor user preference
* Fixed: Sanitized import temp-file path handling and improved AJAX input annotations for password fields
* Fixed: Added explicit annotations for required direct DB cleanup queries and targeted meta-query lookups

= 2.9.6 =
* Fixed: Removed deprecated load_plugin_textdomain() call for WordPress.org translation loading compliance
* Fixed: Updated frontend script defer behavior to modify enqueued script tags (no raw script tag output)
* Fixed: Added missing translators comments for placeholder-based translation strings
* Changed: Moved extra root documentation file out of plugin root to improve Plugin Check packaging compliance

= 2.9.5 =
* Changed: Plugin display name updated to PowerPlus — All-in-One Powerful Toolkit
* Changed: Updated admin/menu/footer and user-facing plugin naming to the new title

= 2.9.4 =
* Fixed: Guarded all plugin constants with defined() checks to prevent activation warnings from duplicate loads
* Fixed: Eliminated unexpected output during activation caused by constant redefinition notices

= 2.9.3 =
* Changed: Plugin display name updated to PowerPlus — All-in-One Powerful Toolkit for WordPress.org naming compliance
* Changed: Admin/menu/footer/user-facing branding strings updated to PowerPlus — All-in-One Powerful Toolkit

= 2.9.2 =
* Changed: Compliance hardening pass for WordPress.org submission prep
* Changed: Translation package aligned to wppowerkit language domain/file naming
* Changed: Ghost plugin-name masking default is now OFF unless explicitly enabled

= 2.9.1 =
* Fixed: Toggle switch click behavior now works directly on the switch body across all settings pages
* Fixed: Legacy DPP toggle conversion now uses label-based switch wrapper for reliable interaction
* Changed: Switch input hit-area now fills the visual toggle control for consistent UX

= 2.9.0 =
* Changed: Rebranded plugin metadata and admin branding to PowerPlus — All-in-One Powerful Toolkit
* Changed: Updated default admin menu/page naming and overview title/subtitle for PowerPlus — All-in-One Powerful Toolkit
* Fixed: Ghost Mode plugin name masking now rewrites plugin folder aliases reliably (including escaped inline URLs)
* Changed: Updated save confirmation messaging and admin footer credits for PowerPlus — All-in-One Powerful Toolkit

= 2.8.8 =
* Changed: Applied unified green visual theme across plugin admin UI accents, buttons, links, focus states, and save bars
* Fixed: Updated switch styling to match requested rounded toggle design globally (same system as Overview)
* Changed: Normalized OFF/ON toggle track colors to light-gray/green with consistent knob shadow

= 2.8.7 =
* Fixed: Unified all module toggles to use the exact same switch system as Overview
* Fixed: Removed conflicting legacy toggle CSS that caused broken checkbox/switch visuals
* Added: Runtime conversion of legacy `.dpp-toggle` inputs into `cle-switch` markup for consistent rendering

= 2.8.6 =
* Fixed: Overview table now forces high-contrast light surface/text colors even when browser or OS dark mode attempts to restyle admin UI
* Fixed: Module row text/icon/link readability on Overview page

= 2.8.5 =
* Fixed: Overview feature table contrast issue on dark-mode systems (text visibility restored)
* Changed: Removed dark-mode CSS override that forced low-contrast dark table rendering
* Changed: Explicit high-contrast text/link/icon colors for overview management table

= 2.8.4 =
* Fixed: Deactivation now explicitly removes auth URL filters immediately using configured filter priority
* Changed: Phase 9 verification pass completed for security/compliance/static quality checks before final packaging

= 2.8.3 =
* Added: Dynamic capability mapping for `cle_settings_group` and `dpp_settings_group` so selected access roles can save settings
* Changed: Admin test mode now forces administrator-only access and save capability
* Fixed: Activity log metadata sanitization now handles non-string values safely

= 2.8.2 =
* Added: Security Operations controls for dashboard toggle, admin-only test mode, and settings activity logging
* Added: Role-based plugin access configuration with administrator-safe fallback
* Added: Settings activity log table with clear action in Security page
* Added: Security event logging for module toggles, scans, tests, snapshots, and rollbacks
* Changed: Plugin admin capability checks now honor configured access roles and admin test mode

= 2.8.1 =
* Added: Deferred loading for CLE admin and module editor scripts to reduce blocking in admin
* Added: Scoped DPP admin stylesheet loading to module-related screens only
* Added: Inline “Learn More” guidance blocks on key settings pages
* Added: Dismissible guided setup panel with smart recommendations on Overview
* Changed: Admin option reads in dashboard controller now use request-level caching to reduce repeated option fetches

= 2.8.0 =
* Changed: Rebuilt Duplicate, SVG Upload, Ghost Mode, and Classic Editor pages into card-based layouts with top/bottom save bars
* Changed: Removed in-page tab bars from module pages and aligned all navigation to left submenu workflow
* Changed: Overview feature control table now uses a 5-column management layout (Element, Status, Usage, Module, Action)
* Changed: Unified switch/toggle rendering by removing conflicting legacy DPP toggle CSS overrides

= 2.7.0 =
* Added: New premium card-based design system for core CLE settings pages (General, Security, Redirects, Compatibility, White Label, Import/Export)
* Added: Top and bottom save controls across settings forms with clear save-state messaging
* Changed: Unified toggle switch UI across CLE and DPP settings pages for consistency
* Changed: Removed legacy in-page tab dependence on converted pages (submenu-driven navigation)
* Changed: Improved spacing, typography, and visual hierarchy to reduce default WordPress form look

= 2.6.4 =
* Added: Native endpoint blocking toggle for `/wp-login.php`, `/wp-login`, and guest `/wp-admin` (404 response)
* Added: Emergency recovery bypass via `CLE_AUTH_RECOVERY_MODE` constant
* Changed: Custom login URL is treated as canonical login route when set, with legacy login page canonical redirect
* Fixed: CLE settings sanitizer now preserves unrelated settings on partial page saves
* Fixed: General page now shows effective login URL (custom URL first, page URL fallback)

= 2.6.3 =
* Fixed: `register_page_id` and `lost_password_page_id` no longer reset to `0` when saving unrelated settings sections
* Fixed: Conflict scan now auto-heals missing auth page IDs and clears stale conflict transient immediately
* Fixed: Required auth page warnings now resolve without waiting for transient expiry

= 2.6.2 =
* Changed: Overview redesigned to a strict minimal “Element / Status” toggle table
* Changed: Removed extra overview cards and helper blocks from the primary feature toggle screen
* Fixed: Admin assets now load reliably for all `cle-settings-*` submenu pages

= 2.6.1 =
* Changed: Overview page features list simplified to a clean table layout
* Changed: Each feature now shows only feature name + one-click ON/OFF switch
* Changed: Removed extra module card text/actions from Overview for faster toggling

= 2.6.0 =
* Changed: Overview module UI to compact grid cards with slider-style enable/disable switches
* Changed: Reduced module card text for cleaner UX
* Changed: One-click module switch now toggles instantly via card switch control
* Changed: Module card "Configure" action simplified to "Open"

= 2.5.0 =
* Added: WordPress left-menu submenu navigation for all plugin sections
* Changed: Removed dependency on in-page tab navigation (tabs are hidden in submenu mode)
* Changed: Settings sections now open as dedicated submenu pages for cleaner workflow

= 2.4.0 =
* Added: Live configuration test actions on Overview (Login URL test + Security Scan)
* Added: Configuration snapshots with one-click rollback restore points
* Added: Module search/filter controls with dependency and impact details
* Added: Sticky quick actions bar, mobile responsiveness improvements, and dark-mode-aware admin styling
* Added: Basic admin activity tracking for settings saves, toggles, scans, and rollbacks

= 2.3.0 =
* Added: 3-step quick setup wizards for Duplicate, SVG Upload, Ghost Mode, and Classic Editor tabs
* Added: One-click “Apply Recommended Settings” presets for major modules
* Added: Card-style section blocks for cleaner module UX
* Added: Contextual help tip for advanced SVG strictness setting
* Changed: Module setup workflow to reduce first-time configuration friction

= 2.2.0 =
* Added: New dashboard-style Overview tab with module cards, quick actions, and health score
* Added: One-click module enable/disable actions from overview with status feedback
* Added: Progressive disclosure controls for advanced settings in Ghost Mode and Classic Editor
* Added: Async loading states for module scan/test actions in admin UI
* Changed: Improved visual hierarchy with card-based layouts and modernized admin styling

= 2.1.1 =
* Fixed: Feature toggles no longer reset to OFF when saving a different tab
* Changed: Replaced key ON/OFF checkboxes with slider-style toggle UI
* Fixed: Classic Editor, SVG, Ghost, and Duplicate settings persistence across reload/logout

= 2.1.0 =
* Added: Classic Editor tab with master toggle and scope controls
* Added: Per post type classic editor selection with dynamic public post type list
* Added: User preference support (Classic/Block) with optional admin bypass
* Added: Gutenberg cleanup controls (widgets, FSE, patterns, block directory, CSS/JS)
* Added: Classic editor toolbar style, default tab, editor notice, and status panel
* Changed: Settings navigation now includes Classic Editor tab

= 2.0.0 =
* Added integrated Duplicate, SVG Upload, and Ghost Mode feature modules
* Added dedicated feature tabs for Duplicate, SVG Upload, and Ghost Mode
* Added server-side SVG sanitization, role-based SVG permissions, media scan, and sanitization logs
* Added Ghost Mode signal controls, endpoint hardening toggles, and detection test tool
* Added custom CMS name support in Ghost Mode (default: Nebula Runtime)
* Added post-login redirect page selector and custom login URL controls in settings
* Improved settings UX with clearer tab-level feature organization
* Security and stability fixes, including stricter settings sanitization

= 1.0.0 =
* Initial release
* Login Form widget with full styling controls
* Register Form widget with configurable fields
* Lost Password widget
* Reset Password widget
* Auth Logo widget
* Auth Message widget
* Social Login widget
* Auth Tabs widget
* CAPTCHA widget with reCAPTCHA v2/v3 and hCaptcha support
* Divider with Text widget
* Terms and Privacy widget
* Redirect Timer widget
* Admin settings panel with General, Redirects, Compatibility, Security, White Label, and Import/Export tabs
* WooCommerce compatibility mode
* Multisite support
* WPML and Polylang compatibility
* WP Rocket and W3 Total Cache auto-exclusion
* Rate limiting and brute force protection
* First-run onboarding wizard

== Upgrade Notice ==

= 2.9.5 =
Branding update: plugin display name is now PowerPlus — All-in-One Powerful Toolkit.

= 2.9.4 =
Activation stability fix: constant definitions are now guarded to avoid duplicate-definition warnings.

= 2.9.3 =
Naming compliance update: plugin display name changed to PowerPlus — All-in-One Powerful Toolkit.

= 2.9.2 =
Compliance prep update: i18n package alignment and safer default for Ghost plugin-name masking.

= 2.9.1 =
Toggle interaction fix: switch body is now directly clickable everywhere, not only related labels/text.

= 2.9.0 =
PowerPlus — All-in-One Powerful Toolkit rebrand update plus stronger Ghost Mode plugin folder masking for frontend asset paths.

= 2.8.8 =
Visual refresh update: global green theme and consistent rounded toggle styling across all settings screens.

= 2.8.7 =
Toggle system unification: all settings pages now use the same Overview-style switch UI.

= 2.8.6 =
Contrast hardening update: Overview page now enforces readable colors regardless of dark-mode overrides.

= 2.8.5 =
UI contrast fix: overview module table now keeps readable high-contrast colors regardless of system dark mode.

= 2.8.4 =
Deactivation hardening update: auth URL filters are now explicitly removed when plugin is deactivated.

= 2.8.3 =
Permission enforcement update: selected access roles can save settings unless admin test mode is enabled.

= 2.8.2 =
Security management update: role-based access, admin test mode, and full settings activity logging from Security tab.

= 2.8.1 =
Performance and guidance update: deferred scripts, scoped admin asset loading, and inline help with guided overview tips.

= 2.8.0 =
Card-system rollout completed for module pages with submenu-only navigation and improved overview controls.

= 2.7.0 =
Major admin UX refresh. Core settings now use a premium card layout, unified toggles, and top/bottom save controls.

= 2.6.4 =
Security routing update with canonical custom login handling and native auth endpoint blocking controls.

= 2.6.3 =
Stability fix release for required auth page detection and automatic page ID recovery.

= 2.6.2 =
Overview now matches a cleaner table-style toggle layout focused only on one-click feature ON/OFF controls.

= 2.6.1 =
Overview simplification update. Features are now toggled from a minimal name + switch list.

= 2.6.0 =
UI refresh. Module controls now use compact grid cards with slider switches for faster enable/disable management.

= 2.5.0 =
Navigation update. Plugin settings now use left admin submenu pages instead of top tab switching.

= 2.4.0 =
Adds live configuration testing, restore points with rollback, and a modernized Overview operations dashboard.

= 2.0.0 =
Major update. Adds Duplicate, SVG Upload, and Ghost Mode modules with expanded settings and security controls.

== External Services ==

This plugin optionally connects to third-party external services when you enable CAPTCHA support. These connections are only made when CAPTCHA is explicitly enabled in the plugin settings.

= Google reCAPTCHA =

When Google reCAPTCHA (v2 or v3) is enabled, this plugin sends the CAPTCHA response token submitted by the user to Google's verification API to confirm the user is not a bot.

* **What data is sent:** The CAPTCHA token generated by the user's browser and your reCAPTCHA secret key.
* **When it is sent:** Only during login, registration, or lost password form submissions when reCAPTCHA is enabled.
* **Service provider:** Google LLC
* **API endpoint:** https://www.google.com/recaptcha/api/siteverify
* **Terms of Service:** https://policies.google.com/terms
* **Privacy Policy:** https://policies.google.com/privacy

= hCaptcha =

When hCaptcha is enabled, this plugin sends the CAPTCHA response token submitted by the user to hCaptcha's verification API.

* **What data is sent:** The CAPTCHA token generated by the user's browser and your hCaptcha secret key.
* **When it is sent:** Only during login, registration, or lost password form submissions when hCaptcha is enabled.
* **Service provider:** Intuition Machines, Inc.
* **API endpoint:** https://hcaptcha.com/siteverify
* **Terms of Service:** https://www.hcaptcha.com/terms
* **Privacy Policy:** https://www.hcaptcha.com/privacy

No data is sent to any external service if CAPTCHA is set to "None" (the default).

== Privacy Policy ==

PowerPlus — All-in-One Powerful Toolkit does not collect, store, or transmit any personal data to external servers or third parties.

**Data stored locally on your site:**

* Plugin settings (stored in wp_options table as cle_settings)
* Custom page IDs created by the plugin (stored in wp_options)
* Login attempt counters for rate limiting (stored as temporary WordPress transients, auto-deleted after the lockout period)

**Data NOT collected:**

* No user data is sent to the plugin author or any third party
* No analytics or tracking of any kind
* No external API calls except for CAPTCHA verification if you enable reCAPTCHA or hCaptcha (in which case Google or Intuition Machines receive the CAPTCHA token — see their respective privacy policies)

**On plugin uninstall:**

All plugin data including settings, custom pages, and transients are permanently deleted from your database.
