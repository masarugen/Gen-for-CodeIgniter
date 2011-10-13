<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Generate extends MY_Controller {

	var $model_path = 'models/';
	var $base_path = 'base/';
	var $sql_path = '../sql/';
	var $web_path = '../web/';
	var $type2default;
	var $exclude;

	function __construct()
	{
		parent::__construct();
		$this->cli_error();
		$this->exclude = array(
			'primary_key',
			'index',
			'unique',
			'database',
			'partition'
		);
		$this->type2default = array(
			'int' => "0",
			'real' => "0",
			'string' => "''",
			'blob' => "''",
			'datetime' => "NULL",
			'year' => "NULL",
			'date' => "NULL",
			'time' => "NULL",
		);
	}

	function index()
	{
		$app_path = $this->_app_path();
		$mode = '';
		while (1) {
			$mode = $this->_mode();
			if ($mode === 'q') break;
			$this->_create($mode, $app_path);
		}
	}

	function _create_database($db_name, $db_info, $common)
	{
		if (!isset($db_info['charset'])) $db_info['charset'] = $common['charset'];
		if (!isset($db_info['collate'])) $db_info['collate'] = $common['collate'];
		$data = array(
			'database_name' => $db_name,
			'charset' => $db_info['charset'],
			'collate' => $db_info['collate'],
		);
		$sql_template = $this->gen_view->load_template('cli/generate/database', $data, 'generate');
		$sql = $this->gen_template->create_view($sql_template, $data, $this);
		return $sql;
	}

	function _create_table_html($table_name, $value, $common)
	{
		$exclude = $this->exclude;
		$fields = array_merge($value, $common['field']);
		$html_template = $this->gen_view->load_template('cli/generate/table_html', array(), 'generate_html_key');
		$data['table_name'] = $table_name;
		if (!isset($value['database'])) $data['database'] = $value['database'];
		if (!isset($data['database'])) $data['database'] = $common['database'];
		if (!isset($data['charset']))  $data['charset']  = $common['charset'];
		if (!isset($data['collate']))  $data['collate']  = $common['collate'];
		if (!isset($data['engine']))   $data['engine']   = $common['engine'];
		$no = 1;
		foreach ($fields as $field_name => $field) {
			if (in_array($field_name, $exclude)) continue;
			$field['no'] = $no;
			$field['field_name'] = $field_name;
			$field['end'] = FALSE;
			$data['fields'][] = $field;
			$no++;
		}
		$data['partition'] = array();
		if (isset($value['partition']) && count($value['partition']) > 0) {
			$data['partition'][0]['partition_name'] = $value['partition']['name'];
			$data['partition'][0]['range_field'] = $value['partition']['range_field'];
		}
		if (isset($value['primary_key']) && count($value['primary_key']) > 0) {
			$data['primary'] = implode(', ', $value['primary_key']);
		}
		$data['index'] = array();
		if (isset($value['index']) && count($value['index']) > 0) {
			$i = 0;
			foreach ($value['index'] as $index_name => $fields) {
				$data['index'][]['index_name'] = $common['index_prefix'].$index_name;
				$data['index'][]['fields'] = implode(', ', $fields);
				$i++;
			}
		}
		$data['unique'] = array();
		if (isset($value['unique']) && count($value['unique']) > 0) {
			$i = 0;
			foreach ($value['unique'] as $index_name => $fields) {
				$data['unique'][$i]['index_name'] = $common['unique_prefix'].$index_name;
				$data['unique'][$i]['fields'] = implode(', ', $fields);
				$i++;
			}
		}

		$html = $this->gen_template->create_view($html_template, $data, $this);
		return $html;
	}

	function _create_table($table_name, $value, $common)
	{
		$exclude = $this->exclude;
		$fields = array_merge($value, $common['field']);
		$sql_template = $this->gen_view->load_template('cli/generate/table', array(), 'generate');
		$data['table_name'] = $table_name;
		if (!isset($value['database'])) $data['database'] = $value['database'];
		if (!isset($data['database'])) $data['database'] = $common['database'];
		if (!isset($data['charset']))  $data['charset']  = $common['charset'];
		if (!isset($data['collate']))  $data['collate']  = $common['collate'];
		if (!isset($data['engine']))   $data['engine']   = $common['engine'];
		foreach ($fields as $field_name => $field) {
			if (in_array($field_name, $exclude)) continue;
			$field['field_name'] = $field_name;
			$field['end'] = FALSE;
			$data['fields'][] = $field;
		}
		$data['primary_key'] = implode(', ', $value['primary_key']);
		$sql = $this->gen_template->create_view($sql_template, $data, $this);
		$sql .= $this->_create_index($table_name, $value, $common);
		$sql .= $this->_create_partition($table_name, $value, $common);
		$sql .= PHP_EOL.PHP_EOL;
		return $sql;
	}

	function _create_partition($table_name, $value, $common)
	{
		$sql = '';
		if (!isset($value['partition']) || count($value['partition']) === 0) return $sql;

		$data['table_name'] = $table_name;
		if (!isset($value['database'])) $data['database'] = $value['database'];
		if (!isset($data['database'])) $data['database'] = $common['database'];

		$data['partition_name'] = $value['partition']['name'];
		$data['range_field'] = $value['partition']['range_field'];

		// half year
		$y = date('Y');
		$m = date('m');
		$data['partition_date'] = sprintf("%04d%02d", $y, $m);
		$data['partition_time'] = $this->_to_days($y, $m + 1, 1);
		for ($i = 1; $i < 6; $i++) {
			$partitions[$i]['partition_name'] = $data['partition_name'];
			$time = mktime(0, 0, 0, $m+$i, 1, $y);
			$partitions[$i]['partition_date'] = date("Ym", $time);
			$partitions[$i]['partition_time'] = $this->_to_days($y, $m + $i + 1, 1);
			$partitions[$i]['end'] = ($i === 5);
		}
		$data['partitions'] = $partitions;

		$sql_template = $this->gen_view->load_template('cli/generate/partition', array(), 'generate');
		$sql = $this->gen_template->create_view($sql_template, $data, $this);

		return $sql;
	}

	function _to_days($year, $month, $day)
	{
		$to_days_20110101 = 734503;
		$time_20110101 = mktime(0, 0, 0, 1, 1, 2011);
		$time = mktime(0, 0, 0, $month, $day, $year);
		$oneday_sec = 24 * 60 * 60;
		$count = ceil(($time - $time_20110101) / $oneday_sec);
		$ret = $count + $to_days_20110101;
		return $ret;
	}

	function _create_index($table_name, $value, $common)
	{
		$sql = '';

		$data['table_name'] = $table_name;
		if (!isset($value['database'])) $data['database'] = $value['database'];
		if (!isset($data['database'])) $data['database'] = $common['database'];

		// primary index
		//if (isset($value['primary_key']) && count($value['primary_key']) > 0) {
		//	$data['fields'] = implode(', ', $value['primary_key']);
		//	$primary_template = $this->gen_view->load_template('cli/generate/key', array(), 'generate', 'primary');
		//	$sql .= $this->gen_template->create_view($primary_template, $data, $this);
		//}

		// none unique index
		if (isset($value['index']) && count($value['index']) > 0) {
			$index_template = $this->gen_view->load_template('cli/generate/key', array(), 'generate', 'index');
			foreach ($value['index'] as $index_name => $fields) {
				$data['index_name'] = $common['index_prefix'].$index_name;
				$data['fields'] = implode(', ', $fields);
				$sql .= $this->gen_template->create_view($index_template, $data, $this);
			}
		}

		// unique index
		if (isset($value['unique']) && count($value['unique']) > 0) {
			$unique_template = $this->gen_view->load_template('cli/generate/key', array(), 'generate', 'unique');
			foreach ($value['unique'] as $index_name => $fields) {
				$data['index_name'] = $common['unique_prefix'].$index_name;
				$data['fields'] = implode(', ', $fields);
				$sql .= $this->gen_template->create_view($unique_template, $data, $this);
			}
		}

		return $sql;
	}

	function _create_insert($app_path, $table, $common)
	{
		$sql = '';
		$config_name = 'gen_sql';
		$sql_template = $this->gen_view->load_template('cli/generate/insert', array(), 'generate');
		$value = $this->config->item('value', $config_name);
		if ($value === FALSE) return;
		foreach ($value as $table_name => $info_list) {
			$data['table_name'] = $table_name;
			if (!isset($table[$table_name]['database'])) $data['database'] = $table[$table_name]['database'];
			if (!isset($data['database'])) $data['database'] = $common['database'];
			foreach ($info_list as $info) {
				$field_names = array();
				$field_values = array();
				foreach ($info as $field_name => $field_value) {
					if ($field_value === NULL) continue;
					$field_names[] = $field_name;
					$field_values[] = "'$field_value'";
				}
				foreach ($common['field'] as $field_name => $field_info) {
					if ($field_info['value'] === NULL) continue;
					$field_names[] = $field_name;
					$field_values[] = $field_info['value'];
				}
				$data['field_name'] = implode(', ', $field_names);
				$data['field_value'] = implode(', ', $field_values);
				$sql .= $this->gen_template->create_view($sql_template, $data, $this);
			}
		}
		file_put_contents($app_path.$this->sql_path.'insert.sql', $sql);
	}

	function _create_sql($app_path)
	{
		$config_name = 'gen_sql';
		$this->config->load($config_name, TRUE, TRUE);
		$common = $this->config->item('common', $config_name);
		$database = $this->config->item('database', $config_name);
		$table = $this->config->item('table', $config_name);

		$sql = '';
		foreach ($database as $db_name => $value) {
			$sql .= $this->_create_database($db_name, $value, $common);
		}

		foreach ($table as $table_name => $value) {
			$sql .= $this->_create_table($table_name, $value, $common);
		}

		$table_html = '';
		foreach ($table as $table_name => $value) {
			$table_html .= $this->_create_table_html($table_name, $value, $common);
		}
		file_put_contents($app_path.$this->web_path.'sql.html', $table_html);

		file_put_contents($app_path.$this->sql_path.'all.sql', $sql);

		$this->_create_insert($app_path, $table, $common);
	}

	function _create_model($app_path)
	{
		$this->_connect_db();
		$models = $this->_select_model();
		$this->_generate_model($app_path, $models);
	}

	function _type2default($type)
	{
		$ret = "NULL";
		$keys = array_keys($this->type2default);
		if (in_array($type, $keys)) {
			$ret = $this->type2default[$type];
		}
		return $ret;
	}

	function _generate_model($app_path, $models)
	{
		foreach ($models as $model) {
			$data = array();
			$data['model_name'] = ucfirst($model.'_model');
			$data['base_model_name'] = ucfirst('base_'.$model.'_model');
			$fields = $this->db->field_data($model);
			$tbl_key = array();
			$tbl_item = array();
			$tbl_info = array();
			$default_order_by = array();
			foreach ($fields as $i=>$field) {
				if ($field->primary_key === 1) {
					$tbl_key[] = "'".$field->name."'";
				}
				$tbl_item[] = "'".$field->name."'";
				$tbl_info[$i]['column'] = "'".$field->name."'";
				$tbl_info[$i]['type'] = "'".$field->type."'";
				$tbl_info[$i]['default'] = $this->_type2default($field->type);
			}
			foreach ($tbl_key as $i=>$column) {
				$data['default_order_by'][$i]['key'] = $column;
			}
			$data['key'] = implode(', ', $tbl_key);
			$data['cache_key'] = $data['key'];
			$data['tbl_name'] = $model;
			$data['tbl_item'] = implode(', ', $tbl_item);
			$data['tbl_info'] = $tbl_info;
			// output model
			$model_file_path = $app_path.$this->model_path.strtolower($data['model_name']).'.php';
			if (!file_exists($model_file_path)) {
				$model_template = $this->gen_view->load_template('cli/generate/model', $data, 'generate');
				$model_template = $this->gen_template->create_view($model_template, $data, $this);
				file_put_contents($model_file_path, $model_template);
			}

			// output base model
			$base_model_template = $this->gen_view->load_template('cli/generate/base_model', $data, 'generate');
			$base_model_template = $this->gen_template->create_view($base_model_template, $data, $this);
			file_put_contents($app_path.$this->model_path.$this->base_path.strtolower($data['base_model_name']).'.php', $base_model_template);
		}
	}

	function _connect_db()
	{
		$databases = $this->gen_connect->databases;
		$databases_keys = array_keys($databases);
		$i = 1;
		$msg = '[ ';
		foreach ($databases as $key => $database) {
			$msg .= $i.':'.$key.' ';
			$i++;
		}
		$msg .= ']: ';
		do {
			$num = $this->_stdin('Select Connection Database '.$msg);
			if (!isset($databases_keys[$num - 1])) {
				$num = '';
				echo "Invalid Value!!\n";
			}
		} while ($num === '');
		$database = $databases_keys[$num - 1];
		$this->load->database($database);
	}

	function _select_model()
	{
		$tables = $this->db->list_tables();
		$msg = '[a:all ';
		$i = 1;
		$list = array('a' => 'all');
		foreach ($tables as $table) {
			$msg .= $i.':'.$table.' ';
			$list[$i] = $table;
			$i++;
		}
		$msg .= ']: ';
		do {
			$num = $this->_stdin('Select Create Model '.$msg);
			if (!isset($list[$num])) {
				$num = '';
				echo "Invalid Value!!\n";
			}
		} while ($num === '');
		if ($num === 'a') {
			$models = $tables;
		} else {
			$models = array($list[$num]);
		}
		return $models;
	}

	function _create_controller()
	{
		echo "controller\n";
	}

	function _create_view()
	{
		echo "view\n";
	}

	function _create($mode, $app_path)
	{
		switch ($mode) {
			case 'c':
				$this->_create_controller($app_path);
				break;
			case 'v':
				$this->_create_view($app_path);
				break;
			case 'm':
				$this->_create_model($app_path);
				break;
			case 's':
				$this->_create_sql($app_path);
				break;
		}
	}

	function _mode()
	{
		$mode = '';
		while ($mode === '') {
			$mode = $this->_stdin('Input create mode [m:model s:sql c:controller v:view q:quit]: ');
			switch ($mode) {
				case 'm':
				case 'c':
				case 'v':
				case 's':
				case 'q':
					break;
				default:
					$mode = '';
					echo "Invalid Value!!\n";
					break;
			}
		}
		return $mode;
	}

	function _app_path()
	{
		do {
			$is_default = $this->_stdin('Use default APPPATH?[Y/N/y/n]: ');
		} while (!in_array($is_default, array('y', 'n', 'Y', 'N', '')));
		switch ($is_default) {
			case 'Y':
			case 'y':
			case '':
				$app_path = APPPATH;
				break;
			case 'N':
			case 'n':
				$app_path = '';
				break;
		}
		while ($app_path === '') {
			$app_path = $this->_stdin('Input application folder path: ');
			if (!is_dir($app_path)) {
				$app_path = '';
				echo "Invalid Folder !!\n";
			}
		}
		return $app_path;
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

	function if_length($data)
	{
		$ret = TRUE;
		if ($data['length'] === NULL) $ret = FALSE;
		return $ret;
	}

	function if_not_null($data)
	{
		$ret = TRUE;
		if ($data['not_null'] === FALSE) $ret = FALSE;
		return $ret;
	}

	function if_default($data)
	{
		$ret = TRUE;
		if ($data['default'] === NULL) $ret = FALSE;
		return $ret;
	}

	function if_auto_increment($data)
	{
		$ret = TRUE;
		if ($data['auto_increment'] === FALSE) $ret = FALSE;
		return $ret;
	}

	function if_end($data)
	{
		$ret = TRUE;
		if ($data['end'] === FALSE) $ret = FALSE;
		return $ret;
	}

}

/* End of file generate.php */
/* Location: ./application/controllers/generate.php */
