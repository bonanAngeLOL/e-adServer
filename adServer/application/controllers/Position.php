<?php
defined("BASEPATH") or exit ("No directy access allowed");
class Position extends CI_Controller{
	public function __construct(){
		parent::__construct();
        $this->load->model(array('positions'));
        $this->load->library(array("memcached"));
	}
    public function testx(){
        echo "Today is " . date("Y/m/d") . "<br>";
        echo "Today is " . date("Y.m.d") . "<br>";
        echo "Today is " . date("Y-m-d") . "<br>";
        //date_default_timezone_set('America/Mexico_City');
        $date = date('m/d/Y h:i:s a', time());
        echo "Today is " . $date;
        echo time();
    }
    private function getExpInterval($position){
        $ads = $this->positions->getPositionAds($position,1,true);
        /*echo 'What ads contains';
        var_dump($ads);
        echo '<br>';*/
        $minutes = 86400;
        foreach($ads as $ad){
            //echo $ad->name.'<br>';
            if($ad->endDate==null){
                continue;
            }
            $currentDate = strtotime(date('m/d/Y H:i:s', time()));
            $expDate = strtotime($ad->endDate);
            $startDate = strtotime($ad->startDate);
            $minutesLeft = round(abs($expDate-$currentDate)/60);
            /*echo '<br>';
            echo "startD= ".$startDate."<br>";
            echo "currentD= ".$currentDate;
            echo 'veras?'.($startDate>$currentDate);*/
            if($startDate>$currentDate&&$startDate<$expDate){
                $minutesLeft = round(abs(strtotime($ad->startDate)-$currentDate)/60);
            }
            if($minutesLeft<$minutes)
                $minutes = $minutesLeft;
        }
        return $minutes==0?1:$minutes;
    }
	public function resource($position){
        if($this->input->method()!="get"){
            $this->output->set_status_header(405);
            return false;
        }
        $key = base64_encode("/position/resource/".$position);
        $cached = $this->memcached->mem()->get($key);
        if($cached !== false){
            //echo 'This result came from memcached<br>';
            echo $cached;
            return true;
        }
        $queryR = $this->positions->getPositionAds($position,1);
        $result = json_encode($queryR);
        $minutes = $this->getExpInterval($position);
        $this->memcached->mem()->set($key,$result,0,$minutes*60);
        header('X-memC-k: '.$key);
        header('X-memC-T: '.$minutes);
        //echo 'This result came from database<br>';
        echo $result;
        return true;
	}

    public function notResource($position){
        if($this->input->method()!="get"){
            $this->output->set_status_header(405);
            return false;
        }
        //$key = base64_encode("/position/resource/".$position);
        //$cached = $this->memcached->mem()->get($key);
        //if($cached !== false){
            //echo 'This result came from memcached<br>';
        //    echo $cached;
        //    return true;
        //}
        $queryR = $this->positions->getPositionNotAds($position);
        $result = json_encode($queryR);
        //$minutes = $this->getExpInterval($position);
        //$this->memcached->mem()->set($key,$result,0,$minutes*60);
        //header('X-memC-k: '.$key);
        //header('X-memC-T: '.$minutes);
        //echo 'This result came from database<br>';
        echo $result;
        return true;
    }

    public function archive($position){
        if($this->input->method()!="get"){
            $this->output->set_status_header(405);
            return false;
        }

        $queryR = $this->positions->getPositionArchive($position);
        $result = json_encode($queryR);

        echo $result;
        return true;
    }

    public function serve($position){
        if($this->input->method()!="get"){
            $this->output->set_status_header(405);
            return false;
        }
        $key = base64_encode("/position/serve/".$position);
        $cached = $this->memcached->mem()->get($key);
        if($cached !== false){
            //echo 'This result came from memcached<br>';
            echo $cached;
            return true;
        }
        $queryR = $this->positions->getPositionAds($position,1);
        $result = [
                    "data" => json_encode($queryR),
                    "id" => $position
                ];
        $view = $this->load->view("servePosition", $result, true);
        $minutes = $this->getExpInterval($position);
        $this->memcached->mem()->set($key,$view,0,$minutes*60);
        header('X-memC-k: '.$key);
        header('X-memC-T: '.$minutes);
        //echo 'This result came from database<br>';
        echo $view;
        return true;
    }
}