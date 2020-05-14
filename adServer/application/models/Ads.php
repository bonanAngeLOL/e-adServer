<?php
defined("BASEPATH") or exit ("No direct access allowed");
class Ads extends CI_Model{
	public function __construct(){
		$this->db->db_debug = FALSE;
	}
	
    public function getParentPosition($id){
        $this->db->select("p.id_position");
        $this->db->from("ads a");
        $this->db->join("adsinposition ap","ap.ad = a.id_ad");
        $this->db->join("positions p","p.id_position = ap.position");
        $this->db->where("a.id_ad",$id);
        /*select p.id_position from ads a
        join adsinposition ap
            on ap.ad = a.id_ad
        join positions p
            on p.id_position = ap.position
        where a.id_ad = $id;*/
        return $this->db->get()->result();
    }

	public function delete($ad){
		$this->db->delete("ads",$ad);
		return $this->db->affected_rows();
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

	public function updateImage($adInfo, $imageInfo, $id){
		$r = ['ad'=>'','image'=>''];
		$this->db->where('id_ad',$id);
		$r["ad"] = $this->db->update('ads',$adInfo);
		if(count($imageInfo)){
			$this->db->where('ad',$id);
			$r["image"] = $this->db->update('image',$imageInfo);
		}
		return $r;
	}

	public function imgInfo($id){
		$this->db->select("a.name, a.startDate, a.endDate, a.active, a.height, a.width,
    i.alt, i.src, i.ad, i.link");
		$this->db->from("ads a");
		$this->db->join("image i","a.id_ad = i.ad");
		$this->db->where("a.id_ad", $id);
		return $this->db->get()->result();
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