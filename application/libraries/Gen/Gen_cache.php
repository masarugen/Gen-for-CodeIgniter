<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * キャッシュを管理するライブラリ
 *
 */
class Gen_cache {

	var $CI;
	var $config_name;
	var $cache_data;
	var $clear_keys;
	var $use_apc;
	var $use_memcache;
	var $use_cache;

	function __construct()
	{
		$this->CI = & get_instance();
		$this->config_name = 'gen_cache';
		$this->CI->config->load($this->config_name, TRUE, TRUE);
		$this->init();
	}

	function init()
	{
		$this->CI->load->driver('cache');
		$this->cache_data = array();
		$this->clear_keys = array();
		$this->use_cache    = $this->CI->config->item('enable', $this->config_name);
		$this->use_apc      = $this->CI->cache->apc->is_supported();
		$this->use_memcache = $this->CI->cache->memcached->is_supported();
	}

	/**
	 * APCからデータの取得
	 */
	function apc($key)
	{
		if ($this->use_cache === FALSE || $this->use_apc === FALSE) return FALSE;
		return $this->CI->cache->apc->get($key);
	}

	/**
	 * APCへのデータのセット
	 */
	function set_apc($key, $value, $ttl = NULL)
	{
		if ($this->use_cache === FALSE || $this->use_apc === FALSE) return FALSE;
		if ($ttl === NULL) $ttl = $this->default_ttl($ttl);
		return $this->CI->cache->apc->save($key, $value, $ttl);
	}

	/**
	 * memcacheからのデータの取得
	 */
	function memcache($key)
	{
		if ($this->use_cache === FALSE || $this->use_memcache === FALSE) return FALSE;
		return $this->CI->cache->memcached->get($key);
	}

	/**
	 * memcacheへデータの設定
	 */
	function set_memcache($key, $value, $ttl = NULL)
	{
		if ($this->use_cache === FALSE || $this->use_memcache === FALSE) return FALSE;
		if ($ttl === NULL) $ttl = $this->default_ttl($ttl);
		$this->CI->cache->memcached->delete($key);
		return $this->CI->cache->memcached->save($key, $value, $ttl);
	}

	/**
	 * デフォルトttlの取得
	 */
	function default_ttl($ttl = NULL)
	{
		if ($this->CI->config->item('ttl', $this->config_name) !== FALSE) {
			$ttl = $this->CI->config->item('ttl', $this->config_name);
		}
		return $ttl;
	}

	/**
	 * Modelのキャシュに使用するキーを取得する
	 */
	function model_cache_key($model)
	{
		if ($this->use_cache === FALSE) return FALSE;
		if (defined('ADMIN')) return FALSE;
		$key = '';
		foreach ($model->cache_key as $column) {
			if (isset($model->cache_key_value[$column])) {
				$key = $column.'_'.$key.'_'.$model->cache_key_value[$column];
			} else if (isset($model->data[$column])) {
				$key = $column.'_'.$key.'_'.$model->data[$column];
			} else {
				if (!$model->use_part_cache) {
					$this->CI->gen_exception->exception("db cache key can't create : not set [ $model->tbl_name : $column ]");
				}
			}
		}
		$ret = 'cache_'.$model->tbl_name;
		if ($key !== '') $ret = $ret.'_'.$key;
		return $ret;
	}

	/**
	 * キャッシュが有効かどうかチェックする
	 */
	function is_enable_cache($cache_key, $cache_data)
	{
		$ret = TRUE;
		$this->update_time = $this->memcache($cache_key.'_time');
		if ($cache_data === FALSE || $cache_data['result'] === FALSE || ($this->update_time !== FALSE && ($cache_data['time'] <= $this->update_time))) {
			$ret = FALSE;
		}
		return $ret;
	}

	/**
	 * キャッシュの更新時間を設定
	 */
	function update_cache_time($cache_key)
	{
		$this->set_memcache($cache_key.'_time', $this->cache_time());
	}

	/**
	 * キャッシュ時間取得
	 */
	function cache_time()
	{
		return microtime(true);
	}

	/**
	 * キャッシュへデータの設定
	 */
	function set($cache_key, $cache_data, $use_apc = FALSE)
	{
		$cache['time'] = $this->cache_time();
		$cache['result'] = $cache_data;
		if ($use_apc) {
			$this->set_apc($cache_key, $cache);
		}
		$this->set_memcache($cache_key, $cache);
		$this->cache_data[$cache_key] = $cache;
		return $cache;
	}

	/**
	 * キャッシュからデータの取得
	 */
	function get($cache_key, $use_apc = FALSE)
	{
		$cache = FALSE;
		if (isset($this->cache_data[$cache_key])) {
			$cache = $this->cache_data[$cache_key];
			$cache['type'] = 'GLOBAL';
		} else {
			if ($use_apc) {
				$cache = $this->apc($cache_key);
			}
			if ($cache === FALSE) {
				$cache = $this->memcache($cache_key);
				if ($cache !== FALSE) $cache['type'] = 'MEMD';
			} else {
				$cache['type'] = 'APC';
			}
			if ($cache !== FALSE) {
				$this->cache_data[$cache_key] = $cache;
			}
		}
		$this->CI->gen_log->cache($cache['type'].':'.$cache_key);
		return $cache;
	}

	/**
	 * キャッシュをクリアする値を設定
	 */
	function add_clear_key($cache_key)
	{
		$this->clear_keys[] = $cache_key;
	}

	/**
	 * 設定されているキャッシュをクリアする
	 */
	function clear_cache()
	{
		foreach ($this->clear_keys as $cache_key) {
			$this->update_cache_time($cache_key);
		}
		$this->clear_keys = array();
	}

}

