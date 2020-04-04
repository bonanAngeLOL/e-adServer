<?php
defined("BASEPATH") or exit ("No direct access allowed");
class positions extends CI_Model{
	public function __construct(){
		$this->db->db_debug = FALSE;
	}
	public function add($data){
		$this->db->insert("positions",$data);
		$return = $this->db->error()["message"];
		if($this->db->insert_id()>0)
			$return = $this->db->insert_id();
		return $return;
	}
    public function delete($data){
    	$this->db->delete("positions",$data);
    	return $this->db->affected_rows();
    }
    public function listPerWebsite($website, $pos=0, $quant=1000){
    	$this->db->select('positions.id_position as id, positions.name, positions.description, positions.active, positions.height, positions.width');
		$this->db->from("positions");
    	if(is_numeric($website)){
    		$this->db->where("positions.website",$website);
    	}
    	else{
    		$this->db->join("websites","websites.id_website = positions.website");
    		$this->db->where("websites.name",$website);
    	}
    	$return = $this->db->get()->result();
    	var_dump($return);
    	return $return;
    }
}