<?php
/**
 * Bootstrap.
 *
 * @package Plance\Plugin\My_Text_Shortcodes
 */

namespace Plance\Plugin\My_Text_Shortcodes;

defined( 'ABSPATH' ) || exit;


const PATH     = __DIR__;
const VERSION  = '1.1.0';
const SECURITY = 'plance_plugin_my_text_shortcodes__xyz';

define( 'PLANCE_PLUGIN_MY_TEXT_SHORTCODES_URL', rtrim( plugin_dir_url( __FILE__ ), '/' ) );


/**
 * Autoload plugin classes.
 */
spl_autoload_register(
	function ( $class ) {
		if ( strpos( $class, __NAMESPACE__ . '\\' ) !== 0 ) {
			return;
		}

		$pieces    = explode( '\\', $class );
		$classname = array_pop( $pieces );
		$file_name = 'class-' . str_replace( '_', '-', strtolower( $classname ) );
		include_once PATH . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . $file_name . '.php';
	}
);
