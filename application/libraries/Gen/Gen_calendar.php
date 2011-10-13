<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * カレンダークラス
 */
class Gen_calendar {

	var $CI;
	var $config_name;

	function __construct($param = array())
	{
		$this->CI =& get_instance();
		$this->config_name = 'gen_calendar';
		$this->CI->config->load($this->config_name, FALSE, TRUE);
	}

	function check_calendar($date, $format = 'yyyy/mm/dd')
	{
		$ret = FALSE;
		if (mb_strlen($format) === mb_strlen($date)) {
			$y_start = mb_strpos($format, 'yyyy');
			$y_len = mb_strlen('yyyy');
			$m_start = mb_strpos($format, 'mm');
			$m_len = mb_strlen('mm');
			$d_start =  mb_strpos($format, 'dd');
			$d_len = mb_strlen('dd');
			$year = mb_substr($date, $y_start, $y_len);
			$mon  = mb_substr($date, $m_start, $m_len);
			$day  = mb_substr($date, $d_start, $d_len);
			$ret = checkdate($mon, $day, $year);
		}
		return $ret;
	}

	function time()
	{
		return $this->CI->config->item('time');
	}

	function microtime()
	{
		return $this->CI->config->item('microtime');
	}

	function raw_now()
	{
		return $this->CI->config->item('raw_now');
	}

	function raw_date()
	{
		return $this->CI->config->item('raw_date');
	}

	function raw_time()
	{
		return $this->CI->config->item('raw_time');
	}

	function now()
	{
		return $this->CI->config->item('now');
	}

	function now_date()
	{
		return $this->CI->config->item('now_date');
	}

	function now_time()
	{
		return $this->CI->config->item('now_time');
	}

	function now_y()
	{
		return $this->CI->config->item('now_y');
	}

	function now_m()
	{
		return $this->CI->config->item('now_m');
	}

	function now_d()
	{
		return $this->CI->config->item('now_d');
	}

	function now_h()
	{
		return $this->CI->config->item('now_h');
	}

	function now_i()
	{
		return $this->CI->config->item('now_i');
	}

	function now_s()
	{
		return $this->CI->config->item('now_s');
	}

}
