<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Actions extends CI_Controller 
{
	//
	// Constructor ….
	//
	function __construct()
	{	
		parent::__construct();
		$this->load->spark('cloudmanic-clicky/0.0.1');
	}

	//
	// Below are examples of the different actions you 
	// can track with clicky. Based on http://goo.gl/jxW5u
	//
	function index()
	{		
		// Track a page view.
		$this->clicky->set_ip('98.137.149.56');
		$this->clicky->set_refer('http://cloudmanic.com');
		$this->clicky->set_user_agent('php-script');
		$this->clicky->set_href('http://localhost/blah.php');
		$this->clicky->set_title('LH : Blah : Signup');
		$this->clicky->log_action('pageview');
		
		// Track a goal action.
		$this->clicky->set_goal(array('name' => 'My First Goal', 'revenue' => '5.00'));
		$this->clicky->log_action('goal');
		
		// Track a download
		$this->clicky->set_ip('98.137.149.56');
		$this->clicky->set_user_agent('php-script');
		$this->clicky->set_href('/some/download.zip');
		$this->clicky->log_action('download');

		// Track click
		$this->clicky->set_ip('98.137.149.56');
		$this->clicky->set_href('/blog');
		$this->clicky->log_action('click');
		
		// Track outbound
		$this->clicky->set_ip('98.137.149.56');
		$this->clicky->set_href('http://yahoo.com');
		$this->clicky->log_action('outbound');

		// Track custom
		$this->clicky->set_session('123');
		$this->clicky->set_custom(array('isloggedin' => 'Yes'));
		$this->clicky->log_action('custom');
		echo $this->clicky->get_last_request(); // Show the url request we just made.
	}
}

/* End File */