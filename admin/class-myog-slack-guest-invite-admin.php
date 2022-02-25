<?php

//require_once '..\..\includes'. DIRECTORY_SEPARATOR .'slack-interface'. DIRECTORY_SEPARATOR .'class-slack.php';
//require_once '..\..\includes'. DIRECTORY_SEPARATOR .'slack-interface'. DIRECTORY_SEPARATOR .'class-slack-access.php';
//require_once '..\..\includes'. DIRECTORY_SEPARATOR .'slack-interface'. DIRECTORY_SEPARATOR .'class-slack-api-exception.php';
use Slack_Interface\Slack;
use Slack_Interface\Slack_API_Exception;
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       myog.io
 * @since      1.0.0
 *
 * @package    Myog_Slack_Guest_Invite
 * @subpackage Myog_Slack_Guest_Invite/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Myog_Slack_Guest_Invite
 * @subpackage Myog_Slack_Guest_Invite/admin
 * @author     Myog.io <contact@myog.io>
 */
class Myog_Slack_Guest_Invite_Admin
{

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action('admin_menu', array($this, 'add_to_menu'));
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Myog_Slack_Guest_Invite_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Myog_Slack_Guest_Invite_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/myog-slack-guest-invite-admin.css', array(), $this->version, 'all');

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Myog_Slack_Guest_Invite_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Myog_Slack_Guest_Invite_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/myog-slack-guest-invite-admin.js', array('jquery'), $this->version, false);

	}
	public static function do_action($slack, $action)
	{
		$result_message = '';
		switch ($action) {
				// Handles the OAuth callback by exchanging the access code to
				// a valid token and saving it in a file
			case 'oauth':
				$code = $_GET['code'];
					// Exchange code to valid access token
				try {
					$access = $slack->do_oauth($code, get_site_url() . '/wp-admin/options-general.php?page=slack_settings&action=oauth');
					if ($access) {

						$team = $slack->get_team_info();
						$result_message = 'The application was successfully added to your Slack channel';
					}
				} catch (Slack_API_Exception $e) {
					$result_message = $e->getMessage();
				}
				break;
				// Sends a notification to Slack

			case 'save_config':
				$CLIENT_ID = $_POST['CLIENT_ID'];
				$CLIENT_SECRET = $_POST['CLIENT_SECRET'];
				if(!empty($CLIENT_ID) && !empty($CLIENT_SECRET)){
					update_option('myog_slack_client_id',$CLIENT_ID);
					update_option('myog_slack_client_secret',$CLIENT_SECRET);
					$result_message = 'Saved with success, <a href="/wp-admin/options-general.php?page=slack_settings">refresh the page</a>';
				}else{
					$result_message = "Please Fill Correct Client ID and Secret";
				}
				break;
			default:
				break;
		}
		return $result_message;

	}
	public static function edit_slack_settings()
	{

		$slack = new Slack();
		
		if (isset($_REQUEST['action'])) {
			$action = $_REQUEST['action'];
			$result_message = self::do_action($slack, $action);
			if ($result_message) {
				?>
					<div class="notice notice-success is-dismissible">
						<p><?php echo $result_message ?></p>
					</div>
				<?php

			}
		}
		$redirect_plain_url = get_site_url() . '/wp-admin/options-general.php?page=slack_settings&action=oauth';
		$redirect_url = urlencode($redirect_plain_url);

		?>
			
			<div class="wrap">
				<h2><?php echo __('Slack Invite Settings', 'myog-slack-guest-invite') ?></h2>
				<?php if (!empty($slack->get_client_id())) : ?>
					<p>
						Click on the button bellow to authorize the application<br/>
						<a href="https://slack.com/oauth/authorize?client_id=<?php echo $slack->get_client_id(); ?>&scope=admin,channels:write,channels:read&redirect_uri=<?php echo $redirect_url ?>">
							<img alt="Add to Slack" height="40" width="139" src="https://platform.slack-edge.com/img/add_to_slack.png" srcset="https://platform.slack-edge.com/img/add_to_slack.png 1x, https://platform.slack-edge.com/img/add_to_slack@2x.png 2x" />
						</a>
						
					</p>
					<?php if ($slack->is_authenticated() && $slack->get_team()) : ?>
						<p>You have already authorized this application, to the team <b><?php echo $slack->get_team()->get_name(); ?></b></p>
					<?php endif; ?>
				<?php endif; ?>
				<form method="post">
					<div class="form-wrap">
						<input type="hidden" name="action" value="save_config">
                            <table class="form-table">
                                <tbody>
                                    <tr>
                                        <th scope="row">CLIENT_ID</th>
                                        <td>
                                            <input type="password" name="CLIENT_ID" id="CLIENT_ID" value="<?php echo get_option('myog_slack_client_id'); ?>" >
                                        </td>
                                    </tr>
									<tr>
                                        <th scope="row">CLIENT_SECRET</th>
                                        <td>
                                            <input type="password" name="CLIENT_SECRET" id="CLIENT_SECRET" value="<?php echo get_option('myog_slack_client_secret'); ?>" >
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="form-field">
                                <?php echo submit_button("Save"); ?>
                            </div>
                        </div>
                    </form>			
				</form>
				<br><br>
				<h2>How to make it work</h2>
				<p>
					To make this plugin work you will have to create your own application with slack!
				</p>
				<ol>
					<li>On your Slack Workspace create a public chat room, where the guests will be joining</li>
					<li>First log into your slack account and go to <a href="https://api.slack.com/apps" target="_BLANK">https://api.slack.com/apps</a>, click on <b>"Create New App"</b> </li>
					<li>Name your App (can be any name), and choose the Workspace you want to be able to invite new members</li>
					<li>Change Permissions on the APP, click on "OAuth & Permissions"
						<ul>
							<li>Add this link as a redirect url <pre><?php echo $redirect_plain_url; ?></pre></li>
							<li>On Scopes choose <pre>admin,channel:write,channels:read</pre><li>
						</ul>
					</li>
					<li>On the "Basic Information" tab yuou will get the <b>CLIENT ID</b> and <b>CLIENT SECRET</b> you can either:
						<ul>
							<li>Fill the form with the CLIENT ID and CLIENT SECRET, but it's saved as plain text and anyone with access to you WP Wordpress can se it</p>
							<li>Or you can set on your wp_config.php
							<pre>define( 'SLACK_CLIENT_ID', 'XXXXXXXXXXXXX.XXXXXXXXXXXXXXX' );
define( 'SLACK_CLIENT_SECRET', 'XXXXXXXXXXXXXXXXXXXXXXX' );</pre>
							</li>
						</ul>
					</li>
					<li>After having that informaiton yuou be able to click on "Add to Slack" Button and authorize your application</li>
					<li>With the application authorized, insert the short-code [slack_invite_form channels=channel-name] (only one channel name) in one of your pages, or popups, and that should work</li>

				</ol>
			</div>
		<?php
	}
	public static function add_to_menu()
	{

		$theme_page = add_submenu_page('options-general.php', __('Slack Invite Settings', 'myog-slack-guest-invite'), __('Slack Invite Settings', 'myog-slack-guest-invite'), 'edit_theme_options', 'slack_settings', array('Myog_Slack_Guest_Invite_Admin', 'edit_slack_settings'));
	}



}
