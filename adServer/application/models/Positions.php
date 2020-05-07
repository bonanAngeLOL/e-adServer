<?php
defined("BASEPATH") or exit ("No direct access allowed");
class Positions extends CI_Model{
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
    	//var_dump($return);
    	return $return;
    }

    public function getPositionAds($position=0,$active=1,$future=false){
        $this->db->select("'image' as type, a.id_ad as id, i.alt, i.src, i.link, a.startDate, a.endDate, a.height, a.width, a.active, a.name");
        $this->db->from("image i");
        $this->db->join("ads a","i.ad = a.id_ad");
        $this->db->join("adsinposition ap","a.id_ad = ap.ad");
        $this->db->where("ap.position",$position);
        $this->db->where("a.active",$active);
        if(!$future){
            $this->db->where("a.startDate <= current_timestamp()");
        }
        $this->db->where("(a.endDate > current_timestamp() OR a.endDate is null)");
        $images = $this->db->get()->result();
        $this->db->select("'code' as type, a.id_ad as id, c.code, a.startDate, a.endDate, a.height, a.width, a.active, a.name");
        $this->db->from("code c");
        $this->db->join("ads a","c.ad = a.id_ad");
        $this->db->join("adsinposition ap","a.id_ad = ap.ad");
        $this->db->where("ap.position",$position);
        $this->db->where("a.active",$active);
        if(!$future){
            $this->db->where("a.startDate <= current_timestamp()");
        }
        $this->db->where("(a.endDate > current_timestamp() OR a.endDate is null)");
        $codes = $this->db->get()->result();
        return array_merge((array)$images,(array)$codes);
    }

    public function getPositionNotAds($position=0){
        $this->db->select("'image' as type, a.id_ad as id, i.alt, i.src, i.link, a.startDate, a.endDate, a.height, a.width, a.active, a.name");
        $this->db->from("image i");
        $this->db->join("ads a","i.ad = a.id_ad");
        $this->db->join("adsinposition ap","a.id_ad = ap.ad");
        $this->db->where("ap.position",$position);
        $this->db->where("
            ( 
                ( 
                    a.active = 0 && ( a.endDate > current_timestamp() OR a.endDate is null ) 
                )               OR 
                a.startDate > current_timestamp()
            )
        ");
        $images = $this->db->get()->result();
        $this->db->select("'code' as type, a.id_ad as id, c.code, a.startDate, a.endDate, a.height, a.width, a.active, a.name");
        $this->db->from("code c");
        $this->db->join("ads a","c.ad = a.id_ad");
        $this->db->join("adsinposition ap","a.id_ad = ap.ad");
        $this->db->where("ap.position",$position);
        $this->db->where("
            ( 
                ( 
                    a.active = 0 && ( a.endDate > current_timestamp() OR a.endDate is null ) 
                )               OR 
                a.startDate > current_timestamp()
            )
        ");
        $codes = $this->db->get()->result();
        return array_merge((array)$images,(array)$codes);
    }

    public function getPositionArchive($position=0){
        $this->db->select("'image' as type, a.id_ad as id, i.alt, i.src, i.link, a.startDate, a.endDate, a.height, a.width, a.active, a.name");
        $this->db->from("image i");
        $this->db->join("ads a","i.ad = a.id_ad");
        $this->db->join("adsinposition ap","a.id_ad = ap.ad");
        $this->db->where("ap.position",$position);
        $this->db->where("a.endDate < current_timestamp()");
        $images = $this->db->get()->result();
        $this->db->select("'code' as type, a.id_ad as id, c.code, a.startDate, a.endDate, a.height, a.width, a.active, a.name");
        $this->db->from("code c");
        $this->db->join("ads a","c.ad = a.id_ad");
        $this->db->join("adsinposition ap","a.id_ad = ap.ad");
        $this->db->where("ap.position",$position);
        $this->db->where("a.endDate < current_timestamp()");
        $codes = $this->db->get()->result();
        return array_merge((array)$images,(array)$codes);
    }

    public function listPerSection($section){
        $this->db->select('p.id_position as id, p.name, p.description, p.active, p.height, p.width');
        $this->db->from("positions p");
        $this->db->join("positioninsection ps","ps.position = p.id_position");
        $this->db->join("sections s","s.id_section = ps.section");
        $this->db->where("s.id_section",$section);
        $return = $this->db->get()->result();
        //var_dump($return);
        return $return;
    }
}