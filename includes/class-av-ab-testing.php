<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.autovisie.nl
 * @since      1.0.0
 *
 * @package    Av_Ab_Testing
 * @subpackage Av_Ab_Testing/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Av_Ab_Testing
 * @subpackage Av_Ab_Testing/includes
 * @author     melvr
 */
class Av_Ab_Testing {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Av_Ab_Testing_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The option name
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $option_name
	 */
	private $option_name = 'av_ab_testing';

	/**
	 * Is the plugin set to active?
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      boolean    $plugin_active
	 */
	private $plugin_active = false;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'av-ab-testing';
		$this->version = '1.0.0';
		$this->plugin_active = ( get_option( $this->option_name . '_general_active', false ) == 1) ? true : false;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Av_Ab_Testing_Loader. Orchestrates the hooks of the plugin.
	 * - Av_Ab_Testing_i18n. Defines internationalization functionality.
	 * - Av_Ab_Testing_Admin. Defines all hooks for the admin area.
	 * - Av_Ab_Testing_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-av-ab-testing-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-av-ab-testing-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-av-ab-testing-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-av-ab-testing-public.php';

		$this->loader = new Av_Ab_Testing_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Av_Ab_Testing_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Av_Ab_Testing_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Av_Ab_Testing_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'av_add_options_page' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'av_register_settings' );

		if( $this->plugin_active ){
			$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'av_add_meta_boxes' );
			$this->loader->add_action( 'save_post', $plugin_admin, 'av_ab_testing_meta_save', 10, 3 );
			$this->loader->add_action( 'edit_form_after_title', $plugin_admin, 'av_after_title_meta_box' );
		}
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Av_Ab_Testing_Public( $this->get_plugin_name(), $this->get_version() );

		if( $this->plugin_active ){
			$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
			$this->loader->add_action( 'wp_head', $plugin_public, 'add_post_id_to_head', 9 );
			$this->loader->add_filter( 'wp_head', $plugin_public, 'add_cookie_to_content', 10 );
			$this->loader->add_action( 'wp_ajax_av_ab_test_get_ajax_titles', $plugin_public, 'get_ajax_titles' );
			$this->loader->add_action( 'wp_ajax_nopriv_av_ab_test_get_ajax_titles', $plugin_public, 'get_ajax_titles' );
			$this->loader->add_action( 'wp_ajax_av_ab_test_register_view', $plugin_public, 'register_view' );
			$this->loader->add_action( 'wp_ajax_nopriv_av_ab_test_register_view', $plugin_public, 'register_view' );
			$this->loader->add_filter( 'the_title', $plugin_public, 'show_title', 10, 2 );
			$this->loader->add_filter( 'single_post_title', $plugin_public, 'show_header_title', 10, 2 );
			$this->loader->add_filter( 'wpseo_title', $plugin_public, 'show_header_title', 10, 2 );
		}

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Av_Ab_Testing_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
