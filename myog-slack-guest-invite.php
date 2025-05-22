<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              myog.io
 * @since             1.0.0
 * @package           Myog_Slack_Guest_Invite
 *
 * @wordpress-plugin
 * Plugin Name:       Slack Guest Invite
 * Plugin URI:        https://bitbucket.org/myowngames/slack-guest-invite/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.1.0
 * Author:            Myog.io
 * Author URI:        myog.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       myog-slack-guest-invite
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


require_once plugin_dir_path( __FILE__ ) . 'includes'. DIRECTORY_SEPARATOR .'slack-interface'. DIRECTORY_SEPARATOR .'class-slack.php';
require_once plugin_dir_path( __FILE__ ) . 'includes'. DIRECTORY_SEPARATOR .'slack-interface'. DIRECTORY_SEPARATOR .'class-slack-access.php';
require_once plugin_dir_path( __FILE__ ) . 'includes'. DIRECTORY_SEPARATOR .'slack-interface'. DIRECTORY_SEPARATOR .'class-slack-team.php';
require_once plugin_dir_path( __FILE__ ) . 'includes'. DIRECTORY_SEPARATOR .'slack-interface'. DIRECTORY_SEPARATOR .'class-slack-api-exception.php';
/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'MYOG_SLACK_GUEST_INVITE_VERSION', '1.1.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-myog-slack-guest-invite-activator.php
 */
function activate_myog_slack_guest_invite() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-myog-slack-guest-invite-activator.php';
	Myog_Slack_Guest_Invite_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-myog-slack-guest-invite-deactivator.php
 */
function deactivate_myog_slack_guest_invite() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-myog-slack-guest-invite-deactivator.php';
	Myog_Slack_Guest_Invite_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_myog_slack_guest_invite' );
register_deactivation_hook( __FILE__, 'deactivate_myog_slack_guest_invite' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-myog-slack-guest-invite.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_myog_slack_guest_invite() {

        if ( ! session_id() ) {
                session_start();
        }

        $plugin = new Myog_Slack_Guest_Invite();
        $plugin->run();

}
run_myog_slack_guest_invite();
