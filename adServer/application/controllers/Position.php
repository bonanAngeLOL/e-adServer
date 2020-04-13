<?php
defined("BASEPATH") or exit ("No directy access allowed");
class Position extends CI_Controller{
	public function __construct(){
		parent::__construct();
        $this->load->model(array('positions'));
        $this->load->library(array("memcached"));
	}
	public function resource($position){
        if($this->input->method()!="get"){
            $this->output->set_status_header(405);
            return false;
        }
        $key = base64_encode("/position/resource/".$position);
        $cached = $this->memcached->mem()->get($key);
        if($cached !== false){
            //echo 'This result came from memcached<br>';
            echo $cached;
            return true;
        }
        $result = json_encode($this->positions->getPositionAds($position,1));
        $this->memcached->mem()->set($key,$result,0,86400);
        //echo 'This result came from database<br>';
        echo $result;
        return true;
	}
    public function serve($position){
        if($this->input->method()!="get"){
            $this->output->set_status_header(405);
            return false;
        }
        $key = base64_encode("/position/serve/".$position);
        $cached = $this->memcached->mem()->get($key);
        if($cached !== false){
            //echo 'This result came from memcached<br>';
            echo $cached;
            return true;
        }
        $result = [ "data" => json_encode($this->positions->getPositionAds($position,1))];
        $view = $this->load->view("servePosition", $result, true);

        $this->memcached->mem()->set($key,$view,0,86400);
        //echo 'This result came from database<br>';
        echo $view;
        return true;
    }
}