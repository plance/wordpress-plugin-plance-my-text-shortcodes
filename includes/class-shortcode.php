<?php
/**
 * Shortcode class.
 *
 * @package Plance\Plugin\My_Text_Shortcodes
 */

namespace Plance\Plugin\My_Text_Shortcodes;

defined( 'ABSPATH' ) || exit;

/**
 * Shortcode class.
 */
class Shortcode {
	use Singleton;

	/**
	 * Init.
	 *
	 * @return void
	 */
	protected function init() {
		global $wpdb;

		if ( is_admin() ) {
			return;
		}

		$shortcodes = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			"SELECT *
			FROM `{$wpdb->prefix}plance_text_shortcodes`
			WHERE `sh_is_lock` = 0",
			ARRAY_A
		);

		foreach ( $shortcodes as $shortcode ) {
			add_shortcode(
				'mtsc-' . $shortcode['sh_code'],
				function() use ( $shortcode ) {
					return $shortcode['sh_description'];
				}
			);
		}
	}
}
