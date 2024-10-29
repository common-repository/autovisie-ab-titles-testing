<?php

/**
 * The plugin bootstrap file
 *
 *
 * @link              http://www.autovisie.nl
 * @since             1.0.0
 * @package           Av_Ab_Testing
 *
 * @wordpress-plugin
 * Plugin Name:       Autovisie AB Testing
 * Plugin URI:        http://autovisie.nl/devblog/
 * Description:       Plugin used for AB Testing.
 * Version:           1.0.3
 * Author:            melvr
 * Author URI:        http://autovisie.nl/devblog/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       av-ab-testing
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-av-ab-testing-activator.php
 */
function activate_av_ab_testing() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-av-ab-testing-activator.php';
	Av_Ab_Testing_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-av-ab-testing-deactivator.php
 */
function deactivate_av_ab_testing() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-av-ab-testing-deactivator.php';
	Av_Ab_Testing_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_av_ab_testing' );
register_deactivation_hook( __FILE__, 'deactivate_av_ab_testing' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-av-ab-testing.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_av_ab_testing() {

	$plugin = new Av_Ab_Testing();
	$plugin->run();

}
run_av_ab_testing();
