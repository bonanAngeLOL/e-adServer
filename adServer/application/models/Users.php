<?php
defined("BASEPATH") or exit ("No direct access allowed");
class Users extends CI_Model{
	public function __construct(){
		//$this->load->database();
		$this->db->db_debug = FALSE;
	}
    public function newUser($data) {
		$this->db->insert("users",$data);
		$return = $this->db->error()["message"];
		if($this->db->insert_id()>0)
			$return = $this->db->insert_id();
		return $return;
    }
    public function deleteUser($data){
    	$this->db->delete("users",$data);
    	return $this->db->affected_rows();
    }
}
