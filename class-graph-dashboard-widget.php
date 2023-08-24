<?php
/**
 * The file that defines the Graph_Dashboard_Widget plugin class.
 *
 * A class definition that includes attributes and functions used for the admin area.
 *
 * @since 1.0.0
 * @package graph-dashboard-widget
 */

/**
 * Plugin Name:       Graph Dashboard Widget
 * Description:       A WordPress Graph Widget using gutenberg library and WP REST API.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           1.0.0
 * Author:            sandeepjainlive
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       graph-dashboard-widget
 *
 * @package           graph-dashboard-widget
 */

/**
 * Class Graph_Dashboard_Widget.
 *
 * This class represents a WordPress plugin that adds a custom dashboard widget containing a graph.
 */
class Graph_Dashboard_Widget {
	/**
	 * Class constructor.
	 *
	 * This constructor sets up various actions and hooks to initialize and configure the plugin.
	 * It registers activation hook, defines constants, sets up autoloading, initializes API, enqueues scripts,
	 * and adds the Graph Widget to the WordPress dashboard.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		// Register the 'graph_widget_activate' method as a callback for the plugin activation hook.
		// This ensures that the Graph Widget's activation function is executed when the plugin is activated.
		register_activation_hook( __FILE__, array( $this, 'graph_widget_activate' ) );

		// Hook into the 'plugins_loaded' action with a priority of 0 (early) to define constants.
		add_action( 'plugins_loaded', array( $this, 'define_constants' ), 0 );

		// Register autoloader for class loading.
		spl_autoload_register( array( $this, 'graph_widget_autoloader' ) );

		// Initialize the Graph Widget API by hooking into the 'plugins_loaded' action.
		add_action( 'plugins_loaded', array( 'Graph_Widget_Api', 'init' ) );

		// Hook into the 'admin_enqueue_scripts' action to enqueue necessary scripts for the admin area.
		add_action( 'admin_enqueue_scripts', array( $this, 'graph_dashboard_scripts' ) );

		// Add the Graph Widget to the WordPress dashboard by hooking into 'wp_dashboard_setup'.
		add_action( 'wp_dashboard_setup', array( $this, 'add_graph_widget' ) );
	}

	/**
	 * Add the Graph Widget to the WordPress dashboard.
	 *
	 * This method registers and adds the Graph Widget to the WordPress dashboard using the `wp_add_dashboard_widget` function.
	 * It sets up the widget's title, content, and callback function to render the widget's content.
	 *
	 * @since 0.1.0
	 */
	public function add_graph_widget() {
		wp_add_dashboard_widget(
			'dashboard_graph_widget',
			__( 'Graph Widget', 'graph-dashboard-widget' ),
			array( $this, 'render_graph_widget' )
		);
	}

	/**
	 * Render the content of the Graph Widget on the dashboard.
	 *
	 * This method generates the HTML content for the Graph Widget that will be displayed on the WordPress dashboard.
	 */
	public function render_graph_widget() {
		// Output the HTML container for the dashboard widget.
		// The <div> element with the ID "dashboard-widget-container" will be used to hold the widget's content.
		echo '<div id="dashboard-widget-container"></div>';
	}

	/**
	 * Define constants used by the plugin.
	 */
	public function define_constants() {
		// Define the plugin directory path.
		define( 'GRAPH_WIDGET_DIR', plugin_dir_path( __FILE__ ) );

		// Define the plugin directory URL.
		define( 'GRAPH_WIDGET_URL', plugin_dir_url( __FILE__ ) );
	}
	/**
	 * Autoloader function to automatically load classes as they're used.
	 *
	 * @param string $class_name The name of the class to load.
	 */
	public function graph_widget_autoloader( $class_name ) {
		$class_path = GRAPH_WIDGET_DIR . 'includes/' . $class_name . '.php';
		if ( file_exists( $class_path ) ) {
			require_once $class_path;
		}
	}
	/**
	 * Enqueue graph dashboard script and pass data to it using wp_localize_script.
	 *
	 * This function is called when loading the admin area of WordPress (hooked to 'admin_enqueue_scripts').
	 * It checks if the current admin page is the index.php and enqueues the 'graph-dashboard-script' with the provided details.
	 *
	 * The script 'graph-dashboard-script' is loaded from the plugin's 'build/index.js' file and set to load in the footer ('true').
	 *
	 * @param string $hook The current admin page being loaded.
	 */
	public function graph_dashboard_scripts( $hook ) {
		if ( 'index.php' !== $hook ) {
			return;
		}
		$asset_file = include GRAPH_WIDGET_DIR . 'build/index.asset.php';
		wp_enqueue_script(
			'graph-dashboard-script',
			plugins_url( 'build/index.js', __FILE__ ),
			$asset_file['dependencies'],
			$asset_file['version'],
			true
		);
	}
		/**
		 * Activate function for the graph widget.
		 *
		 * This function is called when the graph widget is activated. It initializes and stores dummy data for the graph
		 * in the database as a serialized option value.
		 *
		 * The dummy data contains an array of associative arrays, where each inner array represents a data entry for the graph.
		 * Each data entry includes 'date', 'name', 'students', and 'fees' fields.
		 *
		 * Example:
		 * [
		 *    ['date' => '2023-06-12', 'name' => 'php', 'students' => 200, 'fees' => 2000],
		 *    ['date' => '2023-06-14', 'name' => 'java', 'students' => 200, 'fees' => 4000],
		 *    ...
		 * ]
		 *
		 * The serialized data is then saved as a single option value in the database with the name 'react_dummy_data'.
		 *
		 * @since 1.0.0
		 * @access public
		 */
	public function graph_widget_activate() {
		$data            = array(
			array(
				'date'     => '2023-06-12',
				'name'     => 'php',
				'students' => 200,
				'fees'     => 2000,
			),
			array(
				'date'     => '2023-06-14',
				'name'     => 'java',
				'students' => 200,
				'fees'     => 4000,
			),
			array(
				'date'     => '2023-06-15',
				'name'     => 'react',
				'students' => 500,
				'fees'     => 6000,
			),
			array(
				'date'     => '2023-06-16',
				'name'     => 'python',
				'students' => 150,
				'fees'     => 3000,
			),
			array(
				'date'     => '2023-06-17',
				'name'     => 'javascript',
				'students' => 300,
				'fees'     => 5000,
			),
			array(
				'date'     => '2023-06-18',
				'name'     => 'c++',
				'students' => 250,
				'fees'     => 4500,
			),
			array(
				'date'     => '2023-06-19',
				'name'     => 'ruby',
				'students' => 120,
				'fees'     => 2200,
			),
			array(
				'date'     => '2023-06-20',
				'name'     => 'html',
				'students' => 180,
				'fees'     => 3200,
			),
			array(
				'date'     => '2023-06-21',
				'name'     => 'css',
				'students' => 90,
				'fees'     => 1800,
			),
			array(
				'date'     => '2023-06-22',
				'name'     => 'sql',
				'students' => 220,
				'fees'     => 4200,
			),
			array(
				'date'     => '2023-06-23',
				'name'     => 'flutter',
				'students' => 350,
				'fees'     => 5500,
			),
			array(
				'date'     => '2023-06-24',
				'name'     => 'swift',
				'students' => 280,
				'fees'     => 4800,
			),
			array(
				'date'     => '2023-06-25',
				'name'     => 'kotlin',
				'students' => 190,
				'fees'     => 3400,
			),
			array(
				'date'     => '2023-06-26',
				'name'     => 'typescript',
				'students' => 270,
				'fees'     => 4600,
			),
			array(
				'date'     => '2023-06-27',
				'name'     => 'scala',
				'students' => 110,
				'fees'     => 2400,
			),
			array(
				'date'     => '2023-06-28',
				'name'     => 'go',
				'students' => 130,
				'fees'     => 2600,
			),
			array(
				'date'     => '2023-06-29',
				'name'     => 'rust',
				'students' => 80,
				'fees'     => 1600,
			),
			array(
				'date'     => '2023-08-03',
				'name'     => 'php',
				'students' => 80,
				'fees'     => 1600,
			),
			array(
				'date'     => '2023-08-04',
				'name'     => 'react',
				'students' => 420,
				'fees'     => 2800,
			),
			array(
				'date'     => '2023-08-08',
				'name'     => 'python',
				'students' => 240,
				'fees'     => 4200,
			),
			array(
				'date'     => '2023-08-06',
				'name'     => 'javascript',
				'students' => 390,
				'fees'     => 2200,
			),
			array(
				'date'     => '2023-08-08',
				'name'     => 'c++',
				'students' => 280,
				'fees'     => 4500,
			),
			array(
				'date'     => '2023-08-08',
				'name'     => 'html',
				'students' => 170,
				'fees'     => 3000,
			),
			array(
				'date'     => '2023-08-09',
				'name'     => 'css',
				'students' => 110,
				'fees'     => 2000,
			),
			array(
				'date'     => '2023-08-10',
				'name'     => 'sql',
				'students' => 320,
				'fees'     => 5200,
			),
			array(
				'date'     => '2023-08-11',
				'name'     => 'flutter',
				'students' => 420,
				'fees'     => 6000,
			),
			array(
				'date'     => '2023-08-12',
				'name'     => 'swift',
				'students' => 320,
				'fees'     => 4800,
			),
			array(
				'date'     => '2023-08-13',
				'name'     => 'kotlin',
				'students' => 210,
				'fees'     => 3600,
			),
			array(
				'date'     => '2023-08-14',
				'name'     => 'typescript',
				'students' => 310,
				'fees'     => 2200,
			),
			array(
				'date'     => '2023-08-23',
				'name'     => 'scala',
				'students' => 130,
				'fees'     => 2400,
			),
			array(
				'date'     => '2023-08-22',
				'name'     => 'go',
				'students' => 140,
				'fees'     => 2600,
			),
			array(
				'date'     => '2023-08-20',
				'name'     => 'java',
				'students' => 190,
				'fees'     => 3800,
			),
		);
		$serialized_data = serialize( $data );

		// Save the serialized data as a single option value in the database.
		update_option( 'react_dummy_data', $serialized_data );
	}
}

new Graph_Dashboard_Widget();
