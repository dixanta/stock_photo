<?php

/**
 * The base controller which is used by the Front and the Admin controllers
 */
class Base_Controller extends CI_Controller
{
	
	public function __construct()
	{
		
		parent::__construct();

		// load the migrations class
		$this->load->library('migration');
	
		// Migrate to the latest migration file found
		if ( ! $this->migration->latest())
		{
			log_message('error', 'The migration failed');
		}
		
	}//end __construct()
	
}//end Base_Controller


include_once('Admin_Controller.php');
include_once('Front_Controller.php');