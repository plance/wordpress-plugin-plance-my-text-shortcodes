<?php
/**
 * Controller_Table class.
 *
 * @package Plance\Plugin\My_Text_Shortcodes
 */

namespace Plance\Plugin\My_Text_Shortcodes;

defined( 'ABSPATH' ) || exit;

/**
 * Controller_Table class.
 */
class Controller_Table {
	use Singleton;

	const SLUG = 'plance-my-text-shortcodes-table';

	/**
	 * Table.
	 *
	 * @var Table_Shortcodes
	 */
	private $table;

	/**
	 * Action.
	 *
	 * @return void
	 */
	public function action() {
		add_screen_option(
			'per_page',
			array(
				'label'   => __( 'Records', 'my-text-shortcodes' ),
				'default' => 10,
				'option'  => 'plmsc_per_page',
			)
		);

		$this->table = new Table_Shortcodes();
		$action      = $this->table->current_action();

		if ( empty( $action ) ) {
			return;
		}

		$shortcode_ids = array();
		if ( ! empty( $_GET['shortcode_id'] ) && is_array( $_GET['shortcode_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$shortcode_ids = filter_input( INPUT_GET, 'shortcode_id', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY );
		} else {
			$shortcode_ids = array( filter_input( INPUT_GET, 'shortcode_id', FILTER_SANITIZE_NUMBER_INT ) );
		}

		if ( empty( $shortcode_ids ) ) {
			return;
		}

		global $wpdb;

		switch ( $action ) {
			case 'delete':
				foreach ( $shortcode_ids as $shortcode_id ) {
					$wpdb->delete( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
						$wpdb->prefix . 'plance_text_shortcodes',
						array( 'sh_id' => (int) $shortcode_id ),
						array( '%d' )
					);
				}
				Flash::redirect( add_query_arg( array( 'page' => self::SLUG ), admin_url( 'admin.php' ) ), __( 'Shortcodes deleted', 'my-text-shortcodes' ) );
				break;

			case 'hide':
				foreach ( $shortcode_ids as $shortcode_id ) {
					$wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
						$wpdb->prefix . 'plance_text_shortcodes',
						array( 'sh_is_lock' => 1 ),
						array( 'sh_id' => (int) $shortcode_id ),
						array( '%d' ),
						array( '%d' )
					);
				}
				Flash::redirect( add_query_arg( array( 'page' => self::SLUG ), admin_url( 'admin.php' ) ), __( 'Shortcodes hide', 'my-text-shortcodes' ) );
				break;

			case 'show':
				foreach ( $shortcode_ids as $shortcode_id ) {
					$wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
						$wpdb->prefix . 'plance_text_shortcodes',
						array( 'sh_is_lock' => 0 ),
						array( 'sh_id' => (int) $shortcode_id ),
						array( '%d' ),
						array( '%d' )
					);
				}
				Flash::redirect( add_query_arg( array( 'page' => self::SLUG ), admin_url( 'admin.php' ) ), __( 'Shortcodes show', 'my-text-shortcodes' ) );
				break;
		}
	}

	/**
	 * Render.
	 *
	 * @return void
	 */
	public function render() {
		$this->table->prepare_items();
		?>
		<div class="wrap">
			<h2>
				<?php esc_html_e( 'List shortcodes', 'my-text-shortcodes' ); ?>
				<a href="<?php echo esc_url( add_query_arg( array( 'page' => Controller_Form::SLUG ) ) ); ?>" class="page-title-action">
					<?php esc_html_e( 'Add shortcode', 'my-text-shortcodes' ); ?>
				</a>
			</h2>
			<form method="get">
				<input type="hidden" name="page" value="<?php echo esc_attr( self::SLUG ); ?>" />
				<?php $this->table->search_box( __( 'Search', 'my-text-shortcodes' ), 'search_id' ); ?>
				<?php $this->table->display(); ?>
			</form>
		</div>
		<?php
	}
}
