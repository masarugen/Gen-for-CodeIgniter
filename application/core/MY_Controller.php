<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->data = array();
		$this->init();
	}

	function init()
	{

		// アプリケーションの初期化
		$this->gen_appli->init_appli();

		$this->data['base_url'] = $this->config->base_url();
		$this->data['site_url'] = $this->config->site_url();
		$this->data['image_url'] = $this->config->base_url().'images/';
		$this->data['js_url'] = $this->config->site_url().'js/';
		$this->data['css_url'] = $this->config->site_url().'css/';
		$this->data['encoding'] = $this->gen_user_agent->encoding();
		$this->data['onload'] = '';
		$this->data['message_list'] = '';

		if ($this->input->is_cli_request()) return;
	}

	function add_message($msg)
	{
		if (!$msg) return;
		$this->data['message_list'][] = $msg;
	}

	function notification($msg)
	{
		if (!$msg) return;
		$this->data['onload'] = "notification('$msg');";
	}

	function cli_error()
	{
		if (!$this->input->is_cli_request()) {
			show_404();
		}
	}

	function if_pagedefault()
	{
		return true;
	}

}
