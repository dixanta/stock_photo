<?php

class Admin_Controller extends MY_Controller 
{
	
	private $template;
    var $_container='admin/container.php';
    var $admin_url;
	function __construct()
	{
		parent::__construct();
		
		$this->auth->is_logged_in(uri_string());
		
		//load the base language file
		$this->lang->load('admin_common');
		$this->lang->load('media');
	}
	
	function view($view, $vars = array(), $string=false)
	{
		//if there is a template, use it.
		$template	= '';
		if($this->template)
		{
			$template	= $this->template.'_';
		}

		if($string)
		{
			$result	 = $this->load->view('admin/'.$template.'header', $vars, true);
			$result	.= $this->load->view($view, $vars, true);
			$result	.= $this->load->view('admin/'.$template.'footer', $vars, true);
			
			return $result;
		}
		else
		{
			$this->load->view('admin/'.$template.'header', $vars);
			$this->load->view($view, $vars);
			$this->load->view('admin/'.$template.'footer', $vars);
		}
		
		//reset $this->template to blank
		$this->template	= false;
	}
	
	/* Template is a temporary prefix that lasts only for the next call to view */
	function set_template($template)
	{
		$this->template	= $template;
	}
}