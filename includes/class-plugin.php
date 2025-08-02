<?php
/**
 * Plugin class.
 *
 * @package Plance\Plugin\My_Text_Shortcodes
 */

namespace Plance\Plugin\My_Text_Shortcodes;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin class.
 */
class Plugin {

	/**
	 * Activate.
	 *
	 * @return bool
	 */
	public static function activate() {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		self::walk_site(
			function() {
				global $wpdb;

				dbDelta(
					"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}plance_text_shortcodes` (
					`sh_id` INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
					`sh_title` VARCHAR(150) NOT NULL,
					`sh_code` VARCHAR(25) NOT NULL,
					`sh_description` text NOT NULL,
					`sh_is_lock` TINYINT(1) UNSIGNED NOT NULL,
					`sh_date_create` INT(10) UNSIGNED NOT NULL
					) {$wpdb->get_charset_collate()};"
				);
			}
		);

		return true;
	}

	/**
	 * Uninstall.
	 *
	 * @return bool
	 */
	public static function uninstall() {
		self::walk_site(
			function() {
				global $wpdb;

				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange -- intentional schema change on uninstall
				$wpdb->query( 'DROP TABLE IF EXISTS `' . $wpdb->prefix . 'plance_text_shortcodes`' );
			}
		);

		return true;
	}

	/**
	 * Walk site.
	 *
	 * @param  callable $callback Callback.
	 * @return void
	 */
	private static function walk_site( $callback ) {
		if ( is_multisite() ) {
			$sites = get_sites();

			foreach ( $sites as $site ) {
				switch_to_blog( $site->blog_id );

				call_user_func( $callback );

				restore_current_blog();
			}
		} else {
			call_user_func( $callback );
		}
	}
}
