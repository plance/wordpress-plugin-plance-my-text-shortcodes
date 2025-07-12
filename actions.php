<?php
/**
 * Actions.
 *
 * @package Plance\Plugin\My_Text_Shortcodes
 */

namespace Plance\Plugin\My_Text_Shortcodes;

defined( 'ABSPATH' ) || exit;


register_activation_hook( __DIR__ . '/my-text-shortcodes.php', array( Plugin::class, 'activate' ) );
register_uninstall_hook( __DIR__ . '/my-text-shortcodes.php', array( Plugin::class, 'uninstall' ) );


add_action( 'plugins_loaded', array( Flash::class, 'instance' ) );
add_action( 'plugins_loaded', array( Assets::class, 'instance' ) );
add_action( 'plugins_loaded', array( Shortcode::class, 'instance' ) );
add_action( 'plugins_loaded', array( Admin_Menu::class, 'instance' ) );
