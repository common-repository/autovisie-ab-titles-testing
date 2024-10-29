<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.autovisie.nl
 * @since      1.0.0
 *
 * @package    Av_Ab_Testing
 * @subpackage Av_Ab_Testing/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Av_Ab_Testing
 * @subpackage Av_Ab_Testing/admin
 * @author     melvr
 */
class Av_Ab_Testing_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version
	 */
	private $version;

	/**
	 * The text domain
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $text-domain
	 */
	private $text_domain = 'av-ab-testing';

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
	 * Get the general views setting
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      int    $general_views
	 */
	private $general_views = 500;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->plugin_active = ( get_option( $this->option_name . '_general_active', false ) == 1) ? true : false;
		$this->general_views = ( get_option( $this->option_name . '_general_views', false ) != "") ? get_option( $this->option_name . '_general_views', false ) : null;

	}

	/**
	 * Add an options page under the Settings submenu
	 *
	 * @since  1.0.0
	 */
	public function av_add_options_page() {
		add_options_page(
			__( 'AB Testing Settings', $this->text_domain ),
			__( 'AB Testing Settings', $this->text_domain ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'av_display_options_page' )
		);
	}

	/**
	 * Register the Styles for the admin-facing side of the site.
	 *
	 * @since    1.0.1
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/av-ab-testing.css', array(), $this->version, 'all' );
	}

	/**
	 * Render the options page for plugin
	 *
	 * @since  1.0.0
	 */
	public function av_display_options_page() {
		include_once 'partials/av-ab-testing-admin-display.php';
	}

	/**
	 * Add the general section to the options page
	 *
	 * @since 1.0.0
	 */
	public function av_register_settings(){
		/**
		 * Add the title
		 */
		add_settings_section(
			$this->option_name . '_general',
			__( 'General', $this->text_domain ),
			array( $this, $this->option_name . '_general_description' ),
			$this->plugin_name
		);

		/**
		 * Add the active field
		 */
		add_settings_field(
			$this->option_name . '_general_active',
			__( 'Activated', $this->text_domain ),
			array( $this, $this->option_name . '_general_active' ),
			$this->plugin_name,
			$this->option_name . '_general'
		);

		/**
		 * Add the use js field
		 */
		add_settings_field(
			$this->option_name . '_use_js',
			__( 'Use JS for the titles?', $this->text_domain ),
			array( $this, $this->option_name . '_use_js' ),
			$this->plugin_name,
			$this->option_name . '_general'
		);

		/**
		 * Add the hide titles field
		 */
		add_settings_field(
			$this->option_name . '_hide_titles',
			__( 'Hide titles until JS is done?', $this->text_domain ),
			array( $this, $this->option_name . '_hide_titles' ),
			$this->plugin_name,
			$this->option_name . '_general'
		);

		/**
		 * Add the views input
		 */
		add_settings_field(
			$this->option_name . '_general_views',
			__( 'Amount of views', $this->text_domain ),
			array( $this, $this->option_name . '_general_views' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_general_views' )
		);

		/**
		 * Add the decision input
		 */
		add_settings_field(
			$this->option_name . '_general_decision',
			__( 'Percentage (without the % sign) on which we choose a title', $this->text_domain ),
			array( $this, $this->option_name . '_general_decision' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_general_decision' )
		);

		/**
		 * Add the ab posts setting
		 */
		add_settings_field(
			$this->option_name . '_ab_post_ids',
			__( 'Posts using AB testing', $this->text_domain ),
			array( $this, $this->option_name . '_ab_post_ids' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_ab_post_ids' )
		);

		/**
		 * Register the settings
		 */
		register_setting( $this->plugin_name, $this->option_name . '_general_active' );
		register_setting( $this->plugin_name, $this->option_name . '_use_js' );
		register_setting( $this->plugin_name, $this->option_name . '_hide_titles' );
		register_setting( $this->plugin_name, $this->option_name . '_general_views' );
		register_setting( $this->plugin_name, $this->option_name . '_general_decision' );
		register_setting( $this->plugin_name, $this->option_name . '_ab_post_ids' );
	}

	/**
	 * Render the description for the general section
	 *
	 * @since  1.0.0
	 */
	public function av_ab_testing_general_description() {
		echo '<p>' . __( 'Settings for AB Testing', $this->text_domain ) . '</p>';
	}

	/**
	 * Render the radio input field for active option
	 *
	 * @since  1.0.0
	 */
	public function av_ab_testing_general_active() {
		?>
		<fieldset>
			<label>
				<input type="checkbox" name="<?php echo $this->option_name . '_general_active' ?>" id="<?php echo $this->option_name . '_general_active' ?>" value="1" <?php checked( esc_html( get_option( $this->option_name . '_general_active' ) ), '1' ); ?> />
				<?php _e( 'Yes', $this->text_domain ); ?>
			</label>
		</fieldset>
		<?php
	}

	/**
	 * Render the radio input field for use js option
	 *
	 * @since  1.0.0
	 */
	public function av_ab_testing_use_js() {
		?>
		<fieldset>
			<label>
				<input type="checkbox" name="<?php echo $this->option_name . '_use_js' ?>" id="<?php echo $this->option_name . '_use_js' ?>" value="1" <?php checked( esc_html( get_option( $this->option_name . '_use_js' ) ), '1' ); ?> />
				<?php _e( 'Yes', $this->text_domain ); ?>
			</label>
		</fieldset>
		<?php
	}

	/**
	 * Render the radio input field for hide titles option
	 *
	 * @since  1.0.0
	 */
	public function av_ab_testing_hide_titles() {
		?>
		<fieldset>
			<label>
				<input type="checkbox" name="<?php echo $this->option_name . '_hide_titles' ?>" id="<?php echo $this->option_name . '_hide_titles' ?>" value="1" <?php checked( esc_html( get_option( $this->option_name . '_hide_titles' ) ), '1' ); ?> />
				<?php _e( 'Yes', $this->text_domain ); ?>
			</label>
		</fieldset>
		<?php
	}

	/**
	 * Render the views input for this plugin
	 *
	 * @since  1.0.0
	 */
	public function av_ab_testing_general_views() {
		echo '<input type="text" name="' . $this->option_name . '_general_views' . '" id="' . $this->option_name . '_general_views' . '" value="' . esc_html( get_option( $this->option_name . '_general_views' ) ) . '" />';
	}


	/**
	 * Render the decision input for this plugin
	 *
	 * @since  1.0.0
	 */
	public function av_ab_testing_general_decision() {
		echo '<input type="text" name="' . $this->option_name . '_general_decision' . '" id="' . $this->option_name . '_general_decision' . '" value="' . esc_html( get_option( $this->option_name . '_general_decision' ) ) . '" />%';
	}

	/**
	 * Render the ab post id's for this plugin
	 *
	 * @since  1.0.0
	 */
	public function av_ab_testing_ab_post_ids() {
		$current_post_ids =  get_option( $this->option_name . '_ab_post_ids' );
		$current_post_ids = explode( ',', $current_post_ids );

		if( !empty( $current_post_ids ) ) {
			$col_text = '<col width="%s">';
			$th_text  = '<th colspan="%s">%s</th>';
			$td_text  = '<td class="%s">%s</td>';

			$html = '<table cellpadding="0" cellspacing="0" border="0" class="ab_testing_overview">';
			$html .= sprintf( $col_text, '30%' );
			$html .= sprintf( $col_text, '10%' );
			$html .= sprintf( $col_text, '10%' );
			$html .= sprintf( $col_text, '30%' );
			$html .= sprintf( $col_text, '10%' );
			$html .= sprintf( $col_text, '10%' );
			$html .= sprintf( $col_text, '1%' );

			$html .= '<tr>';
			$html .= sprintf( $th_text, '3', 'Title A' );
			$html .= sprintf( $th_text, '3', 'Title B' );
			$html .= '<th></th>';
			$html .= '</tr>';

			foreach ( $current_post_ids as $current_post_id ) {
				if ( !empty( $current_post_id ) ) {
					$title_a_view = (int)get_post_meta( $current_post_id, $this->option_name . '_meta_box_title_a_views', true );
					$post_title_a = get_the_title( $current_post_id );

					$title_b_view = (int)get_post_meta( $current_post_id, $this->option_name . '_meta_box_title_b_views', true );
					$post_title_b = get_post_meta( $current_post_id, $this->option_name . '_meta_box_title_b', true );

					$title_a_percentage = ($title_a_view) ? round( ($title_a_view / ($title_a_view + $title_b_view) * 100), 2 ) : 0;
					$title_b_percentage = ($title_b_view) ? round( ($title_b_view / ($title_a_view + $title_b_view) * 100), 2 ) : 0;

					$class_a = $this->get_color_percentage($title_a_percentage, $title_b_percentage);
					$class_b = $this->get_color_percentage($title_b_percentage, $title_a_percentage);

					$post_url = get_edit_post_link( $current_post_id );
					$html .= '<tr>';
					$html .= sprintf( $td_text, $class_a, '<a href="' . $post_url . '" target="_blank">' . $post_title_a . '</a>' );
					$html .= sprintf( $td_text, $class_a, $title_a_view . __( ' views', $this->text_domain ) );
					$html .= sprintf( $td_text, $class_a, $title_a_percentage . '%' );
					$html .= sprintf( $td_text, $class_b, '<a href="' . $post_url . '" target="_blank">' . $post_title_b . '</a>' );
					$html .= sprintf( $td_text, $class_b, $title_b_view . __( ' views', $this->text_domain ) );
					$html .= sprintf( $td_text, $class_b, $title_b_percentage . '%' );
					$html .= '</tr>';
				}
			}
			$html .= '</table>';
		}else{
			$html = __( 'No AB testing posts available.', $this->text_domain );
		}

		echo $html;
	}

	/**
	 * Register meta boxes.
	 *
	 * @since  1.0.0
	 */
	public function av_add_meta_boxes() {
		if( $this->plugin_active ) {
			add_meta_box( $this->option_name . '_meta_ab_titles', __( 'AB Testing Titles', $this->text_domain ), array( $this, $this->option_name . '_meta_ab_titles' ), 'post', 'after_title', 'high', null );
			add_meta_box( $this->option_name . '_meta_ab_status', __( 'AB Testing Status', $this->text_domain ), array( $this, $this->option_name . '_meta_ab_status' ), 'post', 'side', 'high', null );
		}
	}

	/**
	 * Add the title meta box after the regular title
	 *
	 * @since 1.0.0
	 */
	public function av_after_title_meta_box() {
		if( $this->plugin_active ) {
			global $post, $wp_meta_boxes;
			do_meta_boxes( get_current_screen(), 'after_title', $post );
		}
	}

	/**
	 * Include the HTML for the meta box titles
	 *
	 * @param $object
	 * @since  1.0.0
	 */
	public function av_ab_testing_meta_ab_titles( $object ){
		wp_nonce_field(basename(__FILE__), "meta-ab-titles-nonce");
		?>
		<div>
			<label for="<?php echo $this->option_name . '_meta_box_title_b' ?>"><?php echo __( 'Title B', $this->text_domain ); ?></label>
			<input type="text" style="width: 100%" name="<?php echo $this->option_name . '_meta_box_title_b' ?>" value="<?php echo esc_html( get_post_meta($object->ID, $this->option_name . '_meta_box_title_b', true) ); ?>" />
		</div>
		<?php
	}

	/**
	 * Include the HTML for the meta box status
	 *
	 * @param $object
	 * @since  1.0.0
	 */
	public function av_ab_testing_meta_ab_status( $object ){
		$title_choice = $this->option_name . '_title_choice';
		$title_choice_value = ( get_post_meta($object->ID, $title_choice, true ) );

		$title_a = (int)get_post_meta( $object->ID, $this->option_name . '_meta_box_title_a_views', true );
		$title_b = (int)get_post_meta( $object->ID, $this->option_name . '_meta_box_title_b_views', true );

		$title_a_percentage = ( $title_a ) ? round( ( $title_a / ( $title_a + $title_b ) * 100 ), 2 ) : 0;
		$title_b_percentage = ( $title_b ) ? round( ( $title_b / ( $title_a + $title_b ) * 100 ), 2 ) : 0;

		?>
		<div>
			<p style="color: <?php echo $this->get_color_percentage( $title_a_percentage, $title_b_percentage ); ?>"><?php echo sprintf( __( '<b>Title A:</b> %s%% (%s views)', $this->text_domain ), $title_a_percentage, $title_a ); ?> <?php echo ( $title_choice_value == 'title_a' ) ? __( '- Chosen Title', $this->text_domain ) : ''; ?></p>
			<p style="color: <?php echo $this->get_color_percentage( $title_b_percentage, $title_a_percentage ); ?>"><?php echo sprintf( __( '<b>Title B:</b> %s%% (%s views)', $this->text_domain ), $title_b_percentage, $title_b ); ?> <?php echo ( $title_choice_value == 'title_b' ) ? __( '- Chosen Title', $this->text_domain ) : ''; ?></p>
			<input type="checkbox" name="reset_ab_counters" title="<?php echo __( 'Reset AB counters', $this->text_domain ) ?>" /> <?php echo __( 'Reset AB counters', $this->text_domain ) ?>
		</div>
		<?php
	}

	protected function av_ab_testing_calculate_percentage( $current_views_amount = false ){
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
	 * Save the meta fields
	 *
	 * @param $post_id
	 * @param $post
	 * @param $update
	 * @return mixed
	 */
	public function av_ab_testing_meta_save( $post_id, $post, $update ){
		if( !$this->plugin_active ) {
			return $post_id;
		}

		if ( !isset( $_POST["meta-ab-titles-nonce"]) || !wp_verify_nonce( $_POST["meta-ab-titles-nonce"], basename(__FILE__) ) ){
			return $post_id;
		}

		if( !current_user_can( "edit_post", $post_id ) ){
			return $post_id;
		}

		if( defined( "DOING_AUTOSAVE" ) && DOING_AUTOSAVE ){
			return $post_id;
		}

		if( $post->post_type != "post" ) {
			return $post_id;
		}

		$title_b_field = $this->option_name . '_meta_box_title_b';
		$title_b = ( isset( $_POST[$title_b_field] ) ) ? $_POST[$title_b_field] : "";
		update_post_meta( $post_id, $title_b_field, $title_b );

		//Save the post id to the setting
		$this->post_id_to_setting( $post_id, $title_b );

		//Reset AB counters
		if( isset( $_POST["reset_ab_counters"] ) ){
			if( $_POST["reset_ab_counters"] == 'on' ){
				$this->reset_all_values( $post_id );
			}
		}
	}

	/**
	 * Save the post id to the setting
	 *
	 * @param bool $post_id
	 * @param bool $title_b
	 * @return bool
	 */
	protected function post_id_to_setting( $post_id = false, $title_b = false ){
		if( !$post_id ){
			return false;
		}

		$post_ids_setting = $this->option_name . '_ab_post_ids';
		$current_post_ids = get_option( $post_ids_setting );
		$current_post_ids = explode( ',', $current_post_ids );
		$post_link = get_the_permalink( $post_id );
		$message = false;

		if( empty( $title_b ) || $title_b == "" ){
			if( $key = array_search( $post_id, $current_post_ids ) ){
				unset( $current_post_ids[$key] );
				$message = sprintf( __( 'AB Testing for post %s (%s) inactive.', $this->text_domain ), $post_link, $post_id );
			}
		}
		else{
			if( !in_array( $post_id, $current_post_ids ) ){
				$current_post_ids[] = $post_id;
				$message = sprintf( __( 'AB Testing for post %s (id: %s) active.', $this->text_domain ), $post_link, $post_id );
			}
		}

		$current_post_ids = implode( ',', array_filter( $current_post_ids ) );

		$save = ( get_option( $post_ids_setting ) !== false ) ? update_option( $post_ids_setting, $current_post_ids ) : add_option( $post_ids_setting, $current_post_ids );

		do_action(
			$this->option_name . '_active_message',
			array(
				'post_id' => $post_id,
				'message' => $message
			)
		);

		if( $message && $this->slack_plugin_active() ){
			$slack = new Av_Slack_Notifications_Messages();
			$slack->send_notification( $message );
		}

		return $save;
	}

	/**
	 * Get the font color based on the percentages and which is =, > or <
	 *
	 * @param $one
	 * @param $two
	 * @return string
	 */
	protected function get_color_percentage( $one, $two ){
		if( $one == $two ){
			return 'blue';
		}

		if( $one > $two ){
			return 'green';
		}

		return 'red';
	}

	/**
	 * Reset all values for title a/b and the choice
	 *
	 * @param $post_id
	 * @return bool
	 */
	protected function reset_all_values( $post_id ){
		if( !$post_id ){
			return false;
		}

		$reset_fields = array(
			$this->option_name . '_title_choice',
			$this->option_name . '_meta_box_title_a_views',
			$this->option_name . '_meta_box_title_b_views',
		);

		foreach( $reset_fields as $reset_field ){
			update_post_meta( $post_id, $reset_field, 0 );
		}

		return true;
	}

	/**
	 * Check if the slack plugin is active
	 *
	 * @return bool
	 */
	protected function slack_plugin_active(){
		return is_plugin_active( 'av-slack-notifications/av-slack-notifications.php' );
	}

}
