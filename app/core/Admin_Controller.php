<?php
class Admin_Controller extends Base_Controller
{	var $_container= 'admin/container.php';
	var $admin_url;
        function __construct()
        {

            parent::__construct();

            $this->load->library('auth');
            $this->auth->is_logged_in(uri_string());

//load the base language file
        $this->lang->load('admin_common');
        $this->lang->load('goedit');
		$this->admin_url = site_url($this->config->item('admin_folder')).'/';
        }
}

?>