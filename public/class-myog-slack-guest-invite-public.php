<?php
use Slack_Interface\Slack;
use Slack_Interface\Slack_API_Exception;
/**
 * The public-facing functionality of the plugin.
 *
 * @link       myog.io
 * @since      1.0.0
 *
 * @package    Myog_Slack_Guest_Invite
 * @subpackage Myog_Slack_Guest_Invite/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Myog_Slack_Guest_Invite
 * @subpackage Myog_Slack_Guest_Invite/public
 * @author     Myog.io <contact@myog.io>
 */
class Myog_Slack_Guest_Invite_Public {
	
	//private $csrf_token_name = 'csrf-slack-guest';
	//private $csrf_token_post = 'CSRFtoken';
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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
		add_shortcode( 'slack_invite_form', [$this,'slack_invite_form_shortcode'] );
		
	}
	public static function get_csrf_token_name() {
		return 'csrf-slack-guest'; 
	}
	public static function get_csrf_token_post() {
		return 'CSRFtoken';
	}
	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/myog-slack-guest-invite-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/myog-slack-guest-invite-public.js', array( 'jquery' ), $this->version, false );

	}
	
	public static function check_post() {
		
		$slack = new Slack();
		if(isset($_POST['myog-slack-invite_slack_email']) && isset($_POST[self::get_csrf_token_post()])){
			if (!wp_verify_nonce($_POST[self::get_csrf_token_post()],self::get_csrf_token_name())){
				header('HTTP/1.0 403 Forbidden');
				echo 'Token invalid!';
				exit;
			}
			
			$email = $_POST['myog-slack-invite_slack_email'];
			if(is_email($email)){
				try {
					$first_name = null;
					$last_name = null;
					if(!empty($_POST['myog-slack-invite_slack_first_name'])) {
						$first_name = $_POST['myog-slack-invite_slack_first_name'];
					}
					if(!empty($_POST['myog-slack-invite_slack_last_name'])) {
						$last_name = $_POST['myog-slack-invite_slack_last_name'];
					}
				
					$result = $slack->send_invite(
						$email, // EMAIL
						$_POST['myog-slack-invite-channels'], 
						$first_name, 
						$last_name,
						true,
						true,
						true
					);
					self::set_success_message("Your Invite has been successfully sent, please check your email!");
				} catch (Slack_API_Exception $e) {
					self::set_error_message($e->getMessage());
				}				
			}else{
				self::set_error_message('Please Enter a Valid Email');
			}
		}
		
	}
	public static function get_error_message(){
		if ( isset( $_SESSION[ 'slack_guest_message_error' ] )) {
			$message = $_SESSION[ 'slack_guest_message_error' ];
			unset( $_SESSION[ 'slack_guest_message_error' ] );
			return $message;
		}
		return null;
	}
	public static function get_success_message(){
		if ( isset( $_SESSION[ 'slack_guest_message_success' ] )) {
			$message = $_SESSION[ 'slack_guest_message_success' ];
			unset( $_SESSION[ 'slack_guest_message_success' ] );
			return $message;
		}
		return null;
	}
	public static function set_error_message($message){
		$_SESSION[ 'slack_guest_message_error' ] = $message;
	}
	public static function set_success_message($message){
		$_SESSION[ 'slack_guest_message_success' ] = $message;
	}
	public static function slack_invite_form_shortcode($attrs) {
		$a = shortcode_atts(array(
			'channels' => 'announcements'
		), $attrs);
		
		$slack = new Slack();
		$success = self::get_success_message();
		$error = self::get_error_message();
		// Creating Hash to validate form
		
		if ( is_user_logged_in() && empty( $email ) ) {
			$current_user = wp_get_current_user();
			$email = $current_user->user_email;
		}
		if (!empty($success)) {
			return "
			<div class=\"myog-slack-invitealert myog-slack-invitealert-success\" id=\"myog-slack-invite_slack\">". stripslashes( $success ) ."</div>
			";
		} else {
			$html = "
				<form class=\"myog-slack-inviteform\" id=\"myog-slack-invite_slack\" name=\"myog-slack-invite_slack\" action=\"\" method=\"post\">
					<input type=\"hidden\" id=\"myog-slack-invite-channels\" name=\"myog-slack-invite-channels\" value=\"".$a['channels']."\">
					<div class=\"myog-slack-join-row\">
						<div class=\"myog-slack-team-logo\">
							<img src=\"". $slack->get_team()->get_icons()['image_original'] ."\" />
						</div>
						<div class=\"myog-slack-plus\">
							+
						</div>
						<div class=\"myog-slack-slack-logo\">
							<img src=\"/wp-content/plugins/slack-guest-invite/public/assets/Slack_Mark_Web.png\" />
						</div>";
						if (!empty($success)) {
							$html .= "<p>Thank you, you received an invite to join <b>". $slack->get_team()->get_name() ."</b> on Slack, please check your email inbox and follow the steps to complete your registration</p>";
						} else {
							$html .= "<p>Join <b>". $slack->get_team()->get_name() . "</b> on Slack</p>";
						}
					$html .="</div>
					<div class=\"myog-slack-form-row g-cols vc_row\">";
						if (empty($success)) {
							$html .= "	
							<div class=\"myog-slack-input vc_col-sm-6 vc_col-xs-12 hidden\">
								<label class=\"myog-slack-inviteform-label\" for=\"myog-slack-invite_slack_email\">First Name</label>
								<input type=\"text\" name=\"myog-slack-invite_slack_first_name\" id=\"myog-slack-invite_slack_first_name\" placeholder=\"First Name\">
							</div>
							<div class=\"myog-slack-input vc_col-sm-6 vc_col-xs-12 hidden\">
								<label class=\"myog-slack-inviteform-label\" for=\"myog-slack-invite_slack_email\">Last Name</label>
								<input type=\"text\" name=\"myog-slack-invite_slack_last_name\" id=\"myog-slack-invite_slack_last_name\" placeholder=\"Last Name\">
							</div>
							<div class=\"myog-slack-input vc_col-sm-12 vc_col-xs-12\">
								<label class=\"myog-slack-inviteform-label\" for=\"myog-slack-invite_slack_email\">Email</label>
								<input type=\"text\" name=\"myog-slack-invite_slack_email\" id=\"myog-slack-invite_slack_email\" placeholder=\"your@email.com\">
							</div>";
							if(isset($error)){
								$html .= "<div class=\"myog-slack-error-input vc_col-sm-12 vc_col-xs-12\">
									<span class=\"myog-slack-invitealert myog-slack-invitealert-error\">".stripslashes( $error )."</span>
								</div>";
							}
							$html .= "
							<div class=\"myog-slack-button vc_col-sm-12 vc_col-xs-12\">
								<button class=\"myog-slack-inviteform-button\" id=\"myog-slack-inviteform-button\"> 
									<span class='idle'>GET MY INVITE</span>
									<span class='loading'><i class=\"fa fa-spinner fa-pulse\"></i> SENDING THE INVITE! </span>
								</button>
							</div>";
						}
					
						$token = wp_create_nonce(self::get_csrf_token_name());
						$html .= "<input name='".self::get_csrf_token_post()."' type='hidden' value='$token'>";
					
					$html .= "</div>
				</form>
			";
			return $html;
		}
		
	}

}
