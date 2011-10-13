<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gen_view {

	var $config_name = 'gen_view';
	var $appli_name;

	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->config->load($this->config_name, TRUE, TRUE);
		$config = $this->CI->config->item($this->config_name);
	}

	function load_template($template_name, $data = array(), $key = NULL, $block_name = NULL)
	{
		if ($key === NULL) $key = $this->CI->config->item('appli_name');
		if ($block_name === NULL) $block_name = 'main';
		$config = $this->CI->config->item($key, $this->config_name);
		$template = $this->CI->load->view($template_name, $data, true);
		$template = $this->CI->gen_template->main_block($template, $block_name);
		if ($config !== false && isset($config['header'])) {
			$subtemplate = $this->CI->load->view($config['header'], $data, true);
			$template = $this->CI->gen_template->main_block($subtemplate, $block_name).$template;
		}
		if ($config !== false && isset($config['footer'])) {
			$subtemplate = $this->CI->load->view($config['footer'], $data, true);
			$template = $template.$this->CI->gen_template->main_block($subtemplate, $block_name);
		}
		return $template;
	}

}
