<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	function __construct(){
	    parent::__construct();
	    //$this->load->library(array("jwtload"));
	    $this->load->library( array('jwtload')  );
	}

	public function index()
	{
		//$this->load->view('welcome_message');
		//echo Firebase\JWT\JWT::encode("asdf", "asdfqwer");
		echo $this->jwtload->encode("asdf","asdf");
		echo 'asdf';
		echo CI_VERSION;
	}
}
