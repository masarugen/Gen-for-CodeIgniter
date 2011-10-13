<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Generate_mobileinfo extends MY_Controller {

	var $sql_path = '../sql/';
	var $data_path = '../data/';
	var $config_name = 'gen_generate_mobileinfo';
	var $loadfiles;
	var $header;
	var $usedata;
	var $exclude_word;
	var $merge_key;

	function __construct()
	{
		parent::__construct();
		$this->cli_error();
		$this->config->load($this->config_name, TRUE, TRUE);
		$this->load->library('Gen/gen_file');
		$this->load->library('Gen/gen_csv');
		$this->loadfiles = $this->config->item('loadfiles', $this->config_name);
		$this->header = $this->config->item('header', $this->config_name);
		$this->usedata = $this->config->item('usedata', $this->config_name);
		$this->exclude_word = array('カメラ無し', 'カメラなし');
		$this->merge_key = '機種名';
	}

	function _stdin($out)
	{
		echo $out;
		$stdin = fopen('php://stdin', 'r');
		if (!$stdin) {
			echo "stdin error!!\n";
			exit(1);
		}
		$in = trim(fgets($stdin));
		fclose($stdin);
		return $in;
	}

	function index()
	{
		$input_date = '';
		while (!$this->gen_calendar->check_calendar($input_date)) {
			$input_date = $this->_stdin('Please input load file date [yyyy-mm-dd]:');
			foreach ($this->loadfiles as $base_name) {
				$file = $this->data_path.'mobileinfo/'.$base_name.'_'.$input_date.'.csv';
				if (!file_exists($file)) {
					echo "file not found!! [$file] \n";
					$input_date = '';
					break;
				}
			}
		}
		$data = $this->_load_files($input_date);
		$this->_save_mobile_info($data);
	}

	function _save_mobile_info($data)
	{
		$this->load->model('mobile_info_model');
		foreach ($data as $type => $line) {
			$this->mobile_info_model->reset();
			$this->mobile_info_model->type = $type;
			$is_update = FALSE;
			$select = $this->mobile_info_model->select_one();
			if ($select !== FALSE) {
				$this->mobile_info_model->mobile_info_id = $select['mobile_info_id'];
				$is_update = TRUE;
			}
			foreach ($this->loadfiles as $base_name) {
				$header = $this->header[$base_name];
				$usedata = $this->usedata[$base_name];
				$file_line = $line[$base_name];
				foreach ($usedata as $csv => $tbl) {
					$index = array_search($csv, $header);
					if ($tbl == 'type') {
						$this->mobile_info_model->$tbl = $type;
					} else if ($tbl == 'is_flash') {
						if ($file_line[$index] == '0') {
							$is_flash = 0;
						} else {
							$is_flash = 1;
						}
						$this->mobile_info_model->$tbl = $is_flash;
					} else {
						$this->mobile_info_model->$tbl = $file_line[$index];
					}
				}
			}
			$this->mobile_info_model->save($is_update);
		}
	}

	function _load_files($input_date)
	{
		$data = array();
		foreach ($this->loadfiles as $base_name) {
			$file = $this->data_path.'mobileinfo/'.$base_name.'_'.$input_date.'.csv';
			$this->gen_csv->open_file($file);
			$header = $this->header[$base_name];
			$header_line = $this->gen_csv->current();
			$key_index = array_search($this->merge_key, $header_line);
			foreach ($this->gen_csv as $i => $line) {
				if ($i == 1) continue;
				$type = $line[$key_index];
				$type = $this->_exclude_word($type);
				if (!isset($data[$type])) $data[$type] = array();
				$data[$type][$base_name] = $line;
			}
		}
		return $data;
	}

	function _exclude_word($word)
	{
		$pattern = '';
		foreach ($this->exclude_word as $value) {
			$patterns[] = preg_quote($value, '/');
		}
		$pattern = implode('|', $patterns);
		$ret = preg_replace('/'.$pattern.'/', '', $word);
		return $ret;
	}
}

