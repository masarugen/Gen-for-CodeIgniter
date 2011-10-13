<?php
model_require('mobile_info_model');
class Mobile_info_model extends Base_mobile_info_model {

	function __construct()
	{
		parent::__construct();
		$this->cache_key = array();
		$this->is_apc = TRUE;
	}
}
