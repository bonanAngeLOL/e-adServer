<?php 
defined("BASEPATH") or exit ("No direct access allowed");
class userManagement extends CI_Controller{
    public function index(){
	$UserData = array(
	    "first_name" => $this->input->post("fname"),
	    "last_name"  => $this->input->post("lname"),
	    "username"   => $this->input->post("uname"),
	    "email"      => $this->input->post("email"),
	    "password"	 => $this->input->post("password")
	);i
    }
} 
