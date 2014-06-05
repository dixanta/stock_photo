<?php

class Giftcard extends Front_Controller{


    function __construct()
    {
        parent::__construct();

        //make sure we're not always behind ssl
        remove_ssl();
    }

    function index()
    {
        if(!$this->gift_cards_enabled) redirect('/');

        // Load giftcard settings
        $gc_settings = $this->Settings_model->get_settings("gift_cards");

        $this->load->library('form_validation');

        $data['allow_custom_amount']	= (bool) $gc_settings['allow_custom_amount'];
        $data['preset_values']			= explode(",",$gc_settings['predefined_card_amounts']);

        if($data['allow_custom_amount'])
        {
            $this->form_validation->set_rules('custom_amount', 'lang:custom_amount', 'numeric');
        }

        $this->form_validation->set_rules('amount', 'lang:amount', 'required');
        $this->form_validation->set_rules('preset_amount', 'lang:preset_amount', 'numeric');
        $this->form_validation->set_rules('gc_to_name', 'lang:recipient_name', 'trim|required');
        $this->form_validation->set_rules('gc_to_email', 'lang:recipient_email', 'trim|required|valid_email');
        $this->form_validation->set_rules('gc_from', 'lang:sender_email', 'trim|required');
        $this->form_validation->set_rules('message', 'lang:custom_greeting', 'trim|required');

        if ($this->form_validation->run() == FALSE)
        {
            $data['error']				= validation_errors();
            $data['page_title']			= lang('giftcard');
            $data['gift_cards_enabled']	= $this->gift_cards_enabled;
            $this->load->view('giftcards', $data);
        }
        else
        {

            // add to cart

            $card['price'] = set_value(set_value('amount'));

            $card['id']				= -1; // just a placeholder
            $card['sku']			= lang('giftcard');
            $card['base_price']		= $card['price']; // price gets modified by options, show the baseline still...
            $card['name']			= lang('giftcard');
            $card['code']			= generate_code(); // from the string helper
            $card['excerpt']		= sprintf(lang('giftcard_excerpt'), set_value('gc_to_name'));
            $card['weight']			= 0;
            $card['quantity']		= 1;
            $card['shippable']		= false;
            $card['taxable']		= 0;
            $card['fixed_quantity'] = true;
            $card['is_gc']			= true; // !Important
            $card['track_stock']	= false; // !Imporortant

            $card['gc_info'] = array("to_name"	=> set_value('gc_to_name'),
                "to_email"	=> set_value('gc_to_email'),
                "from"		=> set_value('gc_from'),
                "personal_message"	=> set_value('message')
            );

            // add the card data like a product
            $this->go_cart->insert($card);

            redirect('cart/view_cart');
        }
    }
}
?>