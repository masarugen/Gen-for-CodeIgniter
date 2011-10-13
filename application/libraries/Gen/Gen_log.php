<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gen_log {

	var $CI;
	var $config_name;
	var $debug_level;
	var $info_level;
	var $error_level;
	var $sql_level;
	var $cache_level;

	function __construct()
	{
		$this->CI =& get_instance();
		$this->config_name = 'gen_log';
		$this->CI->config->load($this->config_name, TRUE, TRUE);
		$this->_initialize();
	}

	function _initialize()
	{
		$this->debug_level = $this->CI->config->item('debug_level', $this->config_name);
		$this->info_level  = $this->CI->config->item('info_level', $this->config_name);
		$this->error_level = $this->CI->config->item('error_level', $this->config_name);
		$this->sql_level = $this->CI->config->item('sql_level', $this->config_name);
		$this->cache_level = $this->CI->config->item('cache_level', $this->config_name);
	}

	/**
	 * デバッグログ
	 */
	function debug($msg)
	{
		$this->_log_message($this->debug_level, $msg);
	}

	/**
	 * インフォメーションログ
	 */
	function info($msg)
	{
		$this->_log_message($this->info_level, $msg);
	}

	/**
	 * エラーログ
	 */
	function error($msg)
	{
		$this->_log_message($this->error_level, $msg);
	}

	/**
	 * SQLログ
	 */
	function sql($msg)
	{
		$this->_log_message($this->sql_level, $msg);
	}

	/**
	 * CACHEログ
	 */
	function cache($msg)
	{
		$this->_log_message($this->cache_level, $msg);
	}

	/**
	 * ログメッセージ
	 */
	function _log_message($log_level, $msg)
	{
		log_message($log_level, $msg);
	}

}

