# Changelog

## Version 2.9.9
- Sidebar top-level menu label is now fixed to "PowerKit".
- Removed user-facing "CLE" wording from conflict notices, onboarding labels, widget titles, and UI help text.
- Added `POWERKIT_AUTH_RECOVERY_MODE` constant support (legacy `CLE_AUTH_RECOVERY_MODE` remains supported).

## Version 2.9.8
- Cleared final Plugin Check nonce-verification warning in custom login route action parsing.

## Version 2.9.7
- Addressed remaining Plugin Check nonce verification warnings for read-only admin/query parameters with explicit handling comments.
- Added profile nonce verification before saving Classic Editor user preference.
- Sanitized import temp-file path handling and documented raw password/honeypot field usage in AJAX handlers.
- Added explicit annotations for required one-time DB cleanup queries and targeted meta lookup queries.

## Version 2.9.6
- Removed deprecated `load_plugin_textdomain()` usage for WordPress.org translation loading compliance.
- Fixed frontend script defer handling to modify enqueued script tags instead of outputting raw `<script>` HTML.
- Added missing translators comments for placeholder-based translatable strings.
- Moved root markdown documentation out of plugin root to avoid Plugin Check packaging warnings.

## Version 2.9.5
- Updated plugin display name to **PowerKit - Powerful Tools For Your Website**.
- Updated admin/menu/footer and user-facing plugin naming to the new title.

## Version 2.9.4
- Fixed constant redefinition warnings by guarding all plugin constants with `defined()` checks.
- Fixed activation-time unexpected output caused by duplicate constant notices.

## Version 2.9.3
- Updated plugin display name to **PowerKit - Powerful Tools For Your Website** for WordPress.org naming compliance.
- Updated admin/menu/footer/user-facing branding strings accordingly.

## Version 2.9.2
- Compliance hardening pass for WordPress.org submission prep.
- Translation package aligned to `wppowerkit` domain/file naming.
- Ghost plugin-name masking default changed to OFF unless explicitly enabled.

## Version 2.9.1
- Fixed toggle switch click behavior so the visible switch body is directly clickable across settings screens.
- Switched legacy toggle wrapper generation to label-based markup for reliable interaction.
- Updated switch input hit-area sizing for consistent ON/OFF behavior.

## Version 2.9.0 - Rebranding to PowerKit - Powerful Tools For Your Website
- Rebranded plugin to PowerKit - Powerful Tools For Your Website.
- Updated core metadata, admin naming, and save messaging.
- Improved Ghost Mode plugin-folder masking to hide plugin slugs in aliased asset paths.
- Updated footer credits and overview branding text.

## Version 2.8.8
- Applied unified green visual theme across plugin admin UI accents, buttons, links, and save bars.
- Updated switch styling to rounded toggle design globally.
