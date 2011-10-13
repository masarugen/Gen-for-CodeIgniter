<?php
class Base_mobile_info_model extends MY_Model {

	var $is_apc;
	var $mdb;
	var $sdb;
	var $key;
	var $cache_key;
	var $tbl_name;
	var $tbl_item;
	var $tbl_info;
	var $use_part_cache;
	var $cache_key_value;
	var $default_order_by;
	var $select_where;
	var $delete_where;

	function __construct()
	{
		parent::__construct();
		$this->is_apc = FALSE;
		$this->mdb = 'master';
		$this->sdb = 'slave';
		$this->key = array('mobile_info_id');
		$this->cache_key = array('mobile_info_id');
		$this->tbl_name = 'mobile_info';
		$this->tbl_item = array('mobile_info_id', 'carrier', 'type', 'maker', 'device', 'release_ymd', 'width', 'height', 'is_flash', 'is_invalid', 'update_useragent', 'update_displayinfo', 'update_profiledata', 'created_at', 'updated_at');
		$this->tbl_info = array(
			'mobile_info_id' => array('type' => 'int', 'default' => 0),
			'carrier' => array('type' => 'string', 'default' => ''),
			'type' => array('type' => 'string', 'default' => ''),
			'maker' => array('type' => 'string', 'default' => ''),
			'device' => array('type' => 'string', 'default' => ''),
			'release_ymd' => array('type' => 'date', 'default' => NULL),
			'width' => array('type' => 'int', 'default' => 0),
			'height' => array('type' => 'int', 'default' => 0),
			'is_flash' => array('type' => 'int', 'default' => 0),
			'is_invalid' => array('type' => 'int', 'default' => 0),
			'update_useragent' => array('type' => 'date', 'default' => NULL),
			'update_displayinfo' => array('type' => 'date', 'default' => NULL),
			'update_profiledata' => array('type' => 'date', 'default' => NULL),
			'created_at' => array('type' => 'datetime', 'default' => NULL),
			'updated_at' => array('type' => 'datetime', 'default' => NULL),
		);
		$this->use_part_cache = FALSE;
		$this->cache_key_value = array();
		$this->default_order_by = array(
			'mobile_info_id' => 'asc',
		);
		$this->select_where = array();
		$this->delete_where = array();
	}

}