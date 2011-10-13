<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model {

	var $CI;
	var $data = array();
	var $is_apc = FALSE;
	var $use_part_cache = FALSE;
	var $mdb = 'master';
	var $sdb = 'slave';
	var $key = array();
	var $cache_key = array();
	var $tbl_name = '';
	var $tbl_item = array();
	var $tbl_info = array();
	var $cache_key_value = array();
	var $default_order_by = array();
	var $select_where = array();
	var $delete_where = array();
	var $now;
	var $compile_sql = '';

	function __construct()
	{
		parent::__construct();
		$this->CI = & get_instance();
		$this->now = $this->CI->gen_calendar->now();
	}

	/**
	 * DBへの接続処理
	 */
	function connect($db_name)
	{
		return $this->CI->gen_connect->$db_name();
	}

	/**
	 * 設定されている値のチェック
	 */
	function check_set_value($obj, $key = NULL)
	{
		$ret = FALSE;
		$value = FALSE;
		if ($key === NULL) {
			$value = $obj;
		} else if (is_array($obj) && isset($obj[$key])) {
			$value = $obj[$key];
		} else if (is_object($obj) && isset($obj->$key)) {
			$value = $obj->$key;
		} else {
			$value = FALSE;
		}
		if ($value !== NULL && $value !== FALSE) {
			$ret = TRUE;
		}
		return $ret;
	}

	/**
	 * キャッシュから値の取得
	 * キャッシュに値がない場合は、DBから取得する
	 */
	function cache($db, $param)
	{
		$result = FALSE;
		$cache = FALSE;
		$cache_key = FALSE;
		if ($this->CI->gen_cache->use_cache && !$param['is_forupdate'] && !$param['is_nocache']) {
			// Modelのキャッシュのキーを取得する
			$cache_key = $this->CI->gen_cache->model_cache_key($this);
		}
		// SELECT文の取得
		$sql = $this->compile_sql;
		if ($param['is_forupdate']) {
			$sql .= ' FOR UPDATE ';
		}
		// キャッシュのキーを作成
		$hash = md5($sql);
		if ($cache_key !== FALSE && $this->CI->gen_cache->use_cache && !$param['is_forupdate'] && !$param['is_nocache']) {
			$cache = $this->CI->gen_cache->get($cache_key.'_'.$hash, $this->is_apc);
		}
		if ($this->CI->gen_cache->is_enable_cache($cache_key, $cache)) {
			// 遅延が発生する可能性がある場合はmasterからデータを取得する
			if ($this->delay_check()) {
				// use cache
				$result = $cache['result'];
			} else {
				$db = $this->connect($this->mdb);
			}
		}
		if ($result === FALSE) {
			// select db
			$this->CI->gen_log->sql($sql);
			$query = $db->query($sql);
			$result = $query->result_array();
			if ($cache_key !== FALSE && $this->CI->gen_cache->use_cache && !$param['is_forupdate'] && !$param['is_nocache']) {
				$cache = $this->CI->gen_cache->set($cache_key.'_'.$hash, $result, $this->is_apc);
			}
		}
		return $result;
	}

	/**
	 * 遅延チェック
	 */
	function delay_check()
	{
		$ret = TRUE;
		$delay_time = $this->CI->config->item('delay_time', $this->CI->gen_cache->config_name);
		if ($delay_time === FALSE) $delay_time = 0;
		if ($this->CI->gen_cache->update_time <= ($this->CI->gen_calendar->microtime() + $delay_time)) {
			$ret = FALSE;
		}
		return $ret;
	}

	/**
	 * SELECT時のパラメータの設定
	 */
	function check_select($param)
	{
		$this->compile_sql = '';
		if (!isset($param['type'])) $param['type'] = 'select';
		if (!isset($param['is_one'])) $param['is_one'] = FALSE;
		if (!isset($param['is_reset'])) $param['is_reset'] = TRUE;
		$param['is_key'] = ($param['type'] === 'key') ? TRUE : FALSE;
		$param['is_forupdate'] = ($param['type'] === 'forupdate') ? TRUE : FALSE;
		$param['is_resource'] = ($param['type'] === 'resource') ? TRUE : FALSE;
		$param['is_nocache'] = ($param['type'] === 'nocache') ? TRUE : FALSE;
		if (!isset($param['key'])) {
			$param['key'] = $this->key;
		}
		if (!isset($param['select'])) {
			$param['select'] = '';
		}
		if (!isset($param['like'])) {
			$param['like'] = '';
		}
		if (!isset($param['cnt'])) {
			$param['cnt'] = '';
		}
		if (!isset($param['start'])) {
			$param['start'] = '';
		}
		if (!isset($param['order_by'])) {
			$param['order_by'] = '';
		}
		if (!isset($param['group_by'])) {
			$param['group_by'] = '';
		}
		if (!isset($param['having'])) {
			$param['having'] = '';
		}
		return $param;
	}

	/**
	 * 1件のデータを取得する
	 */
	function select_one($param = array())
	{
		$param['is_one'] = TRUE;
		return $this->select($param);
	}

	/**
	 * PRIMARY_KEYでデータを取得する
	 */
	function select_key($param = array())
	{
		$param['type'] = 'key';
		return $this->select($param);
	}

	/**
	 * forupdateでデータを取得する
	 */
	function select_forupdate($param = array())
	{
		$param['type'] = 'forupdate';
		return $this->select($param);
	}

	/**
	 * リソースデータを取得する
	 */
	function select_resource($param = array())
	{
		$param['type'] = 'resource';
		return $this->select($param);
	}

	/**
	 * キャッシュせずにデータを取得する
	 */
	function select_nocache($param = array())
	{
		$param['type'] = 'nocache';
		return $this->select($param);
	}

	/**
	 * select
	 * select_key
	 * select_forupdate
	 * select_resource
	 * select_nocache
	 * メイン処理
	 * $this->compile_sqlを生成
	 */
	function select($param = array())
	{
		$ret = FALSE;
		$param = $this->check_select($param);
		// DB
		if ($param['is_forupdate'] OR $param['is_nocache'] OR $this->is_transaction()) {
			$db = $this->connect($this->mdb);
		} else {
			$db = $this->connect($this->sdb);
		}

		// SELECT
		$this->compile_sql = 'SELECT ';
		$select = $param['select'];
		if ($select !== '') {
			$this->compile_sql .= $select;
		} else {
			sort($this->tbl_item);
			$this->compile_sql .= implode(', ', $this->tbl_item);
		}

		// FROM
		$this->compile_sql .= ' FROM '.$this->tbl_name;

		// WHERE
		$where_list = array();
		if ($param['is_key']) {
			sort($this->key);
			foreach ($this->key as $column) {
				$value = $this->get_data($column);
				$where_list[] = $column." = '".$value."'";
			}
		} else {
			if (count($this->select_where) > 0) {
				ksort($this->select_where);
				foreach ($this->select_where as $column_condition => $value) {
					$where_list[] = trim($column_condition)." '".$value."'";
				}
			} else {
				sort($this->tbl_item);
				foreach ($this->tbl_item as $column) {
					$value = $this->get_data($column);
					if ($value !== FALSE) {
						$where_list[] = $column." = '".$value."'";
					}
				}
			}
		}

		// LIKE
		$like = $param['like'];
		if ($like !== '') {
			foreach ($like as $column => $value) {
				$where_list[] = $column." LIKE '".$value."'";
			}
		}
		if (count($where_list) > 0) {
			$this->compile_sql .= ' WHERE '.implode(' AND ', $where_list);
		}

		// GROUP BY
		$group_by = $param['group_by'];
		if ($group_by !== '') {
			$this->compile_sql .= ' GROUP BY '.$group_by;
		}

		// HAVING
		$having = $param['having'];
		if ($having !== '') {
			$this->compile_sql .= ' HAVING '.$having;
		}

		// SORT
		$sort_list = array();
		$order_by = $param['order_by'];
		if ($order_by !== '') {
			if (is_array($order_by)) {
				foreach ($order_by as $sort=>$order) {
					$sort_list[] = $sort.' '.$order;
				}
			} else {
				$sort_list[] = $order_by;
			}
		} else {
			foreach ($this->default_order_by as $sort => $order) {
				$sort_list[] = $sort.' '.$order;
			}
		}
		if (count($sort_list) > 0) {
			$this->compile_sql .= ' ORDER BY '.implode(', ', $sort_list);
		}

		// LIMIT
		$cnt = $param['cnt'];
		$start = $param['start'];
		if ($cnt !== '' && $start !== '') {
			$this->compile_sql .= ' LIMIT '.$start.', '.$cnt;
		} else if ($cnt !== '') {
			$this->compile_sql .= ' LIMIT '.$cnt;
		}

		// RESULT
		if ($param['is_resource']) {
			$result = $db->get();
		} else {
			$result = $this->cache($db, $param);
			if (is_array($result) && count($result) === 0) {
				$result = FALSE;
			} else if ($param['is_one']) {
				$result = $result[0];
			}
		}
		if ($param['is_reset']) {
			$this->reset();
		}
		return $result;
	}

	/**
	 * SELECTに使う条件を追加
	 */
	function add_select_where($column, $condition, $value)
	{
		if (!isset($this->tbl_item[$column])) $this->CI->gen_exception->exception('not exist column：['.$this->tbl_name.' : '.$column.']');
		$column_condition = "$column $condition";
		$this->select_where[$column_condition] = $value;
	}

	/**
	 * DELETEに使う条件を追加
	 */
	function add_delete_where($column, $condition, $value)
	{
		if (!isset($this->tbl_item[$column])) $this->CI->gen_exception->exception('not exist column：['.$this->tbl_name.' : '.$column.']');
		$column_condition = "$column $condition";
		$this->delete_where[$column_condition] = $value;
	}

	/**
	 * INSERT/UPDATEの処理
	 */
	function save($is_update = FALSE, $is_reset = TRUE)
	{
		$ret = 0;
		$db = $this->connect($this->mdb);
		foreach ($this->tbl_item as $column) {
			if ($column == 'created_at' OR $column == 'updated_at') continue;
			$value = $this->get_data($column);
			if ($this->check_set_value($value)) {
				$db->set($column, $value);
			}
		}
		$this->add_clear_key();
		if ($is_update) {
			$db->set("updated_at", $this->now);
			foreach ($this->key as $key) {
				$value = $this->get_data($key);
				$db->where($key, $value);
			}
			$ret = $db->update($this->tbl_name);
		} else {
			$db->set("created_at", $this->now);
			$db->set("updated_at", $this->now);
			$ret = $db->insert($this->tbl_name);
		}
		if ($this->is_transaction() === FALSE) $this->CI->gen_cache->clear_cache();
		$ins_id = $db->insert_id();
		if ($ins_id > 0) $ret = $ins_id;
		if ($is_reset) {
			$this->reset();
		}
		return $ret;
	}

	/**
	 * DELETE処理
	 */
	function delete($is_key = TRUE, $is_reset = TRUE)
	{
		$db = $this->connect($this->mdb);
		if ($is_key) {
			foreach ($this->key as $key) {
				$value = $this->get_data($key);
				$db->where($key, $value);
			}
		} else {
			if (count($this->delete_where) > 0) {
				foreach ($this->delete_where as $column_condition=>$value) {
					$db->where($column_condition, $value);
				}
			} else {
				foreach ($this->tbl_item as $column) {
					$value = $this->get_data($column);
					if ($this->check_set_value($value)) {
						$db->where($column, $value);
					}
				}
			}
		}
		$this->add_clear_key();
		$ret = $db->delete($this->tbl_name);
		if ($this->is_transaction() === FALSE) $this->CI->gen_cache->clear_cache();
		if ($is_reset) {
			$this->reset();
		}
		return $ret;
	}

	/**
	 * 値のGET/SET
	 */
	function __call($name, $args)
	{
		$column = str_replace(array('get_', 'set_'), '', $name);
		if (in_array($column, $this->tbl_item)) {
			// getter
			if (strpos($name, 'get_') !== false) {
				return $this->get_data($column);
			}
			// setter
			if (strpos($name, 'set_') !== false) {
				return $this->set_data($column, $args[0]);
			}
		} else {
			$this->CI->gen_exception->exception("__call not exists column : [$this->tbl_name : $column]");
		}
	}

	/**
	 * 値の取得
	 */
	function get_data($column)
	{
		return $this->__get($column);
	}

	/**
	 * 値の設定
	 */
	function set_data($column, $data)
	{
		$this->__set($column, $data);
	}

	/**
	 * 値の取得
	 */
	function __get($column)
	{
		if (in_array($column, $this->tbl_item) === FALSE) {
			$this->CI->gen_exception->exception("__get not exists column : [$this->tbl_name : $column]");
		}
		$ret = FALSE;
		if (isset($this->data[$column])) {
			$ret = $this->data[$column];
		}
		return $ret;
	}

	/**
	 * 値の設定
	 */
	function __set($column, $value)
	{
		if (in_array($column, $this->tbl_item) === FALSE) {
			$this->CI->gen_exception->exception("__set not exists column [$this->tbl_name] : $column");
		}
		$this->data[$column] = $value;
	}

	/**
	 * リセット
	 */
	function reset()
	{
		$this->data = array();
		$this->use_part_cache = FALSE;
		$this->cache_key_value = array();
		$this->select_where = array();
		$this->delete_where = array();
		$this->compile_sql = '';
	}

	/**
	 * デフォルト値の取得
	 */
	function default_data()
	{
		$data = array();
		foreach ($this->tbl_item as $column) {
			$data[$column] = isset($this->tbl_info[$column]) ? $this->tbl_info[$column]['default'] : '';
		}
		return $data;
	}

	/**
	 * GET/POSTデータの設定
	 */
	function set_post_value()
	{
		foreach ($this->tbl_item as $column) {
			$data = $this->input->get_post($column);
			if ($this->check_set_value($data)) {
				$this->set_data($column, $data);
			}
		}
	}

	/**
	 * paramの設定
	 */
	function set_param_value($param)
	{
		if (!is_array($param)) { return; }
		foreach ($this->tbl_item as $column) {
			if ($this->check_set_value($param, $column)) {
				$this->set_data($column, $param[$column]);
			}
		}
	}

	/**
	 * キャッシュのクリアとなるキーを設定する
	 */
	function add_clear_key()
	{
		$key = $this->CI->gen_cache->model_cache_key($this);
		$this->CI->gen_cache->add_clear_key($key);
	}

	/**
	 * SQLの実行
	 */
	function exec($sql, $bind = NULL)
	{
		$db = $this->connect($this->mdb);
		if (is_array($bind)) {
			$result = $db->query($sql, $bind);
		} else {
			$result = $db->query($sql);
		}
		$ins_id = $db->insert_id();
		if ($ins_id > 0) $result = $ins_id;
		return $result;
	}

	/**
	 * SQLの実行
	 */
	function query($sql, $bind = NULL)
	{
		$db = $this->connect($this->sdb);
		if (is_array($bind)) {
			$query = $db->query($sql, $bind);
		} else {
			$query = $db->query($sql);
		}
		return $query;
	}

	/**
	 * Transactionの状態取得
	 */
	function trans_status()
	{
		$db = $this->connect($this->mdb);
		return $db->trans_status();
	}

	/**
	 * Transactionの開始
	 */
	function trans_begin()
	{
		$db = $this->connect($this->mdb);
		$db->is_transaction = TRUE;
		$db->trans_begin();
	}

	/**
	 * Transactionの終了
	 */
	function trans_end()
	{
		$db = $this->connect($this->mdb);
		if ($db->is_transaction == FALSE) return;
		if ($this->trans_status()) {
			$this->trans_commit();
		} else {
			$this->trans_rollback();
		}
	}

	/**
	 * トランザクションのロールバック
	 */
	function trans_rollback()
	{
		$db = $this->connect($this->mdb);
		$db->trans_rollback();
		$this->CI->gen_cache->clear_cache();
		$db->is_transaction = FALSE;
	}

	/**
	 * トランザクションのコミット
	 */
	function trans_commit()
	{
		$db = $this->connect($this->mdb);
		$db->trans_commit();
		$this->CI->gen_cache->clear_cache();
		$db->is_transaction = FALSE;
	}

	/**
	 * トランザクション中かどうかをチェック
	 */
	function is_transaction()
	{
		$db = $this->connect($this->mdb);
		return $db->is_transaction;
	}

	/**
	 * コネクションのクローズ
	 */
	function close()
	{
		$this->CI->gen_connect->close($this->sdb);
		$this->CI->gen_connect->close($this->mdb);
	}

	/**
	 * DBのfetch
	 */
	function fetch($resource)
	{
		return mysql_fetch_assoc($resource->result_id);
	}

	/**
	 * truncate処理
	 */
	function truncate()
	{
		$db = $this->connect($this->mdb);
		$db->truncate($this->tbl_name);
	}

}

