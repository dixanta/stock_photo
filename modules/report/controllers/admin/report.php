<?php

class Report extends Admin_Controller {

	//this is used when editing or adding a customer
	var $customer_id	= false;	

	function __construct()
	{		
		parent::__construct();
		//remove_ssl();

		$this->auth->check_access('Admin', true);
		
		$this->load->model('order/order_model');
		$this->load->model('search/search_model');
		$this->load->helper(array('formatting'));
		
		$this->lang->load('report');
	}
	
	function index()
	{
		$data['page_title']	= lang('reports');
		$data['years']		= $this->order_model->get_sales_years();
		$data['view_page']='report/admin/report/index';
		$this->load->view($this->_container, $data);
	}
	
	function best_sellers()
	{	
		$start	= $this->input->post('start');
		$end	= $this->input->post('end');
		$data['best_sellers']	= $this->order_model->get_best_sellers($start, $end);
		$data['view_page']='report/admin/report/best_sellers';
		$this->load->view($this->_container, $data);
		
	
	}
	
	function sales()
	{	
		$year = $this->input->post('year');
		$data['orders']	= $this->order_model->get_gross_monthly_sales($year);
		$data['view_page']='report/admin/report/sales';
		$this->load->view($this->_container, $data);	
	}

}

?>