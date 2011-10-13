<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * データベースへの接続処理
 */
class Gen_connect {

	var $CI;
	var $config_name = 'gen_connect';
	var $using = NULL;
	var $connected = NULL;
	var $databases = NULL;

	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->config->load($this->config_name, TRUE, TRUE);
		$this->connected = array();
		$databases = $this->CI->config->item($this->config_name);
		if (isset($databases[$this->CI->config->item('appli_name')])) {
			$this->databases = $databases[$this->CI->config->item('appli_name')];
		} else {
			$this->CI->gen_exception->exception('application connection not setting');
		}
	}

	/**
	 * データベースの接続処理
	 */
	function __call($name, $args)
	{
		if (!in_array($name, $this->connected)) {
			array_push($this->connected, $name);
			if (isset($this->databases[$name])) {
				$this->$name = $this->CI->load->database($this->databases[$name], TRUE);
				$this->$name->is_transaction = FALSE;
			} else {
				$this->CI->gen_exception->exception('not exists database connection require');
			}
		}
		$this->using = $name;
		return $this->$name;
	}

	/**
	 * データベースへの接続を閉じる
	 */
	function close($name)
	{
		if (!in_array($name, $this->connected)) { return; }
		if ($this->$name->is_transaction) {
			$this->$name->trans_rollback();
		}
		$this->$name->close();
		$this->using = null;
		$this->$name = null;
		$key = array_search($name, $this->connected);
		unset($this->connected[$key]);
	}
}
