<?php
/**
 * Admin_Settings class.
 *
 * @package Plance\Plugin\My_Text_Shortcodes
 */

namespace Plance\Plugin\My_Text_Shortcodes;

defined( 'ABSPATH' ) || exit;

/**
 * Admin_Menu class.
 */
class Admin_Menu {
	use Singleton;

	/**
	 * Init.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_filter( 'set-screen-option', array( $this, 'set_screen_option' ), 10, 3 );
	}

	/**
	 * Hook: admin_menu.
	 *
	 * @return void
	 */
	public function admin_menu() {
		$hook_list = add_menu_page(
			__( 'List Shortcodes', 'my-text-shortcodes' ),
			__( 'My Shortcodes', 'my-text-shortcodes' ),
			'manage_options',
			Controller_Table::SLUG,
			array( Controller_Table::instance(), 'render' )
		);

		add_submenu_page(
			Controller_Table::SLUG,
			__( 'List Shortcodes', 'my-text-shortcodes' ),
			__( 'List Shortcodes', 'my-text-shortcodes' ),
			'manage_options',
			Controller_Table::SLUG,
			array( Controller_Table::instance(), 'render' )
		);

		$hook_form = add_submenu_page(
			Controller_Table::SLUG,
			__( 'Creating Shortcode', 'my-text-shortcodes' ),
			__( 'Create Shortcode', 'my-text-shortcodes' ),
			'manage_options',
			Controller_Form::SLUG,
			array( Controller_Form::instance(), 'render' )
		);

		add_action( 'load-' . $hook_list, array( Controller_Table::instance(), 'action' ) );
		add_action( 'load-' . $hook_form, array( Controller_Form::instance(), 'action' ) );
	}

	/**
	 * Hook: set_screen_option.
	 *
	 * @param  mixed $status Status.
	 * @param  mixed $option Option.
	 * @param  mixed $value Value.
	 * @return string
	 */
	public function set_screen_option( $status, $option, $value ) {
		if ( 'plmsc_per_page' === $option ) {
			return $value;
		}
		return $status;
	}
}
