<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * アプリケーションクラス
 */
class Gen_appli {

	var $CI;
	var $config_name;

	function __construct($param = array())
	{
		$this->CI =& get_instance();
		$this->config_name = 'gen_appli';
		$this->CI->config->load($this->config_name, TRUE, TRUE);
		$this->init();
	}

	function init()
	{
		$this->CI->config->set_item('appli_name', $this->CI->config->item('appli_name', $this->config_name));
	}

	function init_appli()
	{
	}

}
