<?php
defined("BASEPATH") or exit ("No direct access allowed");
class Login extends CI_Controller{

	private $key = "Test_password_for_JWT_GFDG_@%&Jflsdfg'¿?df°!";

	function __construct(){
		parent::__construct();

		$this->load->model(array('users'));
		$this->load->helper(array('form', 'url'));
		$this->load->library(array('jwtload','form_validation'));
		$this->output->set_content_type('application/json');
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
    	//$userInfo = $this->users->userLogin($this->input->post("username")) ?? [];
    	$userInfo = array();
    	if($this->input->post("username")){
    		$userInfo = $this->users->userLogin($this->input->post("username"));
    	}
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

		$userBasics = new stdClass();

		$userBasics->first_name = $userInfo[0]->first_name;
		$userBasics->last_name = $userInfo[0]->last_name;
		$userBasics->email = $userInfo[0]->email;
		$userBasics->username = $userInfo[0]->username;

		$token = $this->jwtload->encode($userInfo,$this->key);
		echo json_encode(["token"=>$token,"auth"=>true,"info"=>$userBasics]);
		return true;
	}
	public function check(){
		/*$this->output->set_header("Access-Control-Allow-Origin: ".$_SERVER['REMOTE_ADDR'].':'.$_SERVER['REMOTE_PORT']);
		$this->output->set_header("Access-Control-Allow-Credentials: true");
		$this->output->set_header("Access-Control-Allow-Headers: Authorization");*/

    	if($this->input->method()!="get"){
    		$this->output->set_status_header(405);
    		return false;
    	}
		$JWTtoken = str_replace("Bearer ","",$this->input->get_request_header('Authorization'));
		$decodedToken = "";
		if($JWTtoken == "" || $JWTtoken == null){
			echo json_encode(["error"=>"User authentication is required"]);
    		$this->output->set_status_header(401);
    		return false;
		}
		//$decodedToken = $this->jwtload->decode("eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.W3siZmlyc3RfbmFtZSI6ImFkbWluIiwibGFzdF9uYW1lIjoiZS1jb25zdWx0YSIsImVtYWlsIjoic2VydmVyQGUtY29uc3VsdGEuY29tIiwidXNlcm5hbWUiOiJlQWRtaW4iLCJpYXQiOiIxNTg3MTY2NDg4IiwiZXhwIjoiMTU4ODAzMDQ4OCJ9XQ.60U4_3ukAnwXqp517b0xRJXWIM1tT_00CCZWwjGa_2U",$this->key,array('HS256'));
		try{
			$decodedToken = $this->jwtload->decode($JWTtoken,$this->key,array('HS256'));
		}
		catch(Exception $e){
			$decodedToken = false;
			echo json_encode(["error"=>"Invalid Token"]);
			$this->output->set_status_header(401);
			return $decodedToken;
		}

		if(!(time() > $decodedToken[0]->iat && time() < $decodedToken[0]->exp)){
			echo json_encode(["error"=>"User authentication is required"]);
    		$this->output->set_status_header(401);
    		return false;
		}

		//var_dump($decodedToken);
		//echo time();

		$userInfo = (object) [
			"first_name"=>$decodedToken[0]->first_name,
			"last_name"	=>$decodedToken[0]->last_name,
			"email"		=>$decodedToken[0]->email,
			"username"	=>$decodedToken[0]->username
		];
		echo json_encode(["user"=>$userInfo]);
		return true;
	}
}