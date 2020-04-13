<?php 
defined("BASEPATH") or exit ("No direct access allowed");
class adsManagement extends CI_Controller{
	public function __construct(){
		parent::__construct();
		$this->load->helper(array('form', 'url'));
        $this->load->library(array('form_validation','memcached'));
        $this->load->model(array('ads'));
	}
	public function addCode(){
    	if($this->input->method()!="post"){
    		$this->output->set_status_header(405);
    		return false;
    	}
		$this->form_validation->set_rules('name', 'Name', 'trim|required');
    	$this->form_validation->set_rules('startDate', 'Start date', 'trim|required');
    	$this->form_validation->set_rules('endDate', 'End date', 'trim');
    	$this->form_validation->set_rules('code', 'Code', 'trim|required');
		$this->form_validation->set_rules('active', 'Active', 'trim|required');
        $this->form_validation->set_rules('width', 'End date', 'trim');
        $this->form_validation->set_rules('height', 'End date', 'trim');

    	if(!$this->form_validation->run()){
            echo json_encode($this->form_validation->error_array());
    		$this->output->set_status_header(400);
    		return false;
    	}

    	$ad = [
    		"name" 		=> $this->input->post("name"),
    		"startDate" => $this->input->post("startDate")
    		//"endDate"	=> $this->input->post("endDate") ?? null
    	];

    	if($this->input->post("endDate")!=""){
			$ad["endDate"] = $this->input->post("endDate");
    	}
    	if($this->input->post("active")!=""){
    		$ad["active"] = $this->input->post("active");
    	}
        if($this->input->post("width")!=""){
            $ad["width"] = $this->input->post("width");
        }
        if($this->input->post("height")!=""){
            $ad["height"] = $this->input->post("height");
        }

    	$code = [
    		"code"	=> $this->input->post("code")
    	];

    	$newCode = $this->ads->addCode($ad,$code);
		if(is_string($newCode)&strpos($newCode,"Duplicate")!==false){
            echo json_encode(array("error"=>$newCode));
            $this->output->set_status_header(400);
            return false;
        }

    	echo json_encode($newCode);
        return true;
	}

	public function addImage(){
    	if($this->input->method()!="post"){
    		$this->output->set_status_header(405);
    		return false;
    	}

        $config['upload_path']          = './images/';
        $config['allowed_types']        = 'gif|jpg|png|jpeg';
        $config['max_size']             = 500;
        $config['max_width']            = 10000;
        $config['max_height']           = 10000;

    	$this->load->library('upload', $config);

        if ( ! $this->upload->do_upload('image'))
        {
            echo json_encode(array('error' => $this->upload->display_errors()));
        	$this->output->set_status_header(400);
        	return false;
        }

        $imgSrc = $this->upload->data("file_name");

        $img = array('upload_data' => $this->upload->data());

		$this->form_validation->set_rules('name', 'Name', 'trim|required');
    	$this->form_validation->set_rules('startDate', 'Start date', 'trim|required');
    	$this->form_validation->set_rules('endDate', 'End date', 'trim');
        $this->form_validation->set_rules('link','Link','trim');
    	$this->form_validation->set_rules('alt', 'alt', 'trim');

    	if(!$this->form_validation->run()){
            echo json_encode($this->form_validation->error_array());
    		$this->output->set_status_header(400);
    		return false;
    	}

    	$ad = [
    		"name" 		=> $this->input->post("name"),
    		"startDate" => $this->input->post("startDate")
    		//"endDate"	=> $this->input->post("endDate") ?? null
    	];

    	if($this->input->post("endDate")!=""){
			$ad["endDate"] = $this->input->post("endDate");
    	}
    	if($this->input->post("active")!=""){
    		$ad["active"] = $this->input->post("active");
    	}


    	$image = [
    		"src"	=> $imgSrc
    	];

        if($this->input->post("alt")){
            $image["alt"]  = $this->input->post("alt");
        }

        if($this->input->post("link")){
            $image["link"] = $this->input->post("link");
        }

    	$newImage = $this->ads->addImage($ad,$image);

		if(is_string($newImage)&strpos($newImage,"Duplicate")!==false){
            echo json_encode(array("error"=>$newImage));
            $this->output->set_status_header(400);
            return false;
        }

        echo json_encode($newImage);
        return true;
	}
	public function attach(){
    	if($this->input->method()!="post"){
    		$this->output->set_status_header(405);
    		return false;
    	}
		$this->form_validation->set_rules('position', 'Position', 	'trim|required');
    	$this->form_validation->set_rules('ad', 'Ad', 				'trim|required');
    	if(!$this->form_validation->run()){
            echo json_encode($this->form_validation->error_array());
    		$this->output->set_status_header(400);
    		return false;
    	}
    	$data = [
    		"position"	=> $this->input->post("position"),
    		"ad" 		=> $this->input->post("ad")
    	];

    	$newAttachement = $this->ads->attach($data);

		if(is_string($newAttachement)&strpos($newAttachement,"Duplicate")!==false){
            echo json_encode(array("error"=>$newAttachement));
            $this->output->set_status_header(400);
            return false;
        }
        $key = base64_encode("/position/resource/".$this->input->post("position"));
        $this->memcached->mem()->delete($key);
    	echo json_encode($newAttachement);
        return true;
	}
} 