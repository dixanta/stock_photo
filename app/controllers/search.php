<?php
class Search extends Front_Controller{

    function __construct()
    {
        parent::__construct();

        //make sure we're not always behind ssl
        remove_ssl();
    }

    function index($code=false, $page = 0)
    {
        $this->load->model('Search_model');

        //check to see if we have a search term
        if(!$code)
        {
        //if the term is in post, save it to the db and give me a reference
        $term		= $this->input->post('term', true);
        $code		= $this->Search_model->record_term($term);

        // no code? redirect so we can have the code in place for the sorting.
        // I know this isn't the best way...
        redirect('search/index/'.$code.'/'.$page);
        }
        else
        {
        //if we have the md5 string, get the term
        $term	= $this->Search_model->get_term($code);
        }

        if(empty($term))
        {
            //if there is still no search term throw an error
            //if there is still no search term throw an error
        $this->session->set_flashdata('error', lang('search_error'));
        redirect('cart');
        }
            $data['page_title']			= lang('search');
            $data['gift_cards_enabled']	= $this->gift_cards_enabled;

        //fix for the category view page.
        $data['base_url']			= array();

        $sort_array = array(
        'name/asc' => array('by' => 'name', 'sort'=>'ASC'),
        'name/desc' => array('by' => 'name', 'sort'=>'DESC'),
        'price/asc' => array('by' => 'price', 'sort'=>'ASC'),
        'price/desc' => array('by' => 'price', 'sort'=>'DESC'),
        );
        $sort_by	= array('by'=>false, 'sort'=>false);

        if(isset($_GET['by']))
        {
            if(isset($sort_array[$_GET['by']]))
            {
            $sort_by	= $sort_array[$_GET['by']];
            }
        }


        if(empty($term))
        {
            //if there is still no search term throw an error
        $this->load->view('search_error', $data);
		
        }
        else
        {

            $data['page_title']	= lang('search');
            $data['gift_cards_enabled'] = $this->gift_cards_enabled;

            //set up pagination
        $this->load->library('pagination');
        $config['base_url']		= base_url().'search/'.$code.'/';
        $config['uri_segment']	= 4;
        $config['per_page']		= 20;

        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'
		] = '</li>';
        $config['last_link'] = 'Last';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';

        $config['full_tag_open'] = '<div class="pagination"><ul>';
        $config['full_tag_close'] = '</ul></div>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';

        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';

        $config['prev_link'] = '&laquo;';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';

        $config['next_link'] = '&raquo;';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';

        $result	= $this->Product_model->search_products($term, $config['per_page'], $page, $sort_by['by'], $sort_by['sort']);
        $config['total_rows']	= $result['count'];
        $this->pagination->initialize($config);

        $data['products']= $result['products'];
            foreach ($data['products'] as &$p)
            {
                $p->images	= (array)json_decode($p->images);
                $p->options	= $this->Option_model->get_product_options($p->id);
            }
			
        $this->load->view('category', $data);
        }
    }
}
?>