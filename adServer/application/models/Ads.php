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

	public function updateCode($adInfo, $codeInfo, $id){
		$r = ['ad'=>'','code'=>''];
		$this->db->where('id_ad',$id);
		$r["ad"] = $this->db->update('ads',$adInfo);
		if(count($codeInfo)){
			$this->db->where('ad',$id);
			$r["code"] = $this->db->update('code',$codeInfo);
		}
		return $r;
	}

	public function updateFeed($adInfo, $feed, $id){
		$r = ['ad'=>'','code'=>''];
		$this->db->where('id_ad',$id);
		$r["ad"] = $this->db->update('ads',$adInfo);
		if(count($feed)){
			$this->db->where('ad',$id);
			$r["code"] = $this->db->update('feed',$feed);
		}
		return $r;
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


	public function updateFeedImage($feedImg, $id){
		$r = false;
		$this->db->where('id_fi',$id);
		$r = $this->db->update('feedImg',$feedImg);
		$return = $this->db->error()["message"];
		if($return != ""){
			return $return;
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

	public function codeInfo($id){
		$this->db->select("a.id_ad as ad, a.name, a.startDate, a.endDate, a.active, a.height, a.width, c.code");
		$this->db->from("ads a");
		$this->db->join("code c","a.id_ad = c.ad");
		$this->db->where("a.id_ad", $id);
		return $this->db->get()->result();
	} 

	public function feedInfo($id){
		$this->db->select("a.id_ad as ad, a.name, a.startDate, a.endDate, a.active, a.height, a.width, f.direction");
		$this->db->from("ads a");
		$this->db->join("feed f","a.id_ad = f.ad");
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

    public function getFeedImage($id){
        $this->db->select("id_fi as id, src, alt, url, width, height");
        $this->db->from("feedImg");
        $this->db->where("id_fi",$id);
        return $this->db->get()->result();
    }

}