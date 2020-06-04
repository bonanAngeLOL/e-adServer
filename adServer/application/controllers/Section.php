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

    public function attach($section, $position){
        if($this->input->method()!="post"){
            $this->output->set_status_header(405);
            return false;
        }
        if(!is_numeric($section)&&!is_numeric($position)){
            $this->output->set_status_header(400);
            echo json_encode((Object)["error"=>"Invalid website identifier"]);
            return false;
        }
        $result = json_encode((Object)$this->sections->attach(["section"=>$section,"position"=>$position]));
        echo $result;
        return true;
    }

    public function template($id){
        if($this->input->method()!="get"){
            $this->output->set_status_header(405);
            return false;
        }
        if(!is_numeric($id)){
            $this->output->set_status_header(400);
            echo json_encode((Object)["error"=>"Invalid website identifier"]);
            return false;
        }
        $result = json_encode((Object)$this->sections->getTemplate($id)[0]);
        echo $result;
        return true;
    }
}