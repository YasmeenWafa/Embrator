<?php
/*
Plugin Name: Gravity Forms
Plugin URI: http://www.gravityforms.com
Description: Easily create web forms and manage form entries within the WordPress admin.
Version: 2.0.6.5
Author: rocketgenius
Author URI: http://www.rocketgenius.com
Text Domain: gravityforms
Domain Path: /languages

------------------------------------------------------------------------
Copyright 2009-2016 Rocketgenius, Inc.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see http://www.gnu.org/licenses.
*/

//------------------------------------------------------------------------------------------------------------------
//---------- Gravity Forms License Key -----------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
//If you hardcode a Gravity Forms License Key here, it will automatically populate on activation.
$gf_license_key = '';

//-- OR ---//

//You can also add the Gravity Forms license key to your wp-config.php file to automatically populate on activation
//Add the code in the comment below to your wp-config.php to do so:
//define('GF_LICENSE_KEY','YOUR_KEY_GOES_HERE');
//------------------------------------------------------------------------------------------------------------------

//------------------------------------------------------------------------------------------------------------------
//---------- reCAPTCHA Keys -----------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
//If you hardcode your reCAPTCHA Keys here, it will automatically populate on activation.
$gf_recaptcha_private_key = '';
$gf_recaptcha_public_key = '';

//-- OR ---//

//You can  also add the reCAPTCHA keys to your wp-config.php file to automatically populate on activation
//Add the two lines of code in the comment below to your wp-config.php to do so:
//define('GF_RECAPTCHA_SITE_KEY','YOUR_SITE_KEY_GOES_HERE');
//define('GF_RECAPTCHA_SECRET_KEY','YOUR_SECRET_KEY_GOES_HERE');
//------------------------------------------------------------------------------------------------------------------

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Defines the current page.
 * @var string RG_CURRENT_PAGE The current page
 */
if ( ! defined( 'RG_CURRENT_PAGE' ) ) {
	define( 'RG_CURRENT_PAGE', basename( $_SERVER['PHP_SELF'] ) );
}

/**
 * Checks if an admin page is being viewed
 * @var boolean IS_ADMIN True if admin page.  False otherwise.
 */
if ( ! defined( 'IS_ADMIN' ) ) {
	define( 'IS_ADMIN', is_admin() );
}

/**
 * Defines the current view within Gravity Forms.
 * Defined from URL parameters.
 *
 * @var string|boolean RG_CURRENT_VIEW The view if available.  False otherwise.
 */
define( 'RG_CURRENT_VIEW', RGForms::get( 'view' ) );
/**
 * Defines the minimum version of WordPress required for Gravity Forms.
 * @var string GF_MIN_WP_VERSION Minimum version number.
 */
define( 'GF_MIN_WP_VERSION', '3.7' );
/**
 * Checks if the current WordPress version is supported
 * @see GF_MIN_WP_VERSION
 * @var boolean GF_SUPPORTED_VERSION True if supported.  False otherwise.
 */
define( 'GF_SUPPORTED_WP_VERSION', version_compare( get_bloginfo( 'version' ), GF_MIN_WP_VERSION, '>=' ) );
/**
 * Defines the minimum version of WordPress that will be officially supported
 * @var string GF_MIN_WP_VERSION_SUPPORT_TERMS The version number
 */
define( 'GF_MIN_WP_VERSION_SUPPORT_TERMS', '4.5' );

if ( ! defined( 'GRAVITY_MANAGER_URL' ) ) {
	define( 'GRAVITY_MANAGER_URL', 'https://www.gravityhelp.com/wp-content/plugins/gravitymanager' );
}

if ( ! defined( 'GRAVITY_MANAGER_PROXY_URL' ) ) {
	define( 'GRAVITY_MANAGER_PROXY_URL', 'http://proxy.gravityplugins.com' );
}

require_once( plugin_dir_path( __FILE__ ) . 'common.php' );
require_once( plugin_dir_path( __FILE__ ) . 'forms_model.php' );
require_once( plugin_dir_path( __FILE__ ) . 'widget.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/api.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/webapi/webapi.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/fields/class-gf-fields.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/class-gf-download.php' );

// GFCommon::$version is deprecated, set it to current version for backwards compatibility
GFCommon::$version = GFForms::$version;

add_action( 'init', array( 'RGForms', 'init' ) );
add_action( 'wp', array( 'RGForms', 'maybe_process_form' ), 9 );
add_action( 'admin_init', array( 'RGForms', 'maybe_process_form' ), 9 );
add_action( 'wp', array( 'RGForms', 'process_exterior_pages' ) );
add_filter( 'upgrader_pre_install', array( 'GFForms', 'validate_upgrade' ), 10, 2 );
add_filter( 'tiny_mce_before_init',  array( 'GFForms', 'modify_tiny_mce_4' ), 20 );

add_filter( 'user_has_cap', array( 'RGForms', 'user_has_cap' ), 10, 3 );

//Hooks for no-conflict functionality
if ( is_admin() && ( RGForms::is_gravity_page() || RGForms::is_gravity_ajax_action() ) ) {
	add_action( 'wp_print_scripts', array( 'RGForms', 'no_conflict_mode_script' ), 1000 );
	add_action( 'admin_print_footer_scripts', array( 'RGForms', 'no_conflict_mode_script' ), 9 );

	add_action( 'wp_print_styles', array( 'RGForms', 'no_conflict_mode_style' ), 1000 );
	add_action( 'admin_print_styles', array( 'RGForms', 'no_conflict_mode_style' ), 1 );
	add_action( 'admin_print_footer_scripts', array( 'RGForms', 'no_conflict_mode_style' ), 1 );
	add_action( 'admin_footer', array( 'RGForms', 'no_conflict_mode_style' ), 1 );
}

add_action( 'plugins_loaded', array( 'GFForms', 'loaded' ) );

register_activation_hook( __FILE__, array( 'GFForms', 'activation_hook' ) );
register_deactivation_hook( __FILE__, array( 'GFForms', 'deactivation_hook' ) );

/**
 * Class GFForms
 *
 * Handles the loading of Gravity Forms and other core functionality
 */
class GFForms {

	/**
	 * Defines this version of Gravity Forms
	 *
	 * @access public
	 * @static
	 * @var string $version The version number
	 */
	public static $version = '2.0.6.5';

	/**
	 * Runs after Gravity Forms is loaded.
	 *
	 * Initializes add-ons.
	 *
	 * @see GFAddOn
	 * @access public
	 * @static
	 */
	public static function loaded() {

		/**
		 * Fires when Gravity Forms has loaded.
		 *
		 * When developing Add-Ons, use this hook to initialize any functionality that depends on Gravity Forms functionality
		 */
		do_action( 'gform_loaded' );

		//initializing Add-Ons if necessary
		if ( class_exists( 'GFAddOn' ) ) {
			GFAddOn::init_addons();
		}
	}

	/**
	 * Determines if the 3rd party Members plugin is active
	 *
	 * @access public
	 * @static
	 *
	 * @return boolean
	 */
	public static function has_members_plugin() {
		return function_exists( 'members_get_capabilities' );
	}

	/**
	 * Initializes Gravity Forms
	 *
	 * @access public
	 * @static
	 */
	public static function init() {

		if ( ! wp_next_scheduled( 'gravityforms_cron' ) ) {
			wp_schedule_event( time(), 'daily', 'gravityforms_cron' );
		}

		add_action( 'gravityforms_cron', array( 'GFForms', 'cron' ) );

		GF_Download::maybe_process();

		//load text domains
		GFCommon::load_gf_text_domain( 'gravityforms' );

		add_filter( 'gform_logging_supported', array( 'RGForms', 'set_logging_supported' ) );
		add_action( 'admin_head', array( 'GFCommon', 'maybe_output_gf_vars' ) );
		add_action( 'admin_head', array( 'GFForms', 'load_admin_bar_styles' ) );
		add_action( 'wp_head', array( 'GFForms', 'load_admin_bar_styles' ) );

		self::register_scripts();

		//Maybe set up Gravity Forms: only on admin requests for single site installation and always for multisite
		if ( ( IS_ADMIN && false === ( defined( 'DOING_AJAX' ) && true === DOING_AJAX ) ) || is_multisite() ) {
			self::setup();
		}

		//Plugin update actions
		add_filter( 'transient_update_plugins', array( 'GFForms', 'check_update' ) );
		add_filter( 'site_transient_update_plugins', array( 'GFForms', 'check_update' ) );
		add_filter( 'auto_update_plugin', array( 'GFForms', 'maybe_auto_update' ), 10, 2 );

		if ( IS_ADMIN ) {

			global $current_user;

			// Members plugin integration. Adding Gravity Forms roles to the checkbox list
			if ( self::has_members_plugin() ) {
				add_filter( 'members_get_capabilities', array( 'RGForms', 'members_get_capabilities' ) );
			}

			if ( is_multisite() ) {
				add_filter( 'wpmu_drop_tables', array( 'GFFormsModel', 'mu_drop_tables' ) );
			}

			add_action( 'admin_enqueue_scripts', array( 'GFForms', 'enqueue_admin_scripts' ) );
			add_action( 'print_media_templates', array( 'GFForms', 'action_print_media_templates' ) );

			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				add_action( 'admin_footer', array( 'GFForms', 'deprecate_add_on_methods' ) );
			}

			//Loading Gravity Forms if user has access to any functionality
			if ( GFCommon::current_user_can_any( GFCommon::all_caps() ) ) {
				require_once( GFCommon::get_base_path() . '/export.php' );
				GFExport::maybe_export();

				//imports theme forms if configured to be automatic imported
				self::maybe_import_theme_forms();

				//creates the "Forms" left menu
				add_action( 'admin_menu', array( 'RGForms', 'create_menu' ) );

				if ( GF_SUPPORTED_WP_VERSION ) {

					add_action( 'admin_footer', array( 'RGForms', 'check_upload_folder' ) );
					add_action( 'wp_dashboard_setup', array( 'RGForms', 'dashboard_setup' ) );

					// Support modifying the admin page title for settings
					add_filter( 'admin_title', array( __class__, 'modify_admin_title' ), 10, 2 );



					require_once( GFCommon::get_base_path() . '/includes/locking/locking.php' );

					if ( self::is_gravity_page() ) {
						require_once( GFCommon::get_base_path() . '/tooltips.php' );
					} else if ( RG_CURRENT_PAGE == 'media-upload.php' ) {
						require_once( GFCommon::get_base_path() . '/entry_list.php' );
					} else if ( in_array( RG_CURRENT_PAGE, array( 'admin.php', 'admin-ajax.php' ) ) ) {

						add_action( 'wp_ajax_rg_save_form', array( 'RGForms', 'save_form' ) );
						add_action( 'wp_ajax_rg_change_input_type', array( 'RGForms', 'change_input_type' ) );
						add_action( 'wp_ajax_rg_refresh_field_preview', array( 'RGForms', 'refresh_field_preview' ) );
						add_action( 'wp_ajax_rg_add_field', array( 'RGForms', 'add_field' ) );
						add_action( 'wp_ajax_rg_duplicate_field', array( 'RGForms', 'duplicate_field' ) );
						add_action( 'wp_ajax_rg_delete_field', array( 'RGForms', 'delete_field' ) );
						add_action( 'wp_ajax_rg_delete_file', array( 'RGForms', 'delete_file' ) );
						add_action( 'wp_ajax_rg_select_export_form', array( 'RGForms', 'select_export_form' ) );
						add_action( 'wp_ajax_rg_start_export', array( 'RGForms', 'start_export' ) );
						add_action( 'wp_ajax_gf_upgrade_license', array( 'RGForms', 'upgrade_license' ) );
						add_action( 'wp_ajax_gf_delete_custom_choice', array( 'RGForms', 'delete_custom_choice' ) );
						add_action( 'wp_ajax_gf_save_custom_choice', array( 'RGForms', 'save_custom_choice' ) );
						add_action( 'wp_ajax_gf_get_post_categories', array( 'RGForms', 'get_post_category_values' ) );
						add_action( 'wp_ajax_gf_get_address_rule_values_select', array( 'GFForms', 'get_address_rule_values_select' ) );
						add_action( 'wp_ajax_gf_get_notification_post_categories', array( 'RGForms', 'get_notification_post_category_values' ) );
						//add_action( 'wp_ajax_gf_save_confirmation', array( 'RGForms', 'save_confirmation' ) );
						add_action( 'wp_ajax_gf_delete_confirmation', array( 'RGForms', 'delete_confirmation' ) );
						add_action( 'wp_ajax_gf_save_new_form', array( 'RGForms', 'save_new_form' ) );
						add_action( 'wp_ajax_gf_save_title', array( 'RGForms', 'save_form_title' ) );

						//entry list ajax operations
						add_action( 'wp_ajax_rg_update_lead_property', array( 'RGForms', 'update_lead_property' ) );
						add_action( 'wp_ajax_delete-gf_entry', array( 'RGForms', 'update_lead_status' ) );

						//form list ajax operations
						add_action( 'wp_ajax_rg_update_form_active', array( 'RGForms', 'update_form_active' ) );

						//notification list ajax operations
						add_action( 'wp_ajax_rg_update_notification_active', array( 'RGForms', 'update_notification_active' ) );

						//confirmation list ajax operations
						add_action( 'wp_ajax_rg_update_confirmation_active', array( 'RGForms', 'update_confirmation_active' ) );

						//dynamic captcha image
						add_action( 'wp_ajax_rg_captcha_image', array( 'RGForms', 'captcha_image' ) );

						//dashboard message "dismiss upgrade" link
						add_action( 'wp_ajax_rg_dismiss_upgrade', array( 'RGForms', 'dashboard_dismiss_upgrade' ) );

						// entry detail: resend notifications
						add_action( 'wp_ajax_gf_resend_notifications', array( 'RGForms', 'resend_notifications' ) );

						// Shortcode UI
						add_action( 'wp_ajax_gf_do_shortcode',  array( 'GFForms', 'handle_ajax_do_shortcode' ) );

						// Export
						add_filter( 'wp_ajax_gf_process_export', array( 'GFForms', 'ajax_process_export' ) );
						add_filter( 'wp_ajax_gf_download_export', array( 'GFForms', 'ajax_download_export' ) );

						// Dismiss message
						add_action( 'wp_ajax_gf_dismiss_message', array( 'GFForms', 'ajax_dismiss_message' ) );
					}

					add_filter( 'plugins_api', array( 'RGForms', 'get_addon_info' ), 100, 3 );
					add_action( 'after_plugin_row_gravityforms/gravityforms.php', array( 'RGForms', 'plugin_row' ) );
					add_action( 'install_plugins_pre_plugin-information', array( 'RGForms', 'display_changelog' ) );
					add_filter( 'plugin_action_links', array( 'GFForms', 'plugin_settings_link' ), 10, 2 );
				}
			}
			add_action( 'admin_init', array( 'RGForms', 'ajax_parse_request' ), 10 );

			$gf_page = self::get_page();
			if ( $gf_page == 'entry_list' && ! isset( $_GET['filter'] ) ) {
				require_once( GFCommon::get_base_path() . '/entry_list.php' );
				$default_filter = GFEntryList::get_default_filter();
				if ( $default_filter !== 'all' ) {
					$url = add_query_arg( array( 'filter' => $default_filter ) );
					$url = esc_url_raw( $url );
					wp_safe_redirect( $url );
				}
			}

			if ( $gf_page == 'entry_list' ) {
				add_filter( 'set-screen-option', array( 'GFForms', 'set_screen_options' ), 10, 3 );
				add_filter( 'screen_settings', array( 'GFForms', 'show_screen_options' ), 10, 2 );
			}

			if ( $gf_page == 'form_list' ) {
				add_filter( 'set-screen-option', array( 'GFForms', 'set_screen_options' ), 10, 3 );
			}
		} else {
			add_action( 'wp_enqueue_scripts', array( 'RGForms', 'enqueue_scripts' ), 11 );
			add_action( 'wp', array( 'RGForms', 'ajax_parse_request' ), 10 );
		}

		// Add admin bar items
		add_action( 'wp_before_admin_bar_render', array( 'GFForms', 'admin_bar' ) );

		add_shortcode( 'gravityform', array( 'RGForms', 'parse_shortcode' ) );
		add_shortcode( 'gravityforms', array( 'RGForms', 'parse_shortcode' ) );

		// ManageWP premium update filters
		add_filter( 'mwp_premium_update_notification', array( 'RGForms', 'premium_update_push' ) );
		add_filter( 'mwp_premium_perform_update', array( 'RGForms', 'premium_update' ) );

		// Push Gravity Forms to the top of the list of plugins to make sure it's loaded before any add-ons
		add_action( 'activated_plugin', array( 'GFForms', 'load_first' ) );

		// Add the "Add Form" button to the editor. The customizer doesn't run in the admin context.
		if ( GFForms::page_supports_add_form_button() ) {
			// Adding "embed form" button to the editor
			add_action( 'media_buttons', array( 'GFForms', 'add_form_button' ), 20 );
			// Adding the modal
			add_action( 'admin_print_footer_scripts', array( 'GFForms', 'add_mce_popup' ) );
		}
	}

	/**
	 * Ensures that Gravity Forms is loaded first
	 *
	 * @access public
	 * @static
	 */
	public static function load_first() {
		$plugin_path    = basename( dirname( __FILE__ ) ) . '/gravityforms.php';
		$active_plugins = get_option( 'active_plugins' );
		$key            = array_search( $plugin_path, $active_plugins );
		if ( $key > 0 ) {
			array_splice( $active_plugins, $key, 1 );
			array_unshift( $active_plugins, $plugin_path );
			update_option( 'active_plugins', $active_plugins );
		}
	}

	/**
	 * Performs Gravity Forms deactivation tasks.
	 * @access public
	 * @static
	 * @see GFCache
	 */
	public static function deactivation_hook() {
		GFCache::flush( true );
		delete_option( 'gravityforms_rewrite_rules_flushed' );
		flush_rewrite_rules();
	}

	/**
	 * Performs Gravity Forms activation tasks.
	 * @access public
	 * @static
	 */
	public static function activation_hook() {
		update_option( 'gravityforms_rewrite_rules_flushed', false );
	}

	/**
	 * Add Gravity Forms to the plugins that support logging.
	 *
	 * @access public
	 * @static
	 *
	 * @param array $plugins Existing plugins that support logging
	 *
	 * @return array $plugins Supported plugins
	 */
	public static function set_logging_supported( $plugins ) {
		$plugins['gravityforms'] = 'Gravity Forms Core';

		return $plugins;
	}

	/**
	 * Determines if a form should be processed, and passes it off to processing.
	 *
	 * @access public
	 * @static
	 * @see GFFormDisplay
	 */
	public static function maybe_process_form() {

		$form_id = isset( $_POST['gform_submit'] ) ? absint( $_POST['gform_submit'] ) : 0;
		if ( $form_id ) {
			$form_info     = RGFormsModel::get_form( $form_id );
			$is_valid_form = $form_info && $form_info->is_active;

			if ( $is_valid_form ) {
				require_once( GFCommon::get_base_path() . '/form_display.php' );
				GFFormDisplay::process_form( $form_id );
			}
		} elseif ( isset( $_POST['gform_send_resume_link'] ) ) {
			require_once( GFCommon::get_base_path() . '/form_display.php' );
			GFFormDisplay::process_send_resume_link();
		}
	}

	/**
	 * Processes pages that are not loaded directly within WordPress
	 *
	 * @access public
	 * @static
	 * @see GFCommon
	 */
	public static function process_exterior_pages() {
		if ( rgempty( 'gf_page', $_GET ) ) {
			return;
		}

		$page = rgget( 'gf_page' );

		$is_legacy_upload_page = $_SERVER['REQUEST_METHOD'] == 'POST' && $page == 'upload';

		if ( $is_legacy_upload_page && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
			_doing_it_wrong( 'gf_page=upload', 'gf_page=upload is now deprecated. Use GFCommon::get_upload_page_slug() instead', '1.9.6.13' );
		}

		$is_upload_page = $_SERVER['REQUEST_METHOD'] == 'POST' && $page == GFCommon::get_upload_page_slug();

		if ( $is_upload_page || $is_legacy_upload_page ) {
			require_once( GFCommon::get_base_path() . '/includes/upload.php' );
			exit();
		}

		//ensure users are logged in
		if ( ! is_user_logged_in() ) {
			auth_redirect();
		}

		switch ( $page ) {
			case 'preview':
				require_once( GFCommon::get_base_path() . '/preview.php' );
				break;

			case 'print-entry' :
				require_once( GFCommon::get_base_path() . '/print-entry.php' );
				break;

			case 'select_columns' :
				require_once( GFCommon::get_base_path() . '/select_columns.php' );
				break;
		}
		exit();
	}

	/**
	 * Checks for Gravity Forms updates
	 *
	 * @access public
	 * @static
	 * @see GFAutoUpgrade
	 * @see GFCommon
	 *
	 * @param object $update_plugins_option The GFAutoUpgrade object
	 *
	 * @return object The GFAutoUpgrade object
	 */
	public static function check_update( $update_plugins_option ) {
		if ( ! class_exists( 'GFCommon' ) ) {
			require_once( 'common.php' );
		}

		return GFCommon::check_update( $update_plugins_option, true );
	}

	/**
	 * Creates or updates database tables. Will only run when version changes.
	 *
	 * @access public
	 * @static
	 *
	 * @param bool $force_setup If setup should be forced.  Defaults to false.
	 */
	public static function setup( $force_setup = false ) {

		$current_version = get_option( 'rg_form_version' );

		if ( $current_version === false ) {
			if ( ! get_option( 'rg_gforms_key' ) ) {
				// Show installation wizard for all new installations as long as the key wasn't already set e.g. by the CLI.
				update_option( 'gform_pending_installation', true );
			}

			// Turn background updates on by default for all new installations.
			update_option( 'gform_enable_background_updates', true );
		}

		$has_version_changed = $current_version != GFCommon::$version;
		if ( $has_version_changed ) {
			// Making sure version has really changed. Gets around aggressive caching issue on some sites that cause setup to run multiple times.
			$current_version = self::get_wp_option( 'rg_form_version' );
			$has_version_changed = $current_version != GFCommon::$version;
		}

		if ( $has_version_changed || $force_setup ) {

			$blog_id = get_current_blog_id();

			GFCommon::log_debug( "GFForms::setup(): Blog {$blog_id} - Beginning of setup. From version " . get_option( 'rg_form_version' ) . ' to version ' . GFCommon::$version );

			// Setting up database structure
			self::setup_database();

			self::maybe_upgrade_lead_detail_table( $current_version );

			// Auto-setting and auto-validating license key based on value configured via the GF_LICENSE_KEY constant or the gf_license_key variable
			// Auto-populating reCAPTCHA keys base on constant
			self::maybe_populate_keys();

			// Auto-importing forms based on GF_IMPORT_FILE AND GF_THEME_IMPORT_FILE
			self::maybe_import_forms();

			// The format the version info changed to JSON. Make sure the old format is not cached.
			if ( version_compare( get_option( 'rg_form_version' ), '1.8.0.3', '<' ) ) {
				delete_transient( 'gform_update_info' );
			}

			update_option( 'rg_form_version', GFCommon::$version );

			update_option( 'gravityforms_rewrite_rules_flushed', false );

			GFCommon::log_debug( "GFForms::setup(): Blog {$blog_id} - End of setup." );
		}
	}

	/**
	 * Sets up the database for Gravity Forms
	 *
	 * @access public
	 * @static
	 */
	public static function setup_database() {
		global $wpdb;

		require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );

		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE $wpdb->collate";
		}

		//Fixes issue with dbDelta lower-casing table names, which cause problems on case sensitive DB servers.
		add_filter( 'dbdelta_create_queries', array( 'RGForms', 'dbdelta_fix_case' ) );

		/*
		 * Indexes have a maximum size of 767 bytes. Historically, we haven't need to be concerned about that.
		 * As of 4.2, however, WP core moved to utf8mb4, which uses 4 bytes per character. This means that an index which
		 * used to have room for floor(767/3) = 255 characters, now only has room for floor(767/4) = 191 characters.
		 */
		$max_index_length = 191;

		//------ FORM -----------------------------------------------
		$form_table_name = RGFormsModel::get_form_table_name();
		$sql             = 'CREATE TABLE ' . $form_table_name . " (
              id mediumint(8) unsigned not null auto_increment,
              title varchar(150) not null,
              date_created datetime not null,
              is_active tinyint(1) not null default 1,
              is_trash tinyint(1) not null default 0,
              PRIMARY KEY  (id)
            ) $charset_collate;";
		dbDelta( $sql );

		//droping table that was created by mistake in version 1.6.3.2
		$wpdb->query( 'DROP TABLE IF EXISTS A' . $form_table_name );

		//------ META -----------------------------------------------
		$meta_table_name = RGFormsModel::get_meta_table_name();
		$sql             = 'CREATE TABLE ' . $meta_table_name . " (
              form_id mediumint(8) unsigned not null,
              display_meta longtext,
              entries_grid_meta longtext,
              confirmations longtext,
              notifications longtext,
              PRIMARY KEY  (form_id)
            ) $charset_collate;";
		dbDelta( $sql );

		//droping outdated form_id index (if one exists)
		self::drop_index( $meta_table_name, 'form_id' );

		//------ FORM VIEW -----------------------------------------------
		$form_view_table_name = RGFormsModel::get_form_view_table_name();
		$sql                  = 'CREATE TABLE ' . $form_view_table_name . " (
              id bigint(20) unsigned not null auto_increment,
              form_id mediumint(8) unsigned not null,
              date_created datetime not null,
              ip char(15),
              count mediumint(8) unsigned not null default 1,
              PRIMARY KEY  (id),
              KEY date_created (date_created),
              KEY form_id (form_id)
            ) $charset_collate;";
		dbDelta( $sql );

		//------ LEAD -----------------------------------------------
		$lead_table_name = RGFormsModel::get_lead_table_name();
		$sql             = 'CREATE TABLE ' . $lead_table_name . " (
              id int(10) unsigned not null auto_increment,
              form_id mediumint(8) unsigned not null,
              post_id bigint(20) unsigned,
              date_created datetime not null,
              is_starred tinyint(1) not null default 0,
              is_read tinyint(1) not null default 0,
              ip varchar(39) not null,
              source_url varchar(200) not null default '',
              user_agent varchar(250) not null default '',
              currency varchar(5),
              payment_status varchar(15),
              payment_date datetime,
              payment_amount decimal(19,2),
              payment_method varchar(30),
              transaction_id varchar(50),
              is_fulfilled tinyint(1),
              created_by bigint(20) unsigned,
              transaction_type tinyint(1),
              status varchar(20) not null default 'active',
              PRIMARY KEY  (id),
              KEY form_id (form_id),
              KEY status (status)
            ) $charset_collate;";
		dbDelta( $sql );

		//------ LEAD NOTES ------------------------------------------
		$lead_notes_table_name = RGFormsModel::get_lead_notes_table_name();
		$sql                   = 'CREATE TABLE ' . $lead_notes_table_name . " (
              id int(10) unsigned not null auto_increment,
              lead_id int(10) unsigned not null,
              user_name varchar(250),
              user_id bigint(20),
              date_created datetime not null,
              value longtext,
              note_type varchar(50),
              PRIMARY KEY  (id),
              KEY lead_id (lead_id),
              KEY lead_user_key (lead_id,user_id)
            ) $charset_collate;";
		dbDelta( $sql );

		//------ LEAD DETAIL -----------------------------------------
		$lead_detail_table_name = RGFormsModel::get_lead_details_table_name();
		$sql                    = 'CREATE TABLE ' . $lead_detail_table_name . " (
              id bigint(20) unsigned not null auto_increment,
              lead_id int(10) unsigned not null,
              form_id mediumint(8) unsigned not null,
              field_number float not null,
              value longtext,
              PRIMARY KEY  (id),
              KEY form_id (form_id),
              KEY lead_id (lead_id),
              KEY lead_field_number (lead_id,field_number),
              KEY lead_field_value (value($max_index_length))
            ) $charset_collate;";
		dbDelta( $sql );

		//------ LEAD DETAIL LONG -----------------------------------
		$lead_detail_long_table_name = RGFormsModel::get_lead_details_long_table_name();

		$sql = 'CREATE TABLE ' . $lead_detail_long_table_name . " (
              lead_detail_id bigint(20) unsigned not null,
              value longtext,
              PRIMARY KEY  (lead_detail_id)
            ) $charset_collate;";
		dbDelta( $sql );

		// dropping outdated form_id index (if one exists)
		self::drop_index( $lead_detail_long_table_name, 'lead_detail_key' );

		//------ LEAD META ------------------------------------------
		$lead_meta_table_name = RGFormsModel::get_lead_meta_table_name();

		// dropping meta_key and form_id_meta_key (if they exist) to prevent duplicate keys error on upgrade
		if ( version_compare( get_option( 'rg_form_version' ), '1.9.8.12', '<' ) ) {
			self::drop_index( $lead_meta_table_name, 'meta_key' );
			self::drop_index( $lead_meta_table_name, 'form_id_meta_key' );
		}

		$sql                  = 'CREATE TABLE ' . $lead_meta_table_name . " (
              id bigint(20) unsigned not null auto_increment,
              form_id mediumint(8) unsigned not null default 0,
              lead_id bigint(20) unsigned not null,
              meta_key varchar(255),
              meta_value longtext,
              PRIMARY KEY  (id),
              KEY meta_key (meta_key($max_index_length)),
              KEY lead_id (lead_id),
              KEY form_id_meta_key (form_id,meta_key($max_index_length))
            ) $charset_collate;";
		dbDelta( $sql );

		//------ INCOMPLETE SUBMISSIONS -------------------------------
		$incomplete_submissions_table_name = RGFormsModel::get_incomplete_submissions_table_name();
		$sql                               = 'CREATE TABLE ' . $incomplete_submissions_table_name . " (
              uuid char(32) not null,
              email varchar(255),
              form_id mediumint(8) unsigned not null,
              date_created datetime not null,
              ip varchar(39) not null,
              source_url longtext not null,
              submission longtext not null,
              PRIMARY KEY  (uuid),
              KEY form_id (form_id)
            ) $charset_collate;";
		dbDelta( $sql );

		remove_filter( 'dbdelta_create_queries', array( 'RGForms', 'dbdelta_fix_case' ) );

		//fix form_id value needed to update from version 1.6.11
		self::fix_lead_meta_form_id_values();

		//fix checkbox value. needed for version 1.0 and below but won't hurt for higher versions
		self::fix_checkbox_value();

		//fix leading and trailing spaces in Form objects and entry values
		if ( version_compare( get_option( 'rg_form_version' ), '1.8.3.1', '<' ) ) {
			self::fix_leading_and_trailing_spaces();
		}

	}

	/**
	 * Checks whether the values in the longtext table should be copied over to the
	 *
	 * @param string $previous_version The currently installed version before the upgrade to this version.
	 *
	 * @return bool
	 */
	private static function can_upgrade_longtext( $previous_version ) {
		global $wpdb;

		if ( empty( $previous_version ) ) {
			return false;
		}

		// The gform_longtext_ready option was set in 1.9.x to indicate that the lead details table had been upgraded.
		// It was also set for new installations of 1.9.x.
		$is_longtext_ready = (bool) get_option( 'gform_longtext_ready' );

		if ( $is_longtext_ready ) {
			return false;
		}

		// The gform_longtext_upgraded option was added by the Upgrade Wizard Support Tool used to help debug upgrade issues.
		$upgraded = (bool) get_option( 'gform_longtext_upgraded' );

		if ( $upgraded ) {
			return false;
		}

		// Check the length of the value column in the lead detail table to make sure it's now longtext.

		$lead_detail_table_name = GFFormsModel::get_lead_details_table_name();

		$is_longtext = self::check_column( $lead_detail_table_name, 'value', 'longtext' );

		$first_entry_value = $wpdb->get_results( "SELECT value FROM $lead_detail_table_name LIMIT 1" );

		$col_type = $wpdb->get_col_info( 'type', 0 ); // Get type of column from the last wpdb query.

		if ( ! $is_longtext ) {
			// check_column() might fail - try a different approach.
			if ( $col_type == '252' || $col_type == 'blob' ) {
				$is_longtext = true;
			}
		}

		if ( ! $is_longtext ) {

			// Something's wrong with the lead detail value column. Log, add a dismissible admin message and bail.

			GFCommon::log_debug( __METHOD__ . '(): lead detail value column issue' );

			GFCommon::add_dismissible_message( esc_html__( 'There appears to be an issue with one of the Gravity Forms database tables. Please get in touch with support.', 'gravityforms' ), 'gform_long_table_upgrade', 'error', 'gform_full_access', true );

			return false;
		}

		if ( empty( $first_entry_value ) ) {
			// Make sure previous upgrade failure admin message is removed for sites with no entries.
			GFCommon::remove_dismissible_message( 'gform_long_table_upgrade' );
			return false;
		}

		$can_upgrade = false;

		if ( version_compare( $previous_version, '2.0-beta-3.2', '<' )  // No upgrades have been attempted.
		     || ( version_compare( $previous_version, '2.0.2.6', '<' ) && ! method_exists( $wpdb, 'get_col_length' ) )          // $wpdb->get_col_length() was introduced in WP 4.2.1. Attempts to upgrade will have caused a fatal error.
		     || ( version_compare( $previous_version, '2.0.2.6', '<' )  // Some upgrades prior to 2.0.2.6 failed because $wpdb->get_col_length() returned false. e.g. installations using HyperDB
		          && method_exists( $wpdb, 'get_col_length' )
		          && $wpdb->get_col_length( $wpdb->prefix . 'rg_lead_detail', 'value' ) === false )
			|| ( version_compare( $previous_version, '2.0.4.6', '<' ) // Upgrades failed where db layers returned 'blob' as longtext column type.
				&& $col_type == 'blob' )
		) {

			// Check that all IDs in the detail table are unique.

			$results = $wpdb->get_results("
SELECT id
FROM {$wpdb->prefix}rg_lead_detail
GROUP BY id
HAVING count(*) > 1;");

			if ( count( $results ) == 0 ) {

				$can_upgrade = true;

			} else {

				// IDs are not unique - log, add a dismissible admin message.

				GFCommon::log_debug( __METHOD__ . '(): lead detail IDs issue' );

				GFCommon::add_dismissible_message( esc_html__( 'There appears to be an issue with the data in the Gravity Forms database tables. Please get in touch with support.', 'gravityforms' ), 'gform_long_table_upgrade', 'error', 'gform_full_access', true );
			}
		}

		GFCommon::log_debug( __METHOD__ . '(): can_upgrade: ' . $can_upgrade );

		return $can_upgrade;
	}

	/**
	 * Check column matches criteria.
	 *
	 * Based on the WordPress check_column() function.
	 *
	 * @since 2.0.2.6
	 *
	 * @static
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @param string $table_name Table name
	 * @param string $col_name   Column name
	 * @param string $col_type   Column type
	 * @param bool   $is_null    Optional. Check is null.
	 * @param mixed  $key        Optional. Key info.
	 * @param mixed  $default    Optional. Default value.
	 * @param mixed  $extra      Optional. Extra value.
	 * @return bool True, if matches. False, if not matching.
	 */
	private static function check_column( $table_name, $col_name, $col_type, $is_null = null, $key = null, $default = null, $extra = null ) {
		global $wpdb;
		$diffs   = 0;
		$results = $wpdb->get_results( "DESC $table_name" );

		foreach ( $results as $row ) {

			if ( $row->Field == $col_name ) {

				// Got our column, check the params.
				if ( ( $col_type != null ) && ( $row->Type != $col_type ) ) {
					++ $diffs;
				}
				if ( ( $is_null != null ) && ( $row->Null != $is_null ) ) {
					++ $diffs;
				}
				if ( ( $key != null ) && ( $row->Key != $key ) ) {
					++ $diffs;
				}
				if ( ( $default != null ) && ( $row->Default != $default ) ) {
					++ $diffs;
				}
				if ( ( $extra != null ) && ( $row->Extra != $extra ) ) {
					++ $diffs;
				}
				if ( $diffs > 0 ) {
					return false;
				}

				return true;
			} // end if found our column
		}

		return false;
	}

	/**
	 * Upgrades the lead detail table.
	 *
	 * @access private
	 * @static
	 *
	 * @param string $previous_version The currently installed version before the upgrade to this version.
	 */
	private static function maybe_upgrade_lead_detail_table( $previous_version ) {
		global $wpdb;

		GFCommon::log_debug( __METHOD__ . '(): Starting' );

		if ( ! self::can_upgrade_longtext( $previous_version ) ) {
			GFCommon::log_debug( __METHOD__ . '(): Bailing' );
			return;
		}

		// Populate the details value with long table values
		$result = $wpdb->query( "
UPDATE {$wpdb->prefix}rg_lead_detail d
INNER JOIN {$wpdb->prefix}rg_lead_detail_long l ON d.id = l.lead_detail_id
SET d.value = l.value"
		);

		GFCommon::remove_dismissible_message( 'gform_long_table_upgrade' );

		GFCommon::log_debug( __METHOD__ . '(): result: ' . print_r( $result, true ) );
	}

	/**
	 * Adds index and htaccess files to the upload root for security
	 *
	 * @access public
	 * @static
	 */
	public static function add_security_files() {
		GFCommon::log_debug( __METHOD__ . '(): Start adding security files' );

		$upload_root = GFFormsModel::get_upload_root();

		if ( ! is_dir( $upload_root ) ) {
			return;
		}

		GFCommon::recursive_add_index_file( $upload_root );

		GFCommon::add_htaccess_file();
	}

	/**
	 * Self-heals suspicious files
	 *
	 * @access private
	 * @static
	 * @see GFForms::heal_wp_upload_dir
	 * @see GFForms::rename_suspicious_files_recursive
	 */
	private static function do_self_healing() {

		GFCommon::log_debug( __METHOD__ . '(): Start self healing' );

		self::heal_wp_upload_dir();

		$gf_upload_root = GFFormsModel::get_upload_root();

		if ( ! is_dir( $gf_upload_root ) || is_link( $gf_upload_root ) ) {
			return;
		}

		self::rename_suspicious_files_recursive( $gf_upload_root );
	}

	/**
	 * Renames files with a .bak extension if they have a file extension that is not allowed in the Gravity Forms uploads folder.
	 *
	 * Called from GFForms::do_self_healing
	 *
	 * @access private
	 * @static
	 *
	 * @param string $dir The path to process.
	 */
	private static function rename_suspicious_files_recursive( $dir ) {
		if ( ! is_dir( $dir ) || is_link( $dir ) ) {
			return;
		}

		if ( ! ( $dir_handle = opendir( $dir ) ) ) {
			return;
		}

		// ignores all errors
		set_error_handler( create_function( '', 'return 0;' ), E_ALL );

		while ( false !== ( $file = readdir( $dir_handle ) ) ) {
			if ( is_dir( $dir . DIRECTORY_SEPARATOR . $file ) && $file != '.' && $file != '..' ) {
				self::rename_suspicious_files_recursive( $dir . DIRECTORY_SEPARATOR . $file );
			} elseif ( GFCommon::file_name_has_disallowed_extension( $file )
			           && ! GFCommon::match_file_extension( $file, array( 'htaccess', 'bak', 'html' ) ) ) {
				$mini_hash = substr( wp_hash( $file ), 0, 6 );
				$newName   = sprintf( '%s/%s.%s.bak', $dir, $file, $mini_hash );
				rename( $dir . '/' . $file, $newName );
			}
		}

		// restores error handler
		restore_error_handler();

		closedir( $dir_handle );

		return;
	}

	/**
	 * Renames suspicious content within the wp_upload directory
	 *
	 * Called from GFForms::do_self_healing
	 *
	 * @access private
	 * @static
	 */
	private static function heal_wp_upload_dir() {
		$wp_upload_dir = wp_upload_dir();

		$wp_upload_path = $wp_upload_dir['basedir'];

		if ( ! is_dir( $wp_upload_path ) || is_link( $wp_upload_path ) ) {
			return;
		}

		// ignores all errors
		set_error_handler( create_function( '', 'return 0;' ), E_ALL );

		foreach ( glob( $wp_upload_path . DIRECTORY_SEPARATOR . '*_input_*.{php,php5}', GLOB_BRACE ) as $filename ) {
			$mini_hash = substr( wp_hash( $filename ), 0, 6 );
			$newName   = sprintf( '%s.%s.bak', $filename, $mini_hash );
			rename( $filename, $newName );
		}

		// restores error handler
		restore_error_handler();

		return;
	}

	/**
	 * Fixes leading and trailing spaces within Gravity Forms tables
	 *
	 * @access private
	 * @static
	 *
	 * @return array $results Content that was processed through the function
	 */
	private static function fix_leading_and_trailing_spaces() {

		global $wpdb;

		$meta_table_name         = GFFormsModel::get_meta_table_name();
		$lead_details_table      = GFFormsModel::get_lead_details_table_name();

		$result = $wpdb->query( "UPDATE $lead_details_table SET value = TRIM(value)" );

		$results = $wpdb->get_results( "SELECT form_id, display_meta, confirmations, notifications FROM {$meta_table_name}", ARRAY_A );

		foreach ( $results as &$result ) {
			$form_id = $result['form_id'];

			$form         = GFFormsModel::unserialize( $result['display_meta'] );
			$form_updated = false;
			$form         = GFFormsModel::trim_form_meta_values( $form, $form_updated );
			if ( $form_updated ) {
				GFFormsModel::update_form_meta( $form_id, $form );
			}

			$confirmations         = GFFormsModel::unserialize( $result['confirmations'] );
			$confirmations_updated = false;
			$confirmations         = GFFormsModel::trim_conditional_logic_values( $confirmations, $form, $confirmations_updated );
			if ( $confirmations_updated ) {
				GFFormsModel::update_form_meta( $form_id, $confirmations, 'confirmations' );
			}

			$notifications         = GFFormsModel::unserialize( $result['notifications'] );
			$notifications_updated = false;
			$notifications         = GFFormsModel::trim_conditional_logic_values( $notifications, $form, $notifications_updated );
			if ( $notifications_updated ) {
				GFFormsModel::update_form_meta( $form_id, $notifications, 'notifications' );
			}
		}

		return $results;
	}

	/**
	 * Determines if forms should be imported
	 *
	 * @access private
	 * @static
	 * @see GFExport::import_file
	 */
	private static function maybe_import_forms() {
		if ( defined( 'GF_IMPORT_FILE' ) && ! get_option( 'gf_imported_file' ) ) {

			require_once( GFCommon::get_base_path() . '/export.php' );

			GFExport::import_file( GF_IMPORT_FILE );

			update_option( 'gf_imported_file', true );
		}
	}

	/**
	 * Imports theme-specific forms, if needed
	 *
	 * @access private
	 * @static
	 * @see GFExport::import_file
	 */
	private static function maybe_import_theme_forms() {

		//Import theme specific forms if configured. Will only import forms once per theme.
		if ( defined( 'GF_THEME_IMPORT_FILE' ) ) {
			$themes = get_option( 'gf_imported_theme_file' );
			if ( ! is_array( $themes ) ) {
				$themes = array();
			}

			//if current theme has already imported it's forms, don't import again
			$theme = get_template();
			if ( ! isset( $themes[ $theme ] ) ) {

				require_once( GFCommon::get_base_path() . '/export.php' );

				//importing forms
				GFExport::import_file( get_stylesheet_directory() . '/' . GF_THEME_IMPORT_FILE );

				//adding current theme to the list of imported themes. So that forms are not imported again for it.
				$themes[ $theme ] = true;
				update_option( 'gf_imported_theme_file', $themes );
			}
		}

	}

	/**
	 * Imports Gravity Forms license keys, and reCAPTCHA keys from global variables
	 *
	 * @access private
	 * @static
	 */
	private static function maybe_populate_keys() {

		global $gf_license_key;
		$license_key = defined( 'GF_LICENSE_KEY' ) && empty( $gf_license_key ) ? GF_LICENSE_KEY : $gf_license_key;
		if ( ! empty( $license_key ) ) {
			RGFormsModel::save_key( $license_key );
			GFCommon::cache_remote_message();
			GFCommon::get_version_info( false );
		}

		//auto-setting recaptcha keys based on value configured via the constant or global variable
		global $gf_recaptcha_public_key, $gf_recaptcha_private_key;
		$private_key = defined( 'GF_RECAPTCHA_PRIVATE_KEY' ) && empty( $gf_recaptcha_private_key ) ? GF_RECAPTCHA_PRIVATE_KEY : $gf_recaptcha_private_key;
		if ( ! empty( $private_key ) ) {
			update_option( 'rg_gforms_captcha_private_key', $private_key );
		}

		$public_key = defined( 'GF_RECAPTCHA_PUBLIC_KEY' ) && empty( $gf_recaptcha_public_key ) ? GF_RECAPTCHA_PUBLIC_KEY : $gf_recaptcha_public_key;
		if ( ! empty( $public_key ) ) {
			update_option( 'rg_gforms_captcha_public_key', $public_key );
		}

	}

	/**
	 * Gets the value of an option from the wp_options table
	 *
	 * @access public
	 * @static
	 *
	 * @param string $option_name The option to find
	 *
	 * @return string The option value, if found.
	 */
	public static function get_wp_option( $option_name ) {
		global $wpdb;

		return $wpdb->get_var( $wpdb->prepare( "SELECT option_value FROM {$wpdb->prefix}options WHERE option_name=%s", $option_name ) );
	}

	/**
	 * Changes form_id values from default value "0" to the correct value.
	 *
	 * Needed when upgrading users from 1.6.11
	 *
	 * @access private
	 * @static
	 */
	private static function fix_lead_meta_form_id_values() {
		global $wpdb;

		$lead_meta_table_name = RGFormsModel::get_lead_meta_table_name();
		$lead_table_name      = RGFormsModel::get_lead_table_name();

		$sql = "UPDATE $lead_meta_table_name lm,$lead_table_name l SET lm.form_id = l.form_id
				WHERE lm.form_id=0 AND lm.lead_id = l.id;
				";
		$wpdb->get_results( $sql );

	}

	/**
	 * Fixes case for database queries.
	 *
	 * @access public
	 * @static
	 *
	 * @param array $cqueries Queries to be fixed
	 *
	 * @return array $queries Queries after processing
	 */
	public static function dbdelta_fix_case( $cqueries ) {
		foreach ( $cqueries as $table => $qry ) {
			$table_name = $table;
			if ( preg_match( "|CREATE TABLE ([^ ]*)|", $qry, $matches ) ) {
				$query_table_name = trim( $matches[1], '`' );

				//fix table names that are different just by their casing
				if ( strtolower( $query_table_name ) == $table ) {
					$table_name = $query_table_name;
				}
			}
			$queries[ $table_name ] = $qry;
		}

		return $queries;
	}

	/**
	 * Defines styles needed for "no conflict mode"
	 *
	 * @access public
	 * @static
	 * @see GFForms::no_conflict_mode
	 */
	public static function no_conflict_mode_style() {
		if ( ! get_option( 'gform_enable_noconflict' ) ) {
			return;
		}

		global $wp_styles;
		$wp_required_styles = array( 'admin-bar', 'colors', 'ie', 'wp-admin', 'editor-style' );
		$gf_required_styles = array(
			'common'                     => array(),
			'gf_edit_forms'              => array( 'thickbox', 'editor-buttons', 'wp-jquery-ui-dialog', 'media-views', 'buttons', 'wp-pointer' ),
			'gf_edit_forms_notification' => array( 'thickbox', 'editor-buttons', 'wp-jquery-ui-dialog', 'media-views', 'buttons' ),
			'gf_new_form'                => array( 'thickbox' ),
			'gf_entries'                 => array( 'thickbox' ),
			'gf_settings'                => array(),
			'gf_export'                  => array(),
			'gf_help'                    => array(),
		);

		self::no_conflict_mode( $wp_styles, $wp_required_styles, $gf_required_styles, 'styles' );
	}


	/**
	 * Defines scripts needed for "no conflict mode"
	 *
	 * @access public
	 * @static
	 * @see GFForms::no_conflict_mode
	 */
	public static function no_conflict_mode_script() {
		if ( ! get_option( 'gform_enable_noconflict' ) ) {
			return;
		}

		global $wp_scripts;

		$wp_required_scripts = array( 'admin-bar', 'common', 'jquery-color', 'utils', 'svg-painter' );
		$gf_required_scripts = array(
			'common'                     => array( 'gform_tooltip_init', 'sack' ),
			'gf_edit_forms'              => array( 'backbone', 'editor', 'gform_floatmenu', 'gform_forms', 'gform_form_admin', 'gform_form_editor', 'gform_gravityforms', 'gform_json', 'gform_menu', 'gform_placeholder', 'jquery-ui-autocomplete', 'jquery-ui-core', 'jquery-ui-datepicker', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-tabs', 'json2', 'media-editor', 'media-models', 'media-upload', 'media-views', 'plupload', 'plupload-flash', 'plupload-html4', 'plupload-html5', 'quicktags', 'rg_currency', 'thickbox', 'word-count', 'wp-plupload', 'wpdialogs-popup', 'wplink', 'wp-pointer' ),
			'gf_edit_forms_notification' => array( 'editor', 'word-count', 'quicktags', 'wpdialogs-popup', 'media-upload', 'wplink', 'backbone', 'jquery-ui-sortable', 'json2', 'media-editor', 'media-models', 'media-views', 'plupload', 'plupload-flash', 'plupload-html4', 'plupload-html5', 'plupload-silverlight', 'wp-plupload', 'gform_placeholder', 'gform_json', 'jquery-ui-autocomplete' ),
			'gf_new_form'                => array( 'thickbox', 'jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-tabs', 'rg_currency', 'gform_gravityforms', 'gform_json', 'gform_form_admin' ),
			'gf_entries'                 => array( 'thickbox', 'gform_gravityforms', 'wp-lists', 'gform_json', 'gform_field_filter', 'plupload-all', 'postbox' ),
			'gf_settings'                => array(),
			'gf_export'                  => array( 'gform_form_admin', 'jquery-ui-datepicker', 'gform_field_filter' ),
			'gf_help'                    => array(),
		);

		self::no_conflict_mode( $wp_scripts, $wp_required_scripts, $gf_required_scripts, 'scripts' );
	}

	/**
	 * Runs "no conflict mode"
	 *
	 * Called from GFForms::no_conflict_mode_style
	 * Called from GFForms::no_conflict_mode_script
	 *
	 * @access private
	 * @static
	 *
	 * @param object $wp_objects          WP_Scripts object
	 * @param array  $wp_required_objects Scripts required by WordPress Core
	 * @param array  $gf_required_objects Scripts required by Gravity Forms
	 * @param string $type                Determines if scripts or styles are being run through the function
	 */
	private static function no_conflict_mode( &$wp_objects, $wp_required_objects, $gf_required_objects, $type = 'scripts' ) {

		$current_page = trim( strtolower( rgget( 'page' ) ) );
		if ( empty( $current_page ) ) {
			$current_page = trim( strtolower( rgget( 'gf_page' ) ) );
		}
		if ( empty( $current_page ) ) {
			$current_page = RG_CURRENT_PAGE;
		}

		$view         = rgempty( 'view', $_GET ) ? 'default' : rgget( 'view' );
		$page_objects = isset( $gf_required_objects[ $current_page . '_' . $view ] ) ? $gf_required_objects[ $current_page . '_' . $view ] : rgar( $gf_required_objects, $current_page );

		//disable no-conflict if $page_objects is false
		if ( $page_objects === false ) {
			return;
		}

		if ( ! is_array( $page_objects ) ) {
			$page_objects = array();
		}

		//merging wp scripts with gravity forms scripts
		$required_objects = array_merge( $wp_required_objects, $gf_required_objects['common'], $page_objects );

		//allowing addons or other products to change the list of no conflict scripts
		$required_objects = apply_filters( "gform_noconflict_{$type}", $required_objects );

		$queue = array();
		foreach ( $wp_objects->queue as $object ) {
			if ( in_array( $object, $required_objects ) ) {
				$queue[] = $object;
			}
		}
		$wp_objects->queue = $queue;

		$required_objects = self::add_script_dependencies( $wp_objects->registered, $required_objects );

		//unregistering scripts
		$registered = array();
		foreach ( $wp_objects->registered as $script_name => $script_registration ) {
			if ( in_array( $script_name, $required_objects ) ) {
				$registered[ $script_name ] = $script_registration;
			}
		}
		$wp_objects->registered = $registered;
	}

	/**
	 * Adds script dependencies needed
	 *
	 * Called from GFForms::no_conflict_mode
	 *
	 * @access private
	 * @static
	 *
	 * @param array $registered Registered scripts
	 * @param array $scripts    Required scripts
	 *
	 * @return array $scripts Scripts including dependencies
	 */
	private static function add_script_dependencies( $registered, $scripts ) {

		//gets all dependent scripts linked to the $scripts array passed
		do {
			$dependents = array();
			foreach ( $scripts as $script ) {
				$deps = isset( $registered[ $script ] ) && is_array( $registered[ $script ]->deps ) ? $registered[ $script ]->deps : array();
				foreach ( $deps as $dep ) {
					if ( ! in_array( $dep, $scripts ) && ! in_array( $dep, $dependents ) ) {
						$dependents[] = $dep;
					}
				}
			}
			$scripts = array_merge( $scripts, $dependents );
		} while ( ! empty( $dependents ) );

		return $scripts;
	}

	/**
	 * Integration with ManageWP
	 *
	 * @access public
	 * @static
	 *
	 * @param array $premium_update ManageWP update array
	 *
	 * @return array $premium_update
	 */
	public static function premium_update_push( $premium_update ) {

		if ( ! function_exists( 'get_plugin_data' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		$update = GFCommon::get_version_info();
		if ( rgar( $update, 'is_valid_key' ) == true && version_compare( GFCommon::$version, $update['version'], '<' ) ) {
			$gforms                = get_plugin_data( __FILE__ );
			$gforms['type']        = 'plugin';
			$gforms['slug']        = 'gravityforms/gravityforms.php';
			$gforms['new_version'] = ! rgempty( 'version', $update ) ? $update['version'] : false;
			$premium_update[]      = $gforms;
		}

		return $premium_update;
	}

	/**
	 * Integration with ManageWP
	 *
	 * @access public
	 * @static
	 *
	 * @param array $premium_update ManageWP update array
	 *
	 * @return array $premium_update
	 */
	public static function premium_update( $premium_update ) {

		if ( ! function_exists( 'get_plugin_data' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		$update = GFCommon::get_version_info();
		if ( rgar( $update, 'is_valid_key' ) == true && version_compare( GFCommon::$version, $update['version'], '<' ) ) {
			$gforms         = get_plugin_data( __FILE__ );
			$gforms['slug'] = 'gravityforms/gravityforms.php'; // If not set by default, always pass theme template
			$gforms['type'] = 'plugin';
			$gforms['url']  = ! rgempty( 'url', $update ) ? $update['url'] : false; // OR provide your own callback function for managing the update

			array_push( $premium_update, $gforms );
		}

		return $premium_update;
	}

	/**
	 * Drops a table index
	 *
	 * @access public
	 * @static
	 *
	 * @param string $table The table that the index will be dropped from
	 * @param string $index The index to be dropped
	 */
	public static function drop_index( $table, $index ) {
		global $wpdb;

		if ( ! GFFormsModel::is_valid_table( $table ) || ! GFFormsModel::is_valid_index( $index ) ) {
			return;
		}

		// check first if the table exists to prevent errors on first install
		$has_table = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) );
		if ( $has_table ) {

			$has_index = $wpdb->get_var( $wpdb->prepare( "SHOW INDEX FROM {$table} WHERE Key_name=%s", $index ) );

			if ( $has_index ) {
				$wpdb->query( "DROP INDEX {$index} ON {$table}" );
			}
		}
	}

	/**
	 * Validates that Gravity Forms is doing the database upgrade, and has permissions to do so.
	 *
	 * @access public
	 * @static
	 *
	 * @param null   $do_upgrade Not used
	 * @param string $hook_extra The plugin triggering the upgrade
	 *
	 * @return bool|WP_Error True if successful.  Otherwise WP_Error object.
	 */
	public static function validate_upgrade( $do_upgrade, $hook_extra ) {

		if ( rgar( $hook_extra, 'plugin' ) == 'gravityforms/gravityforms.php' && ! GFForms::has_database_permission( $error ) ) {
			return new WP_Error( 'no_db_permission', $error );
		}

		return true;
	}

	/**
	 * Checks if Gravity Forms has permissions to make changes to the database
	 *
	 * Called from GFForms::validate_upgrade
	 *
	 * @access private
	 * @static
	 *
	 * @param string $error Error, if there was a problem somewhere.
	 *
	 * @return bool $has_permissions True if permissions are fine.  False otherwise.
	 */
	private static function has_database_permission( &$error ) {
		global $wpdb;

		$wpdb->hide_errors();

		$has_permission = true;

		$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}rg_test ( col1 int PRIMARY KEY )";
		$wpdb->query( $sql );
		$error = 'Current database user does not have necessary permissions to create tables. Gravity Forms requires that the database user has CREATE and ALTER permissions. If you need assistance in changing database user permissions, contact your hosting provider.';
		if ( ! empty( $wpdb->last_error ) ) {
			$has_permission = false;
		}

		if ( $has_permission ) {
			$sql = "ALTER TABLE {$wpdb->prefix}rg_test ADD COLUMN a" . uniqid() . ' int';
			$wpdb->query( $sql );
			$error = 'Current database user does not have necessary permissions to modify (ALTER) tables. Gravity Forms requires that the database user has CREATE and ALTER permissions. If you need assistance in changing database user permissions, contact your hosting provider.';
			if ( ! empty( $wpdb->last_error ) ) {
				$has_permission = false;
			}

			$sql = "DROP TABLE {$wpdb->prefix}rg_test";
			$wpdb->query( $sql );
		}

		$wpdb->show_errors();

		return $has_permission;
	}

	//Changes checkbox entry values from "!" to the current choice text. Needed when upgrading users from 1.0
	/**
	 * Fixes checkbox values in the database
	 *
	 * @access private
	 * @static
	 */
	private static function fix_checkbox_value() {
		global $wpdb;

		$table_name = RGFormsModel::get_lead_details_table_name();

		$sql     = "select * from $table_name where value= '!'";
		$results = $wpdb->get_results( $sql );
		foreach ( $results as $result ) {
			$form  = RGFormsModel::get_form_meta( $result->form_id );
			$field = RGFormsModel::get_field( $form, $result->field_number );
			if ( $field->type == 'checkbox' ) {
				$input = GFCommon::get_input( $field, $result->field_number );
				$wpdb->update( $table_name, array( 'value' => $input['label'] ), array( 'id' => $result->id ) );
			}
		}
	}

	/**
	 * Determines if a user has a particular capability
	 *
	 * Called from the user_has_cap filter.
	 *
	 * @access public
	 * @static
	 *
	 * @param array $all_caps All capabilities
	 * @param array $cap      Required capability.  Stored in the [0] key
	 * @param array $args     Not used
	 *
	 * @return array $all_caps All capabilities
	 */
	public static function user_has_cap( $all_caps, $cap, $args ) {
		$gf_caps    = GFCommon::all_caps();
		$capability = rgar( $cap, 0 );
		if ( $capability != 'gform_full_access' ) {
			return $all_caps;
		}

		if ( ! self::has_members_plugin() ) {
			//give full access to administrators if the members plugin is not installed
			if ( current_user_can( 'administrator' ) || is_super_admin() ) {
				$all_caps['gform_full_access'] = true;
			}
		} else if ( current_user_can( 'administrator' ) || is_super_admin() ) {

			//checking if user has any GF permission.
			$has_gf_cap = false;
			foreach ( $gf_caps as $gf_cap ) {
				if ( rgar( $all_caps, $gf_cap ) ) {
					$has_gf_cap = true;
				}
			}

			if ( ! $has_gf_cap ) {
				//give full access to administrators if none of the GF permissions are active by the Members plugin
				$all_caps['gform_full_access'] = true;
			}
		}

		return $all_caps;
	}

	/**
	 * Provides the Members plugin with Gravity Forms lists of capabilities
	 *
	 * Called from the members_get_capabilities filter
	 *
	 * @access public
	 * @static
	 *
	 * @param array $caps All capabilities
	 *
	 * @return array
	 */
	public static function members_get_capabilities( $caps ) {
		return array_merge( $caps, GFCommon::all_caps() );
	}

	/**
	 * Tests if the upload folder is writable and displays an error message if not.
	 *
	 * @access public
	 * @static
	 */
	public static function check_upload_folder() {
		//check if upload folder is writable
		$folder = RGFormsModel::get_upload_root();
		if ( empty( $folder ) ) {
			echo "<div class='error'>Upload folder is not writable. Export and file upload features will not be functional.</div>";
		}
	}

	/**
	 * Checks if a Gravity Forms AJAX action is being performed.
	 *
	 * @access public
	 * @static
	 *
	 * @return bool
	 */
	public static function is_gravity_ajax_action() {
		//Gravity Forms AJAX requests
		$current_action  = self::post( 'action' );
		$gf_ajax_actions = array(
			'rg_save_form', 'rg_change_input_type', 'rg_refresh_field_preview', 'rg_add_field', 'rg_duplicate_field',
			'rg_delete_field', 'rg_select_export_form', 'rg_start_export', 'gf_upgrade_license',
			'gf_delete_custom_choice', 'gf_save_custom_choice', 'gf_get_notification_post_categories',
			'rg_update_lead_property', 'delete-gf_entry', 'rg_update_form_active', 'rg_update_notification_active',
			'rg_update_confirmation_active', 'gf_resend_notifications', 'rg_dismiss_upgrade', 'gf_save_confirmation',
			'gf_process_export', 'gf_download_export', 'gf_dismiss_message'
		);

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && in_array( $current_action, $gf_ajax_actions ) ) {
			return true;
		}

		//not a gravity forms ajax request.
		return false;
	}

	//Returns true if the current page is one of Gravity Forms pages. Returns false if not
	/**
	 * Determines if the current page is part of Gravity Forms.
	 *
	 * @access public
	 * @static
	 *
	 * @return bool
	 */
	public static function is_gravity_page() {

		//Gravity Forms pages
		$current_page = trim( strtolower( self::get( 'page' ) ) );
		$gf_pages     = array( 'gf_edit_forms', 'gf_new_form', 'gf_entries', 'gf_settings', 'gf_export', 'gf_help' );

		return in_array( $current_page, $gf_pages );
	}

	/**
	 * Creates the "Forms" left nav.
	 *
	 * WordPress generates the page hook suffix and screen ID by passing the translated menu title through sanitize_title().
	 * Screen options and metabox preferences are stored using the screen ID therefore:
	 * 1. The page suffix or screen ID should never be hard-coded. Use get_current_screen()->id.
	 * 2. The page suffix and screen ID must never change.
	 *  e.g. When an update for Gravity Forms is available an icon will be added to the the menu title.
	 *  The HTML for the icon will be stripped entirely by sanitize_title() because the number 1 is encoded.
	 *
	 * @access public
	 * @static
	 *
	 */
	public static function create_menu() {

		$has_full_access = current_user_can( 'gform_full_access' );
		$min_cap         = GFCommon::current_user_can_which( GFCommon::all_caps() );
		if ( empty( $min_cap ) ) {
			$min_cap = 'gform_full_access';
		}

		$addon_menus = array();
		$addon_menus = apply_filters( 'gform_addon_navigation', $addon_menus );

		$parent_menu = self::get_parent_menu( $addon_menus );

		// Add a top-level left nav.
		$update_icon = GFCommon::has_update() && current_user_can( 'install_plugins' ) ? "<span title='" . esc_attr( __( 'Update Available', 'gravityforms' ) ) . "' class='update-plugins count-1'><span class='update-count'>&#49;</span></span>" : '';

		$admin_icon = self::get_admin_icon_b64( GFForms::is_gravity_page() ? '#fff' : false );

		$forms_hook_suffix = add_menu_page( __( 'Forms', 'gravityforms' ), __( 'Forms', 'gravityforms' ) . $update_icon, $has_full_access ? 'gform_full_access' : $min_cap, $parent_menu['name'], $parent_menu['callback'], $admin_icon, apply_filters( 'gform_menu_position', '16.9' ) );

		add_action( 'load-' . $forms_hook_suffix, array( 'GFForms', 'load_screen_options' ) );

		// Adding submenu pages
		add_submenu_page( $parent_menu['name'], __( 'Forms', 'gravityforms' ), __( 'Forms', 'gravityforms' ), $has_full_access ? 'gform_full_access' : 'gravityforms_edit_forms', 'gf_edit_forms', array( 'RGForms', 'forms' ) );

		add_submenu_page( $parent_menu['name'], __( 'New Form', 'gravityforms' ), __( 'New Form', 'gravityforms' ), $has_full_access ? 'gform_full_access' : 'gravityforms_create_form', 'gf_new_form', array( 'RGForms', 'new_form' ) );

		$entries_hook_suffix = add_submenu_page( $parent_menu['name'], __( 'Entries', 'gravityforms' ), __( 'Entries', 'gravityforms' ), $has_full_access ? 'gform_full_access' : 'gravityforms_view_entries', 'gf_entries', array( 'RGForms', 'all_leads_page' ) );

		add_action( 'load-' . $entries_hook_suffix, array( 'GFForms', 'load_screen_options' ) );

		if ( is_array( $addon_menus ) ) {
			foreach ( $addon_menus as $addon_menu ) {
				add_submenu_page( esc_html( $parent_menu['name'] ), esc_html( $addon_menu['label'] ), esc_html( $addon_menu['label'] ), $has_full_access ? 'gform_full_access' : $addon_menu['permission'], esc_html( $addon_menu['name'] ), $addon_menu['callback'] );
			}
		}

		add_submenu_page( $parent_menu['name'], __( 'Settings', 'gravityforms' ), __( 'Settings', 'gravityforms' ), $has_full_access ? 'gform_full_access' : 'gravityforms_view_settings', 'gf_settings', array( 'RGForms', 'settings_page' ) );

		add_submenu_page( $parent_menu['name'], __( 'Import/Export', 'gravityforms' ), __( 'Import/Export', 'gravityforms' ), $has_full_access ? 'gform_full_access' : 'gravityforms_export_entries', 'gf_export', array( 'RGForms', 'export_page' ) );

		if ( current_user_can( 'install_plugins' ) ) {
			add_submenu_page( $parent_menu['name'], __( 'Updates', 'gravityforms' ), __( 'Updates', 'gravityforms' ), $has_full_access ? 'gform_full_access' : 'gravityforms_view_updates', 'gf_update', array( 'RGForms', 'update_page' ) );
			add_submenu_page( $parent_menu['name'], __( 'Add-Ons', 'gravityforms' ), __( 'Add-Ons', 'gravityforms' ), $has_full_access ? 'gform_full_access' : 'gravityforms_view_addons', 'gf_addons', array( 'RGForms', 'addons_page' ) );
		}

		add_submenu_page( $parent_menu['name'], __( 'Help', 'gravityforms' ), __( 'Help', 'gravityforms' ), $has_full_access ? 'gform_full_access' : $min_cap, 'gf_help', array( 'RGForms', 'help_page' ) );

	}

	/**
	 * Gets the admin icon for the Forms menu item
	 *
	 * @access public
	 * @static
	 *
	 * @param bool|string $color The hex color if changing the color of the icon.  Defualts to false.
	 *
	 * @return string Base64 encoded icon string.
	 */
	public static function get_admin_icon_b64( $color = false ) {

		// replace the hex color (default was #999999) to %s; it will be replaced by the passed $color

		if ( $color ) {
			$svg_xml = '<?xml version="1.0" encoding="utf-8"?>' . self::get_admin_icon_svg( $color );
			$icon = sprintf( 'data:image/svg+xml;base64,%s', base64_encode( sprintf( $svg_xml, $color ) ) );
		} else {
			$svg_b64 = 'PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB2aWV3Qm94PSItMTUgNzcgNTgxIDY0MCIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAtMTUgNzcgNTgxIDY0MCIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+PGcgaWQ9IkxheWVyXzIiPjxwYXRoIGZpbGw9IiM5OTk5OTkiIGQ9Ik00ODkuNSwyMjdMNDg5LjUsMjI3TDMxNS45LDEyNi44Yy0yMi4xLTEyLjgtNTguNC0xMi44LTgwLjUsMEw2MS44LDIyN2MtMjIuMSwxMi44LTQwLjMsNDQuMi00MC4zLDY5Ljd2MjAwLjVjMCwyNS42LDE4LjEsNTYuOSw0MC4zLDY5LjdsMTczLjYsMTAwLjJjMjIuMSwxMi44LDU4LjQsMTIuOCw4MC41LDBMNDg5LjUsNTY3YzIyLjItMTIuOCw0MC4zLTQ0LjIsNDAuMy02OS43VjI5Ni44QzUyOS44LDI3MS4yLDUxMS43LDIzOS44LDQ4OS41LDIyN3ogTTQwMSwzMDAuNHY1OS4zSDI0MXYtNTkuM0g0MDF6IE0xNjMuMyw0OTAuOWMtMTYuNCwwLTI5LjYtMTMuMy0yOS42LTI5LjZjMC0xNi40LDEzLjMtMjkuNiwyOS42LTI5LjZzMjkuNiwxMy4zLDI5LjYsMjkuNkMxOTIuOSw0NzcuNiwxNzkuNiw0OTAuOSwxNjMuMyw0OTAuOXogTTE2My4zLDM1OS43Yy0xNi40LDAtMjkuNi0xMy4zLTI5LjYtMjkuNnMxMy4zLTI5LjYsMjkuNi0yOS42czI5LjYsMTMuMywyOS42LDI5LjZTMTc5LjYsMzU5LjcsMTYzLjMsMzU5Ljd6IE0yNDEsNDkwLjl2LTU5LjNoMTYwdjU5LjNIMjQxeiIvPjwvZz48L3N2Zz4=';

			$icon = 'data:image/svg+xml;base64,' . $svg_b64;
		}

		return $icon;
	}

	/**
	 * Returns the admin icon in SVG format.
	 *
	 * @param string $color The hex color if changing the color of the icon.  Defualts to #999999.
	 *
	 * @return string
	 */
	public static function get_admin_icon_svg( $color = '#999999' ) {
		$svg = '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="-15 77 581 640" enable-background="new -15 77 581 640" xml:space="preserve"><g id="Layer_2"><path fill="%s" d="M489.5,227L489.5,227L315.9,126.8c-22.1-12.8-58.4-12.8-80.5,0L61.8,227c-22.1,12.8-40.3,44.2-40.3,69.7v200.5c0,25.6,18.1,56.9,40.3,69.7l173.6,100.2c22.1,12.8,58.4,12.8,80.5,0L489.5,567c22.2-12.8,40.3-44.2,40.3-69.7V296.8C529.8,271.2,511.7,239.8,489.5,227z M401,300.4v59.3H241v-59.3H401z M163.3,490.9c-16.4,0-29.6-13.3-29.6-29.6c0-16.4,13.3-29.6,29.6-29.6s29.6,13.3,29.6,29.6C192.9,477.6,179.6,490.9,163.3,490.9z M163.3,359.7c-16.4,0-29.6-13.3-29.6-29.6s13.3-29.6,29.6-29.6s29.6,13.3,29.6,29.6S179.6,359.7,163.3,359.7z M241,490.9v-59.3h160v59.3H241z"/></g></svg>';
		return sprintf( $svg, $color );
	}

	/**
	 * Returns the parent menu item.
	 *
	 * It needs to be the same as the first sub-menu (otherwise WP will duplicate the main menu as a sub-menu)
	 *
	 * @access public
	 * @static
	 *
	 * @param array $addon_menus Contains the add-on menu items.
	 *
	 * @return array $parent The parent menu array.
	 */
	public static function get_parent_menu( $addon_menus ) {

		if ( GFCommon::current_user_can_any( 'gravityforms_edit_forms' ) ) {
			$parent = array( 'name' => 'gf_edit_forms', 'callback' => array( 'RGForms', 'forms' ) );
		} else if ( GFCommon::current_user_can_any( 'gravityforms_create_form' ) ) {
			$parent = array( 'name' => 'gf_new_form', 'callback' => array( 'RGForms', 'new_form' ) );
		} else if ( GFCommon::current_user_can_any( 'gravityforms_view_entries' ) ) {
			$parent = array( 'name' => 'gf_entries', 'callback' => array( 'RGForms', 'all_leads_page' ) );
		} else if ( is_array( $addon_menus ) && sizeof( $addon_menus ) > 0 ) {
			foreach ( $addon_menus as $addon_menu ) {
				if ( GFCommon::current_user_can_any( $addon_menu['permission'] ) ) {
					$parent = array( 'name' => $addon_menu['name'], 'callback' => $addon_menu['callback'] );
					break;
				}
			}
		} else if ( GFCommon::current_user_can_any( 'gravityforms_view_settings' ) ) {
			$parent = array( 'name' => 'gf_settings', 'callback' => array( 'RGForms', 'settings_page' ) );
		} else if ( GFCommon::current_user_can_any( 'gravityforms_export_entries' ) ) {
			$parent = array( 'name' => 'gf_export', 'callback' => array( 'RGForms', 'export_page' ) );
		} else if ( GFCommon::current_user_can_any( 'gravityforms_view_updates' ) ) {
			$parent = array( 'name' => 'gf_update', 'callback' => array( 'RGForms', 'update_page' ) );
		} else if ( GFCommon::current_user_can_any( 'gravityforms_view_addons' ) ) {
			$parent = array( 'name' => 'gf_addons', 'callback' => array( 'RGForms', 'addons_page' ) );
		} else if ( GFCommon::current_user_can_any( GFCommon::all_caps() ) ) {
			$parent = array( 'name' => 'gf_help', 'callback' => array( 'RGForms', 'help_page' ) );
		}

		return $parent;
	}

	/**
	 * Modifies the page title when on Gravity Forms settings pages.
	 *
	 * @access public
	 * @static
	 *
	 * @param string $admin_title The current admin title
	 * @param string $title       Not used.
	 *
	 * @return string The modified admin title.
	 */
	public static function modify_admin_title( $admin_title, $title ) {

		$subview = rgget( 'subview' );
		$form_id = rgget( 'id' );

		if ( ! $form_id || rgget( 'page' ) != 'gf_edit_forms' || rgget( 'view' ) != 'settings' ) {
			return $admin_title;
		}

		require_once( GFCommon::get_base_path() . '/form_settings.php' );

		$setting_tabs = GFFormSettings::get_tabs( $form_id );
		$page_title   = '';

		foreach ( $setting_tabs as $tab ) {
			if ( $tab['name'] == $subview ) {
				$page_title = $tab['label'];
			}
		}

		if ( $page_title ) {
			$admin_title = sprintf( __( '%1$s &lsaquo; %2$s &#8212; WordPress', 'gravityforms' ), esc_html( $page_title ), $admin_title );
		}

		return $admin_title;
	}

	/**
	 * Parses Gravity Forms shortcode attributes and displays the form
	 *
	 * @access public
	 * @static
	 *
	 * @param array $attributes The shortcode attributes
	 * @param null  $content    Defines the content of the shortcode.  Defaults to null.
	 *
	 * @return mixed|string|void
	 */
	public static function parse_shortcode( $attributes, $content = null ) {

		extract(
			shortcode_atts(
				array(
					'title'        => true,
					'description'  => true,
					'id'           => 0,
					'name'         => '',
					'field_values' => '',
					'ajax'         => false,
					'tabindex'     => 1,
					'action'       => 'form',
				), $attributes, 'gravityforms'
			)
		);

		$shortcode_string = '';

		switch ( $action ) {
			case 'conditional':
				$shortcode_string = GFCommon::conditional_shortcode( $attributes, $content );
				break;

			default:

				// don't retrieve form markup for custom actions
				if( $action && $action != 'form' ) {
					break;
				}

				//displaying form
				$title        = strtolower( $title ) == 'false' ? false : true;
				$description  = strtolower( $description ) == 'false' ? false : true;
				$field_values = htmlspecialchars_decode( $field_values );
				$field_values = str_replace( '&#038;', '&', $field_values );

				$ajax = strtolower( $ajax ) == 'true' ? true : false;

				//using name to lookup form if id is not specified
				if ( empty( $id ) ) {
					$id = $name;
				}

				parse_str( $field_values, $field_value_array ); //parsing query string like string for field values and placing them into an associative array
				$field_value_array = stripslashes_deep( $field_value_array );

				$shortcode_string = self::get_form( $id, $title, $description, false, $field_value_array, $ajax, $tabindex );

		}

		/**
		 * Filters the shortcode.
		 *
		 * @param string $shortcode_string The full shortcode string.
		 * @param array  $attributes       The attributes within the shortcode.
		 * @param string $content          The content of the shortcode, if available.
		 */
		$shortcode_string = apply_filters( "gform_shortcode_{$action}", $shortcode_string, $attributes, $content );

		return $shortcode_string;
	}

	/**
	 * Includes the add-on framework
	 *
	 * @access public
	 * @static
	 */
	public static function include_addon_framework() {
		require_once( GFCommon::get_base_path() . '/includes/addon/class-gf-addon.php' );
	}

	/**
	 * Includes the feed class for the add-on framework
	 *
	 * @access public
	 * @static
	 */
	public static function include_feed_addon_framework() {
		require_once( GFCommon::get_base_path() . '/includes/addon/class-gf-feed-addon.php' );
	}

	/**
	 * Includes the payment class for te add-on framework
	 *
	 * @access public
	 * @static
	 */
	public static function include_payment_addon_framework() {
		require_once( GFCommon::get_base_path() . '/includes/addon/class-gf-payment-addon.php' );
	}

	/**
	 * Includes the Gravity API
	 *
	 * @access public
	 * @static
	 */
	public static function include_gravity_api(){
		require_once( GFCommon::get_base_path() . '/includes/class-gravity-api.php' );
	}

	//-------------------------------------------------
	//----------- AJAX --------------------------------

	/**
	 * Parses AJAX requests
	 *
	 * @access public
	 * @static
	 *
	 * @param null $wp Not used
	 */
	public static function ajax_parse_request( $wp ) {

		if ( isset( $_POST['gform_ajax'] ) ) {
			parse_str( $_POST['gform_ajax'] );
			$tabindex = isset( $tabindex ) ? absint( $tabindex ) : 1;
			require_once( GFCommon::get_base_path() . '/form_display.php' );

			$form_id             = absint( $form_id );
			$display_title       = (bool) $title;
			$display_description = (bool) $description;

			parse_str( $_POST['gform_field_values'], $field_values );

			$result = GFFormDisplay::get_form( $form_id, $display_title, $display_description, false, $field_values, true, $tabindex );
			die( $result );
		}
	}

	//------------------------------------------------------
	//------------- PAGE/POST EDIT PAGE ---------------------

	/**
	 * Determines if the "Add Form" button should be added to the page.
	 *
	 * @access public
	 * @static
	 *
	 * @return boolean $display_add_form_button True if the page is supported.  False otherwise.
	 */
	public static function page_supports_add_form_button() {
		$is_post_edit_page = in_array( RG_CURRENT_PAGE, array( 'post.php', 'page.php', 'page-new.php', 'post-new.php', 'customize.php' ) );

		$display_add_form_button = apply_filters( 'gform_display_add_form_button', $is_post_edit_page );

		return $display_add_form_button;
	}

	/**
	 * Creates the "Add Form" button.
	 *
	 * @access public
	 * @static
	 */
	public static function add_form_button() {

		$is_add_form_page = self::page_supports_add_form_button();
		if ( ! $is_add_form_page ) {
			return;
		}

		// display button matching new UI
		echo '<style>.gform_media_icon{
                background-position: center center;
			    background-repeat: no-repeat;
			    background-size: 16px auto;
			    float: left;
			    height: 16px;
			    margin: 0;
			    text-align: center;
			    width: 16px;
				padding-top:10px;
                }
                .gform_media_icon:before{
                color: #999;
			    padding: 7px 0;
			    transition: all 0.1s ease-in-out 0s;
                }
                .wp-core-ui a.gform_media_link{
                 padding-left: 0.4em;
                }
             </style>
              <a href="#" class="button gform_media_link" id="add_gform" title="' . esc_attr__( 'Add Gravity Form', 'gravityforms' ) . '"><div class="gform_media_icon svg" style="background-image: url(\'' . self::get_admin_icon_b64()  . '\')"><br /></div><div style="padding-left: 20px;">' . esc_html__( 'Add Form', 'gravityforms' ) . '</div></a>';
	}

	/**
	 * Displays the popup to insert a form to a post/page
	 *
	 * @access public
	 * @static
	 */
	public static function add_mce_popup() {
		?>
		<script>
			function InsertForm() {
				var form_id = jQuery("#add_form_id").val();
				if (form_id == "") {
					alert(<?php echo json_encode( __( 'Please select a form', 'gravityforms' ) ); ?>);
					return;
				}

				var form_name = jQuery("#add_form_id option[value='" + form_id + "']").text().replace(/[\[\]]/g, '');
				var display_title = jQuery("#display_title").is(":checked");
				var display_description = jQuery("#display_description").is(":checked");
				var ajax = jQuery("#gform_ajax").is(":checked");
				var title_qs = !display_title ? " title=\"false\"" : "";
				var description_qs = !display_description ? " description=\"false\"" : "";
				var ajax_qs = ajax ? " ajax=\"true\"" : "";

				window.send_to_editor("[gravityform id=\"" + form_id + "\" name=\"" + form_name + "\"" + title_qs + description_qs + ajax_qs + "]");
			}
		</script>

		<div id="select_gravity_form" style="display:none;">

			<div id="gform-shortcode-ui-wrap" class="wrap <?php echo GFCommon::get_browser_class() ?>">

				<div id="gform-shortcode-ui-container"></div>

			</div>


		</div>

	<?php
	}


	//------------------------------------------------------
	//------------- PLUGINS PAGE ---------------------------
	//------------------------------------------------------

	/**
	 * Creates the Settings link within the Plugins page
	 *
	 * @param array $links Links associated with the plugin
	 * @param string $file The plugin filename
	 *
	 * @return array $links Links associated with the plugin, after the Settings link is added.
	 */
	public static function plugin_settings_link( $links, $file ) {
		if ( $file != plugin_basename( __FILE__ ) ) {
			return $links;
		}

		array_unshift( $links, '<a href="' . esc_url( admin_url( 'admin.php' ) ) . '?page=gf_settings">' . esc_html__( 'Settings', 'gravityforms' ) . '</a>' );

		return $links;
	}

	//Displays message on Plugin's page
	/**
	 * Displays messages for the Gravity Forms listing on the Plugins page
	 *
	 * Displays if the key is invalid or an update is available.
	 *
	 * @access public
	 * @static
	 *
	 * @param string $plugin_name The plugin filename.  Immediately overwritten.
	 */
	public static function plugin_row( $plugin_name ) {
		$key          = GFCommon::get_key();
		$version_info = GFCommon::get_version_info();

		if ( ! rgar( $version_info, 'is_valid_key' ) ) {

			$plugin_name = 'gravityforms/gravityforms.php';

			$new_version = version_compare( GFCommon::$version, $version_info['version'], '<' ) ? esc_html__( 'There is a new version of Gravity Forms available.', 'gravityforms' ) . ' <a class="thickbox" title="Gravity Forms" href="plugin-install.php?tab=plugin-information&plugin=gravityforms&TB_iframe=true&width=640&height=808">' . sprintf( esc_html__( 'View version %s Details', 'gravityforms' ), $version_info['version'] ) . '</a>. ' : '';

			echo '</tr><tr class="plugin-update-tr"><td colspan="3" class="plugin-update"><div class="update-message">' . $new_version . sprintf( esc_html__( '%sRegister%s your copy of Gravity Forms to receive access to automatic upgrades and support. Need a license key? %sPurchase one now%s.', 'gravityforms' ), '<a href="' . admin_url() . 'admin.php?page=gf_settings">', '</a>', '<a href="http://www.gravityforms.com">', '</a>' ) . '</div></td>';
		}
	}

	/**
	 * Displays current version details on Plugins page
	 *
	 * @access public
	 * @static
	 */
	public static function display_changelog() {
		if ( $_REQUEST['plugin'] != 'gravityforms' ) {
			return;
		}

		$page_text = self::get_changelog();
		echo $page_text;

		exit;
	}

	/**
	 * Gets the changelog for the newest version
	 *
	 * @access public
	 * @static
	 *
	 * @return string $page_text The changelog. Error message if there's an issue.
	 */
	public static function get_changelog() {
		$key                = GFCommon::get_key();
		$body               = "key=$key";
		$options            = array( 'method' => 'POST', 'timeout' => 3, 'body' => $body );
		$options['headers'] = array(
			'Content-Type'   => 'application/x-www-form-urlencoded; charset=' . get_option( 'blog_charset' ),
			'Content-Length' => strlen( $body ),
			'User-Agent'     => 'WordPress/' . get_bloginfo( 'version' ),
			'Referer'        => get_bloginfo( 'url' )
		);

		$raw_response = GFCommon::post_to_manager( 'changelog.php', GFCommon::get_remote_request_params(), $options );

		if ( is_wp_error( $raw_response ) || 200 != $raw_response['response']['code'] ) {
			$page_text = sprintf( esc_html__( 'Oops!! Something went wrong. %sPlease try again or %scontact us%s.', 'gravityforms' ), '<br/>', "<a href='http://www.gravityforms.com'>", '</a>' );
		} else {
			$page_text = $raw_response['body'];
			if ( substr( $page_text, 0, 10 ) != '<!--GFM-->' ) {
				$page_text = '';
			}
			else {
				$page_text = '<div style="background-color:white">' . $page_text . '<div>';
			}
		}

		return stripslashes( $page_text );
	}

	//------------------------------------------------------
	//-------------- DASHBOARD PAGE -------------------------

	/**
	 * Registers the dashboard widget
	 *
	 * @access public
	 * @static
	 */
	public static function dashboard_setup() {
		/**
		 * Changes the dashboard widget title
		 *
		 * @param string $dashboard_title The dashboard widget title
		 */
		$dashboard_title = apply_filters( 'gform_dashboard_title', __( 'Forms', 'gravityforms' ) );
		wp_add_dashboard_widget( 'rg_forms_dashboard', $dashboard_title, array( 'RGForms', 'dashboard' ) );
	}

	/**
	 * Displays the dashboard UI
	 *
	 * @access public
	 * @static
	 */
	public static function dashboard() {
		$forms = RGFormsModel::get_form_summary();

		if ( sizeof( $forms ) > 0 ) {
			?>
			<table class="widefat gf_dashboard_view" cellspacing="0" style="border:0px;">
				<thead>
				<tr>
					<td class="gf_dashboard_form_title_header" style="text-align:left; padding:8px 18px!important; font-weight:bold;">
						<i><?php esc_html_e( 'Title', 'gravityforms' ) ?></i></td>
					<td class="gf_dashboard_entries_unread_header" style="text-align:center; padding:8px 18px!important; font-weight:bold;">
						<i><?php esc_html_e( 'Unread', 'gravityforms' ) ?></i></td>
					<td class="gf_dashboard_entries_total_header" style="text-align:center; padding:8px 18px!important; font-weight:bold;">
						<i><?php esc_html_e( 'Total', 'gravityforms' ) ?></i></td>
				</tr>
				</thead>

				<tbody class="list:user user-list">
				<?php
				foreach ( $forms as $form ) {
					$date_display = GFCommon::format_date( $form['last_lead_date'] );
					if ( ! empty( $form['total_leads'] ) ) {
						?>
						<tr class='author-self status-inherit' valign="top">
							<td class="gf_dashboard_form_title column-title" style="padding:8px 18px;">
								<a <?php echo $form['unread_count'] > 0 ? "class='form_title_unread' style='font-weight:bold;'" : '' ?> href="admin.php?page=gf_entries&view=entries&id=<?php echo absint( $form['id'] ) ?>" title="<?php echo esc_attr( $form['title'] ) ?> : <?php esc_attr_e( 'View All Entries', 'gravityforms' ) ?>"><?php echo esc_html( $form['title'] ) ?></a>
							</td>
							<td class="gf_dashboard_entries_unread column-date" style="padding:8px 18px; text-align:center;">
								<a <?php echo $form['unread_count'] > 0 ? "class='form_entries_unread' style='font-weight:bold;'" : '' ?> href="admin.php?page=gf_entries&view=entries&filter=unread&id=<?php echo absint( $form['id'] ) ?>" title="<?php printf( esc_attr__( 'Last Entry: %s', 'gravityforms' ), $date_display ); ?>"><?php echo absint( $form['unread_count'] ) ?></a>
							</td>
							<td class="gf_dashboard_entries_total column-date" style="padding:8px 18px; text-align:center;">
								<a href="admin.php?page=gf_entries&view=entries&id=<?php echo absint( $form['id'] ) ?>" title="<?php esc_attr_e( 'View All Entries', 'gravityforms' ) ?>"><?php echo absint( $form['total_leads'] ) ?></a>
							</td>
						</tr>
					<?php
					}
				}
				?>
				</tbody>
			</table>

			<?php if ( GFCommon::current_user_can_any( 'gravityforms_edit_forms' ) ) : ?>
				<p class="textright">
				<a class="gf_dashboard_button button" href="admin.php?page=gf_edit_forms"><?php esc_html_e( 'View All Forms', 'gravityforms' ) ?></a>
			<?php endif; ?>
			</p>
		<?php
		} else {
			?>
			<div class="gf_dashboard_noforms_notice">
				<?php echo sprintf( esc_html__( "You don't have any forms. Let's go %s create one %s!", 'gravityforms' ), '<a href="admin.php?page=gf_new_form">', '</a>' ); ?>
			</div>
		<?php
		}

		if ( GFCommon::current_user_can_any( 'gravityforms_view_updates' ) && ( ! function_exists( 'is_multisite' ) || ! is_multisite() || is_super_admin() ) ) {
			//displaying update message if there is an update and user has permission
			self::dashboard_update_message();
		}
	}

	/**
	 * Displays the update message on the dashboard
	 *
	 * @access public
	 * @static
	 */
	public static function dashboard_update_message() {
		$version_info = GFCommon::get_version_info();

		//don't display a message if use has dismissed the message for this version
		$ary_dismissed = get_option( 'gf_dismissed_upgrades' );

		$is_dismissed = ! empty( $ary_dismissed ) && in_array( $version_info['version'], $ary_dismissed );

		if ( $is_dismissed ) {
			return;
		}

		if ( version_compare( GFCommon::$version, $version_info['version'], '<' ) ) {
			$message = sprintf( esc_html__( 'There is an update available for Gravity Forms. %sView Details%s', 'gravityforms' ), "<a href='admin.php?page=gf_update'>", '</a>' );
			?>
			<div class='updated' style='padding:15px; position:relative;' id='gf_dashboard_message'><?php echo $message ?>
				<a href="javascript:void(0);" onclick="GFDismissUpgrade();" onkeypress="GFDismissUpgrade();" style='float:right;'><?php esc_html_e( 'Dismiss', 'gravityforms' ) ?></a>
			</div>
			<script type="text/javascript">
				function GFDismissUpgrade() {
					jQuery("#gf_dashboard_message").slideUp();
					jQuery.post(ajaxurl, {action: 'rg_dismiss_upgrade', version: <?php echo json_encode( $version_info['version'] ); ?>});
				}
			</script>
		<?php
		}
	}

	/**
	 * Dismisses the dashboard update message
	 *
	 * @access public
	 * @static
	 */
	public static function dashboard_dismiss_upgrade() {
		$ary = get_option( 'gf_dismissed_upgrades' );
		if ( ! is_array( $ary ) ) {
			$ary = array();
		}

		$ary[] = $_POST['version'];
		update_option( 'gf_dismissed_upgrades', $ary );
	}


	//------------------------------------------------------
	//--------------- ALL OTHER PAGES ----------------------

	/**
	 * Registers Gravity Forms scripts
	 *
	 * If SCRIPT_DEBUG constant is set, uses the un-minified version.
	 *
	 * @access public
	 * @static
	 */
	public static function register_scripts() {

		$base_url = GFCommon::get_base_url();
		$version  = GFForms::$version;

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';

		wp_register_script( 'gform_chosen', $base_url . '/js/chosen.jquery.min.js', array( 'jquery' ), $version );
		wp_register_script( 'gform_conditional_logic', $base_url . "/js/conditional_logic{$min}.js", array( 'jquery', 'gform_gravityforms' ), $version );
		wp_register_script( 'gform_datepicker_init', $base_url . "/js/datepicker{$min}.js", array( 'jquery', 'jquery-ui-datepicker', 'gform_gravityforms' ), $version, true );
		wp_register_script( 'gform_floatmenu', $base_url . "/js/floatmenu_init{$min}.js", array( 'jquery' ), $version );
		wp_register_script( 'gform_form_admin', $base_url . "/js/form_admin{$min}.js", array( 'jquery', 'jquery-ui-autocomplete', 'gform_placeholder' ), $version );
		wp_register_script( 'gform_form_editor', $base_url . "/js/form_editor{$min}.js", array( 'jquery', 'gform_json', 'gform_placeholder' ), $version );
		wp_register_script( 'gform_forms', $base_url . "/js/forms{$min}.js", array( 'jquery' ), $version );
		wp_register_script( 'gform_gravityforms', $base_url . "/js/gravityforms{$min}.js", array( 'jquery', 'gform_json' ), $version );
		wp_register_script( 'gform_json', $base_url . '/js/jquery.json.js', array( 'jquery' ), $version, true );
		wp_register_script( 'gform_masked_input', $base_url . '/js/jquery.maskedinput.min.js', array( 'jquery' ), $version );
		wp_register_script( 'gform_menu', $base_url . "/js/menu{$min}.js", array( 'jquery' ), $version );
		wp_register_script( 'gform_placeholder', $base_url . '/js/placeholders.jquery.min.js', array( 'jquery' ), $version );
		wp_register_script( 'gform_tooltip_init', $base_url . "/js/tooltip_init{$min}.js", array( 'jquery-ui-tooltip' ), $version );
		wp_register_script( 'gform_textarea_counter', $base_url . '/js/jquery.textareaCounter.plugin.js', array( 'jquery' ), $version );
		wp_register_script( 'gform_field_filter', $base_url . "/js/gf_field_filter{$min}.js", array( 'jquery', 'gform_datepicker_init' ), $version );
		wp_register_script( 'gform_shortcode_ui', $base_url . "/js/shortcode-ui{$min}.js", array( 'jquery', 'wp-backbone' ), $version, true );

		wp_register_style( 'gform_shortcode_ui', $base_url . "/css/shortcode-ui{$min}.css", array(), $version );

	}

	/**
	 * Enqueues registered Gravity Forms scripts
	 *
	 * @access public
	 * @static
	 *
	 * @param null $hook Not used
	 */
	public static function enqueue_admin_scripts( $hook ) {

		$scripts = array();
		$page    = self::get_page();

		switch ( $page ) {
			case 'new_form' :
			case 'form_list':
				$scripts = array(
					'gform_gravityforms',
					'gform_json',
					'gform_form_admin',
					'thickbox',
					'sack',
				);
				break;

			case 'form_settings':
				$scripts = array(
					'gform_gravityforms',
					'gform_forms',
					'gform_json',
					'gform_form_admin',
					'gform_placeholder',
					'jquery-ui-datepicker',
					'gform_masked_input',
					'jquery-ui-sortable',
					'sack',
				);
				break;

			case 'form_editor':
				$thickbox = 'thickbox';
				$scripts  = array(
					$thickbox,
					'jquery-ui-core',
					'jquery-ui-sortable',
					'jquery-ui-draggable',
					'jquery-ui-droppable',
					'jquery-ui-tabs',
					'gform_gravityforms',
					'gform_forms',
					'gform_json',
					'gform_form_admin',
					'gform_floatmenu',
					'gform_menu',
					'gform_placeholder',
					'jquery-ui-autocomplete',
					'sack',
				);

				if ( wp_is_mobile() ) {
					$scripts[] = 'jquery-touch-punch';
				}

				break;

			case 'entry_detail':
				$scripts = array(
					'gform_json',
					'sack',
					'postbox',
				);
				break;

			case 'entry_detail_edit':
				$scripts = array(
					'gform_gravityforms',
					'plupload-all',
					'sack',
					'postbox',
				);
				break;

			case 'entry_list':
				$scripts = array(
					'wp-lists',
					'wp-ajax-response',
					'thickbox',
					'gform_json',
					'thickbox',
					'gform_field_filter',
					'sack',
				);
				break;

			case 'notification_list':
				$scripts = array(
					'gform_forms',
					'gform_json',
					'gform_form_admin',
					'sack',
				);
				break;

			case 'notification_new':
			case 'notification_edit':
				$scripts = array(
					'jquery-ui-autocomplete',
					'gform_gravityforms',
					'gform_placeholder',
					'gform_form_admin',
					'gform_forms',
					'gform_json',
					'sack',
				);
				break;

			case 'confirmation':
				$scripts = array(
					'gform_form_admin',
					'gform_forms',
					'gform_gravityforms',
					'gform_placeholder',
					'gform_json',
					'wp-pointer',
					'sack',
				);
				break;

			case 'addons':
				$scripts = array(
					'thickbox',
					'sack',
				);
				break;

			case 'export_entry':
				$scripts = array(
					'jquery-ui-datepicker',
					'gform_form_admin',
					'gform_field_filter',
					'sack',
				);
				break;
			case 'updates' :
				$scripts = array(
					'thickbox',
					'sack',
				);

		}

		if ( self::page_supports_add_form_button() ) {
			require_once( GFCommon::get_base_path() . '/tooltips.php' );
			wp_enqueue_script( 'gform_shortcode_ui' );
			wp_enqueue_style( 'gform_shortcode_ui' );
			wp_localize_script( 'gform_shortcode_ui', 'gfShortcodeUIData', array(
				'shortcodes' => self::get_shortcodes(),
				'previewNonce' => wp_create_nonce( 'gf-shortcode-ui-preview' ),

				/**
				 * Allows the enabling (false) or disabling (true) of a shortcode preview of a form
				 *
				 * @param bool $preview_disabled Defaults to true.  False to enable.
				 */
				'previewDisabled' => apply_filters( 'gform_shortcode_preview_disabled', true ),
				'strings' => array(
					'pleaseSelectAForm' => esc_html__( 'Please select a form.', 'gravityforms' ),
					'errorLoadingPreview' => esc_html__( 'Failed to load the preview for this form.', 'gravityforms' ),
				)
			) );
		}



		if ( empty( $scripts ) ) {
			return;
		}

		foreach ( $scripts as $script ) {
			wp_enqueue_script( $script );
		}


		GFCommon::localize_gform_gravityforms_multifile();

	}

	/**
	 * Gets current page name
	 *
	 * @access public
	 * @static
	 *
	 * @return bool|string Page name or false
	 *   Page names:
	 *
	 *   new_form
	 *   form_list
	 *   form_editor
	 *   form_settings
	 *   confirmation
	 *   notification_list
	 *   notification_new
	 *   notification_edit
	 *   entry_list
	 *   entry_detail
	 *   entry_detail_edit
	 *   settings
	 *   addons
	 *   export_entry
	 *   export_form
	 *   import_form
	 *   updates
	 */
	public static function get_page() {

		if ( rgget( 'page' ) == 'gf_new_form' ) {
			return 'new_form';
		}

		if ( rgget( 'page' ) == 'gf_edit_forms' && ! rgget( 'id' ) ) {
			return 'form_list';
		}

		if ( rgget( 'page' ) == 'gf_edit_forms' && ! rgget( 'view' ) ) {
			return 'form_editor';
		}

		if ( rgget( 'page' ) == 'gf_edit_forms' && rgget( 'view' ) == 'settings' && ( ! rgget( 'subview' ) || rgget( 'subview' ) == 'settings' ) ) {
			return 'form_settings';
		}

		if ( rgget( 'page' ) == 'gf_edit_forms' && rgget( 'view' ) == 'settings' && rgget( 'subview' ) == 'confirmation' ) {
			return 'confirmation';
		}

		if ( rgget( 'page' ) == 'gf_edit_forms' && rgget( 'view' ) == 'settings' && rgget( 'subview' ) == 'notification' && rgget( 'nid' ) ) {
			return 'notification_edit';
		}

		if ( rgget( 'page' ) == 'gf_edit_forms' && rgget( 'view' ) == 'settings' && rgget( 'subview' ) == 'notification' && isset( $_GET['nid'] ) ) {
			return 'notification_edit';
		}

		if ( rgget( 'page' ) == 'gf_edit_forms' && rgget( 'view' ) == 'settings' && rgget( 'subview' ) == 'notification' ) {
			return 'notification_list';
		}

		if ( rgget( 'page' ) == 'gf_entries' && ( ! rgget( 'view' ) || rgget( 'view' ) == 'entries' ) ) {
			return 'entry_list';
		}

		if ( rgget( 'page' ) == 'gf_entries' && rgget( 'view' ) == 'entry' && isset( $_POST['screen_mode'] ) && $_POST['screen_mode'] == 'edit' ) {
			return 'entry_detail_edit';
		}

		if ( rgget( 'page' ) == 'gf_entries' && rgget( 'view' ) == 'entry' ){
			return 'entry_detail';
		}

		if ( rgget( 'page' ) == 'gf_settings' ) {
			return 'settings';
		}

		if ( rgget( 'page' ) == 'gf_addons' ) {
			return 'addons';
		}

		if ( rgget( 'page' ) == 'gf_export' && ( rgget( 'view' ) == 'export_entry' || ! isset( $_GET['view'] ) ) ) {
			return 'export_entry';
		}

		if ( rgget( 'page' ) == 'gf_export' && rgget( 'view' ) == 'export_form' ) {
			return 'export_form';
		}

		if ( rgget( 'page' ) == 'gf_export' && rgget( 'view' ) == 'import_form' ) {
			return 'import_form';
		}

		if ( rgget( 'page' ) == 'gf_update' ) {
			return 'updates';
		}

		return false;
	}

	/**
	 * Gets the form
	 *
	 * Passes data off to GFFormDisplay::get_form
	 *
	 * @access public
	 * @static
	 * @see GFFormDisplay::get_form
	 */
	public static function get_form( $form_id, $display_title = true, $display_description = true, $force_display = false, $field_values = null, $ajax = false, $tabindex = 1 ) {
		require_once( GFCommon::get_base_path() . '/form_display.php' );

		return GFFormDisplay::get_form( $form_id, $display_title, $display_description, $force_display, $field_values, $ajax, $tabindex );
	}

	/**
	 * Runs when the Forms menu item is clicked
	 *
	 * Checks to see if the installation wizard should be displayed instead.
	 *
	 * @access public
	 * @static
	 */
	public static function new_form() {

		if ( self::maybe_display_installation_wizard() ) {
			return;
		};

		self::form_list_page();
	}

	/**
	 * Enqueues scripts
	 *
	 * Passes everything off to GFFormDisplay::enqueue_scripts
	 *
	 * @access public
	 * @static
	 * @see GFFormDisplay::enqueue_scripts
	 */
	public static function enqueue_scripts() {
		require_once( GFCommon::get_base_path() . '/form_display.php' );
		GFFormDisplay::enqueue_scripts();
	}

	/**
	 * Prints form scripts
	 *
	 * Passes everything off to GFFormDisplay::print_form_scripts
	 *
	 * @access public
	 * @static
	 * @see GFFormDisplay::print_form_scripts
	 */
	public static function print_form_scripts( $form, $ajax ) {
		require_once( GFCommon::get_base_path() . '/form_display.php' );
		GFFormDisplay::print_form_scripts( $form, $ajax );
	}

	/**
	 * Displays the Forms page
	 *
	 * Passes everything off to GFFormDetail::forms_page
	 *
	 * @access public
	 * @static
	 * @see GFFormDetail::forms_page
	 */
	public static function forms_page( $form_id ) {
		require_once( GFCommon::get_base_path() . '/form_detail.php' );
		GFFormDetail::forms_page( $form_id );
	}

	/**
	 * Runs the Gravity Forms settings page.
	 *
	 * Checks to see if the installation wizard should be displayed.
	 *
	 * @access public
	 * @static
	 * @see GFSettings::settings_page
	 */
	public static function settings_page() {

		if ( self::maybe_display_installation_wizard() ) {
			return;
		};

		require_once( GFCommon::get_base_path() . '/settings.php' );
		GFSettings::settings_page();
	}

	/**
	 * Adds pages to the Gravity Forms Settings page
	 *
	 * Passes eveything off to GFSettings::add_settings_page
	 *
	 * @access public
	 * @static
	 * @see GFSettings::add_settings_page
	 */
	public static function add_settings_page( $name, $handle = '', $icon_path = '' ) {
		require_once( GFCommon::get_base_path() . '/settings.php' );
		GFSettings::add_settings_page( $name, $handle, $icon_path );
	}

	/**
	 * Displays the help page
	 *
	 * Passes everything off to GFHelp::help_page
	 *
	 * @access public
	 * @static
	 * @see GFHelp::help_page
	 */
	public static function help_page() {
		require_once( GFCommon::get_base_path() . '/help.php' );
		GFHelp::help_page();
	}

	/**
	 * Displays the Gravity Forms Export page
	 *
	 * Checks if the installation wizard should be run before passing off to GFExport::export_page
	 *
	 * @access public
	 * @static
	 * @see GFExport::export_page
	 */
	public static function export_page() {

		if ( self::maybe_display_installation_wizard() ) {
			return;
		};

		require_once( GFCommon::get_base_path() . '/export.php' );
		GFExport::export_page();
	}

	/**
	 * Target for the wp_ajax_gf_process_export ajax action requested from the export entries page.
	 *
	 * Passes everything off to GFExport::ajax_process_export
	 *
	 * @since 2.0.0
	 * @access public
	 * @static
	 * @see GFExport::ajax_process_export
	 */
	public static function ajax_process_export() {

		require_once( GFCommon::get_base_path() . '/export.php' );
		GFExport::ajax_process_export();
	}

	/**
	 * Target for the wp_ajax_gf_download_export ajax action requested from the export entries page.
	 *
	 * Passes eveything off to GFExport::ajax_download_export
	 *
	 * @since 2.0.0
	 * @access public
	 * @static
	 * @see GFExport::ajax_download_export
	 */
	public static function ajax_download_export() {

		require_once( GFCommon::get_base_path() . '/export.php' );
		GFExport::ajax_download_export();
	}

	/**
	 * Target for the wp_ajax_gf_dismiss_message ajax action requested from the Gravity Forms admin pages.
	 *
	 * Gets the key and passes off to GFCommon::dismiss_message
	 *
	 * @since 2.0.0
	 * @access public
	 * @static
	 * @see GFCommon::dismiss_message
	 */
	public static function ajax_dismiss_message() {

		check_admin_referer( 'gf_dismissible_nonce', 'nonce' );

		$key = rgget( 'message_key' );
		$key = sanitize_key( $key );


		GFCommon::dismiss_message( $key );
	}

	/**
	 * Runs the update page
	 *
	 * Passes off to GFUpdate::update_page
	 *
	 * @access public
	 * @static
	 * @see GFUpdate::update_page
	 */
	public static function update_page() {
		require_once( GFCommon::get_base_path() . '/update.php' );
		GFUpdate::update_page();
	}

	/**
	 * Runs the add-ons page
	 *
	 * If the display wizard needs to be displayed, do that instead.
	 */
	public static function addons_page() {

		if ( self::maybe_display_installation_wizard() ) {
			return;
		};

		wp_print_styles( array( 'thickbox' ) );

		$plugins           = get_plugins();
		$installed_plugins = array();
		foreach ( $plugins as $key => $plugin ) {
			$is_active                            = is_plugin_active( $key );
			$installed_plugin                     = array( 'plugin' => $key, 'name' => $plugin['Name'], 'is_active' => $is_active );
			$installed_plugin['activation_url']   = $is_active ? '' : wp_nonce_url( "plugins.php?action=activate&plugin={$key}", "activate-plugin_{$key}" );
			$installed_plugin['deactivation_url'] = ! $is_active ? '' : wp_nonce_url( "plugins.php?action=deactivate&plugin={$key}", "deactivate-plugin_{$key}" );

			$installed_plugins[] = $installed_plugin;
		}

		$nonces = self::get_addon_nonces();

		$body    = array( 'plugins' => urlencode( serialize( $installed_plugins ) ), 'nonces' => urlencode( serialize( $nonces ) ), 'key' => GFCommon::get_key() );
		$options = array( 'body' => $body, 'headers' => array( 'Referer' => get_bloginfo( 'url' ) ), 'timeout' => 15 );

		$raw_response = GFCommon::post_to_manager( 'api.php', "op=plugin_browser&{$_SERVER['QUERY_STRING']}", $options );

		if ( is_wp_error( $raw_response ) || $raw_response['response']['code'] != 200 ) {
			echo "<div class='error' style='margin-top:50px; padding:20px;'>" . esc_html__( 'Add-On browser is currently unavailable. Please try again later.', 'gravityforms' ) . '</div>';
		} else {
			echo GFCommon::get_remote_message();
			echo $raw_response['body'];
		}
	}

	/**
	 * Gets all add-on information
	 *
	 * @access public
	 * @static
	 *
	 * @param string $api    The API URL
	 * @param string $action The action needed.  Determines the view.
	 * @param object $args   Additional arguments sent to the API
	 *
	 * @return bool|object API object if successful.  False if error.
	 */
	public static function get_addon_info( $api, $action, $args ) {

		if ( $action == 'plugin_information' && empty( $api ) && ( ! rgempty( 'rg', $_GET ) || $args->slug == 'gravityforms' ) ) {
			$key = GFCommon::get_key();
			$raw_response = GFCommon::post_to_manager( 'api.php', "op=get_plugin&slug={$args->slug}&key={$key}", array() );

			if ( is_wp_error( $raw_response ) || $raw_response['response']['code'] != 200 ) {
				return false;
			}

			$plugin = unserialize( $raw_response['body'] );

			$api                = new stdClass();
			$api->name          = $plugin['title'];
			$api->version       = $plugin['version'];
			$api->download_link = $plugin['download_url'];
			$api->tested = '10.0';

		}

		return $api;
	}

	/**
	 * Creates nonces for add-on installation pages
	 *
	 * @access public
	 * @static
	 *
	 * @return array|bool $nonces The nonces if the API response is fine.  Otherwise, false.
	 */
	public static function get_addon_nonces() {

		$raw_response = GFCommon::post_to_manager( 'api.php', 'op=get_plugins', array() );

		if ( is_wp_error( $raw_response ) || $raw_response['response']['code'] != 200 ) {
			return false;
		}

		$addons = unserialize( $raw_response['body'] );
		$nonces = array();
		foreach ( $addons as $addon ) {
			$nonces[ $addon['key'] ] = wp_create_nonce( "install-plugin_{$addon['key']}" );
		}

		return $nonces;
	}

	/**
	 * Begins exports
	 *
	 * Passes off to GFExport::start_export
	 *
	 * @access public
	 * @static
	 * @see GFExport::start_export
	 */
	public static function start_export() {
		require_once( GFCommon::get_base_path() . '/export.php' );
		GFExport::start_export();
	}

	/**
	 * Gets the post categories
	 *
	 * Passes off to GFFormDetail::get_post_category_values
	 *
	 * @access public
	 * @static
	 * @see GFFormDetail::get_post_category_values
	 */
	public static function get_post_category_values() {
		require_once( GFCommon::get_base_path() . '/form_detail.php' );
		GFFormDetail::get_post_category_values();
	}

	/**
	 * Gets and displays the rules for an address field, depending on the address type
	 *
	 * @access public
	 * @static
	 */
	public static function get_address_rule_values_select() {

		$address_type  = rgpost( 'address_type' );
		$value         = rgpost( 'value' );
		$id            = rgpost( 'id' );
		$address_field = new GF_Field_Address();
		$markup        = '';

		switch( $address_type ) {
			case '':
			case 'international':
				$items = $address_field->get_countries();
				break;
			case 'us':
				$items = $address_field->get_us_states();
				break;
			case 'canadian':
				$items = $address_field->get_canadian_provinces();
				break;
		}

		foreach( $items as $item ) {
			$markup .= sprintf( '<option value="%1$s" %2$s>%1$s</option>', $item, selected( $value, $item, false ) );
		}

		$markup = sprintf( '<select id="%1$s" name="%1$s" class="gfield_rule_select gfield_rule_value_dropdown">%2$s</select>', $id, $markup );

		echo $markup;

		die();

	}

	/**
	 * Gets post categories for display in Notifications
	 *
	 * Passes off to GFNotification::get_post_category_values
	 *
	 * @access public
	 * @static
	 * @see GFNotification::get_post_category_values
	 */
	public static function get_notification_post_category_values() {
		require_once( GFCommon::get_base_path() . '/notification.php' );
		GFNotification::get_post_category_values();
	}

	/**
	 * Fires off the entries page.
	 *
	 * Checks if the installation wizard is needed.  If so, does that instead.
	 * Passes everything off to GFEntryDetail::lead_detail_page or GFEntryList::all_entries_page depending on current view
	 *
	 * @access public
	 * @static
	 * @see GFEntryDetail::lead_detail_page
	 * @see GFEntryList::all_entries_page
	 */
	public static function all_leads_page() {

		if ( self::maybe_display_installation_wizard() ) {
			return;
		};

		$view    = rgget( 'view' );
		$lead_id = rgget( 'lid' );

		if ( $view == 'entry' && ( rgget( 'lid' ) || ! rgblank( rgget( 'pos' ) ) ) ) {
			require_once( GFCommon::get_base_path() . '/entry_detail.php' );
			GFEntryDetail::lead_detail_page();
		} else if ( $view == 'entries' || empty( $view ) ) {
			require_once( GFCommon::get_base_path() . '/entry_list.php' );
			GFEntryList::all_entries_page();
		} else {
			$form_id = rgget( 'id' );
			$form_id = absint( $form_id );
			/**
			 * Fires when viewing entries of a certain form
			 *
			 * @param string $view    The current view/entry type
			 * @param string $form_id The current form ID
			 * @param string $lead_id The current entry ID
			 */
			do_action( 'gform_entries_view', $view, $form_id, $lead_id );
		}

	}

	/**
	 * Gets the Form List page
	 *
	 * Passes off to GFFormList::form_list_page
	 *
	 * @access public
	 * @static
	 * @see GFFormList::form_list_page
	 */
	public static function form_list_page() {
		require_once( GFCommon::get_base_path() . '/form_list.php' );
		GFFormList::form_list_page();
	}

	/**
	 * Handles the view when accessing specific forms
	 *
	 * If needed, displays the installation wizard instead.
	 *
	 * @acces public
	 * @static
	 * @see GFEntryList::leads_page
	 * @see GFEntryDetail::lead_detail_page
	 * @see GFFormSettings::form_settings_page
	 * @see GFForms::forms_page
	 * @see GFForms::form_list_page
	 */
	public static function forms() {
		if ( ! GFCommon::ensure_wp_version() ) {
			return;
		}

		if ( self::maybe_display_installation_wizard() ) {
			return;
		};

		$id   = RGForms::get( 'id' );
		$view = RGForms::get( 'view' );

		if ( $view == 'entries' ) {
			require_once( GFCommon::get_base_path() . '/entry_list.php' );
			GFEntryList::leads_page( $id );
		} else if ( $view == 'entry' ) {
			require_once( GFCommon::get_base_path() . '/entry_detail.php' );
			GFEntryDetail::lead_detail_page();
		} else if ( $view == 'notification' ) {
			require_once( GFCommon::get_base_path() . '/notification.php' );
			//GFNotification::notification_page($id);
		} else if ( $view == 'settings' ) {
			require_once( GFCommon::get_base_path() . '/form_settings.php' );
			GFFormSettings::form_settings_page( $id );
		} else if ( empty( $view ) ) {
			if ( is_numeric( $id ) ) {
				self::forms_page( $id );
			} else {
				self::form_list_page();
			}
		}

		/**
		 * Fires an action based on the form view
		 *
		 * @param string $view The current view
		 * @param string $id   The form ID
		 */
		do_action( 'gform_view', $view, $id );

	}

	/**
	 * Obtains $_GET values or values from an array
	 *
	 * @access public
	 * @static
	 *
	 * @param string $name The ID of a specific value.
	 * @param array $array An optional array to search through.  Defaults to null.
	 *
	 * @return string The value.  Empty if not found.
	 */
	public static function get( $name, $array = null ) {
		if ( ! isset( $array ) ) {
			$array = $_GET;
		}

		if ( isset( $array[ $name ] ) ) {
			return $array[ $name ];
		}

		return '';
	}

	/**
	 * Obtains $_POST values
	 *
	 * @access public
	 * @static
	 *
	 * @param string $name            The ID of the value to obtain
	 * @param bool   $do_stripslashes If stripslashes_deep should be run on the result.  Defaults to true.
	 *
	 * @return string The value.  Empty if not found.
	 */
	public static function post( $name, $do_stripslashes = true ) {

		if ( isset( $_POST[ $name ] ) ) {
			return $do_stripslashes ? stripslashes_deep( $_POST[ $name ] ) : $_POST[ $name ];
		}

		return '';
	}

	/**
	 * Resends failed notifications
	 *
	 * @access public
	 * @static
	 * @see GFCommon::send_notification
	 */
	public static function resend_notifications() {

		check_admin_referer( 'gf_resend_notifications', 'gf_resend_notifications' );
		$form_id = absint( rgpost( 'formId' ) );
		$leads   = rgpost( 'leadIds' ); // may be a single ID or an array of IDs
		if ( 0 == $leads ) {
			// get all the lead ids for the current filter / search
			$filter = rgpost( 'filter' );
			$search = rgpost( 'search' );
			$star   = $filter == 'star' ? 1 : null;
			$read   = $filter == 'unread' ? 0 : null;
			$status = in_array( $filter, array( 'trash', 'spam' ) ) ? $filter : 'active';

			$search_criteria['status'] = $status;

			if ( $star ) {
				$search_criteria['field_filters'][] = array( 'key' => 'is_starred', 'value' => (bool) $star );
			}
			if ( ! is_null( $read ) ) {
				$search_criteria['field_filters'][] = array( 'key' => 'is_read', 'value' => (bool) $read );
			}

			$search_field_id = rgpost( 'fieldId' );

			if ( isset( $_POST['fieldId'] ) && $_POST['fieldId'] !== '' ) {
				$key            = $search_field_id;
				$val            = $search;
				$strpos_row_key = strpos( $search_field_id, '|' );
				if ( $strpos_row_key !== false ) { //multi-row
					$key_array = explode( '|', $search_field_id );
					$key       = $key_array[0];
					$val       = $key_array[1] . ':' . $val;
				}
				$search_criteria['field_filters'][] = array(
					'key'      => $key,
					'operator' => rgempty( 'operator', $_POST ) ? 'is' : rgpost( 'operator' ),
					'value'    => $val,
				);
			}

			$leads = GFFormsModel::search_lead_ids( $form_id, $search_criteria );
		} else {
			$leads = ! is_array( $leads ) ? array( $leads ) : $leads;
		}

		/**
		 * Filters the notifications to be re-sent
		 *
		 * @param array $form_meta The Form Object
		 * @param array $leads     The entry IDs
		 */
		$form = gf_apply_filters( array( 'gform_before_resend_notifications', $form_id ), RGFormsModel::get_form_meta( $form_id ), $leads );

		if ( empty( $leads ) || empty( $form ) ) {
			esc_html_e( 'There was an error while resending the notifications.', 'gravityforms' );
			die();
		};

		$notifications = json_decode( rgpost( 'notifications' ) );
		if ( ! is_array( $notifications ) ) {
			die( esc_html__( 'No notifications have been selected. Please select a notification to be sent.', 'gravityforms' ) );
		}

		if ( ! rgempty( 'sendTo', $_POST ) && ! GFCommon::is_valid_email_list( rgpost( 'sendTo' ) ) ) {
			die( sprintf( esc_html__( 'The %sSend To%s email address provided is not valid.', 'gravityforms' ), '<strong>', '</strong>' ) );
		}

		foreach ( $leads as $lead_id ) {

			$lead = RGFormsModel::get_lead( $lead_id );
			foreach ( $notifications as $notification_id ) {
				$notification = $form['notifications'][ $notification_id ];
				if ( ! $notification ) {
					continue;
				}

				//overriding To email if one was specified
				if ( rgpost( 'sendTo' ) ) {
					$notification['to']     = rgpost( 'sendTo' );
					$notification['toType'] = 'email';
				}

				GFCommon::send_notification( $notification, $form, $lead );
			}
		}

		die();
	}

	//-------------------------------------------------
	//----------- AJAX CALLS --------------------------

	/**
	 * Gets the CAPTCHA image for the form editor and displays it
	 *
	 * Called via AJAX
	 *
	 * @access public
	 * @static
	 */
	public static function captcha_image() {
		$field_properties = array( 'type' => 'captcha', 'simpleCaptchaSize' => $_GET['size'], 'simpleCaptchaFontColor' => $_GET['fg'], 'simpleCaptchaBackgroundColor' => $_GET['bg'] );
		/* @var GF_Field_CAPTCHA $field */
		$field = GF_Fields::create( $field_properties );
		if ( $_GET['type'] == 'math' ) {
			$captcha = $field->get_math_captcha( $_GET['pos'] );
		} else {
			$captcha = $field->get_captcha();
		}

		@ini_set( 'memory_limit', '256M' );
		$image = imagecreatefrompng( $captcha['path'] );

		include_once( ABSPATH . 'wp-admin/includes/image-edit.php' );
		wp_stream_image( $image, 'image/png', 0 );
		imagedestroy( $image );
		die();
	}

	/**
	 * Updates the form status (active/inactive)
	 *
	 * Called via AJAX
	 * Passes data to RGFormsModel::update_form_active
	 *
	 * @access public
	 * @static
	 * @see RGFormsModel::update_form_active
	 */
	public static function update_form_active() {
		check_ajax_referer( 'rg_update_form_active', 'rg_update_form_active' );
		RGFormsModel::update_form_active( $_POST['form_id'], $_POST['is_active'] );
	}

	/**
	 * Updates the notification status (active/inactive)
	 *
	 * Called via AJAX
	 * Passes data to RGFormsModel::update_notification_active
	 *
	 * @access public
	 * @static
	 * @see RGFormsModel::update_notification_active
	 */
	public static function update_notification_active() {
		check_ajax_referer( 'rg_update_notification_active', 'rg_update_notification_active' );
		RGFormsModel::update_notification_active( $_POST['form_id'], $_POST['notification_id'], $_POST['is_active'] );
	}

	/**
	 * Updates the confirmation status (active/inactive)
	 *
	 * Called via AJAX
	 * Passes data to RGFormsModel::update_confirmation_active
	 *
	 * @access public
	 * @static
	 * @see RGFormsModel::update_confirmation_active
	 */
	public static function update_confirmation_active() {
		check_ajax_referer( 'rg_update_confirmation_active', 'rg_update_confirmation_active' );
		RGFormsModel::update_confirmation_active( $_POST['form_id'], $_POST['confirmation_id'], $_POST['is_active'] );
	}

	/**
	 * Updates the entry properties
	 *
	 * Called via AJAX
	 * Passes data to RGFormsModel::update_lead_property
	 *
	 * @access public
	 * @static
	 * @see RGFormsModel::update_lead_property
	 */
	public static function update_lead_property() {
		check_ajax_referer( 'rg_update_lead_property', 'rg_update_lead_property' );
		RGFormsModel::update_lead_property( $_POST['lead_id'], $_POST['name'], $_POST['value'] );
	}

	/**
	 * Updates the entry status
	 *
	 * Called via AJAX
	 * Passes data off to either RGFormsModel::update_lead_property or RGFormsModel::delete_lead
	 *
	 * @access public
	 * @static
	 * @see RGFormsModel::update_lead_property
	 * @see RGFormsModel::delete_lead
	 */
	public static function update_lead_status() {
		check_ajax_referer( 'gf_delete_entry' );
		$status  = rgpost( 'status' );
		$lead_id = rgpost( 'entry' );

		$entry = GFAPI::get_entry( $lead_id );
		$form = GFAPI::get_form( $entry['form_id'] );

		switch ( $status ) {
			case 'unspam' :
				RGFormsModel::update_lead_property( $lead_id, 'status', 'active' );
				break;

			case 'delete' :
				if ( GFCommon::current_user_can_any( 'gravityforms_delete_entries' ) ) {
					RGFormsModel::delete_lead( $lead_id );
				}
				break;

			default :
				RGFormsModel::update_lead_property( $lead_id, 'status', $status );
				break;
		}
		require_once( 'entry_list.php' );


		$filter_links = GFEntryList::get_filter_links( $form );

		$counts = array();
		foreach ( $filter_links as $filter_link ) {
			$id = $filter_link['id'] == '' ? 'all' : $filter_link['id'];
			$counts[ $id . '_count' ] = $filter_link['count'];
		}

		$x = new WP_Ajax_Response();
		$x->add( array(
			'what' => 'gf_entry',
			'id' => $lead_id,
			'supplemental' => $counts,
		) );
		$x->send();
	}

	//settings
	/**
	 * Runs the license upgrade
	 *
	 * Called via AJAX
	 * Passes data off to GFSettings::upgrade_license
	 *
	 * @access public
	 * @static
	 * @see GFSettings::upgrade_license
	 */
	public static function upgrade_license() {
		require_once( GFCommon::get_base_path() . '/settings.php' );
		GFSettings::upgrade_license();
	}

	//form detail
	/**
	 * Saves the form in the form editor
	 *
	 * Called via AJAX
	 * Passes data off to GFFormDetail::save_form
	 *
	 * @access public
	 * @static
	 * @see GFFormDetail::save_form
	 */
	public static function save_form() {
		require_once( GFCommon::get_base_path() . '/form_detail.php' );
		GFFormDetail::save_form();
	}

	/**
	 * Adds fields in the form editor
	 *
	 * Called via AJAX
	 * Passes data off to GFFormDetail::add_field
	 *
	 * @access public
	 * @static
	 * @see GFFormDetail::add_field
	 */
	public static function add_field() {
		require_once( GFCommon::get_base_path() . '/form_detail.php' );
		GFFormDetail::add_field();
	}

	/**
	 * Duplicates fields in the form editor
	 *
	 * Called via AJAX
	 * Passes data off to GFFormDetail::duplicate_field
	 *
	 * @access public
	 * @static
	 * @see GFFormDetail::duplicate_field
	 */
	public static function duplicate_field() {
		require_once( GFCommon::get_base_path() . '/form_detail.php' );
		GFFormDetail::duplicate_field();
	}

	/**
	 * Deletes fields in the form editor
	 *
	 * Called via AJAX
	 * Passes data off to GFFormDetail::delete_field
	 *
	 * @access public
	 * @static
	 * @see GFFormDetail::delete_field
	 */
	public static function delete_field() {
		require_once( GFCommon::get_base_path() . '/form_detail.php' );
		GFFormDetail::delete_field();
	}

	/**
	 * Changes the input type in the form editor
	 *
	 * Called via AJAX
	 * Passes data of to GFFormDetail::change_input_type
	 *
	 * @access public
	 * @static
	 * @see GFFormDetail::change_input_type
	 */
	public static function change_input_type() {
		require_once( GFCommon::get_base_path() . '/form_detail.php' );
		GFFormDetail::change_input_type();
	}

	/**
	 * Refreshes the field preview
	 *
	 * Called via AJAX
	 * Passes data off to GFFormDetail::refresh_field_preview
	 *
	 * @access public
	 * @static
	 * @see GFFormDetail::refresh_field_preview
	 */
	public static function refresh_field_preview() {
		require_once( GFCommon::get_base_path() . '/form_detail.php' );
		GFFormDetail::refresh_field_preview();
	}

	/**
	 * Deletes custom choices from radio/checkbox/select/etc fields
	 *
	 * Called via AJAX
	 * Passes data off to GFFormDetail::delete_custom_choice
	 *
	 * @access public
	 * @static
	 * @see GFFormDetail::delete_custom_choice
	 */
	public static function delete_custom_choice() {
		require_once( GFCommon::get_base_path() . '/form_detail.php' );
		GFFormDetail::delete_custom_choice();
	}

	/**
	 * Saves custom choices from radio/checkbox/select/etc fields
	 *
	 * Called via AJAX
	 * Passes data off to GFFormDetail::save_custom_choices
	 *
	 * @access public
	 * @static
	 * @see GFFormDetail::save_custom_choice
	 */
	public static function save_custom_choice() {
		require_once( GFCommon::get_base_path() . '/form_detail.php' );
		GFFormDetail::save_custom_choice();
	}

	//entry detail
	/**
	 * Deletes a file from the enry detail view
	 *
	 * Called via AJAX
	 * Passes data off to RGFormsModel::delete_file
	 *
	 * @access public
	 * @static
	 * @see RGFormsModel::delete_file
	 */
	public static function delete_file() {
		check_ajax_referer( 'rg_delete_file', 'rg_delete_file' );
		$lead_id    = intval( $_POST['lead_id'] );
		$field_id   = intval( $_POST['field_id'] );
		$file_index = intval( $_POST['file_index'] );

		RGFormsModel::delete_file( $lead_id, $field_id, $file_index );
		die( "EndDeleteFile($field_id, $file_index);" );
	}

	//export
	/**
	 * Gets the form export data
	 *
	 * Called via AJAX
	 *
	 * @access public
	 * @static
	 * @see GFExport::add_default_export_fields
	 */
	public static function select_export_form() {
		check_ajax_referer( 'rg_select_export_form', 'rg_select_export_form' );
		$form_id = intval( $_POST['form_id'] );
		$form    = RGFormsModel::get_form_meta( $form_id );

		/**
		 * Filters through the Form Export Page
		 *
		 * @param int $form The Form Object of the form to export
		 */
		$form = gf_apply_filters( array( 'gform_form_export_page', $form_id ), $form );

		$filter_settings      = GFCommon::get_field_filter_settings( $form );
		$filter_settings_json = json_encode( $filter_settings );
		$fields               = array();

		$form = GFExport::add_default_export_fields( $form );

		if ( is_array( $form['fields'] ) ) {
			/* @var GF_Field $field */
			foreach ( $form['fields'] as $field ) {
				$inputs = $field->get_entry_inputs();
				if ( is_array( $inputs ) ) {
					foreach ( $inputs as $input ) {
						$fields[] = array( $input['id'], GFCommon::get_label( $field, $input['id'] ) );
					}
				} else if ( ! $field->displayOnly ) {
					$fields[] = array( $field->id, GFCommon::get_label( $field ) );
				}
			}
		}
		$field_json = GFCommon::json_encode( $fields );

		die( "EndSelectExportForm($field_json, $filter_settings_json);" );
	}

	// form settings
	/**
	 * Saves a form confirmation
	 *
	 * Called via AJAX
	 * Passes data off to GFFormSettings::save_confirmation
	 *
	 * @access public
	 * @static
	 * @see GFFormSettings::save_confirmation
	 */
//	public static function save_confirmation() {
//		require_once( GFCommon::get_base_path() . '/form_settings.php' );
//		GFFormSettings::save_confirmation();
//	}

	/**
	 * Saves the form title
	 *
	 * Called via AJAX
	 * Passes data off to GFFormSettings::save_form_title
	 *
	 * @access public
	 * @static
	 * @see GFFormSettings::save_form_title
	 */
	public static function save_form_title() {
		require_once( GFCommon::get_base_path() . '/form_settings.php' );
		GFFormSettings::save_form_title();
	}


	/**
	 * Deletes a form confirmation
	 *
	 * Called via AJAX
	 * Passes data off to GFFormSettings::delete_confirmation
	 *
	 * @access public
	 * @static
	 * @see GFFormSettings::delete_confirmation
	 */
	public static function delete_confirmation() {
		require_once( GFCommon::get_base_path() . '/form_settings.php' );
		GFFormSettings::delete_confirmation();
	}

	// form list
	/**
	 * Saves a new form
	 *
	 * Called via AJAX
	 * Passes data off to GFFormList::save_new_form
	 *
	 * @access public
	 * @static
	 * @see GFFormList::save_new_form
	 */
	public static function save_new_form() {
		require_once( GFCommon::get_base_path() . '/form_list.php' );
		GFFormList::save_new_form();
	}


	/**
	 * Displays the edit title popup
	 * @access public
	 * @static
	 */
	public static function edit_form_title( $form ){

		//Only allow users with form edit permissions to edit forms
		if( ! GFCommon::current_user_can_any( 'gravityforms_edit_forms' ) ){
			return;
		}

		?>

		<div id="edit-title-container" class="add_field_button_container" >
			<div class="button-title-link gf_button_title_active">
				<div id="edit-title-header">
					<?php esc_html_e( 'Form Title', 'gravityforms' ); ?>
					<span id="edit-title-close" onclick="GF_CloseEditTitle();"><i class="fa fa-times"></i></span>
				</div>
			</div>
			<div class="add-buttons">
				<input type="text" id='edit-title-input' value='<?php echo esc_attr( $form['title'] ); ?>'/>

				<div class="edit-form-footer">
					<input type="button" value="<?php esc_html_e( 'Update', 'gravityforms'); ?>" class="button-primary" onclick="GF_SaveTitle();"/>
				</div>
			</div>

		</div>
		
		<script type="text/javascript">
			function GF_ShowEditTitle(){
				jQuery( '#edit-title-container' ).css( 'visibility', 'visible' );
			}

			function GF_CloseEditTitle(){
				jQuery( '#edit-title-container' ).css( 'visibility', 'hidden' );
			}

			function GF_SaveTitle(){
				
				var title = jQuery( '#edit-title-input' ).val();
				
				jQuery.post(ajaxurl, {
						action			: "gf_save_title",
						gf_save_title	: '<?php echo wp_create_nonce( 'gf_save_title' ); ?>',
						title			: jQuery.toJSON(title),
						formId  		: '<?php echo absint( $form['id'] ); ?>'
					})
					.done(function( data ) {
						var is_error = data !== '0';
						if( is_error ){
							alert('<?php esc_attr_e('Oops! There was an error saving the form title. Please refresh the page and try again.', 'gravityforms'); ?>');
						}
						else{
							var title = jQuery( '#edit-title-input' ).val();
							jQuery( '#gform_settings_page_title' ).text( title );
							jQuery( '#form_title_input').val( title );
							<?php echo GFCommon::is_form_editor() ? 'form.title = title;' : ''; ?>
						}

						GF_CloseEditTitle();
					})
					.fail(function(){
						alert('<?php esc_attr_e('Oops! There was an error saving the form title. Please refresh the page and try again.', 'gravityforms'); ?>');
						GF_CloseEditTitle();
					});

			}

			function GF_IsOutsideTitleWindow( element ){
				var parents = jQuery( element ).parents( '#edit-title-container' );
				return parents.length == 0;
			}

			jQuery( document ).mousedown( function( event ){
				if ( GF_IsOutsideTitleWindow( event.target ) ){
					GF_CloseEditTitle();
				}
			});

			jQuery( document ).ready( function(){
				jQuery( '#edit-title-input' ).keypress( function( event ){
					if( event.keyCode == 13 ){
						GF_SaveTitle();
					}
				});
			});

		</script>

		<?php
	}

	/**
	 * Displays the form switcher dropdown
	 *
	 * @access public
	 * @static
	 */
	public static function form_switcher() {

		// Get all forms.
		$all_forms = RGFormsModel::get_forms( null, 'title' );
		
		// Sort forms by active state.
		$forms = array( 'active' => array(), 'inactive' => array(), );
		foreach ( $all_forms as $form ) {
			if ( '1' === $form->is_active ) {
				$forms['active'][] = $form;
			} else if ( '0' === $form->is_active ) {
				$forms['inactive'][] = $form;
			}
		}

		//Enqueuing chosen script
		wp_enqueue_script( 'gform_chosen', false, array('jquery'), GFCommon::$version, true );

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';
		wp_enqueue_style( 'gform_chosen', GFCommon::get_base_url() . "/css/chosen{$min}.css", array(), GFCommon::$version );

		?>
		<a href="#" onclick="GF_SetupChosen( this ); event.stopPropagation();" id='form_switcher_arrow' class='form_switcher_arrow'><i class='fa fa-angle-down'></i></a>
		<div id="form_switcher_container">
			<select data-placeholder="<?php esc_attr_e( 'Switch Form', 'gravityforms' ) ?>" name="form_switcher" id="form_switcher" onchange="GF_SwitchForm(jQuery(this).val());" >
				<option></option>
				<?php
				foreach ( $all_forms as $form_info ) {
					$title = $form_info->title;
					?>
					<option value="<?php echo absint( $form_info->id ); ?>"><?php echo esc_html( $title )?></option>
					<?php
				}
				?>
			</select>
		</div>

		<script type="text/javascript">
			function GF_ReplaceQuery(key, newValue) {
				var new_query = "";
				var query = document.location.search.substring(1);
				var ary = query.split("&");
				var has_key = false;
				for (i = 0; i < ary.length; i++) {
					var key_value = ary[i].split("=");

					if (key_value[0] == key) {
						new_query += key + "=" + newValue + "&";
						has_key = true;
					}
					else if (key_value[0] != "display_settings") {
						new_query += key_value[0] + "=" + key_value[1] + "&";
					}
				}

				if (new_query.length > 0)
					new_query = new_query.substring(0, new_query.length - 1);

				if (!has_key)
					new_query += new_query.length > 0 ? "&" + key + "=" + newValue : "?" + key + "=" + newValue;

				return new_query;
			}

			function GF_RemoveQuery(key, query) {
				var new_query = "";
				if (query == "") {
					query = document.location.search.substring(1);
				}
				var ary = query.split("&");
				for (i = 0; i < ary.length; i++) {
					var key_value = ary[i].split("=");

					if (key_value[0] != key) {
						new_query += key_value[0] + "=" + key_value[1] + "&";
					}
				}

				if (new_query.length > 0)
					new_query = new_query.substring(0, new_query.length - 1);

				return new_query;
			}

			function GF_SwitchForm(id) {

				if (id.length > 0) {
					id = parseInt( id );
					var query = GF_ReplaceQuery("id", id);
					//remove paging from querystring when changing forms
					var  new_query = GF_RemoveQuery("paged", query);
					new_query = new_query.replace("gf_new_form", "gf_edit_forms");

					//remove filter vars from querystring when changing forms
					new_query = GF_RemoveQuery("s", new_query);
					new_query = GF_RemoveQuery("operator", new_query);
					new_query = GF_RemoveQuery("type", new_query);
					new_query = GF_RemoveQuery("field_id", new_query);
					new_query = GF_RemoveQuery("lid", new_query);
					new_query = GF_RemoveQuery("filter", new_query);
					new_query = GF_RemoveQuery("pos", new_query);

					//When switching forms within any form settings tab, go back to main form settings tab
					var is_form_settings = new_query.indexOf("page=gf_edit_forms") >= 0 && new_query.indexOf("view=settings") >= 0;
					if (is_form_settings) {
						//going back to main form settings tab
						new_query = "page=gf_edit_forms&view=settings&id=" + id;
					}

					//When switching forms within any form entries tab, go back to main form entries tab
					var is_form_entries = new_query.indexOf("page=gf_entries") >= 0;
					if (is_form_entries) {
						//going back to main form settings tab
						new_query = "page=gf_entries&id=" + id;
					}

					document.location = "?" + new_query;
				}
			}

			function ToggleFormSettings() {
				FieldClick(jQuery('#gform_heading')[0]);
			}

			function GF_PositionFormSwitcher(){

				var $container = jQuery('#form_switcher_container');
				var position = jQuery( '#form_switcher_arrow' ).position().left,
					width    = $container.outerWidth(),
					offset   = width > position ? 0 : position - width + 20;

				$container.css(  'left', offset + 'px' );
			}

			function GF_SetupChosen( elem ){

				//displaying container
				var $container = jQuery('#form_switcher_container');
				$container.show();

				//initializing and activating chosen
				var $ch = jQuery('#form_switcher').chosen();
				$ch.trigger( 'chosen:open' );

				//setting form switcher position
				GF_PositionFormSwitcher();

				//binding to close event
				$ch.bind('chosen:hiding_dropdown', function(ch){
					jQuery('#form_switcher_container').hide();
				});
			}

			jQuery(document).ready(function () {
				if (document.location.search.indexOf("display_settings") > 0)
					ToggleFormSettings()

				jQuery('a.gf_toolbar_disabled').click(function (event) {
					event.preventDefault();
				});
			});

			jQuery( window ).resize(function() {
				GF_PositionFormSwitcher();
			});


		</script>
		<?php
	}

	/**
	 * Displays the top toolbar withing Gravity Forms pages
	 *
	 * @access public
	 * @static
	 * @see GFForms::get_toolbar_menu_items
	 * @see GFForms::format_toolbar_menu_items
	 */
	public static function top_toolbar() {

		$forms = RGFormsModel::get_forms( null, 'title' );
		$id    = rgempty( 'id', $_GET ) ? count( $forms ) > 0 ? $forms[0]->id : '0' : rgget( 'id' );
		?>
		<div id="gf_form_toolbar">
			<ul id="gf_form_toolbar_links">

				<?php
				$menu_items = apply_filters( 'gform_toolbar_menu', self::get_toolbar_menu_items( $id ), $id );
				echo self::format_toolbar_menu_items( $menu_items );
				?>
			</ul>
		</div>

	<?php

	}

	/**
	 * Formats the menu items for display in the Gravity Forms toolbar
	 *
	 * Called from GFForms::top_toolbar
	 *
	 * @access public
	 * @static
	 * @see GFForms::top_toolbar
	 * @see GFForms::toolbar_sub_menu_items
	 *
	 * @param array $menu_items Contains the menu items to be displayed
	 * @param bool  $compact    If true, uses the compact labels.  Defaults to false.
	 *
	 * @return string $output The formatted toolbar menu items
	 */
	public static function format_toolbar_menu_items( $menu_items, $compact = false ) {
		if ( empty( $menu_items ) ) {
			return '';
		}

		$output = '';

		$priorities = array();
		foreach ( $menu_items as $k => $menu_item ) {
			$priorities[ $k ] = rgar( $menu_item, 'priority' );
		}

		array_multisort( $priorities, SORT_DESC, $menu_items );

		$keys     = array_keys( $menu_items );
		$last_key = array_pop( $keys ); // array_pop(array_keys($menu_items)) causes a Strict Standards warning in WP 3.6 on PHP 5.4

		foreach ( $menu_items as $key => $menu_item ) {
			if ( is_array( $menu_item ) ) {
				if ( GFCommon::current_user_can_any( rgar( $menu_item, 'capabilities' ) ) ) {
					$sub_menu_str         = '';
					$count_sub_menu_items = 0;
					$sub_menu_items       = rgar( $menu_item, 'sub_menu_items' );
					if ( is_array( $sub_menu_items ) ) {
						foreach ( $sub_menu_items as $k => $val ) {
							if ( false === GFCommon::current_user_can_any( rgar( $sub_menu_items[ $k ], 'capabilities' ) ) ) {
								unset( $sub_menu_items[ $k ] );
							}
						}
						$sub_menu_items       = array_values( $sub_menu_items ); //reset numeric keys
						$count_sub_menu_items = count( $sub_menu_items );
					}

					$menu_class = rgar( $menu_item, 'menu_class' );

					if ( $count_sub_menu_items == 1 ) {
						$label     = $compact ? rgar( $menu_item, 'label' ) : rgar( $sub_menu_items[0], 'label' );
						$menu_item = $sub_menu_items[0];
					} else {
						$label        = rgar( $menu_item, 'label' );
						$sub_menu_str = self::toolbar_sub_menu_items( $sub_menu_items, $compact );
					}
					$link_class = esc_attr( rgar( $menu_item, 'link_class' ) );
					$icon       = rgar( $menu_item, 'icon' );
					$url        = esc_url( rgar( $menu_item, 'url' ) );
					$title      = esc_attr( rgar( $menu_item, 'title' ) );
					$onclick    = esc_attr( rgar( $menu_item, 'onclick' ) );
					$label 		= esc_html( $label );
					$target 	= rgar( $menu_item, 'target' );

					$link   	= "<a class='{$link_class}' onclick='{$onclick}' onkeypress='{$onclick}' title='{$title}' href='{$url}' target='{$target}'>{$icon} {$label}</a>" . $sub_menu_str;
					if ( $compact ) {
						if ( $key == 'delete' ) {

							/**
							 * A filter to allow the modification of the HTML link to delete a form
							 *
							 * @param string $link The HTML "Delete Form" Link
							 */
							$link = apply_filters( 'gform_form_delete_link', $link );
						}
						$divider = $key == $last_key ? '' : ' | ';
						if ( $count_sub_menu_items > 0 ) {
							$menu_class .= ' gf_form_action_has_submenu';
						}
						$output .= '<span class="' . $menu_class . '">' . $link . $divider . '</span>';
					} else {

						$output .= "<li class='{$menu_class}'>{$link}</li>";
					}
				}
			} elseif ( $compact ) {
				//for backwards compatibility <1.7: form actions only
				$divider = $key == $last_key ? '' : ' | ';
				$output .= '<span class="edit">' . $menu_item . $divider . '</span>';
			}
		}

		return $output;
	}

	/**
	 * Gets the menu items to be displayed within the toolbar
	 *
	 * Called from GFForms::top_toolbar
	 *
	 * @access public
	 * @static
	 * @see GFForms::toolbar_class
	 * @see GFForms::top_toolbar
	 *
	 * @param string $form_id The form ID
	 * @param bool   $compact True if the compact label should be used.  Defaults to false.
	 *
	 * @return array $menu_items The menu items to be displayed
	 */
	public static function get_toolbar_menu_items( $form_id, $compact = false ) {
		$menu_items = array();

		$is_mobile = wp_is_mobile();

		$form_id = absint( $form_id );

		//---- Form Editor ----
		$edit_capabilities = array( 'gravityforms_edit_forms' );

		$menu_items['edit'] = array(
			'label'        => __( 'Edit', 'gravityforms' ),
			'short_label' => esc_html__( 'Editor', 'gravityforms' ),
			'icon'         => '<i class="fa fa-pencil-square-o fa-lg"></i>',
			'title'        => __( 'Edit this form', 'gravityforms' ),
			'url'          => '?page=gf_edit_forms&id=' . $form_id,
			'menu_class'   => 'gf_form_toolbar_editor',
			'link_class'   => self::toolbar_class( 'editor' ),
			'capabilities' => $edit_capabilities,
			'priority'     => 1000,
		);

		//---- Form Settings ----

		$sub_menu_items = self::get_form_settings_sub_menu_items( $form_id );

		$menu_items['settings'] = array(
			'label'          => __( 'Settings', 'gravityforms' ),
			'icon'           => '<i class="fa fa-cogs fa-lg"></i>',
			'title'          => __( 'Edit settings for this form', 'gravityforms' ),
			'url'            => $is_mobile ? '#' : '?page=gf_edit_forms&view=settings&id=' . $form_id,
			'menu_class'     => 'gf_form_toolbar_settings',
			'link_class'     => self::toolbar_class( 'settings' ),
			'sub_menu_items' => $sub_menu_items,
			'capabilities'   => $edit_capabilities,
			'priority'       => 900,
		);


		//---- Entries ----

		$entries_capabilities = array( 'gravityforms_view_entries', 'gravityforms_edit_entries', 'gravityforms_delete_entries' );

		$menu_items['entries'] = array(
			'label'        => __( 'Entries', 'gravityforms' ),
			'icon'         => '<i class="fa fa-comment-o fa-lg"></i>',
			'title'        => __( 'View entries generated by this form', 'gravityforms' ),
			'url'          => '?page=gf_entries&id=' . $form_id,
			'menu_class'   => 'gf_form_toolbar_entries',
			'link_class'   => self::toolbar_class( 'entries' ),
			'capabilities' => $entries_capabilities,
			'priority'     => 800,
		);

		//---- Preview ----

		$preview_capabilities = array( 'gravityforms_edit_forms', 'gravityforms_create_form', 'gravityforms_preview_forms' );

		$menu_items['preview'] = array(
			'label'        => __( 'Preview', 'gravityforms' ),
			'icon'         => '<i class="fa fa-eye fa-lg"></i>',
			'title'        => __( 'Preview this form', 'gravityforms' ),
			'url'          => trailingslashit( site_url() ) . '?gf_page=preview&id=' . $form_id,
			'menu_class'   => 'gf_form_toolbar_preview',
			'link_class'   => self::toolbar_class( 'preview' ),
			'target'       => '_blank',
			'capabilities' => $preview_capabilities,
			'priority'     => 700,
		);

		/*
		//---- Duplicate ----

		$duplicate_capabilities = array( 'gravityforms_edit_forms', 'gravityforms_create_form' );

		$menu_items['duplicate'] = array(
			'label'        => __( 'Duplicate', 'gravityforms' ),
			'icon'         => '<i class="fa fa-files-o fa-lg"></i>',
			'title'        => __( 'Duplicate this form', 'gravityforms' ),
			'url'		   => wp_nonce_url( "?page=gf_edit_forms&action=duplicate&arg={$form_id}", "gf_duplicate_form_{$form_id}" ),
			'menu_class'   => 'gf_form_toolbar_duplicate',
			'link_class'   => self::toolbar_class( 'duplicate' ),
			'capabilities' => $duplicate_capabilities,
			'priority'     => 600,
		);

		//---- Trash ----

		$trash_capabilities = array( 'gravityforms_delete_forms' );

		$menu_items['trash'] = array(
			'label'        => __( 'Trash', 'gravityforms' ),
			'icon'         => '<i class="fa fa-trash-o fa-lg"></i>',
			'title'        => __( 'Trash this form', 'gravityforms' ),
			'url'		   => wp_nonce_url( "?page=gf_edit_forms&action=trash&arg={$form_id}", "gf_delete_form_{$form_id}" ),
			'menu_class'   => 'gf_form_toolbar_trash',
			'link_class'   => self::toolbar_class( 'trash' ),
			'capabilities' => $trash_capabilities,
			'priority'     => 500,
		);
		*/

		return $menu_items;
	}

	/**
	 * Builds the sub-menu items within the Gravity Forms toolbar
	 *
	 * Called from GFForms::format_toolbar_menu_items
	 *
	 * @access public
	 * @static
	 *
	 * @param array $menu_items The menu items to be built
	 * @param bool $compact     True if the compact label should be used.  False otherwise.
	 *
	 * @return string $sub_menu_items_string The menu item HTML
	 */
	public static function toolbar_sub_menu_items( $menu_items, $compact = false ) {
		if ( empty( $menu_items ) ) {
			return '';
		}

		$sub_menu_items_string = '';
		foreach ( $menu_items as $menu_item ) {
			if ( GFCommon::current_user_can_any( rgar( $menu_item, 'capabilities' ) ) ) {
				$menu_class = esc_attr( rgar( $menu_item, 'menu_class' ) );
				$link_class = esc_attr( rgar( $menu_item, 'link_class' ) );
				$url        = esc_url( rgar( $menu_item, 'url' ) );
				$label      = esc_html( rgar( $menu_item, 'label' ) );
				$target     = esc_attr( rgar( $menu_item, 'target' ) );
				$sub_menu_items_string .= "<li class='{$menu_class}'><a href='{$url}' class='{$link_class}' target='{$target}'>{$label}</a></li>";
			}
		}
		if ( $compact ) {
			$sub_menu_items_string = '<div class="gf_submenu"><ul>' . $sub_menu_items_string . '</ul></div>';
		} else {
			$sub_menu_items_string = '<div class="gf_submenu"><ul>' . $sub_menu_items_string . '</ul></div>';
		}

		return $sub_menu_items_string;
	}

	/**
	 * Gets the form settings sub-menu items
	 *
	 * Called from GFForms::get_toolbar_menu_items
	 *
	 * @access public
	 * @static
	 * @see GFFormSettings::get_tabs
	 * @see GFForms::get_toolbar_menu_items
	 *
	 * @param string $form_id The form ID
	 *
	 * @return array $sub_menu_items The sub-menu items
	 */
	public static function get_form_settings_sub_menu_items( $form_id ) {
		require_once( GFCommon::get_base_path() . '/form_settings.php' );

		$sub_menu_items = array();
		$tabs           = GFFormSettings::get_tabs( $form_id );

		foreach ( $tabs as $tab ) {

			if ( $tab['name'] == 'settings' ) {
				$form_setting_menu_item['label'] = 'Settings';
			}

			$sub_menu_items[] = array(
				'url'          => admin_url( "admin.php?page=gf_edit_forms&view=settings&subview={$tab['name']}&id={$form_id}" ),
				'label'        => $tab['label'],
				'capabilities' => array( 'gravityforms_edit_forms' )
			);

		}

		return $sub_menu_items;
	}

	/**
	 * Gets the CSS class to be used for the toolbar
	 *
	 * Called from GFForms::get_toolbar_menu_items
	 *
	 * @access private
	 * @static
	 * @see GFForms::get_toolbar_menu_items
	 *
	 * @param string $item The Gravity Forms view (current page)
	 *
	 * @return string The class name.  Empty string if the view isn't found
	 */
	private static function toolbar_class( $item ) {

		switch ( $item ) {

			case 'editor':
				if ( in_array( rgget( 'page' ), array( 'gf_edit_forms', 'gf_new_form' ) ) && rgempty( 'view', $_GET ) ) {
					return 'gf_toolbar_active';
				}
				break;

			case 'settings':
				if ( rgget( 'view' ) == 'settings' ) {
					return 'gf_toolbar_active';
				}
				break;

			case 'notifications' :
				if ( rgget( 'page' ) == 'gf_new_form' ) {
					return 'gf_toolbar_disabled';
				} else if ( rgget( 'page' ) == 'gf_edit_forms' && rgget( 'view' ) == 'notification' ) {
					return 'gf_toolbar_active';
				}
				break;

			case 'entries' :
				if ( rgget( 'page' ) == 'gf_new_form' ) {
					return 'gf_toolbar_disabled';
				} else if ( rgget( 'page' ) == 'gf_entries' && rgempty( 'view', $_GET ) ) {
					return 'gf_toolbar_active';
				}

				break;

			case 'preview' :
				if ( rgget( 'page' ) == 'gf_new_form' ) {
					return 'gf_toolbar_disabled';
				}

				break;
		}

		return '';
	}

	/**
	 * Modifies the top WordPress toolbar to add Gravity Forms menu items.
	 *
	 * Called from the wp_before_admin_bar_render action.
	 *
	 * @access public
	 * @static
	 * @global $wp_admin_bar
	 * @see Action: wp_before_admin_bar_render
	 *
	 */
	public static function admin_bar() {
		/**
		 * @var  WP_Admin_Bar $wp_admin_bar
		 */
		global $wp_admin_bar;

		if ( GFCommon::current_user_can_any( 'gravityforms_create_form' ) ) {
			$wp_admin_bar->add_node(
				array(
					'id'     => 'gravityforms-new-form',
					'parent' => 'new-content',
					'title'  => esc_attr__( 'Form', 'gravityforms' ),
					'href'   => admin_url( 'admin.php?page="gf_new_form' ),
				)
			);
		}

		if ( ! get_option( 'gform_enable_toolbar_menu' ) ) {
			return;
		}

		if ( ! GFCommon::current_user_can_any( array( 'gravityforms_edit_forms', 'gravityforms_create_form', 'gravityforms_preview_forms', 'gravityforms_view_entries' ) ) ) {
			// The current user can't use anything on the menu so bail.
			return;
		}

		$args = array(
			'id'    => 'gform-forms',
			'title' => '<div class="ab-item gforms-menu-icon svg" style="background-image: url(\'' . self::get_admin_icon_b64('#888888') . '\');"></div><span class="ab-label">' . esc_html__( 'Forms', 'gravityforms' ) . '</span></a>',
			'href'  => admin_url( 'admin.php?page=gf_edit_forms' ),
		);

		$wp_admin_bar->add_node( $args );

		$current_user_id = get_current_user_id();
		$recent_form_ids = get_user_meta( $current_user_id, 'gform_recent_forms', true );

		if ( empty( $recent_form_ids ) ) {
			$all_form_ids = GFFormsModel::get_form_ids();
			$all_form_ids = array_reverse( $all_form_ids );
			$recent_form_ids = array_slice( $all_form_ids, 0, 10 );
			if ( $recent_form_ids ) {
				update_user_meta( $current_user_id, 'gform_recent_forms', $recent_form_ids );
			}
		}

		if ( $recent_form_ids ) {
			$forms = GFFormsModel::get_form_meta_by_id( $recent_form_ids );

			$wp_admin_bar->add_node(
				array(
					'id'     => 'gform-form-recent-forms',
					'parent' => 'gform-forms',
					'title'  => esc_html__( 'Recent', 'gravityforms' ),
					'group' => true,
				)
			);

			foreach ( $recent_form_ids as $recent_form_id ) {

				foreach ( $forms as $form ) {
					if ( $form['id'] == $recent_form_id ) {
						$wp_admin_bar->add_node(
							array(
								'id'     => 'gform-form-' . $recent_form_id,
								'parent' => 'gform-form-recent-forms',
								'title'  => $form['title'],
								'href'   => GFCommon::current_user_can_any( 'gravityforms_edit_forms' ) ? admin_url( 'admin.php?page=gf_edit_forms&id=' . $recent_form_id ) : '',
							)
						);

						if ( GFCommon::current_user_can_any( 'gravityforms_edit_forms' ) ) {
							$wp_admin_bar->add_node(
								array(
									'id'     => 'gform-form-' . $recent_form_id . '-edit',
									'parent' => 'gform-form-' . $recent_form_id,
									'title'  => esc_html__( 'Edit', 'gravityforms' ),
									'href'   => admin_url( 'admin.php?page=gf_edit_forms&id=' . $recent_form_id ),
								)
							);
						}

						if ( GFCommon::current_user_can_any( 'gravityforms_view_entries' ) ) {
							$wp_admin_bar->add_node(
								array(
									'id'     => 'gform-form-' . $recent_form_id . '-entries',
									'parent' => 'gform-form-' . $recent_form_id,
									'title'  => esc_html__( 'Entries', 'gravityforms' ),
									'href'   => admin_url( 'admin.php?page=gf_entries&id=' . $recent_form_id ),
								)
							);
						}

						if ( GFCommon::current_user_can_any( 'gravityforms_edit_forms' ) ) {
							$wp_admin_bar->add_node(
								array(
									'id'     => 'gform-form-' . $recent_form_id . '-settings',
									'parent' => 'gform-form-' . $recent_form_id,
									'title'  => esc_html__( 'Settings', 'gravityforms' ),
									'href'   => admin_url( 'admin.php?page=gf_edit_forms&view=settings&subview=settings&id=' . $recent_form_id ),
								)
							);
						}

						if ( GFCommon::current_user_can_any( array( 'gravityforms_edit_forms', 'gravityforms_create_form', 'gravityforms_preview_forms' ) ) ) {
							$wp_admin_bar->add_node(
								array(
									'id'     => 'gform-form-' . $recent_form_id . '-preview',
									'parent' => 'gform-form-' . $recent_form_id,
									'title'  => esc_html__( 'Preview', 'gravityforms' ),
									'href'   => trailingslashit( site_url() ) . '?gf_page=preview&id=' . $recent_form_id,
								)
							);
						}
					}
				}
			}
		}

		if ( GFCommon::current_user_can_any( 'gravityforms_edit_forms' ) ) {
			$wp_admin_bar->add_node(
				array(
					'id'     => 'gform-forms-view-all',
					'parent' => 'gform-forms',
					'title'  => esc_attr__( 'All Forms', 'gravityforms' ),
					'href'   => admin_url( 'admin.php?page=gf_edit_forms' ),
				)
			);
		}

		if ( GFCommon::current_user_can_any( 'gravityforms_create_form' ) ) {
			$wp_admin_bar->add_node(
				array(
					'id'     => 'gform-forms-new-form',
					'parent' => 'gform-forms',
					'title'  => esc_attr__( 'New Form', 'gravityforms' ),
					'href'   => admin_url( 'admin.php?page=gf_new_form' ),
				)
			);
		}

	}

	/**
	 * Determines if automatic updating should be processed
	 *
	 * Called from the auto_update_$type filter
	 *
	 * @access public
	 * @static
	 * @see Filter: auto_update_$type
	 * @see GFForms::is_auto_update_disabled
	 *
	 * @param bool $update Whether or not to update
	 * @param object $item The update offer object
	 *
	 * @return bool True if update should be processed.  False otherwise.
	 */
	public static function maybe_auto_update( $update, $item ) {

		if ( isset( $item->slug ) && $item->slug == 'gravityforms' ) {

			GFCommon::log_debug( 'GFForms::maybe_auto_update() - Starting auto-update for gravityforms.' );

			$auto_update_disabled = self::is_auto_update_disabled();
			GFCommon::log_debug( 'GFForms::maybe_auto_update() - $auto_update_disabled: ' . var_export( $auto_update_disabled, true ) );

			if ( $auto_update_disabled || version_compare( GFForms::$version, $item->new_version, '=>' ) ) {
				GFCommon::log_debug( 'GFForms::maybe_auto_update() - Aborting update.' );
				return false;
			}

			$current_major = implode( '.', array_slice( preg_split( '/[.-]/', GFForms::$version ), 0, 1 ) );
			$new_major     = implode( '.', array_slice( preg_split( '/[.-]/', $item->new_version ), 0, 1 ) );

			$current_branch = implode( '.', array_slice( preg_split( '/[.-]/', GFForms::$version ), 0, 2 ) );
			$new_branch     = implode( '.', array_slice( preg_split( '/[.-]/', $item->new_version ), 0, 2 ) );

			if ( $current_major == $new_major && $current_branch == $new_branch ) {
				GFCommon::log_debug( __METHOD__ . '() - OK to update.' );
				return true;
			} else {
				GFCommon::log_debug( __METHOD__ . '() - Aborting update. Not on the same major version.' );
				return false;
			}
		}

		return $update;
	}

	/**
	 * Checks if automatic updates are disabled
	 *
	 * Called from GFForms::maybe_auto_update
	 *
	 * @access public
	 * @static
	 * @see GFForms::maybe_auto_update
	 *
	 * @return bool True if auto update is disabled.  False otherwise.
	 */
	public static function is_auto_update_disabled() {

		// Currently WordPress won't ask Gravity Forms to update if background updates are disabled.
		// Let's double check anyway.

		// WordPress background updates are disabled if you don't want file changes.
		if ( defined( 'DISALLOW_FILE_MODS' ) && DISALLOW_FILE_MODS ) {
			return true;
		}

		if ( defined( 'WP_INSTALLING' ) ) {
			return true;
		}

		$wp_updates_disabled = defined( 'AUTOMATIC_UPDATER_DISABLED' ) && AUTOMATIC_UPDATER_DISABLED;

		/**
		 * Overrides the WordPress AUTOMATIC_UPDATER_DISABLED constant.
		 *
		 * @param bool $wp_updates_disabled True if disables.  False otherwise.
		 */
		$wp_updates_disabled = apply_filters( 'automatic_updater_disabled', $wp_updates_disabled );

		if ( $wp_updates_disabled ) {
			GFCommon::log_debug( __METHOD__ . '() - Background updates are disabled in WordPress.' );
			return true;
		}

		// Now check Gravity Forms Background Update Settings

		$enabled = get_option( 'gform_enable_background_updates' );
		GFCommon::log_debug( 'GFForms::is_auto_update_disabled() - $enabled: ' . var_export( $enabled, true ) );

		/**
		 * Filter to disable Gravity Forms Automatic updates
		 *
		 * @param bool $enabled Check if automatic updates are enabled, and then disable it
		 */
		$disabled = apply_filters( 'gform_disable_auto_update', ! $enabled );
		GFCommon::log_debug( 'GFForms::is_auto_update_disabled() - $disabled: ' . var_export( $disabled, true ) );

		if ( ! $disabled ) {
			$disabled = defined( 'GFORM_DISABLE_AUTO_UPDATE' ) && GFORM_DISABLE_AUTO_UPDATE;
			GFCommon::log_debug( 'GFForms::is_auto_update_disabled() - GFORM_DISABLE_AUTO_UPDATE: ' . var_export( $disabled, true ) );
		}

		return $disabled;
	}

	public static function deprecate_add_on_methods() {
		if ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ( defined( 'DOING_CRON' ) && DOING_CRON ) || ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) ) {
			return;
		}
		$deprecated = GFAddOn::get_all_deprecated_protected_methods();
		if ( ! empty( $deprecated ) ) {
			foreach ( $deprecated as $method ) {
				_deprecated_function( $method, '1.9', 'public access level' );
			}
		}
	}

	/**
	 * Shortcode UI
	 */

	/**
	 * Output a shortcode
	 *
	 * Called via AJAX
	 * Used for displaying the shortcode in the TinyMCE editor
	 *
	 * @access public
	 * @static
	 * @global $post
	 */
	public static function handle_ajax_do_shortcode() {

		$shortcode = ! empty( $_POST['shortcode'] ) ? sanitize_text_field( stripslashes( $_POST['shortcode'] ) ) : null;
		$post_id   = ! empty( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : null;

		if ( ! current_user_can( 'edit_post', $post_id ) || ! wp_verify_nonce( $_POST['nonce'], 'gf-shortcode-ui-preview' ) ) {
			echo esc_html__( 'Error', 'gravityforms' );
			exit;
		}

		$form_id   = ! empty( $_POST['form_id'] ) ? intval( $_POST['form_id'] ) : null;

		global $post;
		$post = get_post( $post_id );
		setup_postdata( $post );

		self::enqueue_form_scripts( $form_id, true );
		wp_print_scripts();
		wp_print_styles();

		echo do_shortcode( $shortcode );

		// Disable the elements on the form
		?>
		<script type="text/javascript">
			jQuery('.gform_wrapper input, .gform_wrapper select, .gform_wrapper textarea').prop('disabled', true);
			jQuery('a img').each(function () {
				var image = this.src;
				var img = jQuery('<img>', { src: image });
				$(this).parent().replaceWith(img);
			});
			jQuery('a').each(function () {
				jQuery(this).replaceWith(jQuery(this).text());
			});
		</script>
		<?php
		exit;
	}

	/**
	 * Displays the shortcode editor
	 *
	 * Called by the print_media_templates action
	 *
	 * @access public
	 * @static
	 * @see Action: print_media_templates
	 * @see GFForms::get_view
	 */
	public static function action_print_media_templates() {

		echo GFForms::get_view( 'edit-shortcode-form' );
	}

	/**
	 * Gets the view and loads the appropriate template
	 *
	 * Called from GFForms::action_print_media_templates
	 *
	 * @access public
	 * @static
	 * @see GFForms::action_print_media_templates
	 *
	 * @param string $template The template to be loaded
	 *
	 * @return mixed The contents of the template file.
	 */
	public static function get_view( $template ) {

		if ( ! file_exists( $template ) ) {

			$template_dir  = GFCommon::get_base_path() . '/includes/templates/';
			$template = $template_dir . $template . '.tpl.php';

			if ( ! file_exists( $template ) ) {
				return '';
			}
		}

		ob_start();
		include $template;

		return ob_get_clean();
	}

	/**
	 * Modifies the TinyMCE editor styling
	 *
	 * Called from the tiny_mce_before_init filter
	 *
	 * @access public
	 * @static
	 * @see Filter: tiny_mce_before_init
	 *
	 * @param array $init Init data passed from the tiny_mce_before_init filter
	 *
	 * @return array $init Data after filtering
	 */
	public static function modify_tiny_mce_4( $init ) {

		// Hack to fix compatibility issue with ACF PRO
		if ( ! isset( $init['content_css'] ) ) {
			return $init;
		}

		$base_url = GFCommon::get_base_url();

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';

		$editor_styles = $base_url . "/css/shortcode-ui-editor-styles{$min}.css,";
		$form_styles = $base_url . "/css/formsmain{$min}.css";

		if ( isset( $init['content_css'] ) ) {
			if ( empty( $init['content_css'] ) ) {
				$init['content_css'] = '';
			} elseif ( is_array( $init['content_css'] ) ) {
				$init['content_css'][] = $editor_styles;
				$init['content_css'][] = $form_styles;
				return $init;
			} else {
				$init['content_css'] = $init['content_css'] . ',';
			}
		}

		// Note: Using .= here can trigger a fatal error
		$init['content_css'] = $init['content_css'] . $editor_styles . $form_styles;
		return $init;
	}

	/**
	 * Gets the available shortcode attributes
	 *
	 * @access public
	 * @static
	 *
	 * @return array $shortcodes Shortcode attributes
	 */
	public static function get_shortcodes() {

		$forms             = RGFormsModel::get_forms( 1, 'title' );
		$forms_options[''] = __( 'Select a Form', 'gravityforms' );
		foreach ( $forms as $form ) {
			$forms_options[ absint( $form->id ) ] = $form->title;
		}

		$default_attrs = array(
			array(
				'label'       => __( 'Select a form below to add it to your post or page.', 'gravityforms' ),
				'tooltip'     => __( 'Select a form from the list to add it to your post or page.', 'gravityforms' ),
				'attr'        => 'id',
				'type'        => 'select',
				'section'     => 'required',
				'description' => __( "Can't find your form? Make sure it is active.", 'gravityforms' ),
				'options'     => $forms_options,
			),
			array(
				'label'   => __( 'Display form title', 'gravityforms' ),
				'attr'    => 'title',
				'default' => 'true',
				'section' => 'standard',
				'type'    => 'checkbox',
				'tooltip' => __( 'Whether or not to display the form title.', 'gravityforms' )
			),
			array(
				'label'   => __( 'Display form description', 'gravityforms' ),
				'attr'    => 'description',
				'default' => 'true',
				'section' => 'standard',
				'type'    => 'checkbox',
				'tooltip' => __( 'Whether or not to display the form description.', 'gravityforms' )
			),
			array(
				'label'   => __( 'Enable AJAX', 'gravityforms' ),
				'attr'    => 'ajax',
				'section' => 'standard',
				'type'    => 'checkbox',
				'tooltip' => __( 'Specify whether or not to use AJAX to submit the form.', 'gravityforms' )
			),
			array(
				'label'   => 'Tabindex',
				'attr'    => 'tabindex',
				'type'    => 'number',
				'tooltip' => __( 'Specify the starting tab index for the fields of this form.', 'gravityforms' )
			),

		);

		/**
		 * Filters through the shortcode builder actions (ajax, tabindex, form title) for adding a new form to a post, page, etc.
		 *
		 * @param array() Array of additional shortcode builder actions.  Empty by default.
		 */
		$add_on_actions = apply_filters( 'gform_shortcode_builder_actions', array() );

		if ( ! empty( $add_on_actions ) ) {
			$action_options = array( '' => __( 'Select an action', 'gravityforms' ) );
			foreach ( $add_on_actions as $add_on_action ) {
				foreach ( $add_on_action as $key => $array ) {
					$action_options[ $key ] = $array['label'];
				}
			}

			$default_attrs[] = array(
				'label'   => 'Action',
				'attr'    => 'action',
				'type'    => 'select',
				'options' => $action_options,
				'tooltip' => __( 'Select an action for this shortcode. Actions are added by some add-ons.', 'gravityforms' )
			);
		}

		$shortcode = array(
			'shortcode_tag' => 'gravityform',
			'action_tag'    => '',
			'label'         => 'Gravity Forms',
			'attrs'         => $default_attrs,
		);

		$shortcodes[] = $shortcode;

		if ( ! empty( $add_on_actions ) ) {
			foreach ( $add_on_actions as $add_on_action ) {
				foreach ( $add_on_action as $key => $array ) {
					$attrs     = array_merge( $default_attrs, $array['attrs'] );
					$shortcode = array(
						'shortcode_tag' => 'gravityform',
						'action_tag'    => $key,
						'label'         => rgar( $array, 'label' ),
						'attrs'         => $attrs,
					);
				}
			}
			$shortcodes[] = $shortcode;
		}

		return $shortcodes;
	}

	/**
	 * Enqueues scripts needed to display the form
	 *
	 * @access public
	 * @static
	 * @see GFFormDisplay::enqueue_form_scripts
	 * @see GFAddOn::get_registered_addons
	 *
	 * @param string $form_id The displayed form ID
	 * @param bool   $is_ajax True if form uses AJAX.  False otherwise.
	 */
	public static function enqueue_form_scripts( $form_id, $is_ajax = false ) {
		require_once( GFCommon::get_base_path() . '/form_display.php' );
		$form = RGFormsModel::get_form_meta( $form_id );
		GFFormDisplay::enqueue_form_scripts( $form, $is_ajax );
		$addons = GFAddOn::get_registered_addons();
		foreach ( $addons as $addon ) {
			$a = call_user_func( array( $addon, 'get_instance' ) );
			$a->enqueue_scripts( $form, $is_ajax );
		}
	}

	/**
	 * Displays the installation wizard on single site installations and on multisite
	 *
	 * @access public
	 * @static
	 *
	 * @return bool Was the installation wizard displayed?
	 */
	public static function maybe_display_installation_wizard() {

		if ( defined( 'GF_LICENSE_KEY' ) && is_multisite() && ! is_main_site() ) {
			return false;
		}

		$pending_installation = get_option( 'gform_pending_installation' ) || isset( $_GET['gform_installation_wizard'] );

		if ( $pending_installation ) {
			require_once( GFCommon::get_base_path() . '/includes/wizard/class-gf-installation-wizard.php' );
			$wizard = new GF_Installation_Wizard;
			$result = $wizard->display();
			return $result;
		}
	}

	/**
	 * Sets the screen options for the entry list.
	 *
	 * Called from the set-screen-option filter.
	 *
	 * @since 2.0
	 * @access public
	 * @static
	 * @see Filter: set-screen-option
	 *
	 * @param bool|int $status Screen option value.  Not used.  Defaults to false.
	 * @param string   $option The option to check
	 * @param int      $value  The number of rows to display per page
	 *
	 * @return array $return The filtered data
	 */
	public static function set_screen_options( $status, $option, $value ) {
		$return = false;
		if ( $option == 'gform_entries_screen_options' ) {
			$return                   = array();
			$return['default_filter'] = sanitize_key( rgpost( 'gform_default_filter' ) );
			$return['per_page']       = sanitize_key( rgpost( 'gform_per_page' ) );
		} elseif ( $option = 'gform_forms_per_page' ) {
			$return = $value;
		}

		return $return;
	}

	/**
	 * Returns the markup for the screen options for the entry list.
	 *
	 * Called by the screen_settings filter
	 *
	 * @since 2.0
	 * @access public
	 * @static
	 * @see Filter: screen_settings
	 * @see GFEntryList::get_screen_options_markup
	 *
	 * @param string $status The current screen settings
	 * @param object $args   WP_Screen object
	 *
	 * @return string $return The filtered screen settings
	 */
	public static function show_screen_options( $status, $args ) {

		$return = $status;

		if ( self::get_page() == 'entry_list' ) {
			require_once( GFCommon::get_base_path() . '/entry_list.php' );
			$return = GFEntryList::get_screen_options_markup( $status, $args );
		}

		return $return;
	}

	/**
	 * Loads the screen options for the entry detail page.
	 *
	 * @since 2.0
	 * @access public
	 * @static
	 * @see GFEntryDetail::add_meta_boxes
	 */
	public static function load_screen_options() {
		$screen = get_current_screen();

		if ( ! is_object( $screen ) ) {
			return;
		}

		$page = GFForms::get_page();

		if ( $page == 'form_list' ) {
			$args = array(
				'label' => __( 'Forms per page', 'gravityforms' ),
				'default' => 20,
				'option' => 'gform_forms_per_page',
			);
			add_screen_option( 'per_page', $args );
		} elseif ( in_array( $page, array( 'entry_detail', 'entry_detail_edit' ) ) ) {

			require_once( GFCommon::get_base_path() . '/entry_detail.php' );

			GFEntryDetail::add_meta_boxes();
		}
	}

	/**
	 * Daily cron task. Target for the gravityforms_cron action.
	 *
	 * - Performs self-healing
	 * - Adds empty index files
	 * - Deletes unclaimed export files.
	 * - Deleted old log files.
	 * - Deletes orphaned entry rows from the lead table.
	 *
	 * @since 2.0.0
	 * @access public
	 * @static
	 * @see GFForms::add_security_files
	 * @see GFForms::delete_old_export_files
	 * @see GFForms::delete_old_log_files
	 * @see GFForms::do_self_healing
	 * @see GFForms::delete_orphaned_entries
	 */
	public static function cron() {

		GFCommon::log_debug( __METHOD__ . '(): Starting cron.' );

		self::add_security_files();

		self::delete_old_export_files();

		self::delete_old_log_files();

		self::do_self_healing();

		self::delete_orphaned_entries();

		GFCommon::log_debug( __METHOD__ . '(): Done.' );
	}

	/**
	 * Deletes all entry export files from the server that haven't been claimed within 24 hours.
	 *
	 * @since 2.0.0
	 * @access public
	 * @static
	 */
	public static function delete_old_export_files() {
		GFCommon::log_debug( __METHOD__ . '(): Starting.' );
		$uploads_folder = RGFormsModel::get_upload_root();
		if ( ! is_dir( $uploads_folder ) || is_link( $uploads_folder ) ) {
			GFCommon::log_debug( __METHOD__ . '(): No upload root - bailing.' );
			return;
		}
		$export_folder = $uploads_folder . 'export';
		if ( ! is_dir( $export_folder ) || is_link( $export_folder ) ) {
			GFCommon::log_debug( __METHOD__ . '():  No export root - bailing.' );
			return;
		}
		GFCommon::log_debug( __METHOD__ . '(): Start deleting old export files' );
		foreach ( glob( $export_folder . DIRECTORY_SEPARATOR . '*.csv', GLOB_BRACE ) as $filename ) {
			$timestamp = filemtime( $filename );
			if ( $timestamp < time() - DAY_IN_SECONDS ) {
				// Delete files over a day old
				GFCommon::log_debug( __METHOD__ . '(): Proceeding to delete ' . $filename );
				$success = unlink( $filename );
				GFCommon::log_debug( __METHOD__ . '(): Delete successful: ' . ( $success ? 'yes' : 'no' ) );
			}
		}
	}

	/**
	 * Deletes any log files that are older than one month.
	 *
	 * @since 2.0.0
	 * @access public
	 * @static
	 */
	public static function delete_old_log_files() {
		GFCommon::log_debug( __METHOD__ . '(): Starting.' );
		$uploads_folder = RGFormsModel::get_upload_root();
		if ( ! is_dir( $uploads_folder ) || is_link( $uploads_folder ) ) {
			GFCommon::log_debug( __METHOD__ . '(): No upload root - bailing.' );
			return;
		}
		$logs_folder = $uploads_folder . 'logs';
		if ( ! is_dir( $logs_folder ) || is_link( $logs_folder ) ) {
			GFCommon::log_debug( __METHOD__ . '():  No logs folder - bailing.' );
			return;
		}
		GFCommon::log_debug( __METHOD__ . '(): Start deleting old log files' );
		foreach ( glob( $logs_folder . DIRECTORY_SEPARATOR . '*.txt', GLOB_BRACE ) as $filename ) {
			$timestamp = filemtime( $filename );
			if ( $timestamp < time() - MONTH_IN_SECONDS ) {
				// Delete files over one month old
				GFCommon::log_debug( __METHOD__ . '(): Proceeding to delete ' . $filename );
				$success = unlink( $filename );
				GFCommon::log_debug( __METHOD__ . '(): Delete successful: ' . ( $success ? 'yes' : 'no' ) );
			}
		}
	}

	/**
	 * Deletes all rows in the lead table that don't have corresponding rows in the details table.
	 *
	 * @since 2.0.0
	 * @access public
	 * @static
	 * @global $wpdb
	 */
	public static function delete_orphaned_entries() {
		global $wpdb;
		GFCommon::log_debug( __METHOD__ . '(): Starting to delete orphaned entries' );
		$lead_table = GFFormsModel::get_lead_table_name();
		$lead_details_table = GFFormsModel::get_lead_details_table_name();
		$sql = "DELETE FROM {$lead_table} WHERE id NOT IN( SELECT lead_id FROM {$lead_details_table} )";
		$result = $wpdb->query( $sql );
		GFCommon::log_debug( __METHOD__ . '(): Delete result: ' . print_r( $result, true ) );
	}

	/**
	 * Hooked into the 'admin_head' action.
	 *
	 * Outputs the styles for the Forms Toolbar menu.
	 * Outputs gf vars if required.
	 *
	 * @since 2.0.1.2
	 */
	public static function load_admin_bar_styles() {

		if ( ! get_option( 'gform_enable_toolbar_menu' ) ) {
			return;
		}

		if ( ! GFCommon::current_user_can_any( array( 'gravityforms_edit_forms', 'gravityforms_create_form', 'gravityforms_preview_forms', 'gravityforms_view_entries' ) ) ) {
			// The current user can't use anything on the menu so bail.
			return;
		}

		?>
		<style>
			.gforms-menu-icon{
				float: left;
				width: 26px!important;
				height: 30px!important;
				background-repeat: no-repeat;
				background-position: 0 6px;
				background-size: 20px;
			}

			@media screen and ( max-width: 782px ) {
				#wpadminbar #wp-admin-bar-gform-forms .ab-item {
					line-height: 53px;
					height: 46px !important;
					width: 52px !important;
					display: block;
					background-size: 36px 36px;
					background-position: 7px 6px;
				}
				#wpadminbar li#wp-admin-bar-gform-forms {
					display: block;
				}

			}
		</style>
		<?php

	}
}

/**
 * Class RGForms
 *
 * @deprecated
 * Exists only for backwards compatibility.
 */
class RGForms extends GFForms { }

/**
 * Main Gravity Forms function call.
 *
 * Should be used to insert a Gravity Form from code.
 *
 * @param string     $id                  The form ID
 * @param bool       $display_title       If the form title should be displayed in the form. Defaults to true.
 * @param bool       $display_description If the form description should be displayed in the form. Defaults to true.
 * @param bool       $display_inactive    If the form should be displayed if marked as inactive. Defaults to false.
 * @param array|null $field_values        Default field values. Defaults to null.
 * @param bool       $ajax                If submission should be processed via AJAX. Defaults to false.
 * @param int        $tabindex            Starting tabindex. Defaults to 1.
 * @param bool       $echo                If the field should be echoed.  Defaults to true.
 *
 * @return string|void
 */
function gravity_form( $id, $display_title = true, $display_description = true, $display_inactive = false, $field_values = null, $ajax = false, $tabindex = 1, $echo = true ) {
	if ( ! $echo ) {
		return RGForms::get_form( $id, $display_title, $display_description, $display_inactive, $field_values, $ajax, $tabindex );
	}

	echo RGForms::get_form( $id, $display_title, $display_description, $display_inactive, $field_values, $ajax, $tabindex );
}

/**
 * Enqueues form scripts for the specified form.
 *
 * Passes data off to GFForms::enqueue_form_scripts
 *
 * @see GFForms::enqueue_form_scripts
 *
 * @param string $form_id The form ID.
 * @param bool   $is_ajax If the form is submitted via AJAX.  Defaults to false.
 */
function gravity_form_enqueue_scripts( $form_id, $is_ajax = false ) {
	GFForms::enqueue_form_scripts( $form_id, $is_ajax );
}

if ( ! function_exists( 'rgget' ) ) {
	/**
	 * Helper function for getting values from query strings or arrays
	 *
	 * @param string $name  The key
	 * @param array  $array The array to search through.  If null, checks query strings.  Defaults to null.
	 *
	 * @return string The value.  If none found, empty string.
	 */
	function rgget( $name, $array = null ) {
		if ( ! isset( $array ) ) {
			$array = $_GET;
		}

		if ( isset( $array[ $name ] ) ) {
			return $array[ $name ];
		}

		return '';
	}
}

if ( ! function_exists( 'rgpost' ) ) {
	/**
	 * Helper function to obtain POST values
	 *
	 * @param string $name            The key
	 * @param bool   $do_stripslashes Optional. Performs stripslashes_deep.  Defaults to true.
	 *
	 * @return string The value.  If none found, empty string.
	 */
	function rgpost( $name, $do_stripslashes = true ) {
		if ( isset( $_POST[ $name ] ) ) {
			return $do_stripslashes ? stripslashes_deep( $_POST[ $name ] ) : $_POST[ $name ];
		}

		return '';
	}
}

if ( ! function_exists( 'rgar' ) ) {
	/**
	 * Get a specific property of an array without needing to check if that property exists. Provide a default value if
	 * you want to return a specific value if the property is not set.
	 *
	 * @param array  $array   Array from which the property's value should be retrieved.
	 * @param string $prop    Name of the property to be retrieved.
	 * @param string $default Optional. Value that should be returned if the property is not set or empty. Defaults to null.
	 *
	 * @return null|string|mixed The value
	 */
	function rgar( $array, $prop, $default = null ) {

		if ( ! is_array( $array ) && ! ( is_object( $array ) && $array instanceof ArrayAccess ) ) {
			return $default;
		}

		if ( isset( $array[ $prop ] ) ) {
			$value = $array[ $prop ];
		} else {
			$value = '';
		}

		return empty( $value ) && $default !== null ? $default : $value;
	}
}

if ( ! function_exists( 'rgars' ) ) {
	/**
	 * Gets a specific property within a multidimensional array.
	 *
	 * @see rgar
	 *
	 * @param array  $array The array to search in
	 * @param string $name  The name of the property to find.
	 * @param string $default Optional. Value that should be returned if the property is not set or empty. Defaults to null.
	 *
	 * @return null|string|mixed The value
	 */
	function rgars( $array, $name, $default = null ) {

		if ( ! is_array( $array ) && ! ( is_object( $array ) && $array instanceof ArrayAccess ) ) {
			return $default;
		}

		$names = explode( '/', $name );
		$val   = $array;
		foreach ( $names as $current_name ) {
			$val = rgar( $val, $current_name, $default );
		}

		return $val;
	}
}

if ( ! function_exists( 'rgempty' ) ) {
	/**
	 * Determines if a value is empty
	 *
	 * @param string $name The property name to check
	 * @param array $array Optional. An array to check through.  Otherwise, checks for POST variables.
	 *
	 * @return bool True if empty.  False otherwise.
	 */
	function rgempty( $name, $array = null ) {

		if ( is_array( $name ) ) {
			return empty( $name );
		}

		if ( ! $array ) {
			$array = $_POST;
		}

		$val = rgar( $array, $name );

		return empty( $val );
	}
}

if ( ! function_exists( 'rgblank' ) ) {
	/**
	 * Checks if the string is empty
	 *
	 * @param string $text The string to check
	 *
	 * @return bool True if empty.  False otherwise.
	 */
	function rgblank( $text ) {
		return empty( $text ) && strval( $text ) != '0';
	}
}

if ( ! function_exists( 'rgobj' ) ) {
	/**
	 * Gets a property value from an object
	 *
	 * @param object $obj  The object to check
	 * @param string $name The property name to check for
	 *
	 * @return string The property value
	 */
	function rgobj( $obj, $name ) {
		if ( isset( $obj->$name ) ) {
			return $obj->$name;
		}

		return '';
	}
}
if ( ! function_exists( 'rgexplode' ) ) {
	/**
	 * Converts a delimiter separated string to an array
	 *
	 * @param string $sep    The delimiter between values
	 * @param string $string The string to convert
	 * @param int    $count  The expected number of items in the resulting array
	 *
	 * @return array $ary The exploded array
	 */
	function rgexplode( $sep, $string, $count ) {
		$ary = explode( $sep, $string );
		while ( count( $ary ) < $count ) {
			$ary[] = '';
		}

		return $ary;
	}
}

if ( ! function_exists( 'gf_apply_filters' ) ) {
	//function gf_apply_filters( $filter, $modifiers, $value ) {
	/**
	 * Gravity Forms pre-processing for apply_filters
	 *
	 * Allows additional filters based on form and field ID to be defined easily.
	 *
	 * @param string $filter The name of the filter
	 * @param mixed  $value  The value to filter
	 *
	 * @return mixed The filtered value.
	 */
	function gf_apply_filters( $filter, $value ) {

		$args = func_get_args();

		if( is_array( $filter ) ) {
			// func parameters are: $filter, $value
			$modifiers = array_splice( $filter, 1, count( $filter ) );
			$filter    = $filter[0];
			$args      = array_slice( $args, 2 );
		} else {
			//_deprecated_argument( 'gf_apply_filters', '1.9.14.20', "Modifiers should no longer be passed as a separate parameter. Combine the filter name and modifier(s) into an array and pass that array as the first parameter of the function. Example: gf_apply_filters( array( 'action_name', 'mod1', 'mod2' ), \$value, \$arg1, \$arg2 );" );
			// func parameters are: $filter, $modifier, $value
			$modifiers = ! is_array( $value ) ? array( $value ) : $value;
			$value     = $args[2];
			$args      = array_slice( $args, 3 );
		}

		// add an empty modifier so the base filter will be applied as well
		array_unshift( $modifiers, '' );

		$args = array_pad( $args, 10, null );

		// apply modified versions of filter
		foreach ( $modifiers as $modifier ) {
			$modifier = empty( $modifier ) ? '' : sprintf( '_%s', $modifier );
			$filter  .= $modifier;
			$value    = apply_filters( $filter, $value, $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9] );
		}

		return $value;
	}
}

if ( ! function_exists( 'gf_do_action' ) ) {
	/**
	 * Gravity Forms pre-processing for do_action
	 *
	 * Allows additional actions based on form and field ID to be defined easily.
	 *
	 * @since 1.9.14.20 Modifiers should no longer be passed as a separate parameter.
	 * @since 1.9.12
	 *
	 * @param string $action The action
	 */
	function gf_do_action( $action ) {

		$args = func_get_args();

		if( is_array( $action ) ) {
			// Func parameters are: $action, $value
			$modifiers = array_splice( $action, 1, count( $action ) );
			$action    = $action[0];
			$args      = array_slice( $args, 1 );
		} else {
			//_deprecated_argument( 'gf_do_action', '1.9.14.20', "Modifiers should no longer be passed as a separate parameter. Combine the action name and modifier(s) into an array and pass that array as the first parameter of the function. Example: gf_do_action( array( 'action_name', 'mod1', 'mod2' ), \$arg1, \$arg2 );" );
			// Func parameters are: $action, $modifier, $value
			$modifiers = ! is_array( $args[1] ) ? array( $args[1] ) : $args[1];
			$args      = array_slice( $args, 2 );
		}

		// Add an empty modifier so the base filter will be applied as well
		array_unshift( $modifiers, '' );

		$args = array_pad( $args, 10, null );

		// Apply modified versions of filter
		foreach ( $modifiers as $modifier ) {
			$modifier = empty( $modifier ) ? '' : sprintf( '_%s', $modifier );
			$action  .= $modifier;
			do_action( $action, $args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9] );
		}
	}
}
