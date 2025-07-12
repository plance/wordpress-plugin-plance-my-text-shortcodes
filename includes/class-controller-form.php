<?php
/**
 * Controller_Form class.
 *
 * @package Plance\Plugin\My_Text_Shortcodes
 */

namespace Plance\Plugin\My_Text_Shortcodes;

defined( 'ABSPATH' ) || exit;

/**
 * Controller_Form class.
 */
class Controller_Form {
	use Singleton;

	const SLUG = 'plance-my-text-shortcodes-form';

	/**
	 * Shortcode
	 *
	 * @var array
	 */
	private $shortcode = array(
		'sh_title'       => '',
		'sh_code'        => '',
		'sh_is_lock'     => '',
		'sh_description' => '',
	);

	/**
	 * Action.
	 *
	 * @return void
	 */
	public function action() {
		global $wpdb;

		$is_ajax_request = strtolower( filter_input( INPUT_SERVER, 'HTTP_X_REQUESTED_WITH', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ) === 'xmlhttprequest';
		if ( $is_ajax_request ) {
			return;
		}

		$shortcode_id    = (int) filter_input( INPUT_GET, 'shortcode_id', FILTER_SANITIZE_NUMBER_INT );
		$is_post_request = strtolower( filter_input( INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ) === 'post';

		if ( $is_post_request ) {
			$input  = filter_input( INPUT_POST, 'shortcode', FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY );
			$errors = $this->validate( $input, $shortcode_id );

			if ( empty( $errors ) ) {
				if ( $shortcode_id ) {
					// Update.
					$wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
						$wpdb->prefix . 'plance_text_shortcodes',
						array(
							'sh_title'       => $input['sh_title'],
							'sh_code'        => $input['sh_code'],
							'sh_description' => $input['sh_description'],
							'sh_is_lock'     => $input['sh_is_lock'],
						),
						array( 'sh_id' => $shortcode_id ),
						array( '%s', '%s', '%s', '%d' ),
						array( '%d' )
					);

					$query_args = array(
						'page'         => self::SLUG,
						'shortcode_id' => $shortcode_id,
					);
					Flash::redirect( add_query_arg( $query_args, admin_url( 'admin.php' ) ), __( 'Shortcode updated', 'my-text-shortcodes' ) );
				}

				// Create.
				$wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$wpdb->prefix . 'plance_text_shortcodes',
					array(
						'sh_title'       => $input['sh_title'],
						'sh_code'        => $input['sh_code'],
						'sh_description' => $input['sh_description'],
						'sh_is_lock'     => $input['sh_is_lock'],
						'sh_date_create' => time(),
					),
					array( '%s', '%s', '%s', '%d', '%s' )
				);

				Flash::redirect( add_query_arg( array( 'page' => self::SLUG ), admin_url( 'admin.php' ) ), __( 'Shortcode created', 'my-text-shortcodes' ) );
			} else {
				Flash::instance()->print( $errors, false );
				$this->shortcode = array_merge( $this->shortcode, $input );
			}

			return;
		}

		if ( $shortcode_id ) {
			$sql = "
				SELECT *
				FROM `{$wpdb->prefix}plance_text_shortcodes`
				WHERE `sh_id` = %d
			";

			$sql_prepared = $wpdb->prepare(
				$sql, // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				array(
					$shortcode_id,
				)
			);

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$this->shortcode = $wpdb->get_row(
				$sql_prepared, // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				ARRAY_A
			);

			if ( empty( $this->shortcode ) ) {
				Flash::redirect( add_query_arg( array( 'page' => self::SLUG ), admin_url( 'admin.php' ) ), __( 'Shortcode not found!', 'my-text-shortcodes' ), false );
			}
		}
	}

	/**
	 * Render.
	 *
	 * @return void
	 */
	public function render() {
		$shortcode_id = (int) filter_input( INPUT_GET, 'shortcode_id', FILTER_SANITIZE_NUMBER_INT );
		$query_args   = array( 'page' => self::SLUG );

		if ( $shortcode_id ) {
			$form_title                 = __( 'Editing shortcode', 'my-text-shortcodes' );
			$query_args['shortcode_id'] = $shortcode_id;
		} else {
			$form_title = __( 'Creating shortcode', 'my-text-shortcodes' );
		}

		load_template(
			PATH . '/templates/admin/form.php',
			false,
			array(
				'form_title'  => $form_title,
				'form_action' => add_query_arg( $query_args, admin_url( 'admin.php' ) ),
				'shortcode'   => $this->shortcode,
			)
		);
	}

	/**
	 * Validate.
	 *
	 * @param  array $data Data.
	 * @param  int   $shortcode_id ID.
	 * @return array
	 */
	private function validate( $data, $shortcode_id ) {
		$errors = array();
		$labels = array(
			'sh_title'       => __( 'Title', 'my-text-shortcodes' ),
			'sh_code'        => __( 'Code', 'my-text-shortcodes' ),
			'sh_is_lock'     => __( 'State', 'my-text-shortcodes' ),
			'sh_description' => __( 'Description', 'my-text-shortcodes' ),
		);

		if ( $shortcode_id ) {
			if ( ! $this->is_shortcode_exist( $shortcode_id ) ) {
				$errors[] = __( 'This shortcode does not exist', 'my-text-shortcodes' );
			}
		}

		$data = array_map( 'trim', $data );
		if ( isset( $data['sh_is_lock'] ) ) {
			$data['sh_is_lock'] = intval( $data['sh_is_lock'] );
		}

		foreach ( array( 'sh_title', 'sh_code', 'sh_description' ) as $field ) {
			if ( empty( $data[ $field ] ) ) {
				// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
				$errors[] = sprintf( __( '"%s" must not be empty', 'my-text-shortcodes' ), $labels[ $field ] );
			}
		}

		if ( ! empty( $data['sh_title'] ) && mb_strlen( $data['sh_title'] ) > 150 ) {
			// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
			$errors[] = sprintf( __( '"%1$s" must not exceed %1$d characters long', 'my-text-shortcodes' ), $labels['sh_title'], 150 );
		}

		if ( ! empty( $data['sh_code'] ) && mb_strlen( $data['sh_code'] ) > 25 ) {
			// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
			$errors[] = sprintf( __( '"%1$s" must not exceed %1$d characters long', 'my-text-shortcodes' ), $labels['sh_code'], 25 );
		}

		if ( ! empty( $data['sh_code'] ) && ! preg_match( '/^[a-z0-9]+[a-z0-9\-]*[a-z0-9]+$/i', $data['sh_code'] ) ) {
			// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
			$errors[] = sprintf( __( '"%s" does not match the required format', 'my-text-shortcodes' ), $labels['sh_code'] );
		}

		if ( ! empty( $data['sh_code'] ) ) {
			if ( ! $this->is_allow_code( $data['sh_code'], $shortcode_id ) ) {
				$errors[] = __( 'This shortcode has already been taken, select another shortcod', 'my-text-shortcodes' );
			}
		}

		if ( isset( $data['sh_is_lock'] ) && ! in_array( $data['sh_is_lock'], array( 0, 1 ), true ) ) {
			// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
			$errors[] = sprintf( __( '"%s" must be one of the available options', 'my-text-shortcodes' ), $labels['sh_is_lock'] );
		}

		return $errors;
	}

	/**
	 * Shortcode exist or not.
	 *
	 * @param  int $id ID.
	 * @return bool
	 */
	public function is_shortcode_exist( $id ) {
		global $wpdb;

		$sql = "
			SELECT `sh_id`
			FROM `{$wpdb->prefix}plance_text_shortcodes`
			WHERE `sh_id` = %d
		";

		$sql_prepared = $wpdb->prepare(
			$sql, // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			array(
				(int) $id,
			)
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
		return $wpdb->get_var( $sql_prepared ) > 0;
	}

	/**
	 * Allow code or not.
	 *
	 * @param  string $code Code.
	 * @param  int    $id ID.
	 * @return bool
	 */
	public function is_allow_code( $code, $id ) {
		global $wpdb;

		$sql = "
			SELECT `sh_id`
			FROM `{$wpdb->prefix}plance_text_shortcodes`
			WHERE `sh_code` = %s
			AND `sh_id` <> %d
		";

		$sql_prepared = $wpdb->prepare(
			$sql, // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			array(
				$code,
				(int) $id,
			)
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
		return $wpdb->get_var( $sql_prepared ) > 0 ? false : true;
	}
}
