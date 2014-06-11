<?php

class Product extends Front_Controller{

    function __construct()
    {
        parent::__construct();

        //make sure we're not always behind ssl
       // remove_ssl();
	   
    }

    function index($id)
    {
				
        //get the product
		
        $data['product']	= $this->Product_model->get_product($id);


        if(!$data['product'] || $data['product']->enabled==0)
        {
            show_404();
        }

        $data['base_url']			= $this->uri->segment_array();

        // load the digital language stuff
        $this->lang->load('digital_product');

        $data['options']	= $this->Option_model->get_product_options($data['product']->id);

        $related			= $data['product']->related_products;
        $data['related']	= array();



        $data['posted_options']	= $this->session->flashdata('option_values');

        $data['page_title']			= $data['product']->name;
        $data['meta']				= $data['product']->meta;
        $data['seo_title']			= (!empty($data['product']->seo_title))?$data['product']->seo_title:$data['product']->name;

        if($data['product']->images == 'false')
        {
            $data['product']->images = array();
        }
        else
        {
            $data['product']->images	= array_values((array)json_decode($data['product']->images));
        }

        $data['gift_cards_enabled'] = $this->gift_cards_enabled;
		$data['view_page'] = 'product';
        $this->load->view($this->_container, $data);
    }

}


?>