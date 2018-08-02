<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://mydigitalsauce.com/
 * @since             0.1.0
 * @package           Saucy_Followers
 *
 * @wordpress-plugin
 * Plugin Name:       Saucy Followers
 * Plugin URI:        https://mydigitalsauce.com/
 * Description:       Allows users to follow other users and see updates from users they follow.
 * Version:           0.1.0
 * Author:            MyDigitalSauce
 * Author URI:        https://mydigitalsauce.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       saucy-followers
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 0.1.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SAUCE_FOLLOWERS_VERSION', '0.1.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-saucy-followers-activator.php
 */
function activate_saucy_followers() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-saucy-followers-activator.php';
	Saucy_Followers_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-saucy-followers-deactivator.php
 */
function deactivate_saucy_followers() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-saucy-followers-deactivator.php';
	Saucy_Followers_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_saucy_followers' );
register_deactivation_hook( __FILE__, 'deactivate_saucy_followers' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-saucy-followers.php';

require plugin_dir_path( __FILE__ ) . '/includes/actions/actions.php';
require plugin_dir_path( __FILE__ ) . '/includes/display-helpers/display-helpers.php' ;
require plugin_dir_path( __FILE__ ) . '/includes/follow-helpers/follow-helpers.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.1.0
 */
function run_saucy_followers() {

	$plugin = new Saucy_Followers();
	$plugin->run();

}
run_saucy_followers();
