=== My Text Shortcodes ===
Contributors: plance
Tags: shortcode, text, html, banner, ad
Requires at least: 4.0.0
Tested up to: 6.8
Stable tag: 1.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A lightweight plugin for creating and managing custom text shortcodes.

== Description ==
A lightweight plugin for creating and managing custom text shortcodes.

With this plugin, you can create an unlimited number of custom text shortcodes via the WordPress admin panel. Each shortcode can contain any text or HTML content, such as ads, banners, counters, or reusable blocks of content.

All created shortcodes are listed in an admin table for easy access and management.
Shortcodes are stored in a separate custom database table.
Each shortcode is automatically assigned a unique name in the format `[mtsc-name-id]`, where `mtsc-` is the default prefix to avoid conflicts with other shortcodes.

== Installation ==
1. Upload the plugin to the `/wp-content/plugins/` directory or install it via the WordPress plugin installer.
2. Activate the plugin through the “Plugins” menu in WordPress.
3. Navigate to the plugin page in the admin panel to start adding your shortcodes.

== Screenshots ==
1. Admin interface: list of all created shortcodes in a sortable table.
2. Admin form for creating a new shortcode with name and content fields.

== Changelog ==
= 1.1.0 =
* Complete code refactoring.

= 1.0.2 =
* Added default `mtsc-` prefix to all shortcodes to avoid naming conflicts.

= 1.0.1 =
* Fixed pagination bug when searching shortcodes in the admin table.

= 1.0 =
* Initial release.
