<?php
defined("BASEPATH") or exit ("No direct access allowed");
class Login extends CI_Controller{

	private $key = "Test_password_for_JWT_GFDG_@%&Jflsdfg'¿?df°!";

	function __construct(){
		parent::__construct();

		$this->load->model(array('users'));
		$this->load->helper(array('form', 'url'));
		$this->load->library(array('jwtload','form_validation'));
	}

	function auth(){
    	if($this->input->method()!="post"){
    		$this->output->set_status_header(405);
    		return false;
    	}
    	$this->form_validation->set_rules("username","User name","trim|required");
    	$this->form_validation->set_rules("password","password","trim|required");

    	if(!$this->form_validation->run()){
            echo json_encode($this->form_validation->error_array());
    		$this->output->set_status_header(400);
    		return false;
    	}
    	$userInfo = $this->users->userLogin($this->input->post("username")) ?? [];
    	if(
    		count($userInfo)==0 || 
			!password_verify($this->input->post("password"),$userInfo[0]->password)
    	)
    	{
    		$this->output->set_status_header(401);
			echo json_encode(array("error"=>"Incorrect credentials"));
    		return false;
    	}
		unset($userInfo[0]->password);

		echo $this->jwtload->encode($userInfo,$this->key);
		return true;
	}
	public function check(){
		$JWTtoken = $this->input->get_request_header('Authorization');
		$decodedToken = $this->jwtload->decode("eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.W3siZmlyc3RfbmFtZSI6ImFkbWluIiwibGFzdF9uYW1lIjoiZS1jb25zdWx0YSIsImVtYWlsIjoic2VydmVyQGUtY29uc3VsdGEuY29tIiwidXNlcm5hbWUiOiJlQWRtaW4iLCJpYXQiOiIxNTg3MTY2NDg4IiwiZXhwIjoiMTU4ODAzMDQ4OCJ9XQ.60U4_3ukAnwXqp517b0xRJXWIM1tT_00CCZWwjGa_2U",$this->key,array('HS256'));
		var_dump($decodedToken);
	}
}