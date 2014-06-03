<?php

class Front_Controller extends Base_Controller
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

            //load GoCart library
            $this->load->library('Go_cart');

            //load needed models
            $this->load->model(array('Page_model', 'Product_model', 'Digital_Product_model', 'Gift_card_model', 'Option_model', 'Order_model', 'Settings_model'));

            //load helpers
            $this->load->helper(array('form_helper', 'formatting_helper'));

             //fill in our variables
            $this->categories	= $this->Category_model->get_categories_tierd(0);
            $this->pages		= $this->Page_model->get_pages();

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

             //load the theme package
            $this->load->add_package_path('themes/'.$this->config->item('theme').'/');
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
        This function simple calls $this->load->view()
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
}
?>