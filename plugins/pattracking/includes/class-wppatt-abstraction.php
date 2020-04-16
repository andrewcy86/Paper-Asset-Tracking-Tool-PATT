<?php

abstract class PATT_DB {

	/**
	 * The name of our database table
	 *
	 * @access  public
	 * @since   1.0
	 */
	public $table_name;

	/**
	 * The version of our database table
	 *
	 * @access  public
	 * @since   1.0
	 */
	public $version;

	/**
	 * The name of the primary column
	 *
	 * @access  public
	 * @since   1.0
	 */
	public $primary_key;

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	 */
	public function __construct() {}

	/**
	 * Whitelist of columns
	 *
	 * @access  public
	 * @since   1.0
	 * @return  array
	 */
	public function get_columns() {
		return array();
	}

	/**
	 * Default column values
	 *
	 * @access  public
	 * @since   1.0
	 * @return  array
	 */
	public function get_column_defaults() {
		return array();
	}

	/**
	 * Retrieve a row by the primary key
	 *
	 * @access  public
	 * @since   1.0
	 * @return  object
	 */
	public function get_row( $args, $count =  false) {
		global $wpdb;

		$order = '';
		if(isset($args['order'])){
			$order = "ORDER BY {$args['order'][0]} {$args['order'][1]}";
		}

		$where = '';
		if(isset($args['where'])){
			$where = "WHERE {$args['where'][0]} {$args['where'][1]}";
		}

		$join = '';
		if(isset($args['join'])){
			foreach($args['join'] as $join){
				$join .= "{$join['type']} {$join['table']} ON {$join['table']}.{$join['key']} {$join['compare']} {$this->table_name}.{$join['foreign_key']}";
			}
		}

		$select = isset($args['select']) ? $args['select'] : '*';

		return $wpdb->get_row( $wpdb->prepare( "SELECT {$select} FROM $this->table_name {$join} {$where} LIMIT 1;" ) );
	}

	/**
	 * Retrieve a var by the primary key
	 *
	 * @access  public
	 * @since   1.0
	 * @return  object
	 */
	public function get_value( $key, $value, $count =  false) {
		global $wpdb;
		if($count) {
			$result = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $this->table_name WHERE $key = $value LIMIT 1"));
		} else {
			$result = $wpdb->get_var( $wpdb->prepare( "SELECT * FROM $this->table_name WHERE $key = $value LIMIT 1;" ) );
		}
		return $result;
	}

	/**
	 * Retrieve a var by the primary key
	 *
	 * @access  public
	 * @since   1.0
	 * @return  object
	 */
	public function get_results( $args, $count =  false) {
		global $wpdb;

		$order = '';
		if(isset($args['order'])){
			$order = "ORDER BY {$args['order'][0]} {$args['order'][1]}";
		}

		$select = isset($args['select']) ? $args['select'] : '*';

		$where = '';
		if(isset($args['where'])){
			if(is_array($args['where'][0])){
				$i = 1;
				foreach($args['where'][0] as $where) {
					if($i == 1){
						$where = " WHERE {$args['where'][0]} {$args['where'][1]}";
					} else {
						$where = " {$args['where'][2]} {$args['where'][0]} {$args['where'][1]}";
					}
					$i++;
				}
			} else {
				$where = " WHERE {$args['where'][0]} {$args['where'][1]}";
			}
		}

		$join = '';
		if(isset($args['join'])){
			foreach($args['join'] as $join){
				$join .= "{$join['type']} {$join['table']} ON {$join['table']}.{$join['key']} {$join['compare']} {$this->table_name}.{$join['foreign_key']}";
			}
		}

		if($count) {
			$result = $wpdb->get_results($wpdb->prepare("SELECT COUNT(*) FROM $this->table_name {$join} {$where}}"));
		} else {
			$result = $wpdb->get_results( $wpdb->prepare( "SELECT {$select} FROM $this->table_name {$join} {$where}} {$order}"));
		}
		return $result;
	}

	/**
	 * Retrieve a row by a specific column / value
	 *
	 * @access  public
	 * @since   1.0
	 * @return  object
	 */
	public function get_by( $column, $row_id ) {
		global $wpdb;
		$column = esc_sql( $column );
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->table_name WHERE $column = %s LIMIT 1;", $row_id ) );
	}

	/**
	 * Retrieve a specific column's value by the primary key
	 *
	 * @access  public
	 * @since   1.0
	 * @return  string
	 */
	public function get_column( $column, $row_id ) {
		global $wpdb;
		$column = esc_sql( $column );
		return $wpdb->get_var( $wpdb->prepare( "SELECT $column FROM $this->table_name WHERE $this->primary_key = %s LIMIT 1;", $row_id ) );
	}

	/**
	 * Retrieve a specific column's value by the the specified column / value
	 *
	 * @access  public
	 * @since   1.0
	 * @return  string
	 */
	public function get_column_by( $column, $column_where, $column_value ) {
		global $wpdb;
		$column_where = esc_sql( $column_where );
		$column       = esc_sql( $column );
		return $wpdb->get_var( $wpdb->prepare( "SELECT $column FROM $this->table_name WHERE $column_where = %s LIMIT 1;", $column_value ) );
	}

	/**
	 * Insert a new row
	 *
	 * @access  public
	 * @since   1.0
	 * @return  int
	 */
	public function insert( $data, $type = '' ) {
		global $wpdb;

		// Set default values
		$data = wp_parse_args( $data, $this->get_column_defaults() );

		do_action( 'edd_pre_insert_' . $type, $data );

		// Initialize column format array
		$column_formats = $this->get_columns();

		// Force fields to lower case
		$data = array_change_key_case( $data );

		// White list columns
		$data = array_intersect_key( $data, $column_formats );

		// Reorder $column_formats to match the order of columns given in $data
		$data_keys = array_keys( $data );
		$column_formats = array_merge( array_flip( $data_keys ), $column_formats );

		$wpdb->insert( $this->table_name, $data, $column_formats );

		do_action( 'edd_post_insert_' . $type, $wpdb->insert_id, $data );

		return $wpdb->insert_id;
	}

	/**
	 * Update a row
	 *
	 * @access  public
	 * @since   1.0
	 * @return  bool
	 */
	public function update( $row_id, $data = array(), $where = '' ) {

		global $wpdb;

		// Row ID must be positive integer
		$row_id = absint( $row_id );

		if( empty( $row_id ) ) {
			return false;
		}

		if( empty( $where ) ) {
			$where = $this->primary_key;
		}

		// Initialize column format array
		$column_formats = $this->get_columns();

		// Force fields to lower case
		$data = array_change_key_case( $data );

		// White list columns
		$data = array_intersect_key( $data, $column_formats );

		// Reorder $column_formats to match the order of columns given in $data
		$data_keys = array_keys( $data );
		$column_formats = array_merge( array_flip( $data_keys ), $column_formats );

		if ( false === $wpdb->update( $this->table_name, $data, array( $where => $row_id ), $column_formats ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Delete a row identified by the primary key
	 *
	 * @access  public
	 * @since   1.0
	 * @return  bool
	 */
	public function delete( $row_id = 0 ) {

		global $wpdb;

		// Row ID must be positive integer
		$row_id = absint( $row_id );

		if( empty( $row_id ) ) {
			return false;
		}

		if ( false === $wpdb->query( $wpdb->prepare( "DELETE FROM $this->table_name WHERE $this->primary_key = %d", $row_id ) ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if the given table exists
	 *
	 * @since  1.0
	 * @param  string $table The table name
	 * @return bool  If the table name exists
	 */
	public function table_exists( $table ) {
		global $wpdb;
		$table = sanitize_text_field( $table );

		return $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE '%s'", $table ) ) === $table;
	}

}

class WP_CUST_QUERY Extends PATT_DB {
	
	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	 */
	public function __construct($table_name) {
		$this->table_name = $table_name;
	}
}