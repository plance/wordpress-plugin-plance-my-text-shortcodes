<?php
/**
 * Flash class.
 *
 * @package Plance\Plugin\My_Text_Shortcodes
 */

namespace Plance\Plugin\My_Text_Shortcodes;

defined( 'ABSPATH' ) || exit;

/**
 * Flash class.
 */
class Flash {
	use Singleton;

	/**
	 * Init flash
	 */
	public function init() {
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}

	/**
	 * Hook: admin_notices.
	 *
	 * @return void
	 */
	public function admin_notices() {
		$flash = get_option( $this->get_key(), false );
		if ( ! $flash || ! is_array( $flash ) ) {
			return;
		}

		update_option( $this->get_key(), false );

		$this->print( $flash['message'], $flash['type'], true );
	}

	/**
	 * Redirect.
	 *
	 * @param string $url URL.
	 * @param string $message Message.
	 * @param mixed  $type Type.
	 * @return void
	 */
	public static function redirect( $url, $message, $type = true ) {
		update_option(
			self::instance()->get_key(),
			array(
				'type'    => $type,
				'message' => $message,
			)
		);

		wp_safe_redirect( $url );
		exit;
	}

	/**
	 * Print notice.
	 *
	 * @param  string      $message Message.
	 * @param  bool|string $type Type.
	 * @param  bool        $echo Print or return data.
	 * @return void|string
	 */
	public function print( $message, $type, $echo = true ) {
		if ( is_bool( $type ) ) {
			$class = true === $type ? 'updated' : 'error';
		} else {
			$class = $type;
		}

		$result = '<div id="message" class="' . $class . ' notice is-dismissible">';
		if ( is_array( $message ) ) {
			foreach ( $message as $text ) {
				$result .= '<p>' . esc_attr( $text ) . '</p>';
			}
		} else {
			$result .= '<p>' . esc_attr( $message ) . '</p>';
		}
		$result .= '</div>';

		if ( $echo ) {
			echo $result; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		return $result;
	}

	/**
	 * Return key.
	 *
	 * @return string
	 */
	private function get_key() {
		$key = str_replace( '\\', '_', __CLASS__ );
		$key = strtolower( $key );
		$key = '__' . $key;

		return $key;
	}
}
