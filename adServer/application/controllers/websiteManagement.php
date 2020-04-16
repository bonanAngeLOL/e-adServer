<?php
defined("BASEPATH") or die ("No direct access allowed");
class WebsiteManagement extends CI_Controller{
	public function __construct(){
		parent::__construct();
		$this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->model(array('websites'));
	}
	public function add(){
    	if($this->input->method()!="post"){
    		$this->output->set_status_header(405);
    		return false;
    	}
    	$this->form_validation->set_rules('name', 'Name', 'trim|required', array());
    	$this->form_validation->set_rules('active', 'Name', 'trim|is_unique[websites.name]', array());
    	if(!$this->form_validation->run()){
            echo json_encode($this->form_validation->error_array());
    		$this->output->set_status_header(400);
    		return false;
    	}
		$websiteData = [
			"name"   => $this->input->post("name")
		];
		if($this->input->post("active")===0){
			$websiteData["active"] = 0;
		}
		$newWebsite = $this->websites->create($websiteData);
		if(is_string($newWebsite)&strpos($newWebsite,"Duplicate")!==false){
            echo json_encode(array("error"=>$newWebsite));
            $this->output->set_status_header(400);
            return false;
        }
        $websiteData["id"] = $newWebsite;
        echo json_encode($websiteData);
        return true;
	}
	public function delete($id){
        if($this->input->method()!="delete"){
            $this->output->set_status_header(405);
            return false;
        }
        if(!is_numeric($id) || !isset($id)){
            echo json_encode(array("error"=>"A valid ID is required ".is_int($id)));
            $this->output->set_status_header(400);
            return false;
        }
        $confirm = $this->websites->delete(["id_website"=>$id]);
        if($confirm==0){
            echo json_encode(["error"=>"Invalid ID"]);
            $this->output->set_status_header(400);
            return false;
        }
        echo json_encode(["message"=>"Website has been deleted","id"=>$id]);
        return true;
	}
	public function toList($pos=0,$quant=1000){
        if($this->input->method()!="get"){
            $this->output->set_status_header(405);
            return false;
        }
		if(!is_numeric($pos)&!is_numeric($quant)){
			echo json_encode(["error"=>"Arguments must be numbers"]);
			$this->output->set_status_header(400);
			return false;
		}
		echo json_encode($this->websites->list($pos,$quant));
	}
	public function search(){
        if($this->input->method()!="get"){
            $this->output->set_status_header(405);
            return false;
        }
		echo json_encode($this->websites->search($this->input->get("name")));
	}
} 