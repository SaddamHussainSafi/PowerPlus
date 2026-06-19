=== PowerPlus — All-in-One Powerful Toolkit ===
Contributors: saddamhussainsafi, profilmi
Donate link: https://saddamhussain.com.np/
Tags: login, elementor, custom login, login page, register
Requires at least: 6.0
Tested up to: 6.9
Stable tag: 3.10.4
Requires PHP: 8.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Power Packed Tools - Complete WordPress Management Suite.

== Description ==

**PowerPlus** helps you create branded WordPress login, register, lost-password and reset-password pages — without replacing WordPress authentication. Default WordPress login pages are plain, limited and off-brand; PowerPlus gives you a working, customizable login experience in a few guided steps, then lets you refine the design with built-in styling or Elementor widgets.

Customize forms, fields, buttons, typography, spacing, colors, messages and layouts from one clean dashboard — while WordPress-native login, registration, password reset and security hooks keep working exactly as they should.

= Overview =

PowerPlus is built around one hero feature — the **Custom Auth Builder** — with four supporting utility modules:

**Main module**

* **Custom Auth Builder** — Branded login, register, lost-password and reset-password pages on native WordPress auth. Custom login URL, redirects, CAPTCHA, WooCommerce compatibility, and Elementor design support.

**Utility modules**

* **Duplicate Content** — Duplicate posts, pages, products and custom post types, preserving meta and Elementor data.
* **SVG Upload Control** — Allow SVG uploads with server-side sanitization, role permissions and scan logs.
* **Privacy Hardening** (Ghost Mode) — Reduce common WordPress fingerprints with configurable source/API controls and safe defaults.
* **Classic Editor Control** — Disable the block editor per post type, with user preferences.

= Who is this for? =

* **Agencies** building client sites that need branded auth pages
* **Business owners** who want a professional login experience
* **Developers** who want full control without writing custom CSS

= How it works =

1. Install and activate the plugin
2. A guided setup wizard welcomes you — choose which modules to enable, or skip and configure later
3. Enabling Login Customization creates working Login, Register, Lost Password and Reset Password pages on native WordPress auth — no manual setup
4. Customize the design with the built-in styling controls, or open a page in Elementor to drag in the PowerPlus widgets
5. Set a custom (hidden) login URL and review everything from one dashboard

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
* No data sent externally unless optional CAPTCHA integrations are enabled
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

PowerPlus stores only plugin settings on your WordPress installation and runs no analytics or tracking. The only data sent externally is when you enable optional CAPTCHA (Google reCAPTCHA or hCaptcha), which transmits a verification token to that service. See the External Services section for details.

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

1. You're taken to a guided setup wizard — you can Start Setup, skip it, or configure later
2. The wizard creates the auth pages it needs and enables only the modules you choose (no duplicate pages on re-activation)
3. Customize the design with the built-in controls or in Elementor, and set your custom login URL

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

= Is it privacy-friendly? =

PowerPlus is privacy-friendly by design. It stores only plugin settings in your WordPress database and does not run analytics or tracking. The one exception is optional CAPTCHA: if you enable Google reCAPTCHA or hCaptcha, those services receive a verification token from the visitor's browser (see the External Services section). It also integrates with WordPress's privacy tools, including the personal data eraser and exporter.

= What happens if I delete the custom login page by accident? =

The plugin detects missing pages automatically and shows an admin notice with a one-click option to recreate them.

= Can I use it on a multisite installation? =

Yes. Each subsite gets its own set of custom pages. Network admins can manage settings across the network.

= Does it conflict with caching plugins? =

The plugin automatically registers custom auth pages with WP Rocket and W3 Total Cache to exclude them from caching. For other caching plugins, the plugin adds no-cache headers on all auth pages.

= Can I show my own branding instead of the plugin name? =

Yes. The Branding module white-labels the native login screen (logo, link, background, accent color, messages) and the admin chrome (custom footer text, hide the WordPress version, hide the admin-bar logo). You can also hide PowerPlus from the plugins list on client sites.

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

= 3.10.4 =
* Added: Guided setup wizard to enable features and create the login, register, lost-password and reset-password pages in one step.
* Improved: Refined dashboard design, motion and onboarding experience.

= 3.9.0 =
* Added: Hidden login URL with a configurable redirect for blocked visitors, plus protection for wp-login.php and wp-admin.
* Improved: One editable page each for login, register, lost password and reset password.

= 3.8.0 =
* Added: Branding module to white-label the login screen and admin area.
* Added: One-click Elementor install and an optional auto-update-all-plugins switch.

= 3.6.0 =
* Security: Strengthened SVG sanitization, login rate limiting and an IP allow-list.
* Improved: More reliable settings saving and Elementor page duplication.

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

PowerPlus stores plugin data locally on your site and does not run analytics or tracking. The only data transmitted externally is an optional CAPTCHA verification token, sent to Google reCAPTCHA or hCaptcha only when you enable that feature (see External Services).

**Data stored locally on your site:**

* Plugin settings (stored in the wp_options table under the `pkwt_settings` key, plus per-module keys `pkwt_dpp_settings`, `pkwt_dpp_svg_settings`, `pkwt_dpp_ghost_settings` and `pkwt_dpp_classic_settings`). Sites upgraded from the plugin's earlier "CLE/PowerKit" releases may retain legacy `cle_*` keys, which are migration leftovers and are cleaned up on uninstall.
* Custom page IDs created by the plugin (stored in wp_options)
* Login attempt counters for rate limiting (stored as temporary WordPress transients, auto-deleted after the lockout period)

**Data NOT collected:**

* No user data is sent to the plugin author or any third party
* No analytics or tracking of any kind
* No external API calls except for CAPTCHA verification if you enable reCAPTCHA or hCaptcha (in which case Google or Intuition Machines receive the CAPTCHA token — see their respective privacy policies)

**On plugin uninstall:**

All plugin data including settings, custom pages, and transients are permanently deleted from your database.
