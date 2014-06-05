<?php

class Page extends Front_Controller{

    function __construct()
    {
        parent::__construct();

        //make sure we're not always behind ssl
        remove_ssl();
    }

    function index($id = false)
    {
        //if there is no page id provided redirect to the homepage.
        $data['page']	= $this->Page_model->get_page($id);
        if(!$data['page'])
        {
            show_404();
        }
        $this->load->model('Page_model');
        $data['base_url']			= $this->uri->segment_array();

        $data['fb_like']			= true;

        $data['page_title']			= $data['page']->title;

        $data['meta']				= $data['page']->meta;
        $data['seo_title']			= (!empty($data['page']->seo_title))?$data['page']->seo_title:$data['page']->title;

        $data['gift_cards_enabled'] = $this->gift_cards_enabled;

        $this->load->view('page', $data);
    }
}
?>