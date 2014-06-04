<?php

class Front_Controller extends MY_Controller
{
	
	//we collect the categories automatically with each load rather than for each function
	//this just cuts the codebase down a bit
	var $categories	= '';
	
	//load all the pages into this variable so we can call it from all the methods
	var $pages = '';
	
	// determine whether to display gift card link on all cart pages
	//  This is Not the place to enable gift cards. It is a setting that is loaded during instantiation.
	var $gift_cards_enabled;
	
	function __construct(){
		
		parent::__construct();

		//load the theme package
		$this->load->add_package_path('themes/'.config_item('theme').'/');

		//load GoCart library
		$this->load->library('Banners');

		//load needed models
		$this->load->model(array('page/page_model', 'Product_model', 'Digital_Product_model', 'Gift_card_model', 'Option_model', 'Order_model', 'Settings_model'));
		
		//load helpers
		$this->load->helper(array('form_helper', 'formatting_helper'));
		
		//load common language
		//$this->lang->load('common');

		//fill in our variables
		$this->categories	= $this->category_model->get_categories_tiered(0);
		$this->pages		= $this->page_model->get_pages_tiered();
		
		// check if giftcards are enabled
		$gc_setting = $this->Settings_model->get_settings('gift_cards');
		if(!empty($gc_setting['enabled']) && $gc_setting['enabled']==1)
		{
			$this->gift_cards_enabled = true;
		}			
		else
		{
			$this->gift_cards_enabled = false;
		}
	}
	
	/*
	This works exactly like the regular $this->load->view()
	The difference is it automatically pulls in a header and footer.
	*/
	function view($view, $vars = array(), $string=false)
	{
		if($string)
		{
			$result	 = $this->load->view('header', $vars, true);
			$result	.= $this->load->view($view, $vars, true);
			$result	.= $this->load->view('footer', $vars, true);
			
			return $result;
		}
		else
		{
			$this->load->view('header', $vars);
			$this->load->view($view, $vars);
			$this->load->view('footer', $vars);
		}
	}
	
	/*
	This function simply calls $this->load->view()
	*/
	function partial($view, $vars = array(), $string=false)
	{
		if($string)
		{
			return $this->load->view($view, $vars, true);
		}
		else
		{
			$this->load->view($view, $vars);
		}
	}
	
	
	protected function pagination_config()
	{
		$config['first_link'] = 'First';
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['last_link'] = 'Last';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';

		$config['full_tag_open'] = '<div class="pagination"><ul>';
		$config['full_tag_close'] = '</ul></div>';
		$config['cur_tag_open'] = '<li class="active"><a href="">';
		$config['cur_tag_close'] = '</a></li>';
		
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		
		$config['prev_link'] = '&laquo;';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';

		$config['next_link'] = '&raquo;';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';	
		return $config;
	}
}

