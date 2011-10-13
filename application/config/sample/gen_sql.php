<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// insert生成
// $config['value']['テーブル名'][] = array('カラム名' => '値');

// commom
$config['common']['field']['created_at'] = array('field_type' => 'DATETIME', 'length' => NULL, 'not_null' => TRUE, 'auto_increment' => FALSE, 'default' => NULL, 'value' => 'NOW()');
$config['common']['field']['updated_at'] = array('field_type' => 'DATETIME', 'length' => NULL, 'not_null' => TRUE, 'auto_increment' => FALSE, 'default' => NULL, 'value' => 'NOW()');
$config['common']['engine'] = 'InnoDB';
$config['common']['charset'] = 'utf8';
$config['common']['collate'] = 'utf8_general_ci';
$config['common']['index_prefix'] = 'IDX_';
$config['common']['unique_prefix'] = 'UIDX_';
$config['common']['database'] = 'sample';

// database
$config['database']['sample'] = array();

// [table]mobile_info
$config['table']['mobile_info']['primary_key'] = array('mobile_info_id');
$config['table']['mobile_info']['index'] = array();
$config['table']['mobile_info']['unique'] = array();
$config['table']['mobile_info']['database'] = '';
$config['table']['mobile_info']['mobile_info_id'] = array('field_type' => 'INT', 'length' => NULL, 'not_null' => TRUE, 'auto_increment' => TRUE, 'default' => NULL);
$config['table']['mobile_info']['carrier'] = array('field_type' => 'VARCHAR', 'length' => 10, 'not_null' => TRUE, 'auto_increment' => FALSE, 'default' => NULL);
$config['table']['mobile_info']['type'] = array('field_type' => 'VARCHAR', 'length' => 20, 'not_null' => TRUE, 'auto_increment' => FALSE, 'default' => NULL);
$config['table']['mobile_info']['maker'] = array('field_type' => 'VARCHAR', 'length' => 20, 'not_null' => TRUE, 'auto_increment' => FALSE, 'default' => NULL);
$config['table']['mobile_info']['device'] = array('field_type' => 'VARCHAR', 'length' => 20, 'not_null' => TRUE, 'auto_increment' => FALSE, 'default' => NULL);
$config['table']['mobile_info']['release_ymd'] = array('field_type' => 'DATE', 'length' => NULL, 'not_null' => TRUE, 'auto_increment' => FALSE, 'default' => NULL);
$config['table']['mobile_info']['width'] = array('field_type' => 'INT', 'length' => NULL, 'not_null' => TRUE, 'auto_increment' => FALSE, 'default' => NULL);
$config['table']['mobile_info']['height'] = array('field_type' => 'INT', 'length' => NULL, 'not_null' => TRUE, 'auto_increment' => FALSE, 'default' => NULL);
$config['table']['mobile_info']['is_flash'] = array('field_type' => 'TINYINT', 'length' => NULL, 'not_null' => TRUE, 'auto_increment' => FALSE, 'default' => 0);
$config['table']['mobile_info']['is_invalid'] = array('field_type' => 'TINYINT', 'length' => NULL, 'not_null' => TRUE, 'auto_increment' => FALSE, 'default' => 0);
$config['table']['mobile_info']['update_useragent'] = array('field_type' => 'DATE', 'length' => NULL, 'not_null' => TRUE, 'auto_increment' => FALSE, 'default' => NULL);
$config['table']['mobile_info']['update_displayinfo'] = array('field_type' => 'DATE', 'length' => NULL, 'not_null' => TRUE, 'auto_increment' => FALSE, 'default' => NULL);
$config['table']['mobile_info']['update_profiledata'] = array('field_type' => 'DATE', 'length' => NULL, 'not_null' => TRUE, 'auto_increment' => FALSE, 'default' => NULL);

