<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gen_file implements Iterator {

	var $fine_name;
	var $fp;
	var $rowno;
	var $current_line;

	function __construct($params=array())
	{
		$this->file_name = $this->is_set($params, 'file');
		if ($this->file_name == '') {
			$this->fp = FALSE;
		} else {
			$this->fp = fopen($this->file_name, $this->is_set($params, 'open_type', 'r'));
			$this->next();
		}
		$this->rowno = 0;
	}

	function is_set($array, $key, $default='')
	{
		return isset($array[$key]) ? $array[$key] : $default;
	}

	function list_file($dir)
	{
		$list = scandir($dir);
		if ($list === FALSE) return array();
		$ret = array();
		foreach ($list as $name) {
			if (is_file($dir.$name) && $name != '.' && $name != '..') {
				$fileinfo['basename'] = mb_substr($name, 0, mb_strrpos($name, '.'));
				$fileinfo['filename'] = $name;
				$fileinfo['fullpath'] = $dir.$name;
				$fileinfo['ext'] = trim(mb_substr($name, mb_strrpos($name, '.')+1), '.');
				$ret[] = $fileinfo;
			}
		}
		return $ret;
	}

	function open_file($file_name, $open_type='r')
	{
		if ($this->fp !== FALSE) {
			@fclose($this->fp);
		}
		$this->file_name = $file_name;
		$this->fp = fopen($file_name, $open_type);
		$this->rowno = 0;
		$this->next();
	}

	function close()
	{
		if (!$this->fp) return;
		fclose($this->fp);
		$this->fp = FALSE;
		$this->rowno = 0;
		$this->file_name = '';
	}

	function write_line($line)
	{
		if ($this->fp === FALSE) return FALSE;
		$line = ltrim($line, PHP_EOL);
		return fwrite($this->fp, $line.PHP_EOL);
	}

	function rewind()
	{
		if (!$this->fp) return;
		rewind($this->fp);
		$this->rowno = 0;
		$this->next();
	}

	function current()
	{
		return $this->current_line;
	}

	function key()
	{
		return $this->rowno;
	}

	function next()
	{
		if ($this->fp === FALSE) return FALSE;
		$this->current_line = fgets($this->fp);
		if ($this->current_line == FALSE) return FALSE;
		$this->current_line = mb_convert_encoding($this->current_line, 'UTF-8', 'UTF-8, eucjp-win, sjis-win, auto');
		$this->current_line = trim($this->current_line);
		$this->rowno++;
		return $this->current_line;
	}

	function valid()
	{
		if (feof($this->fp)) {
			return FALSE;
		}
		return TRUE;
	}

	function output($path, $text, $enc='UTF-8')
	{
		$dir = '';
		if (mb_substr($path, 0, 1) == '/') $dir = '/';
		$paths = explode('/', $path);
		$filename = array_pop($paths);
		foreach ($paths as $value) {
			$dir .= $value;
			if (!file_exists($dir)) {
				mkdir($dir);
			}
			$dir .= '/';
		}
		$text = mb_convert_encoding($text, $enc, 'UTF-8, eucjp-win, sjis-win, auto');
		$ret = file_put_contents($dir.$filename, $text);
		return $ret;
	}

	function read_all($path, $enc='UTF-8')
	{
		$text = file_get_contents($path);
		$ret = mb_convert_encoding($text, $enc, 'UTF-8, eucjp-win, sjis-win, auto');
		return $ret;
	}

}
