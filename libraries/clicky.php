<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

//
// By: Spicer Matthews
// Company: Cloudmanic Labs, LLC (http://cloudmanic.com)
// Modified From: 
//

class Clicky
{
	private $_site_id;
	private $_sitekey_admin;
	private $_actions = array('pageview', 'download', 'outbound', 'click', 'custom', 'goal');
	private $_api_url = 'http://in.getclicky.com/in.php';
	private $_auth_params = '';
	private $_request_params = '';
	private $_last_request = '';
	
	//
	// Constructor …
	//
	function __construct()
	{
		$this->load->config('clicky');
		$this->set_configs($this->config->item('clicky_site_id'), $this->config->item('clicky_sitekey_admin'));
	}

	//
	// Set clicky configs.
	//
	function set_configs($site_id, $sitekey_admin)
	{
		if(empty($site_id) || empty($sitekey_admin))
		{
			show_error('Clicky: Must have the site_id and sitekey_admin set in the clicky.php file.');
		}
	
		$this->_site_id = $site_id;
		$this->_sitekey_admin = $sitekey_admin;
		$this->_auth_params = '?site_id=' . $this->_site_id . '&sitekey_admin=' . $this->_sitekey_admin;
	}
	
	// ------------------- Setters --------------------- //

	//
	// Set custom data. We pass in an index'ed array.
	//
	function set_custom($data)
	{
		if(! is_array($data))
		{
			show_error('Clicky: Must pass an array into set_custom()');
		}
		
		// Custom data, must come in as array of key=>values
		foreach($data AS $key => $row) 
		{
			$this->_request_params .= "&custom[" . urlencode($key) . "]=" . urlencode($row);
		}
	}

	//
	// Set a goal. Goals can come in as integer or array, for convenience.
	// $goal (int) - The id of the goal.
	// $goal (array) - array('id', 'name', 'revenue'). Pass in id or name not both.
	//
	function set_goal($goal)
	{
		if(is_numeric($goal)) 
		{
		  $this->_request_params .= "&goal[id]=$goal";
		  return true;
		} 
		  
		if(is_array($goal))
		{
			if(isset($goal['id']) && (! is_numeric($goal['id']))) 
			{
				show_error('Clicky: Must pass an array or integer into set_goal()');
			}
			
			foreach($goal AS $key => $row) 
			{
				$this->_request_params .= "&goal[" . urlencode($key) . "]=" . urlencode($row);
			}
		}
	}
	
	//
	// Set ip address.
	//
	function set_ip($ip = NULL)
	{
		// Default to server var if set.
		if(is_null($ip) && isset($_SERVER['REMOTE_ADDR'])) 
		{
		  $ip = $_SERVER['REMOTE_ADDR']; 
		}
		
		// Validate the ip address.
		if(! preg_match("#^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$#", $ip)) 
		{
			show_error('Clicky: Not a valid IP address.');
		}
		
		$this->_request_params .= "&ip_address=$ip";
	}

	//
	// Set session.
	//
	function set_session($session)
	{		
		// We need either a session_id or an ip_address...
		if(! is_numeric($session)) 
		{
			show_error('Clicky: Not a valid session id must be a numberic.');
		}
		
		$this->_request_params .= "&session_id=$session";
	}
	
	//
	// Set refer. We should only call this once per session.
	//
	function set_refer($ref)
	{
		$this->_request_params .= '&ref=' . urlencode($ref);
	}
	
	//
	// Set user agent. We should only call this once per session.
	//
	function set_user_agent($ua)
	{
		$this->_request_params .= '&ua=' . urlencode($ua);
	}

	//
	// Set href. 
	//
	function set_href($href)
	{
		$this->_request_params .=  "&href=" . urlencode($href);
	}
	
	//
	// Set page title. 
	//
	function set_title($title)
	{
		$this->_request_params .=  "&title=" . urlencode($title);
	}
	
	// ------------------- Get Data -------------------- //
		
	// 
	// Returns the full url request of the last request to clicky.
	//
	function get_last_request()
	{
		return $this->_last_request;
	}

	// ------------------- Request Functions ----------- //
	
	//
	// Based On: http://goo.gl/jxW5u
	// This function allows you to log visitor actions to Clicky from an internal script. 
	// For example, some sites use internal redirect scripts for external links; 
	// Clicky would not normally be able to track these, but with this script it is possible.
	//
	function log_action($type)
	{		
		// If we did not pass in the correct type we default to a "pageview".
		if(! in_array($type, $this->_actions))
		{
			show_error('Clicky: Action type unknown log_action()');
		}
		
		$this->_last_request = $this->_api_url . $this->_auth_params . $this->_request_params . "&type=" . $type;
		
		return file_get_contents($this->_last_request) ? true : false;
	}
	
	// 
	// Clear all request vars.
	//
	function clear()
	{
		$this->_request_params = '';
	}

	//
	// Make it so we can use the CI super object.
	//
	function __get($key)
	{
		$CI =& get_instance();
		return $CI->$key;
	}
}

/* End File */