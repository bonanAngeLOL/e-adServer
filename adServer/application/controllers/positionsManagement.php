<?php
defined("BASEPATH") or exit ("No direct access allowed");
class PositionsManagement extends CI_Controller{
	public function __construct(){
		parent::__construct();
		$this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->model(array('positions'));
	}
	public function add(){
    	if($this->input->method()!="post"){
    		$this->output->set_status_header(405);
    		return false;
    	}
    	$this->form_validation->set_rules('name', 'Name', 'trim|required|is_unique[positions.name]');
    	$this->form_validation->set_rules('description', 'Description', 'trim');
    	$this->form_validation->set_rules('active', 'Active', 'trim');
    	$this->form_validation->set_rules('website', 'Website', 'trim|required|is_natural');
    	$this->form_validation->set_rules('height', 'Height', 'trim');
    	$this->form_validation->set_rules('width', 'Width', 'trim');
    	if(!$this->form_validation->run()){
            echo json_encode($this->form_validation->error_array());
    		$this->output->set_status_header(400);
    		return false;
    	}
    	$positionData = [
			"name"			=>	$this->input->post("name"),
			"description"	=>	$this->input->post("description"),
			"active"		=>	$this->input->post("active"),
			"website"		=>	$this->input->post("website"),
			"height"		=>	$this->input->post("height"),
			"width"			=>  $this->input->post("width")
    	];
    	$newPosition = $this->positions->add($positionData);
		if(is_string($newPosition)&strpos($newPosition,"Duplicate")!==false){
            echo json_encode(array("error"=>$newPosition));
            $this->output->set_status_header(400);
            return false;
        }
        $positionData["ID"] = $newPosition;
        echo json_encode($positionData);
        return true;
	}
    public function delete($id){
        if($this->input->method()!="delete"){
            $this->output->set_status_header(405);
            return false;
        }
        if(!is_numeric($id) || !isset($id)){
            echo json_encode(array("error"=>"Valid ID is required ".is_int($id)));
            $this->output->set_status_header(400);
            return false;
        }
        $confirm = $this->positions->delete(["id_position"=>$id]);
        if($confirm==0){
            echo json_encode(["error"=>"Invalid ID"]);
            $this->output->set_status_header(400);
            return false;
        }
        echo json_encode(["message"=>"Position has been deleted","id"=>$id]);
        return true;
    }
    public function toList($website,$pos=0,$quant=1000){
    	echo json_encode($this->positions->listPerWebsite($website,$pos,$quant));
    	return true;
    }
}