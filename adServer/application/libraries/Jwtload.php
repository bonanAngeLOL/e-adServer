<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require '../vendor/autoload.php';
class Jwtload extends Firebase\JWT\JWT{
    function __construct(){
	$this->CI =& get_instance();
		    
    }

    function noteSeo($noteInfo){
    }

}

