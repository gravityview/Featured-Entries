<?php
/*
Plugin Name: GravityView - Featured Entries Extension
Plugin URI: https://gravityview.co
Description: Promote Featured entries in Views
Version: 1.0.1
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

		protected $_title = 'Featured Entries';

		protected $_version = '1.0.1';

		protected $_text_domain = 'gravity-view-featured-entries';

		protected $_featured_entries = array();

		/**
		 * @todo Change to 1.1.6 pre-launch
		 */
		protected $_min_gravityview_version = '1.1.5';

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

			add_filter( 'gravityview_get_entries', array( $this, 'calculate_view_entries' ), 10, 3 );

			add_filter( 'gravityview_view_entries', array( $this, 'sort_view_entries' ), 10, 2 );

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

			wp_enqueue_style( 'gravityview-featured-entries', plugin_dir_url(__FILE__) . 'assets/css/featured-entries.css', array(), $this->_version );

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
				'name'              => __('Display Featured Entries at Top', 'gravity-view-featured-entries'),
				'type'              => 'checkbox',
				'group'             => 'default',
				'value'             => 0,
				'show_in_shortcode' => true,
			);

			$args['featured_entries_to_top'] = $settings;

			return $args;

		}

		/**
		 * Add tooltip to display in Settings metabox
		 *
		 * @since  1.0.1
		 *
		 * @param  array  $tooltips Existing GV tooltips, with `title` and `value` keys
		 *
		 * @return array           Modified tooltips
		 */
		public function tooltips( $tooltips = array() ) {

			$tooltips['gv_featured_entries_to_top'] = array(
				'title'	=> __('Display Featured Entries at Top', 'gravity-view-featured-entries'),
				'value'	=> __('Always move Featured entries to the top of search results. If not enabled, Featured entries will be shown in the default order, but will be highlighted.', 'gravity-view-featured-entries' ),
			);

			return $tooltips;
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

			GravityView_Admin_Views::render_setting_row( 'featured_entries_to_top', $current_settings );

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
		public function calculate_view_entries( $filters, $args = array(), $form_id ) {

			// If featured entries is enabled...
			if ( !empty( $args['featured_entries_to_top'] ) ) {

				// Get featured entries and store in class property
				$this->_featured_entries = $this->get_featured_entries( $args, $form_id );

				$featured_count = count( $this->_featured_entries );

				do_action( 'gravityview_log_debug', '[featured_entries] Found ' . $featured_count . ' Featured Entries', $this->_featured_entries );

				// Only get entries that aren't starred
				$filters['search_criteria']['field_filters'][] = array( 'key' => 'is_starred', 'value' => 0, 'operator' => '=' );

				// Calculate paging based on the number of featured entries returned
				$paging = $this->calculate_paging( $featured_count, $args );

				if ( ! empty( $paging ) ) {

					$filters['paging'] = $paging;

				}

				do_action( 'gravityview_log_debug', '[featured_entries] Final sort filter for non-featured entries: ', $filters );

			}

			return $filters;

		}

		protected function get_featured_entries( $args = array(), $form_id ) {

			$parameters = array();

			// Only starred entries
			$parameters['search_criteria']['field_filters'][] = array( 'key' => 'is_starred', 'value' => 1, 'operator' => '=' );
			$parameters['search_criteria']['status'] = 'active';

			// Apply the same sorting to featured entries query
			if( ! empty( $args['sort_field'] ) ) {
				$parameters['sorting'] = array( 'key' => $args['sort_field'], 'direction' => $args['sort_direction'] );
			}

			// Paging & offset
			$page_size = !empty( $args['page_size'] ) ? $args['page_size'] : apply_filters( 'gravityview_default_page_size', 25 );

			if( isset( $args['offset'] ) ) {
				$offset = $args['offset'];
			} else {
				$curr_page = empty( $_GET['pagenum'] ) ? 1 : intval( $_GET['pagenum'] );
				$offset = ( $curr_page - 1 ) * $page_size;
			}
			$parameters['paging'] = array( 'offset' => $offset, 'page_size' => $page_size );

			$featured = gravityview_get_entries( $form_id, $parameters );

			return $featured;

		}

		protected function calculate_paging( $featured_count = 0, $args = array() ) {

			// @TODO: This part doesn't work

			$page_size = ! empty( $args['page_size'] ) ? $args['page_size'] : apply_filters( 'gravityview_default_page_size', 25 );

			$page_size = $page_size - $featured_count;

			if( isset( $args['offset'] ) ) {
				$offset = $args['offset'];
			} else {
				$curr_page = empty( $_GET['pagenum'] ) ? 1 : intval( $_GET['pagenum'] );
				$offset = ( $curr_page - 1 ) * $page_size;
			}

			$paging = array( 'offset' => $offset, 'page_size' => $page_size );

			return $paging;

		}

		public function sort_view_entries( $entries, $args ) {

			if ( ! empty ( $this->_featured_entries ) ) {

				return array_merge( $this->_featured_entries, $entries );

			}

			return $entries;
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

			/**
			 * Enable or disable featured entries for this entry
			 *
			 * @param GravityView_View $view The current GravityView_View instance
			 * @param array $entry Gravity Forms entry array
			 * @return boolean Whether to enable featured entries for this entry
			 */
			if ( apply_filters( 'gravityview_featured_entries_enable', true, $view, $entry ) ) {

				// If the entry is starred, add the featured-entry class
				if ( $entry['is_starred'] ) {

					$class .= ' gv-featured-entry';

				}

			}

			return $class;

		}

	}

	new GravityView_Featured_Entries;

}
