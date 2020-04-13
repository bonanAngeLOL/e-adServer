<?php
defined("BASEPATH") or exit ("No direct access allowed");
class ads extends CI_Model{
	public function __construct(){
		$this->db->db_debug = FALSE;
	}
	
	public function addImage($ad, $image){
		$this->db->insert("ads",$ad);
		$return = $this->db->error()["message"];
		if($return != ""){
			return $return;
		}
		$image["ad"] = $this->db->insert_id();
		$this->db->insert("image",$image);
		return array_merge($ad,$image);
	}

	public function addCode($ad, $code){
		$this->db->insert("ads",$ad);
		$return = $this->db->error()["message"];
		if($return != ""){
			return $return;
		}
		$code["ad"] = $this->db->insert_id();
		$this->db->insert("code",$code);
		return array_merge($ad,$code);
	}

	public function attach($data){
		$this->db->insert("adsinposition",$data);
		$return = $this->db->error()["message"];
		if($return != ""){
			return $return;
		}
		$data["id"] = $this->db->insert_id();
		return $data;
	}
}