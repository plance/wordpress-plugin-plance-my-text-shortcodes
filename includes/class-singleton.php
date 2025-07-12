<?php
/**
 * Singleton class.
 *
 * @package Plance\Plugin\My_Text_Shortcodes
 */

namespace Plance\Plugin\My_Text_Shortcodes;

defined( 'ABSPATH' ) || exit;

/**
 * Singleton class.
 */
trait Singleton {
	/**
	 * Object instance
	 *
	 * @var static|null
	 */
	protected static $instance = null;

	/**
	 * Gets the instance
	 *
	 * @return static
	 */
	final public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new static();
		}
		return self::$instance;
	}

	/**
	 * The constructor
	 */
	final protected function __construct() {
		if ( method_exists( $this, 'init' ) ) {
			$this->init();
		}
	}
}
