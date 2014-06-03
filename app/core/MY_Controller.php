<?php

/**
 * The base controller which is used by the Front and the Admin controllers
 */
class MY_Controller extends CI_Controller
{
	
	public function __construct()
	{
		
		parent::__construct();


		//kill any references to the following methods
		$mthd = $this->router->method;
		if($mthd == 'view' || $mthd == 'partial' || $mthd == 'set_template')
		{
			show_404();
		}
		
		//load base libraries, helpers and models
		$this->load->database();

		// load the migrations class & settings model
		//$this->load->library('migration');
		$this->load->model('Settings_model');
	
		// Migrate to the latest migration file found
		/*if ( ! $this->migration->latest())
		{
			echo $this->migration->error_string();
		}*/

		//load in config items from the database
		$settings = $this->Settings_model->get_settings('gocart');
		foreach($settings as $key=>$setting)
		{
			//special for the order status settings
			if($key == 'order_statuses')
			{
				$setting = json_decode($setting, true);
			}
			$this->config->set_item($key, $setting);
		}

		//load the default libraries
		$this->load->library(array('session', 'auth', 'go_cart'));
		$this->load->model(array('Customer_model', 'Category_model', 'Location_model'));
		$this->load->helper(array('url', 'file', 'string', 'html', 'language'));
        
        //if SSL is enabled in config force it here.
        if (config_item('ssl_support') && (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off'))
		{
			$CI =& get_instance();
			$CI->config->config['base_url'] = str_replace('http://', 'https://', $CI->config->config['base_url']);
			redirect($CI->uri->uri_string());
		}
	}
	
}//end Base_Controller

