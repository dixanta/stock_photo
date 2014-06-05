<?php

class Category extends Front_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	function index($id)
	{
		
		//get the category
		$data['category'] = $this->category_model->get_category($id);
				
		if (!$data['category'] || $data['category']->enabled==0)
		{
			show_404();
		}
				
		$product_count = $this->Product_model->count_products($data['category']->id);
		
		//set up pagination
		$segments	= $this->uri->total_segments();
		$base_url	= $this->uri->segment_array();
		
		if($data['category']->slug == $base_url[count($base_url)])
		{
			$page	= 0;
			$segments++;
		}
		else
		{
			$page	= array_splice($base_url, -1, 1);
			$page	= $page[0];
		}
		
		$data['base_url']	= $base_url;
		$base_url			= implode('/', $base_url);
		
		$data['product_columns']	= $this->config->item('product_columns');
		$data['gift_cards_enabled'] = $this->gift_cards_enabled;
		
		$data['meta']		= $data['category']->meta;
		$data['seo_title']	= (!empty($data['category']->seo_title))?$data['category']->seo_title:$data['category']->name;
		$data['page_title']	= $data['category']->name;
		
		$sort_array = array(
							'name/asc' => array('by' => 'products.name', 'sort'=>'ASC'),
							'name/desc' => array('by' => 'products.name', 'sort'=>'DESC'),
							'price/asc' => array('by' => 'sort_price', 'sort'=>'ASC'),
							'price/desc' => array('by' => 'sort_price', 'sort'=>'DESC'),
							);
		$sort_by	= array('by'=>'sequence', 'sort'=>'ASC');
	
		if(isset($_GET['by']))
		{
			if(isset($sort_array[$_GET['by']]))
			{
				$sort_by	= $sort_array[$_GET['by']];
			}
		}
		
		//set up pagination
		$this->load->library('pagination');
		
		$config=$this->pagination_config(); //From Front Controller
		
		$config['base_url']		= site_url($base_url);
		
		$config['uri_segment']	= $segments;
		$config['per_page']		= 24;
		$config['total_rows']	= $product_count;
		
		$this->pagination->initialize($config);
		
		
		$data['products']	= $this->Product_model->get_products($data['category']->id, $config['per_page'], $page, $sort_by['by'], $sort_by['sort']);
		
		
		foreach ($data['products'] as &$p)
		{
			$p->images	= (array)json_decode($p->images);
			$p->options	= $this->Option_model->get_product_options($p->id);
		}
		
		$this->view('category', $data);
	}

	
}