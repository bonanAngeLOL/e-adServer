<?php
defined("BASEPATH") or exit ("No directy access allowed");
class Section extends CI_Controller{
	public function __construct(){
		parent::__construct();
        $this->load->model(array('sections'));
        $this->load->library(array("memcached"));
	}
	public function inWebsite($website){
        if($this->input->method()!="get"){
            $this->output->set_status_header(405);
            return false;
        }
        if(!is_numeric($website)){
        	$this->output->set_status_header(400);
        	echo json_encode((Object)["error"=>"Invalid website identifier"]);
        	return false;
        }
        $result = json_encode($this->sections->inWebsite($website));
        echo $result;
        return true;
	}
}