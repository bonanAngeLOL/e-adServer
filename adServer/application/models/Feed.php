<?php
defined("BASEPATH") or exit ("No direct access allowed");
class Feed extends CI_Model{

	public function __construct(){
		$this->db->db_debug = FALSE;
	}

	public function addFeed($ad, $feed){
		$this->db->insert("ads",$ad);
		$return = $this->db->error()["message"];
		if($return != ""){
			return $return;
		}
		$feed["ad"] = $this->db->insert_id();
		$this->db->insert("feed",$feed);
		$return = $this->db->error()["message"];
		if($return != ""){
			return $return;
		}
		$feed["feed"] = $this->db->insert_id();
		return array_merge($ad,$feed);
	}

	public function addImage($image){
		$this->db->insert("feedImg",$image);
		$return = $this->db->error()["message"];
		if($return != ""){
			return $return;
		}
		$image["id"] = $this->db->insert_id();
		return $image;
	}

}	