<?php

require_once(APPPATH.'libraries/HTML/Emoji.php');
class Gen_mobile_hook {
	
		var $globals;
		var $emoji;
		var $CI;

	function __construct()
	{
		$this->emoji = HTML_Emoji::getInstance();
	}
	
	function input()
	{
		// $_GET
		if (is_array($_GET)) {
			foreach ($_GET as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $sub_key => $sub_value) {
						$value[$sub_key] = $this->emoji->filter($sub_value, 'input');
					}
					$_GET[$key] = $value;
				} else {
					$_GET[$key] = $this->emoji->filter($value, 'input');
				}
			}
		}
		// $_POST
		if (is_array($_POST)) {
			foreach ($_POST as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $sub_key => $sub_value) {
						$value[$sub_key] = $this->emoji->filter($sub_value, 'input');
					}
					$_POST[$key] = $value;
				} else {
					$_POST[$key] = $this->emoji->filter($value, 'input');
				}
			}
		}
		$_POST = array_merge($_POST, $_GET);
	}

	function output()
	{
		$this->CI =& get_instance();
		$this->CI->load->library('gen_flash');
		if ($this->CI->gen_flash->is_flash) {
			$this->CI->output->set_header('Cache-Control: no-cache');
			$this->CI->gen_flash->output();
		} else {
			$this->CI->load->library('gen_user_agent');
			if ($this->CI->gen_user_agent->is_au()) {
				$this->CI->output->set_header('Content-Type: application/xhtml+xml; charset=Shift_JIS');
				$this->CI->output->set_header('Cache-Control: no-cache');
			} else if ($this->CI->gen_user_agent->is_docomo()) {
				$this->CI->output->set_header('Content-Type: application/xhtml+xml; charset=Shift_JIS');
			} else if ($this->CI->gen_user_agent->is_softbank()) {
				$this->CI->output->set_header('Content-Type: application/xhtml+xml; charset=UTF-8');
			} else {
				$this->CI->output->set_header('Content-Type: text/html; charset=UTF-8');
			}
			$template = $this->CI->output->get_output();
			$this->emoji->setImageUrl($this->CI->gen_url->pc_url('emoji'));
			$template = $this->emoji->filter($template, 'output');
			$this->CI->output->set_output($template);
		}
	}

}
