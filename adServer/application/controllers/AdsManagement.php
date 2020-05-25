<?php 
defined("BASEPATH") or exit ("No direct access allowed");
class AdsManagement extends CI_Controller{
	public function __construct(){
		parent::__construct();
		$this->load->helper(array('form', 'url'));
        $this->load->library(array('form_validation','memcached'));
        $this->load->model(array('ads'));
        error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
	}

    public function imgInfo($id){
        if($this->input->method()!="get"){
            $this->output->set_status_header(405);
            return false;
        }
        if(!is_numeric($id) || !isset($id)){
            echo json_encode(array("error"=>"Valid ID is required ".is_int($id)));
            $this->output->set_status_header(400);
            return false;
        }
        $ad = '{}';
        $ad = $this->ads->imgInfo($id)[0];
        echo json_encode( (Object) $ad );
        return true;
    }

    public function codeInfo($id){
        if($this->input->method()!="get"){
            $this->output->set_status_header(405);
            return false;
        }
        if(!is_numeric($id) || !isset($id)){
            echo json_encode(array("error"=>"Valid ID is required ".is_int($id)));
            $this->output->set_status_header(400);
            return false;
        }
        $ad = '{}';
        $ad = $this->ads->codeInfo($id)[0];
        echo json_encode( (Object) $ad );
        return true;
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
        $config['max_size']             = 2048;
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
        if($this->input->post("height")!=""){
            $ad["height"] = $this->input->post("height");
        }
        if($this->input->post("width")!=""){
            $ad["width"] = $this->input->post("width");
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

		if(is_string($newImage)){
            if(strpos($newImage,"Duplicate")!==false){
                echo json_encode(array("error"=>$newImage));
                $this->output->set_status_header(400);
                return false;
            }
        }

        echo json_encode($newImage);
        return true;
	}

    private function getParentPos($id){
        return $this->ads->getParentPosition($id);
    }

    private function resetPMem($positions){
        //$positions = $this->ads->getParentPosition($id);
        //var_dump($positions);
        foreach ($positions as $position) {
            //var_dump($position);
            $key = base64_encode("/position/resource/".$position->id_position);
            $this->memcached->mem()->delete($key);
            header('X-memCres: '.$key);
            $key = base64_encode("/position/serve/".$position->id_position);
            $this->memcached->mem()->delete($key);
            header('X-memCserve: '.$key);
        }
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
        $parent = $this->getParentPos($id);
        $confirm = $this->ads->delete(["id_ad"=>$id]);
        if($confirm==0){
            echo json_encode(["error"=>"Invalid ID"]);
            $this->output->set_status_header(400);
            return false;
        }
        $this->resetPMem($parent);
        echo json_encode((Object)["message"=>"Position has been deleted","id"=>$id]);
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

		if(is_string($newAttachement)){
            if(strpos($newAttachement,"Duplicate")!==false){
                echo json_encode(array("error"=>$newAttachement));
                $this->output->set_status_header(400);
                return false;
            }
        }
        $key = base64_encode("/position/resource/".$this->input->post("position"));
        $this->memcached->mem()->delete($key);
        header('X-memCres: '.$key);
        $key = base64_encode("/position/serve/".$this->input->post("position"));
        $this->memcached->mem()->delete($key);
        header('X-memCserve: '.$key);
    	echo json_encode($newAttachement);
        return true;
	}

    public function updateCode(){
        if($this->input->method()!="post"){
            $this->output->set_status_header(405);
            return false;
        }

        $this->form_validation->set_rules('id','ID','trim|required');
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('startDate', 'Start date', 'trim|required');
        $this->form_validation->set_rules('endDate', 'End date', 'trim');
        $this->form_validation->set_rules('active', 'active', 'trim');
        $this->form_validation->set_rules('code', 'code', 'trim');

        if(!$this->form_validation->run()){
            echo json_encode($this->form_validation->error_array());
            $this->output->set_status_header(400);
            return false;
        }

        $ad = [
            "name"      => $this->input->post("name"),
            "startDate" => $this->input->post("startDate")
            //"endDate" => $this->input->post("endDate") ?? null
        ];

        $endDate = $this->input->post("endDate");
        $active = $this->input->post("active");
        $ad["endDate"] = $endDate == 'null' || $endDate == '0000-00-00T00:00:00' || $endDate == '' || $endDate == 'undefined' ? null : $endDate;
        $ad["active"] = $active == 1 || $active == '1' ? 1 : 0;

        $code['code'] = $this->input->post("code");

        $updated = $this->ads->updateCode($ad,$code,$this->input->post("id"));

        $parent = $this->getParentPos($this->input->post("id"));
        $this->resetPMem($parent);
        echo json_encode((Object)$updated);
        return true;

    }

    public function updateImg(){
        if($this->input->method()!="post"){
            $this->output->set_status_header(405);
            return false;
        }
        $imgSrc = false;
        if(count($_FILES)>0){
            $config['upload_path']          = './images/';
            $config['allowed_types']        = 'gif|jpg|png|jpeg';
            $config['max_size']             = 2048;
            $config['max_width']            = 10000;
            $config['max_height']           = 10000;

            $this->load->library('upload', $config);

            if ( ! $this->upload->do_upload('src'))
            {
                echo json_encode(array('error' => $this->upload->display_errors()));
                $this->output->set_status_header(400);
                return false;
            }
            $imgSrc = $this->upload->data("file_name");
        }
        $this->form_validation->set_rules('id','ID','trim|required');
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('startDate', 'Start date', 'trim|required');
        $this->form_validation->set_rules('endDate', 'End date', 'trim');
        $this->form_validation->set_rules('link','Link','trim');
        $this->form_validation->set_rules('alt', 'alt', 'trim');
        $this->form_validation->set_rules('active', 'active', 'trim');
        $this->form_validation->set_rules('width', 'width', 'trim');
        $this->form_validation->set_rules('height', 'height', 'trim');

        if(!$this->form_validation->run()){
            echo json_encode($this->form_validation->error_array());
            $this->output->set_status_header(400);
            return false;
        }

        $ad = [
            "name"      => $this->input->post("name"),
            "startDate" => $this->input->post("startDate")
            //"endDate" => $this->input->post("endDate") ?? null
        ];

        $endDate = $this->input->post("endDate");
        $active = $this->input->post("active");
        $height = $this->input->post("height");
        $width = $this->input->post("width");
        $ad["endDate"] = $endDate == 'null' || $endDate == '0000-00-00T00:00:00' || $endDate == '' || $endDate == 'undefined' ? null : $endDate;
        $ad["active"] = $active == 1 || $active == '1' ? 1 : 0;
        $ad["height"] = $height == '' || $height == 'null' || $height == 'undefined' ? null : $height;
        $ad["width"] = $width == '' || $width == 'null' || $width == 'undefined' ? null : $width;

        $alt = $this->input->post("alt");
        $link = $this->input->post("link");

        $image = [];
        $image["alt"] = $alt == '' || $alt == 'null' || $alt == 'undefined' ? null : $alt;
        if($imgSrc){
            $image["src"] = $imgSrc;
        }
        $image["link"] = $link == '' || $link == 'null' || $link == 'undefined' ? null : $link;

        $updated = $this->ads->updateImage($ad,$image,$this->input->post("id"));

/*        if(is_string($newImage)){
            if(strpos($newImage,"Duplicate")!==false){
                echo json_encode(array("error"=>$newImage));
                $this->output->set_status_header(400);
                return false;
            }
        }
*/
        $parent = $this->getParentPos($this->input->post("id"));
        $this->resetPMem($parent);
        echo json_encode((Object)$updated);
        return true;

    }
} 