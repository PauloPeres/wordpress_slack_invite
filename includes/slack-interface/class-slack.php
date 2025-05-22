<?php
namespace Slack_Interface;

use Requests;

/**
 * A basic Slack interface you can use as a starting point
 * for your own Slack projects.
 *
 * @author Jarkko Laine <jarkko@jarkkolaine.com>
 */
class Slack {

	private static $api_root = 'https://slack.com/api/';
	private $api_team_url = null;
	/**
	 * @var Slack_Access    Slack authorization data
	 */
	private $access;
	/**
	 * @var Slack_Team    Slack authorization data
	 */
	private $team;

	/**
	 * @var array $slash_commands An array of slash commands attached to this Slack interface
	 */
	private $slash_commands;

	/**
	 * Sets up the Slack interface object.
	 *
	 * @param array $access_data An associative array containing OAuth
	 *                           authentication information. If the user
	 *                           is not yet authenticated, pass an empty array.
	 */
	public function __construct( ) {
		if(get_option( 'myog_slack_access')){
			
			$access_data =  json_decode( get_option( 'myog_slack_access'), true );
			$this->access = new Slack_Access( $access_data );
		}
		if(get_option('myog_slack_team')) {
			
			$team_data = json_decode( get_option( 'myog_slack_team' ), true);
			$this->team = new Slack_Team($team_data);
			$this->api_team_url = 'https://' . $this->get_team()->get_domain() . '.slack.com/api/';
			
		}

		$this->slash_commands = array();
	}

	/**
	 * Checks if the Slack interface was initialized with authorization data.
	 *
	 * @return bool True if authentication data is present. Otherwise false.
	 */
	public function is_authenticated() {
		return isset( $this->access ) && $this->access->is_configured();
	}
	public function get_access() {
		return $this->access;
	}
	public function get_api_url($api_call) {
		return $this->api_team_url ? $this->api_team_url.$api_call : self::$api_root.$api_call;
	}
	private function get_resquest_headers($is_json = false, $merge_header_array =[]){
		$headers = array( 'Accept' => 'application/json' );
		$token = $this->access->get_access_token();

		if($token) $headers['Authorization'] = "Bearer $token";

		if($is_json){
			$headers['Content-Type'] = "application/json; charset=utf-8";
		} else {
			$headers['Content-Type'] = "application/x-www-form-urlencoded";
		}

		
		return array_merge(
			$headers, 
			$merge_header_array
		);
	}
	private function get_request_options(){
		return array( 'auth' => array( $this->get_client_id(), $this->get_client_secret() ) );
	}
	

	/**
	 * Completes the OAuth authentication flow by exchanging the received
	 * authentication code to actual authentication data.
	 *
	 * @param string $code  Authentication code sent to the OAuth callback function
	 *
	 * @return bool|Slack_Access    An access object with the authentication data in place
	 *                              if the authentication flow was completed successfully.
	 *                              Otherwise false.
	 *
	 * @throws Slack_API_Exception
	 */
	public function do_oauth( $code , $redir_url = null) {
		// Add the one-time token to request parameters
		$data = array( 'code' => $code );
		$data['client_secret'] = $this->get_client_secret();
		$data['client_id'] = $this->get_client_id();
		$data['single_channel'] = true;
		if($redir_url){
			$data['redirect_uri'] = $redir_url; 
		}

               $response = Requests::post( self::$api_root . 'oauth.v2.access', $this->get_resquest_headers(), $data, $this->get_request_options() );

		// Handle the JSON response
		$json_response = json_decode( $response->body );
	
		if ( ! $json_response->ok ) {
			// There was an error in the request
			throw new Slack_API_Exception( $json_response->error );
		}

		// The action was completed successfully, store and return access data
               $team_name = '';
               $team_id   = '';
               if ( isset( $json_response->team ) ) {
                       $team_name = $json_response->team->name;
                       $team_id   = $json_response->team->id;
               } elseif ( isset( $json_response->team_name ) ) {
                       $team_name = $json_response->team_name;
                       $team_id   = $json_response->team_id;
               }

               $this->access = new Slack_Access(
                       array(
                               'access_token' => $json_response->access_token,
                               'scope' => isset( $json_response->scope ) ? explode( ',', $json_response->scope ) : array(),
                               'team_name' => $team_name,
                               'team_id' => $team_id,
                               'incoming_webhook' => isset($json_response->incoming_webhook)? $json_response->incoming_webhook : null
                       )
               );
		update_option( 'myog_slack_access',$this->access->to_json());
		return $this->access;
	}
	public function get_team(){
		return $this->team;
	}
	public function get_team_info(){
		if ( ! $this->is_authenticated() ) {
			throw new Slack_API_Exception( 'Access token not specified' );
		}
		$data = array( 
			'token' => $this->access->get_access_token(),
		);
		$response = Requests::post( self::$api_root . 'team.info', $this->get_resquest_headers(), $data);

		// Handle the JSON response
		$json_response = json_decode( $response->body );
		
		if ( ! $json_response->ok ) {
			// There was an error in the request
			throw new Slack_API_Exception( $json_response->error );
		}
		$team = $json_response->team;
		$this->team = new Slack_Team(
			array(
				'id' => $team->id,
				'name' => $team->name,
				'domain' => $team->domain,
				'email_domain' => $team->email_domain,
				'icons' => isset($team->icon) ? $team->icon : null,
				'enterprise_id' => isset($team->enterprise_id) ? $team->enterprise_id : null,
				'enterprise_name' => isset($team->enterprise_name) ? $team->enterprise_name : null
			)
		);
		update_option( 'myog_slack_team',$this->team->to_json());
		return $this->team;
	}
	public function get_channels($channels) {
		if( ! $this->is_authenticated() ) {
			throw new Slack_API_Exception( 'Access token not specified' );
		}
		$data = array( 
			'token' => $this->access->get_access_token(),
		);
		$response = Requests::post( $this->get_api_url('conversations.list'), $this->get_resquest_headers(), $data);
		$json_response = json_decode( $response->body );
		if( ! $json_response->ok ){
			throw new Slack_API_Exception( $json_response->error );
		}
		// TODO Paginate
		$clist = explode(',',$channels); 
		$cids = array();
		foreach ($json_response->channels as $key => $ch) {
			if($ch->is_private == false){
				if(in_array($ch->name, $clist)){
					$cids[] = $ch->id;
				}
			}
		}
		return $cids;

	}
	public function send_invite($email = null, $channels = null, $first_name = null, $last_name = null,$resend=false,$restricted = true, $ultra_restricted=true) {
		if ( ! $this->is_authenticated() ) {
			throw new Slack_API_Exception( 'Access token not specified' );
		}
		
		$cids = $this->get_channels($channels);
		
		$data = array(
			'email'=>$email,
			'channels' => trim(implode(",",$cids),","),
			'real_name'=>$first_name && $last_name? "$first_name $last_name" : null,
			'first_name'=>$first_name,
			'last_name'=>$last_name,
			'team_id'=>$this->get_team()->get_id(),
			'resend' => $resend,
			'ultra_restricted' => $ultra_restricted ? true:  null,
			'restricted' => $restricted && !$ultra_restricted? true : null,
			
		);
		foreach ($data as $key => $value) {
			if($value == null){
				unset($data[$key]);
			}
		}
		 $json = json_encode($data);
     $response = Requests::post(
             $this->get_api_url('admin.users.invite'),
             $this->get_resquest_headers(false),
             $data,
     );
		
		// Handle the JSON response
		$json_response = json_decode( $response->body );
		if ( ! $json_response->ok ) {
			// There was an error in the request
			$message = $json_response->error;
			switch($message){
				case 'requires_channel':
					$message = 'Please Include a channel parameter to your short code, with only one channel';
					break;
				case 'paid_teams_only':
					$message = 	'Inviting Guests to your channel is Only for users who <a href="https://slack.com/plans" target="_NEW">pay for Slack</a> paid_teams_only';
					break;
				case 'already_invited':
					$message = 	'User has already received an email invitation';
					break;
				case 'already_in_team':
					$message = 	'User is already part of the team';
					break;
				case 'channel_not_found':
					$message = 	'Provided channel ID does not match a real channel';
					break;
				case 'sent_recently':
					$message = 	'The Invite email has been sent recently, please check your email inbox or use another email';
					break;
				case 'user_disabled':
					$message = 	'User account has been deactivated';
					break;
				case 'missing_scope':
					$message = 	'Using an access_token not authorized for \'client\' scope';
					break;
				case 'invalid_email':
					$message = 	'Invalid email address (e.g. "qwe"). Note that Slack does not recognize some email addresses even though they are technically valid. This is a known issue.';
					break;
				case 'not_allowed':
					$message = 	'When SSO is enabeld this method can not be used to invite new users except guests. The SCIM API needs to be used instead to invite new users. For inviting guests the restricted or ultra_restricted property needs to be provided';
					break;
				case 'not_allowed_token_type':
					$message = 	'Token type is invalid. Workspace tokens do not seem to be compatible with this method';
					break;
				case 'requires_one_channel':
					$message = 	'When ultra_restricted is true and no channel is provided. A single channel must be provided.';
					break;
				case 'not_authed':
					$message = 	'No authentication token provided.';
					break;
				default:
					break;
			}
			throw new Slack_API_Exception( $message );
		}
		return $json_response;

	}

	/**
	 * Sends a notification to the Slack channel defined in the
	 * authorization (Add to Slack) flow.
	 *
	 * @param string $text          The message to post to Slack
	 * @param array $attachments    Optional list of attachments to send
	 *                              with the notification
	 *
	 * @throws Slack_API_Exception
	 */
	public function send_notification( $text, $attachments = array() ) {
		if ( ! $this->is_authenticated() ) {
			throw new Slack_API_Exception( 'Access token not specified' );
		}

		

		$url = $this->access->get_incoming_webhook();
		$data = json_encode(
			array(
				'text' => $text,
				'attachments' => $attachments,
				'channel' => $this->access->get_incoming_webhook_channel()
			)
		);

		$response = Requests::post( $url, $this->get_resquest_headers(), $data );

		if ( $response->body != 'ok' ) {
			throw new Slack_API_Exception( 'There was an error when posting to Slack' );
		}
	}

	/**
	 * Registers a new slash command to be available through this
	 * Slack interface.
	 *
	 * @param string    $command    The slash command
	 * @param callback  $callback   The function to call to execute the command
	 */
	public function register_slash_command( $command, $callback ) {
		$this->slash_commands[$command] = $callback;
	}

	/**
	 * Runs the slash command passed in the $_POST data if the
	 * command is valid and has been registered using register_slash_command.
	 *
	 * The response written by the function will be read by Slack.
	 */
	public function do_slash_command() {
		// Collect request parameters
		$token      = isset( $_POST['token'] ) ? $_POST['token'] : '';
		$command    = isset( $_POST['command'] ) ? $_POST['command'] : '';
		$text       = isset( $_POST['text'] ) ? $_POST['text'] : '';
		$user_name  = isset( $_POST['user_name'] ) ? $_POST['user_name'] : '';

		// Use the command verification token to verify the request
		if ( ! empty( $token ) && $this->get_command_token() == $_POST['token'] ) {
			header( 'Content-Type: application/json' );

			if ( isset( $this->slash_commands[$command] ) ) {
				// This slash command exists, call the callback function to handle the command
				$response = call_user_func( $this->slash_commands[$command], $text, $user_name );

			} else {
				// Unknown slash command
				echo json_encode( array(
					'text' => "Sorry, I don't know how to respond to the command."
				) );
			}
		} else {
			echo json_encode( array(
				'text' => 'Oops... Something went wrong.'
			) );
		}

		// Don't print anything after the response
		exit;
	}

	/**
	 * Returns the Slack client ID.
	 *
	 * @return string   The client ID or empty string if not configured
	 */
	public function get_client_id() {
		// First, check if client ID is defined in a constant
		if ( defined( 'SLACK_CLIENT_ID' ) ) {
			return SLACK_CLIENT_ID;
		}

		// If no constant found, look for environment variable
		if ( getenv( 'SLACK_CLIENT_ID' ) ) {
			return getenv( 'SLACK_CLIENT_ID' );
		}

		// If nothing found look for the option
		if ( get_option('myog_slack_client_id') ){
			return get_option('myog_slack_client_id');
		}
		
		// Not configured, return empty string
		return '';
	}

	/**
	 * Returns the Slack client secret.
	 *
	 * @return string   The client secret or empty string if not configured
	 */
	private function get_client_secret() {
		// First, check if client secret is defined in a constant
		if ( defined( 'SLACK_CLIENT_SECRET' ) ) {
			return SLACK_CLIENT_SECRET;
		}

		// If no constant found, look for environment variable
		if ( getenv( 'SLACK_CLIENT_SECRET' ) ) {
			return getenv( 'SLACK_CLIENT_SECRET' );
		}

		// If nothing found look for the option
		if ( get_option('myog_slack_client_secret') ){
			return get_option('myog_slack_client_secret');
		}

		// Not configured, return empty string
		return '';
	}

	/**
	 * Returns the command verification token.
	 *
	 * @return string   The command verification token or empty string if not configured
	 */
	private function get_command_token() {
		// First, check if command token is defined in a constant
		if ( defined( 'SLACK_COMMAND_TOKEN' ) ) {
			return SLACK_COMMAND_TOKEN;
		}

		// If no constant found, look for environment variable
		if ( getenv( 'SLACK_COMMAND_TOKEN' ) ) {
			return getenv( 'SLACK_COMMAND_TOKEN' );
		}

		// Not configured, return empty string
		return '';
	}

}