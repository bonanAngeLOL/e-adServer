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

    public function getPositionAds($position=0,$active=1){
        $this->db->select("'image' as type, a.id_ad, i.alt, i.src, i.link, a.endDate, a.height, a.width");
        $this->db->from("image i");
        $this->db->join("ads a","i.ad = a.id_ad");
        $this->db->join("adsinposition ap","a.id_ad = ap.ad");
        $this->db->where("ap.position",$position);
        $this->db->where("a.active",$active);
        $this->db->where("a.startDate <= current_timestamp()");
        $this->db->where("(a.endDate > current_timestamp() OR a.endDate is null)");
        $images = $this->db->get()->result();
        $this->db->select("'code' as type, a.id_ad, c.code, a.endDate, a.height, a.width");
        $this->db->from("code c");
        $this->db->join("ads a","c.ad = a.id_ad");
        $this->db->join("adsinposition ap","a.id_ad = ap.ad");
        $this->db->where("ap.position",$position);
        $this->db->where("a.active",$active);
        $this->db->where("a.startDate <= current_timestamp()");
        $this->db->where("(a.endDate > current_timestamp() OR a.endDate is null)");
        $codes = $this->db->get()->result();
        return array_merge((array)$images,(array)$codes);
    }
}