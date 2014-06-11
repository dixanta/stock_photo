<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| EMAIL CONFING
| -------------------------------------------------------------------
| Configuration of outgoing mail server.
| */
$config['protocol']='smtp';
$config['smtp_host']='smtp.vianet.com.np';
$config['smtp_port']='25';

//$config['smtp_user']='user';
//$config['smtp_pass']='password';
//$config['_smtp_auth']=TRUE;
$config['charset']='iso-8859-1';
$config['wordwrap'] = true;
$config['newline']="\r\n";
/* End of file email.php */
/* Location: ./system/application/config/email.php */