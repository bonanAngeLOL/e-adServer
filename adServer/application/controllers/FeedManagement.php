<?php 
defined("BASEPATH") or exit ("No direct access allowed");
class FeedManagement extends CI_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->helper(array('form', 'url'));
        $this->load->library(array('form_validation','memcached'));
        $this->load->model(array('ads','feed'));
        error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
	}

	public function attach($position, $ad){
    	$data = [
    		"position"	=> $position,
    		"ad" 		=> $ad
    	];

    	$newAttachement = $this->ads->attach($data);

		if(is_string($newAttachement)){
            if(strpos($newAttachement,"Duplicate")!==false){
                echo json_encode(array("error on attachement"=>$newAttachement));
                $this->output->set_status_header(400);
                return false;
            }
        }
        $key = base64_encode("/position/resource/".$position);
        $this->memcached->mem()->delete($key);
        header('X-memCres: '.$key);
        $key = base64_encode("/position/serve/".$position);
        $this->memcached->mem()->delete($key);
        header('X-memCserve: '.$key);
    	//echo json_encode($newAttachement);
        return true;
	}

	public function addFeed(){

        if($this->input->method()!="post"){
            $this->output->set_status_header(405);
            return false;
        }

		$this->form_validation->set_rules('name', 'Name', 'trim|required');
    	$this->form_validation->set_rules('startDate', 'Start date', 'trim');
    	$this->form_validation->set_rules('endDate', 'End date', 'trim');
    	$this->form_validation->set_rules('active', 'Active', 'trim');
    	$this->form_validation->set_rules('orientation', 'Orientation', 'trim|required');

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

    	$feed = [
    		'direction' => $this->input->post("orientation")
    	];

		$newFeed = $this->feed->addFeed($ad,$feed);

		if(is_string($newFeed)&strpos($newFeed,"Duplicate")!==false){
            echo json_encode(array("error"=>$newFeed));
            $this->output->set_status_header(400);
            return false;
        }

        $images = [];

		$files = $_FILES;
    	$count = count($_FILES['image']['name']);
        //$quant = count($_FILES);
        $config['upload_path']          = './images/';
        $config['allowed_types']        = 'gif|jpg|png|jpeg';
        $config['max_size']             = 2048;
        $config['max_width']            = 10000;
        $config['max_height']           = 10000;

        for($i=0; $i<$count; $i++)
        {   
        	$this->load->library('upload', $config);

			$_FILES['image']['name']= $files['image']['name'][$i];
			$_FILES['image']['type']= $files['image']['type'][$i];
			$_FILES['image']['tmp_name']= $files['image']['tmp_name'][$i];
			$_FILES['image']['error']= $files['image']['error'][$i];
			$_FILES['image']['size']= $files['image']['size'][$i];


	        if ( ! $this->upload->do_upload('image'))
	        {
	            echo json_encode(array('error' => $this->upload->display_errors()));
	        	$this->output->set_status_header(400);
	        	return false;
	        }

	        $imgSrc = $this->upload->data("file_name");

			$feedImg = [
				'src' 	=> $imgSrc,
				'alt' 	=> $_POST["alt"][$i],
				'url' 	=> $_POST["url"][$i],
				'width' => $_POST["width"][$i],
				'height'=> $_POST["height"][$i],
				'feed' 	=> $newFeed["feed"]
			];

			$nImg = $this->feed->addImage($feedImg);
			if(is_string($nImg)&strpos($nImg,"Duplicate")!==false){
	            echo json_encode(array("error"=>$nImg));
	            $this->output->set_status_header(400);
	            return false;
	        }
	        array_push($images, $nImg);
        }
        $result = (Object) [ "feed" => $newFeed, "images" => $images];
        if($this->input->post("position")!=''||$this->input->post("position")!=null){
        	$attachement = $this->attach($this->input->post("position"), $newFeed['ad']);
        }
        echo json_encode($result);
        return $result;
	}

}