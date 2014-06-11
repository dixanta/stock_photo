<?php

class Page extends Front_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('page_model');
	}

	function index($id = false)
	{
		//if there is no page id provided redirect to the homepage.
		$data['page']	= $this->page_model->get_page($id);
		if(!$data['page'])
		{
			show_404();
		}
		$this->load->model('page/page_model');
		$data['base_url']			= $this->uri->segment_array();
		
		$data['fb_like']			= true;

		$data['page_title']			= $data['page']->title;
		
		$data['meta']				= $data['page']->meta;
		$data['seo_title']			= (!empty($data['page']->seo_title))?$data['page']->seo_title:$data['page']->title;
		
		$data['gift_cards_enabled'] = $this->gift_cards_enabled;
		
		$data['view_page']='page';
		$this->view('page', $data);
	}
	
}

?>