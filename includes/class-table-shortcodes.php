<?php
/**
 * Table_Shortcodes class.
 *
 * @package Plance\Plugin\My_Text_Shortcodes
 */

namespace Plance\Plugin\My_Text_Shortcodes;

defined( 'ABSPATH' ) || exit;

use WP_List_Table;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Table_Shortcodes class.
 */
class Table_Shortcodes extends WP_List_Table {
	/**
	 * Prepares the list of items for displaying.
	 *
	 * @return void
	 */
	public function prepare_items() {
		global $wpdb;

		$total_items = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			"SELECT COUNT(`sh_id`)
			FROM `{$wpdb->prefix}plance_text_shortcodes`
			{$this->get_part_sql_where()}" // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		);

		$per_page = $this->get_items_per_page( 'plmsc_per_page', 10 );

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
			)
		);

		$this->items = $this->table_data();
		// phpcs:ignore Squiz.PHP.CommentedOutCode.Found
		// $this->_column_headers = $this->get_column_info();
		// $this->items           = $data;
	}

	/**
	 * Return columns.
	 *
	 * @return array
	 */
	public function get_columns() {
		return array(
			'cb'             => '<input type="checkbox" />',
			'sh_id'          => __( 'ID', 'my-text-shortcodes' ),
			'sh_title'       => __( 'Title', 'my-text-shortcodes' ),
			'sh_code'        => __( 'Code', 'my-text-shortcodes' ),
			'sh_is_lock'     => __( 'State', 'my-text-shortcodes' ),
			'sh_date_create' => __( 'Date create', 'my-text-shortcodes' ),
		);
	}

	/**
	 * Return sortable columns.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		return array(
			'sh_id'          => array( 'sh_id', false ),
			'sh_title'       => array( 'sh_title', false ),
			'sh_code'        => array( 'sh_code', false ),
			'sh_is_lock'     => array( 'sh_is_lock', false ),
			'sh_date_create' => array( 'sh_date_create', false ),
		);
	}

	/**
	 * Return table data.
	 *
	 * @return array
	 */
	private function table_data() {
		global $wpdb;

		$per_page = (int) $this->get_pagination_arg( 'per_page' );
		$pagenum  = (int) $this->get_pagenum();
		$order_ar = $this->get_sortable_columns();

		$order   = 'ASC';
		$orderby = 'sh_title';

		$input_order   = filter_input( INPUT_GET, 'order', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$input_orderby = filter_input( INPUT_GET, 'orderby', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( ! empty( $input_order ) ) {
			$order = 'asc' === $input_order ? 'ASC' : 'DESC';
		}

		if ( ! empty( $input_orderby ) && ! empty( $order_ar[ $input_orderby ] ) ) {
			$orderby = $input_orderby;
		}

		$sql = "
			SELECT *
			FROM `{$wpdb->prefix}plance_text_shortcodes`
			{$this->get_part_sql_where()}
			ORDER BY `{$orderby}` {$order}
			LIMIT %d, %d
		";

		$sql_prepared = $wpdb->prepare(
			$sql, // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			array(
				( ( $pagenum - 1 ) * $per_page ),
				$per_page,
			)
		);

		$itetms = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$sql_prepared, // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			ARRAY_A
		);

		return $itetms;
	}

	/**
	 * Print no items.
	 *
	 * @return void
	 */
	public function no_items() {
		esc_html_e( 'Data not found', 'my-text-shortcodes' );
	}

	/**
	 * Print column like default.
	 *
	 * @param  array  $item Item.
	 * @param  string $column_name Column name.
	 * @return string
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {

			case 'sh_id':
			case 'sh_title':
				return $item[ $column_name ];

			case 'sh_code':
				return '[mtsc-' . $item['sh_code'] . ']';

			case 'sh_date_create':
				return '<abbr title="' . wp_date( 'd.m.Y H:i', $item['sh_date_create'] ) . '">' . wp_date( 'd.m.Y', $item['sh_date_create'] ) . '</abbr>';
		}
	}

	/**
	 * Create checkbox.
	 *
	 * @param object|array $item Item.
	 * @return string
	 */
	public function column_cb( $item ) {
		return '<input type="checkbox" name="shortcode_id[]" value="' . $item['sh_id'] . '" />';
	}

	/**
	 * Return state column.
	 *
	 * @param array $item Item.
	 * @return string
	 */
	public function column_sh_is_lock( $item ) {
		if ( intval( $item['sh_is_lock'] ) ) {
			return __( 'hidden', 'my-text-shortcodes' );
		}
		return __( 'showen', 'my-text-shortcodes' );
	}

	/**
	 * Return title column.
	 *
	 * @param array $item Item.
	 * @return string
	 */
	public function column_sh_title( $item ) {
		$url_edit = add_query_arg(
			array(
				'page'         => Controller_Form::SLUG,
				'shortcode_id' => $item['sh_id'],
			)
		);

		$url_delete = add_query_arg(
			array(
				'page'         => Controller_Table::SLUG,
				'action'       => 'delete',
				'shortcode_id' => $item['sh_id'],
			)
		);

		return $item['sh_title'] . ' ' . $this->row_actions(
			array(
				'edit'   => '<a href="' . $url_edit . '">' . __( 'edit', 'my-text-shortcodes' ) . '</a>',
				'delete' => '<a href="' . $url_delete . '">' . __( 'delete', 'my-text-shortcodes' ) . '</a>',
			)
		);
	}

	/**
	 * Return bulk actions.
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		return array(
			'delete' => __( 'Delete', 'my-text-shortcodes' ),
			'show'   => __( 'Show', 'my-text-shortcodes' ),
			'hide'   => __( 'Hide', 'my-text-shortcodes' ),
		);
	}

	/**
	 * Get "where" for sql.
	 *
	 * @return string
	 */
	private function get_part_sql_where() {
		global $wpdb;

		$where = '';
		$input = filter_input( INPUT_GET, 's', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( ! empty( $input ) ) {

			$like  = '%' . $wpdb->esc_like( $input ) . '%';
			$where = $wpdb->prepare(
				'WHERE
				`sh_title` LIKE %s
					OR
				`sh_code` LIKE %s
					OR
				`sh_description` LIKE %s',
				array(
					$like,
					$like,
					$like,
				)
			);
		}

		return $where;
	}
}
