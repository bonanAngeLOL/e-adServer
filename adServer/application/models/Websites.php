<?php
defined("BASEPATH") or exit ("No direct access allowed");
class Websites extends CI_Model{
	public function __construct(){
		$this->db->db_debug = FALSE;
	}
	public function create($data){
		$this->db->insert("websites",$data);
		$return = $this->db->error()["message"];
		if($this->db->insert_id()>0)
			$return = $this->db->insert_id();
		return $return;
	}
	public function delete($data){
		$this->db->delete("websites",$data);
		return $this->db->affected_rows();
	}
	public function list($pos=0, $quant=1000){
		//return $this->db->get("websites",$quant, $pos)->result();
		$this->db->select('id_website as id, name, active');
		$this->db->limit($quant, $pos);
		return $this->db->get('websites')->result();
	}
	public function search($data){
		$this->db->select('id_website as id, name');
		$this->db->like("name", $data, "both");
		return $this->db->get('websites')->result();
	}
}
