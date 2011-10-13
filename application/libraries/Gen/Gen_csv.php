<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

ci_require('Gen_file', 'Gen');
class Gen_csv extends Gen_file {

	var $delimiter;
	var $raw_line;

	function __construct($params=array())
	{
		parent::__construct($params);
		$this->delimiter = $this->is_set($params, 'delimiter', ',');
	}

	function open_file($file_name, $delimiter=',', $open_type='r')
	{
		parent::open_file($file_name, $open_type);
		$this->delimiter = $delimiter;
	}

	function raw()
	{
		return $this->raw_line;
	}

	function next()
	{
		parent::next();
		if ($this->current_line == false) return false;
		$this->raw_line = $this->current_line;
		$this->current_line = explode($this->delimiter, $this->current_line);
		return $this->current_line;
	}

	function trim($str,$charlist='')
	{
		if ($charlist != '') {
			$str = trim($str, $charlist);
		}
		$str = trim($str);
		return $str;
	}
}
