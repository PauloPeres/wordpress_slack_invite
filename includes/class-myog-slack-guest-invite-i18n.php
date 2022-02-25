<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       myog.io
 * @since      1.0.0
 *
 * @package    Myog_Slack_Guest_Invite
 * @subpackage Myog_Slack_Guest_Invite/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Myog_Slack_Guest_Invite
 * @subpackage Myog_Slack_Guest_Invite/includes
 * @author     Myog.io <contact@myog.io>
 */
class Myog_Slack_Guest_Invite_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'myog-slack-guest-invite',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
