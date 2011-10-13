<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 例外処理クラス
 */
class Gen_exception {

	var $CI;
	var $config_name;
	var $is_error;

	function __construct()
	{
		$this->CI =& get_instance();
		$this->config_name = 'gen_exception';
		$this->CI->config->load($this->config_name, TRUE, TRUE);
		$this->is_error = FALSE;
	}

	/**
	 * 例外処理
	 */
	function exception($msg, $status_code = 200)
	{
		if (isset($this->CI->gen_connect)) {
			foreach ($this->CI->gen_connect->connected as $db_name) {
				$this->CI->gen_connect->close($db_name);
			}
		}
		$this->CI->gen_log->error("$status_code : $msg");
		show_error($msg, $status_code);
	}

	/**
	 * エラー処理
	 */
	function error($msg)
	{
		$this->is_error = TRUE;
		$this->CI->gen_log->error($msg);
	}

	/**
	 * エラーチェック
	 */
	function is_error()
	{
		$ret = FALSE;
		if ($this->is_error === TRUE) $ret = TRUE;
		return $ret;
	}

}

