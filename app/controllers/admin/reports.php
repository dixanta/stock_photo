<?php

class Reports extends Admin_Controller {

	//this is used when editing or adding a customer
	var $customer_id	= false;	

	function __construct()
	{		
		parent::__construct();
		remove_ssl();

		$this->auth->check_access('Admin', true);
		
		$this->load->model('Order_model');
		$this->load->model('Search_model');
		$this->load->helper(array('formatting'));
		
		$this->lang->load('report');
	}
	
	function index()
	{
		$data['page_title']	= lang('reports');
		$data['years']		= $this->Order_model->get_sales_years();
		$data['view_page']=$this->config->item('admin_folder').'/report/index';
		$this->load->view($this->_container, $data);
	}
	
	function best_sellers()
	{
		$start	= $this->input->post('start');
		$end	= $this->input->post('end');
		$data['best_sellers']	= $this->Order_model->get_best_sellers($start, $end);
		$data['view_page']=$this->config->item('admin_folder').'/reports/best_sellers';
		$this->load->view($this->_container, $data);
	
	}
	
	function sales()
	{
		$year			= $this->input->post('year');
		$data['orders']	= $this->Order_model->get_gross_monthly_sales($year);
		$data['view_page']=$this->config->item('admin_folder').'/reports/sales';
		$this->load->view($this->_container, $data);	
	}

}