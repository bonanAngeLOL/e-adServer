<?php
defined("BASEPATH") or exit ("No direct access allowed")
class users extends CI_Model{
    public function newUser($data) {
	return $this->db->insert("users",$data);
    }
} 
