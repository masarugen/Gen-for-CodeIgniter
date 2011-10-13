<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gen_user_agent {

	var $CI;
	var $config_name;

	var $is_pc       = FALSE;
	var $is_mobile   = FALSE;
	var $is_softbank = FALSE;
	var $is_docomo   = FALSE;
	var $is_au       = FALSE;
	var $is_android  = FALSE;
	var $is_iphone   = FALSE;
	var $is_ipad     = FALSE;
	var $is_emobile  = FALSE;
	var $is_willcom  = FALSE;
	var $is_snsplatform = FALSE;
	var $is_apachebench = FALSE;
	var $is_invalid    = FALSE;
	var $useragent;
	var $device;
	var $x_jphone_msname;

	function __construct()
	{
		$this->CI =& get_instance();
		$this->config_name = 'gen_user_agent';
		$this->CI->config->load($this->config_name, TRUE, TRUE);
		$this->_initialize();
	}

	function _initialize()
	{
		$this->pc       = $this->CI->config->item('pc', $this->config_name);
		$this->smart    = $this->CI->config->item('smart', $this->config_name);
		$this->mobile   = $this->CI->config->item('mobile', $this->config_name);
		$this->invalid  = $this->CI->config->item('invalid', $this->config_name);
		$this->docomo   = $this->CI->config->item('docomo', $this->config_name);
		$this->softbank = $this->CI->config->item('softbank', $this->config_name);
		$this->au       = $this->CI->config->item('au', $this->config_name);
		$this->emobile  = $this->CI->config->item('emobile', $this->config_name);
		$this->willcom  = $this->CI->config->item('willcom', $this->config_name);
		$this->iphone   = $this->CI->config->item('iphone', $this->config_name);
		$this->ipad     = $this->CI->config->item('ipad', $this->config_name);
		$this->android  = $this->CI->config->item('android', $this->config_name);
		$this->sns      = $this->CI->config->item('sns_platform', $this->config_name);
		$this->ab       = $this->CI->config->item('apache_bench', $this->config_name);

		$this->CI->load->library('user_agent');
		$this->useragent = strtolower($this->CI->agent->agent_string());

		$carrier_setting = FALSE;

		// check docomo
		if ($carrier_setting === FALSE) {
			$regex = $this->_create_regex_word($this->docomo);
			if ($this->_check_carrier($regex)) {
				$this->is_docomo = TRUE;
				$carrier_setting = TRUE;
				$this->device = $this->_device_docomo();
			}
		}

		// check softbank
		if ($carrier_setting === FALSE) {
			$regex = $this->_create_regex_word($this->softbank);
			if ($this->_check_carrier($regex)) {
				$this->is_softbank = TRUE;
				$carrier_setting = TRUE;
				$this->device = $this->_device_softbank();
			}
		}

		// check au
		if ($carrier_setting === FALSE) {
			$regex = $this->_create_regex_word($this->au);
			if ($this->_check_carrier($regex)) {
				$this->is_au = TRUE;
				$carrier_setting = TRUE;
				$this->device = $this->_device_au();
			}
		}

		// check emobile
		if ($carrier_setting === FALSE) {
			$regex = $this->_create_regex_word($this->emobile);
			if ($this->_check_carrier($regex)) {
				$this->is_emobile = TRUE;
				$carrier_setting = TRUE;
				$this->device = $this->_device_emobile();
			}
		}

		// check willcom
		if ($carrier_setting === FALSE) {
			$regex = $this->_create_regex_word($this->willcom);
			if ($this->_check_carrier($regex)) {
				$this->is_willcom = TRUE;
				$carrier_setting = TRUE;
				$this->device = $this->_device_willcom();
			}
		}

		// check iphone
		if ($carrier_setting === FALSE) {
			$regex = $this->_create_regex_word($this->iphone);
			if ($this->_check_carrier($regex)) {
				$this->is_iphone = TRUE;
				$carrier_setting = TRUE;
				$this->device = $this->_device_iphone();
			}
		}

		// check ipad
		if ($carrier_setting === FALSE) {
			$regex = $this->_create_regex_word($this->ipad);
			if ($this->_check_carrier($regex)) {
				$this->is_ipad = TRUE;
				$carrier_setting = TRUE;
				$this->device = $this->_deivce_ipad();
			}
		}

		// check android
		if ($carrier_setting === FALSE) {
			$regex = $this->_create_regex_word($this->android);
			if ($this->_check_carrier($regex)) {
				$this->is_android = TRUE;
				$carrier_setting = TRUE;
				$this->device = $this->_device_android();
			}
		}

		// check sns platform
		if ($carrier_setting === FALSE) {
			$regex = $this->_create_regex_word($this->sns);
			if ($this->_check_carrier($regex)) {
				$this->is_snsplatform = TRUE;
				$carrier_setting = TRUE;
				$this->device = $this->_device_snsplatform();
			}
		}

		// check apachebench
		if ($carrier_setting === FALSE) {
			$regex = $this->_create_regex_word($this->ab);
			if ($this->_check_carrier($regex)) {
				$this->is_apachebench = TRUE;
				$carrier_setting = TRUE;
				$this->device = $this->_device_apachebench();
			}
		}

		// check mobile or pc or smart
		if ($this->_is_mobile()) {
			$this->is_mobile = TRUE;
		} else if ($this->_is_smart()) {
			$this->is_smart = TRUE;
		} else {
			$this->is_pc = TRUE;
		}

		// check invalid
		if ($this->_is_invalid()) {
			$this->is_invalid = TRUE;
		}

	}

	function _is_invalid()
	{
		$ret = FALSE;
		foreach ($this->invalid as $key => $value) {
			$check = 'is_'.$value;
			if ($this->$check === TRUE) {
				$ret = TRUE;
				break;
			}
		}
		// 対象機種の場合テーブルに対応しているかどうかをチェックする
		if ($ret === FALSE) {
			$mobile_info = FALSE;
			if (ci_exists_model('mobile_info_model')) {
				$this->CI->load->model('mobile_info_model');
				$this->CI->mobile_info_model->device = $this->device;
				$mobile_info = $this->CI->mobile_info_model->select_one();
			}
			if ($mobile_info !== FALSE) {
				$this->width = $mobile_info['width'];
				$this->rate = $this->width / 240;
				$ret = $mobile_info['is_invalid'] === 0 ? FALSE : 1;
			} else {
				$this->width = 240;
				$this->rate = 1;
				$ret = FALSE;
			}
		}
		return $ret;
	}

	function _is_mobile()
	{
		$ret = FALSE;
		foreach ($this->mobile as $key => $value) {
			$check = 'is_'.$value;
			if ($this->$check === TRUE) {
				$ret = TRUE;
				break;
			}
		}
		return $ret;
	}

	function _is_smart()
	{
		$ret = FALSE;
		foreach ($this->smart as $key => $value) {
			$check = 'is_'.$value;
			if ($this->$check === TRUE) {
				$ret = TRUE;
				break;
			}
		}
		return $ret;
	}

	function _check_carrier($key)
	{
		$ret = FALSE;
		if (preg_match('/'.$key.'/', $this->useragent)) {
			$ret = TRUE;
		}
		return $ret;
	}

	function _create_regex_word($param)
	{
		foreach ($param as $key => $value) {
			$param[$key] = preg_quote($value, '/');
		}
		$ret = implode('|', $param);
		return $ret;
	}

	function is_pc()
	{
		return $this->is_pc;
	}

	function is_mobile()
	{
		return $this->is_mobile;
	}

	function is_softbank()
	{
		return $this->is_softbank;
	}

	function is_docomo()
	{
		return $this->is_docomo;
	}

	function is_au()
	{
		return $this->is_au;
	}

	function is_iphone()
	{
		return $this->is_iphone;
	}

	function is_android()
	{
		return $this->is_android;
	}

	function is_ipad()
	{
		return $this->is_ipad;
	}

	function is_emobile()
	{
		return $this->is_emobile;
	}

	function is_willcom()
	{
		return $this->is_willcom;
	}

	function is_invalid()
	{
		return $this->is_invalid;
	}

	function encoding()
	{
		$ret = 'UTF-8';
		if ($this->is_au() || $this->is_docomo()) {
			$ret = 'Shift_JIS';
		}
		return $ret;
	}

	// docomo
	function _device_docomo()
	{
		$device_name = '';
		@list($main, $foma) = explode(' ', $this->useragent, 2);
		if ($foma == null || preg_match('/^\((.*)\)$/', $foma)) {
			// MOVA
			@list($name, $version, $device_name, $cache, $rest) = explode('/', $main, 5);
			if ($device_name == 'SH505i2') {
				$device_name = 'SH505i';
			}
		} else {
			// FOMA
			if (!preg_match('/^([^(\s]+)/', $foma, $matches)) {
				$device_name = '';
			} else {
				$device_name = $matches[1];
				if ($device_name == 'MST_v_SH2101V') {
					$device_name = 'SH2101V';
				}
			}
		}
		return $device_name;
	}

	// softbank
	function _device_softbank()
	{
		$device_name = '';
		$header = getallheaders();
		if (isset($header['X-JPHONE-MSNAME'])) {
			$device_name = $header['X-JPHONE-MSNAME'];
		}
		return $device_name;
	}

	// au
	function _device_au()
	{
		$device_name = '';
		if (preg_match('/^KDDI-(.*)/', $this->useragent, $matches)) {
			// KDDI-TS21 UP.Browser/6.0.2.276 (GUI) MMP/1.1
			@list($device_name, $browser, $opt, $server_name) = explode(' ', $matches[1], 4);
		} else {
			// UP.Browser/3.01-HI01 UP.Link/3.4.5.2
			@list($browser, $server_name, $comment) = explode(' ', $this->useragent, 3);
			@list($name, $software) = explode('/', $browser);
			@list($version, $device_name) = explode('-', $software);
		}
		return $device_name;
	}

	// willcom
	function _device_willcom()
	{
		$device_name = '';
		if (preg_match('!^Mozilla/3\.0\((?:DDIPOCKET|WILLCOM);(.*)\)!', $this->useragent, $matches)) {
			@list($vendor, $device_name, $model_version, $browser_version, $cache) = explode('/', $matches[1]);
		}
		return $device_name;
	}

	// iphone
	function _device_iphone()
	{
		$device = 'iphone';
		return $device;
	}

	// ipad
	function _device_ipad()
	{
		$device = 'ipad';
		return $device;
	}

	// android
	function _device_android()
	{
		$device = 'android';
		return $device;
	}

	// sns_platform
	function _device_snsplatform()
	{
		$device = 'sns_platform';
		return $device;
	}

	// apachebench
	function _device_apachebench()
	{
		$device = 'apachebench';
		return $device;
	}

}

