<?php
defined("BASEPATH") or exit ("No direct access allowed");
class Sections extends CI_Model{
	public function __construct(){
		$this->db->db_debug = FALSE;
	}
	public function inWebsite($website){
		$this->db->select("s.id_section as id, s.name as name, w.name as website, s.path");
		$this->db->from("sections s");
		$this->db->join("websites w","w.id_website = s.website");
		$this->db->where("w.id_website",$website);
		return $this->db->get()->result();
    }

	public function getTemplate($id){
		$this->db->select("file");
		$this->db->from("sections");
		$this->db->where("id_section",$id);
		return $this->db->get()->result();
    }

    public function attach($attachData){
    	$this->db->insert("positioninsection",$attachData);
    	$return = $this->db->error()["message"];
		if($this->db->insert_id()>0)
			$return = $this->db->insert_id();
		return $return;
    }

}