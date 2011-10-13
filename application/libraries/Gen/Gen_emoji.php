<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

ci_require('HTML_Emoji', 'HTML', 'Emoji');
class Gen_emoji {

	var $CI;
	var $config_name;
	var $emoji;

	function __construct()
	{
		$this->CI =& get_instance();
		$this->config_name = 'gen_emoji';
		$this->CI->config->load($this->config_name, TRUE, TRUE);
		$this->emoji = HTML_Emoji::getInstance();
	}

	/**
	 * 入力値を変換する
	 */
	function input($msg)
	{
		$text = $this->emoji->filter($msg, array('DecToUtf8', 'HexToUtf8'));
		return $text;
	}

	/**
	 * 出力用に変換する
	 */
	function output($msg)
	{
		$this->emoji->setImageUrl($this->CI->gen_url->pc_url('emoji'));
		$text = $this->emoji->filter($msg, 'output');
		return $text;
	}
}

