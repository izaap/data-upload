<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// ------------------------------------------------------------------------

/**
 * Email Helpers
 * Inspiration from PHP Cookbook by David Sklar and Adam Trachtenberg
 * 
 * @author		Sivanesan
 * 
 */

// ------------------------------------------------------------------------

/**
 * Email Config
 *
 *
 */
if ( ! function_exists('email_config'))
{
	function email_config()
	{
	  
/*	working
		  $config['protocol'] = 'smtp';
		  $config['smtp_host'] = 'ssl://smtp.gmail.com';
		  $config['smtp_port'] = 465;
		  $config['smtp_user'] = 'siva.vivid@gmail.com';
		  $config['smtp_pass'] = 'siva1234';
		  $config['mailtype'] = 'html';
*/
		  
		  
		  //testing
		  
		  $config['protocol'] = 'mail';
		  //$config['smtp_host'] = 'ssl://smtp.gmail.com';
		  //$config['smtp_port'] = 465;
		  //$config['smtp_user'] = 'siva.vivid@gmail.com';
		  //$config['smtp_pass'] = 'siva1234';
		  $config['mailtype'] = 'html';
		  
		  
		  return $config;		 
	}
}



/* End of file email_helper.php */
/* Location: ./system/helpers/email_helper.php */
