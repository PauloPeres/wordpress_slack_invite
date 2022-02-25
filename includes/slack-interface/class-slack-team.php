<?php
namespace Slack_Interface;

/**
 * A class for holding Slack team data.
 *
 * @author Myog 
 */
class Slack_Team {
    private $id;
	private $name;
	private $domain;
	private $email_domain;
    private $icons;
    private $enterprise_id;
    private $enterprise_name;

    public function __construct( $data ) {
        $this->id = isset($data['id']) ? $data['id'] : '';
        $this->name = isset($data['name']) ? $data['name'] : '';
        $this->domain = isset($data['domain']) ? $data['domain'] : '';
        $this->email_domain = isset($data['email_domain']) ? $data['email_domain'] : '';
        $this->icons = isset($data['icons']) ? $data['icons'] : '';
        $this->enterprise_id = isset($data['enterprise_id']) ? $data['enterprise_id'] : '';
        $this->enterprise_name = isset($data['enterprise_name']) ? $data['enterprise_name'] : '';
    }
    
    public function get_id(){
        return $this->id;
    }
    public function set_id($val = '') {
        $this->id = $val;
    }
    public function get_name(){
        return $this->name;
    }
    public function set_name($val = '') {
        $this->name = $val;
    }
    public function get_domain(){
        return $this->domain;
    }
    public function set_domain($val = '') {
        $this->domain = $val;
    }
    public function get_email_domain(){
        return $this->email_domain;
    }
    public function set_email_domain($val = '') {
        $this->email_domain = $val;
    }
    public function get_icons(){
        return $this->icons;
    }
    public function set_icons($val = '') {
        $this->icons = $val;
    }
    public function get_enterprise_id(){
        return $this->enterprise_id;
    }
    public function set_enterprise_id($val = '') {
        $this->enterprise_id = $val;
    }
    public function get_enterprise_name(){
        return $this->enterprise_name;
    }
    public function set_enterprise_name($val = '') {
        $this->enterprise_name = $val;
    }

    public function to_json() {
		$data = array(
            'id' => $this->get_id(),
            'name' => $this->get_name(),
            'domain' => $this->get_domain(),
            'email_domain' => $this->get_email_domain(),
            'icons' => $this->get_icons(),
            'enterprise_id' => $this->get_enterprise_id(),
            'enterprise_name' => $this->get_enterprise_name()
		);

		return json_encode( $data );
	}
}