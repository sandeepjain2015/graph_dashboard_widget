<?php
/**
 * The file that defines the Graph_Widget_Api plugin class.
 *
 * A class definition that includes Graph Widget functionality and API interactions used for the admin area.
 *
 * @since 1.0.0
 * @package Graph_Widget
 */

/**
 * Dashboard Widget React API Class.
 *
 * This class handles the Graph Widget functionality and API interactions.
 *
 * The class registers REST API routes and provides methods to handle API requests, such as retrieving filtered data.
 *
 * @since 1.0.0
 * @access public
 */
class Graph_Widget_Api {

	/**
	 * Constructor for the Graph Widget class.
	 *
	 * This constructor initializes the Graph Widget by setting up necessary hooks and actions.
	 * It registers the REST API routes and activates the widget on plugin activation.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		// Register the 'register_rest_routes' method to the 'rest_api_init' action hook.
		// This will enable registering REST API routes for the Graph Widget.
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
	}
	/**
	 * Initialize the plugin.
	 *
	 * This static method creates an instance of the current class to start the rest api functionality.
	 *
	 * @static
	 */
	public static function init() {
		$class = __CLASS__;
		new $class();
	}
	/**
	 * Register REST routes for the Graph Widget.
	 *
	 * This function is responsible for registering a REST route for the Graph Widget API.
	 * The route will be accessible at '/graph-widget/v1/data' using the HTTP GET method.
	 *
	 * When the API endpoint is accessed, the 'get_data' method of the current class instance (object) will be called.
	 * The 'get_data' method should be implemented to handle the request and return the appropriate response data.
	 *
	 * Example Usage:
	 * - Sending an HTTP GET request to '/graph-widget/v1/data' will trigger the 'get_data' callback method.
	 * - The 'get_data' method should handle the request and return the data to be sent back in the API response.
	 *
	 * @since 1.0.0
	 */
	public function register_rest_routes() {
		register_rest_route(
			'graph-widget/v1',
			'/data',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_data' ),
				'permission_callback' => function () {
					return is_user_logged_in();
				},
			)
		);
	}

	/**
	 * Get data for the Graph Widget REST API endpoint.
	 *
	 * This function is a callback for the '/graph-widget/v1/data' REST API endpoint. It retrieves data based on the specified period
	 * from the 'react_dummy_data' option stored in the database. The data is filtered based on the selected period and returned
	 * as a REST response.
	 *
	 * @param WP_REST_Request $request The REST request object containing request parameters.
	 *  Expected parameters:
	 *  - 'period': (string) The selected period for data filtering.
	 *  Possible values: '7days', '15days', '1month', or null (for all data).
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return WP_REST_Response The REST response object containing the filtered data.
	 */
	public function get_data( WP_REST_Request $request ) {
		$period = $request->get_param( 'period' );

		// Get the raw data from the wp_options table.
		$raw_data = get_option( 'react_dummy_data', array() );

		// Parse the raw data (if it exists).
		$data = ! empty( $raw_data ) ? maybe_unserialize( $raw_data ) : array();

		// Filter the data based on the selected period.
		$filtered_data = array();
		$today         = strtotime( 'today' );
		switch ( $period ) {
			case '7days':
				$start_date = strtotime( '-7 days', $today );
				break;
			case '15days':
				$start_date = strtotime( '-15 days', $today );
				break;
			case '1month':
				$start_date = strtotime( '-1 month', $today );
				break;
			default:
				$start_date = 0; // By default, retrieve all data.
		}
		foreach ( $data as $entry ) {
			$entry_date = strtotime( $entry['date'] );
			if ( $entry_date >= $start_date && $entry_date <= $today ) {
				$filtered_data[] = $entry;
			}
		}
		return rest_ensure_response( $filtered_data );
	}
}


