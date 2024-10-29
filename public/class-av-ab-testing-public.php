<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.autovisie.nl
 * @since      1.0.0
 *
 * @package    Av_Ab_Testing
 * @subpackage Av_Ab_Testing/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * @package    Av_Ab_Testing
 * @subpackage Av_Ab_Testing/public
 * @author     melvr
 */
class Av_Ab_Testing_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The option name
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $option_name
	 */
	private $option_name = 'av_ab_testing';

	/**
	 * Get the general views setting
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      int    $general_views
	 */
	private $general_views = 500;


	/**
	 * Get the general decision setting
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      int    $general_views
	 */
	private $general_decision = 50;

	/**
	 * Get the use js setting
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      int    $use_js
	 */
	private $use_js = null;

	/**
	 * Get the hide titles setting
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      int    $_hide_titles
	 */
	private $hide_titles = null;

	/**
	 * The text domain
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $text-domain
	 */
	private $text_domain = 'av-ab-testing';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->general_views = ( get_option( $this->option_name . '_general_views', false ) != "") ? get_option( $this->option_name . '_general_views', false ) : $this->general_views;
		$this->general_decision = ( get_option( $this->option_name . '_general_decision', false ) != "") ? get_option( $this->option_name . '_general_decision', false ) : $this->general_decision;

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/av-ab-testing-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'av_ab_test', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));
	}

	/**
	 * Add post id to head so we can use it for the rest of the JS
	 */
	public function add_post_id_to_head(){
		if( is_404() ){
			return;
		}

		global $post;

		$hide_titles = ( $this->hide_titles() ) ? 'true' : 'false';

		echo '
		<script type="text/javascript">
		  var ab_post_id = "' . $post->ID . '";
		  var ab_hide_titles = ' . $hide_titles . ';
		</script>
		';
	}

	/**
	 * Add JS containing the cookie to the footer
	 */
	public function add_cookie_to_content(){
		include_once plugin_dir_path( dirname( __FILE__ ) ) .  '/public/partials/av-ab-testing-public-display.php';
	}

	/**
	 * Get the titles by ajax call
	 */
	public function get_ajax_titles(){
		$info = array();

		if ( isset( $_POST['post_ids'] ) ) {
			$this->use_js = false;
			$single_item = ( isset( $_POST['single_item'] ) && $_POST['single_item'] == "true" ) ? true : false;

			foreach( $_POST['post_ids'] as $post_id ){
				$post_id = esc_html( $post_id );
				$post = get_post( $post_id );

				$title = $this->show_title( $post->post_title, $post_id, $single_item );
				$info[$post_id] = $title;
			}
		}

		echo json_encode( $info, JSON_UNESCAPED_UNICODE );

		$this->use_js = null;
		die();
	}

	/**
	 * Show the correct title
	 *
	 * @param $title
	 * @param $id
	 * @return bool|mixed
	 */
	public function show_title( $title, $id, $single_item = false, $raw = false ){
		$use_js = $this->use_js();
		$hide_titles = $this->hide_titles();

		$wrap_title = ( $use_js && !$raw ) ? "<span class='ab-title-box ab-title-" . $id . "' data-id='" . $id . "'>%s</span>" : '%s';
		
		$post_title_b = get_post_meta($id, $this->option_name . '_meta_box_title_b', true);

		if( !$post_title_b ){
			return $title;
		}

		if( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ){
			return $title;
		}

		$decision_made = $this->title_choice_made( $id, true );

		if( $decision_made != false ){
			return sprintf( $wrap_title, $decision_made );
		}

		if( is_feed() ){
			return sprintf( $wrap_title, $title );
		}

		$cookie_title = sprintf( 'wordpress_title_%s', $id );
		$views_cookie = sprintf( 'wordpress_views_type_%s', $id );
		$cookie_value = $this->getCookie( $cookie_title );

		if( empty( $use_js ) && $single_item ){
			$this->register_view( $views_cookie, $id );
		}

		if( $cookie_value ){
			return sprintf( $wrap_title, $cookie_value );
		}

		if( $post_title_b ) {
			if (rand(1, 2) == 1) {
				$this->setCookie($cookie_title, $post_title_b);
				$this->setCookie($views_cookie, 'title_b');
				$title = $post_title_b;
			} else {
				$this->setCookie($cookie_title, $title);
				$this->setCookie($views_cookie, 'title_a');
			}
		}

		return sprintf( $wrap_title, $title );
	}


	/**
	 * Show header title
	 *
	 * @param $title
	 * @param $post
	 * @return bool|mixed
	 */
	public function show_header_title( $title = false, $post = null ){
		if ( empty($post) || is_null( $post ) || !$post ) {
			$post = $GLOBALS['wp_query']->get_queried_object();
		}

		if( $title && $post ){
			return $this->show_title( $title, $post->ID, false, true );
		}

		return $title;
	}

	/**
	 * Register the view in the post and a cookie to prevent double view registration
	 */
	public function register_view( $views_cookie, $post_id ){
		if ( $views_cookie && $post_id ) {
			$a_views = $this->option_name . '_meta_box_title_a_views';
			$b_views = $this->option_name . '_meta_box_title_b_views';

			$current_view = $this->getCookie( $views_cookie );

			$viewed_cookie = sprintf( 'wordpress_viewed_%s', $post_id );
			$viewed_cookie_exists = $this->getCookie( $viewed_cookie );

			if( $this->title_choice_made( $post_id ) || $this->not_register_view() ){
				if( !$viewed_cookie_exists ){
					$this->setCookie( $viewed_cookie, '1' );
				}
				return false;
			}

			if( !$viewed_cookie_exists && $current_view ){
				$update_views = false;
				$current_value = false;

				if( $current_view == 'title_a' ){
					$current_value = (int) get_post_meta( $post_id, $a_views, true );
					$update_views = update_post_meta( $post_id, $a_views, ( $current_value + 1 ) );
					$this->make_title_choice( ( $current_value + 1 ), $current_view, $post_id );
				}

				if( $current_view == 'title_b' ){
					$current_value = (int) get_post_meta($post_id, $b_views, true );
					$update_views = update_post_meta( $post_id, $b_views, ( $current_value + 1 ) );
				}

				if( $update_views ){
					$this->setCookie( $viewed_cookie, '1' );
				}

				if( $current_value ){
					$this->make_title_choice( ( $current_value + 1 ), $current_view, $post_id );
				}

				return true;
			}

		}

		return false;
	}

	/**
	 * Check if the title has been chosen already
	 *
	 * @param $post_id
	 * @param $return_title
	 * @return bool|string
	 */
	protected function title_choice_made( $post_id, $return_title = false ){
		if(!$post_id){
			return false;
		}

		$title_choice = $this->option_name . '_title_choice';
		$title_choice_value = ( get_post_meta( $post_id, $title_choice, true ) );

		if( !$return_title ){
			return ( empty( $title_choice_value ) ) ? false : true;
		}

		if( !$title_choice_value || empty( $title_choice_value ) ){
			return false;
		}

		if( $title_choice_value == 'title_a' ){
			$post = get_post( $post_id );
			return isset( $post->post_title ) ? $post->post_title : '';
		}

		if( $title_choice_value == 'title_b' ){
			return get_post_meta( $post_id, $this->option_name . '_meta_box_title_b', true );
		}

		return false;
	}

	/**
	 * Do we need to make a decision?
	 *
	 * IDEA: Move this function to a cron?
	 *
	 * @param $current_views_amount
	 * @param $title
	 * @param $post_id
	 * @return bool|int
	 */
	protected function make_title_choice( $current_views_amount, $title, $post_id ){
		$choice_value = $this->option_name . '_title_choice';
		$current_views_amount = $this->calculate_percentage( $current_views_amount );
		$decision = $this->general_decision;

		if( $current_views_amount >= $decision ){
			$update = update_post_meta( $post_id, $choice_value, $title );
			$post_link = get_the_permalink( $post_id );
			$title_chosen = $this->title_choice_made( $post_id, true );
			$message = sprintf( __( 'Title for post %s (id: %s) chosen: %s (%s)', $this->text_domain ), $post_link, $post_id, $title_chosen, $title );

			do_action(
				$this->option_name . '_title_choice',
				array(
					'post_id' => $post_id,
					'message' => $message
				)
			);

			if( $this->slack_plugin_active() ){
				$slack = new Av_Slack_Notifications_Messages();
				$slack->send_notification( $message );
			}

			return $update;
		}

		return false;
	}

	/**
	 * Calculate the total percentage based on the current views amount
	 *
	 * @param bool $current_views_amount
	 * @return float|int
	 */
	protected function calculate_percentage( $current_views_amount = false ){
		if( !$current_views_amount || empty( $current_views_amount ) || is_null( $this->general_views ) ){
			return 0;
		}

		$general_views_amount = ( (int)$this->general_views / 100 );
		$current_views_amount = (int)$current_views_amount;

		if($general_views_amount > 0 && $current_views_amount > 0 ){
			return ( $current_views_amount / $general_views_amount );
		}

		return 0;
	}

	/**
	 * Set a cookie value
	 *
	 * @since 1.0.0
	 * @param $name
	 * @param $value
	 * @param int $time
	 * @return bool
	 */
	protected function setCookie( $name = false, $value = false, $time = 0 ){
		if( !$name || !$value ){
			return false;
		}

		$name = esc_html( $name );
		$value = esc_attr( $value );

		$secure = ( 'https' === parse_url( site_url(), PHP_URL_SCHEME ) && 'https' === parse_url( home_url(), PHP_URL_SCHEME ) );
		return setcookie( $name, $value, $time, COOKIEPATH, COOKIE_DOMAIN, $secure );
	}


	/**
	 * Get a cookie value
	 *
	 * @since 1.0.0
	 * @param $name
	 * @return bool
	 */
	protected function getCookie( $name ){
		return ( isset( $_COOKIE[$name] ) ) ? esc_attr( $_COOKIE[$name] ) : false;
	}

	/**
	 * Delete a cookie
	 *
	 * @since 1.0.0
	 * @param $name
	 * @return bool
	 */
	protected function deleteCookie( $name ){
		if( $this->getCookie( $name ) ) {
			unset( $_COOKIE[$name] );
			$this->setCookie( $name, '' );

			return true;
		}

		return false;
	}

	/**
	 * Get the use js setting
	 *
	 * @return int|mixed|void
	 */
	protected function use_js(){
		if( is_null( $this->use_js ) ){
			$this->use_js = get_option( $this->option_name . '_use_js' );
		}

		return $this->use_js;
	}

	/**
	 * Get the hide titles setting
	 *
	 * @return int|mixed|void
	 */
	protected function hide_titles(){
		if( is_null( $this->hide_titles ) ){
			$this->hide_titles = get_option( $this->option_name . '_hide_titles' );
		}

		return $this->hide_titles;
	}

	/**
	 * Check if the slack plugin is active
	 *
	 * @return bool
	 */
	protected function slack_plugin_active(){
		return is_plugin_active( 'av-slack-notifications/av-slack-notifications.php' );
	}

	/**
	 * Loggedin user and can edit post?
	 *
	 * @return bool
	 */
	protected function not_register_view(){
		if( !is_user_logged_in() ){
			return false;
		}

		return current_user_can( 'edit_posts' );
	}

}
