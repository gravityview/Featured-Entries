<?php
/*
Plugin Name: GravityView - Featured Entries Extension
Plugin URI: https://gravityview.co
Description: Promote featured entries in views
Version: 1.0.0
Author: Katz Web Services, Inc.
Author URI: https://katz.co
Text Domain: gravity-view-featured-entries
Domain Path: /languages/
*/

add_action( 'plugins_loaded', 'gv_extension_featured_entries_load' );

/**
 * Wrapper function to make sure GravityView_Extension has loaded
 * @return void
 */
function gv_extension_featured_entries_load() {

	// We prefer to use the one bundled with GravityView, but if it doesn't exist, go here.
	if( !class_exists( 'GravityView_Extension' ) ) {
		include_once plugin_dir_path( __FILE__ ) . 'lib/class-gravityview-extension.php';
	}


	class GravityView_Featured_Entries extends GravityView_Extension {

		protected $_title = 'Featured_Entries';

		protected $_version = '1.0.0';

		protected $_min_gravityview_version = '1.1.2';

		protected $_path = __FILE__;


		/**
		 * Put all plugin hooks here
		 *
		 * @since 1.0.0
		 */
		function add_hooks() {

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_style' ) );

			add_filter( 'gravityview_default_args', array( $this, 'featured_setting_arg' ) );

			add_action( 'gravityview_admin_directory_settings', array( $this, 'featured_settings' ) );

			add_filter( 'gravityview_get_entries', array( $this, 'sort_featured_entries' ), 10, 2 );

			add_filter( 'gravityview_entry_class', array( $this, 'featured_class' ), 10, 3 );

		}


		/**
		 * Enqueue relevant stylesheets
		 *
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function enqueue_style() {

			wp_enqueue_style( 'gravityview-featured-entries', plugin_dir_url(__FILE__) . 'lib/css/featured-entries.css', array(), $this->_version );

		}


		/**
		 * Add settings to the view setting array
		 *
		 * @since  1.0.0
		 *
		 * @param  array  $args Array of other view settings
		 *
		 * @return array        Appended aray of view settings
		 */
		public function featured_setting_arg( $args ) {

			$settings = array(
				'name'              => __('Display Featured at Top', 'gravity-view'),
				'type'              => 'checkbox',
				'group'             => 'default',
				'value'             => 1,
				'tooltip'           => NULL,
				'show_in_shortcode' => true,
			);

			$args['featured_entries_enabled'] = $settings;

			return $args;

		}


		/**
		 * Render the setting in the metabox
		 *
		 * @since  1.0.0
		 *
		 * @param  array  $current_settings Array of current settings
		 *
		 * @return void
		 */
		public function featured_settings( $current_settings ) {

			GravityView_Admin_Views::render_setting_row( 'featured_entries_enabled', $current_settings );

		}


		/**
		 * Maybe add a sort filter before grabbing entries
		 *
		 * @since  1.0.0
		 *
		 * @param  array  $filters Array of current pre-built filters
		 * @param  array  $args    Array of settings for the current view
		 *
		 * @return array           Array of filters
		 */
		public function sort_featured_entries( $filters, $args ) {

			// If featured entries is enabled...
			if ( $args['featured_entries_enabled'] ) {

				$filters['sorting'] = array( 'key' => 'is_starred', 'direction' => 'DESC' );

				do_action( 'gravityview_log_debug', '[featured_entries] Updated sort filter to: ', $filters );

			}

			return $filters;

		}


		/**
		 * Maybe add featured class to entry
		 *
		 * @since  1.0.0
		 *
		 * @param  string  $class Current class value
		 * @param  array   $entry Array of entry data
		 * @param  obj     $view  Current GravityView_View object
		 *
		 * @return string         CSS classes to use for the entry markup
		 */
		public function featured_class( $class, $entry, $view ) {

			// If featured entries is enabled...
			if ( $view->atts['featured_entries_enabled'] ) {

				// If the entry is starred, add the featured-entry class
				if ( $entry['is_starred'] ) {

					$class .= ' featured-entry';

				}

			}

			return $class;

		}

	}

	new GravityView_Featured_Entries;

}
