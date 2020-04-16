<?php 
defined("BASEPATH") or exit ("No direct access allowed");
class UserManagement extends CI_Controller{
	public function __construct(){
		parent::__construct();
		$this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->model(array('users'));
        $this->load->library('encryption');
	}
    public function addUser(){

    	if($this->input->method()!="post"){
    		$this->output->set_status_header(405);
    		return false;
    	}
    	$this->form_validation->set_rules('first_name', 'First name', 'trim|required', array());
    	$this->form_validation->set_rules('last_name', 'Last_name', 'trim|required');
    	$this->form_validation->set_rules('username', 'Username', 'trim|required');
    	$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
    	$this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');

    	if(!$this->form_validation->run()){
            echo json_encode($this->form_validation->error_array());
    		$this->output->set_status_header(400);
    		return false;
    	}

		$UserData = array(
			"first_name" => $this->input->post("first_name"),
			"last_name"  => $this->input->post("last_name"),
			"username"   => $this->input->post("username"),
			"email"      => $this->input->post("email"),
			"password"	 => $this->input->post("password")
		);
        //var_dump($UserData);
		$newUser = $this->users->newUser($UserData);
		if(is_string($newUser)&strpos($newUser,"Duplicate")!==false){
            echo json_encode(array("error"=>$newUser));
            $this->output->set_status_header(400);
            return false;
        }
        unset($UserData["password"]);
        $UserData["userID"] = $newUser;
        echo json_encode($UserData);
        return true;
    }
    public function deleteUser($id){
        if($this->input->method()!="delete"){
            $this->output->set_status_header(405);
            return false;
        }
        if(!is_numeric($id) || !isset($id)){
            echo json_encode(array("error"=>"Valid ID is required ".is_int($id)));
            $this->output->set_status_header(400);
            return false;
        }
        $confirm = $this->users->deleteUser(["id_user"=>$id]);
        if($confirm==0){
            echo json_encode(["error"=>"Invalid ID"]);
            $this->output->set_status_header(400);
            return false;
        }
        echo json_encode(["message"=>"User has been deleted","id"=>$id]);
        return true;
    }
}