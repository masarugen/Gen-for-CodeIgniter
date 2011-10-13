<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Session extends CI_Session {

	var $CI;
	var $_isstart = FALSE;
	var $_iscookie = TRUE;
	var $_issecure = FALSE;
	var $_isregenerate = FALSE;

	function __construct($param = array())
	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->_setinstance($param, 'cookie');
		$this->_setinstance($param, 'secure');
		$this->_setinstance($param, 'regenerate');
		$this->_session();
	}

	// インスタンス変数の設定
	function _setinstance($param, $key)
	{
		if (isset($param[$key])) {
			$instance = "_is$key";
			$this->$instance = $param[$key];
		}
	}

	// セッションのリセット
	function reset()
	{
		if (!$this->_isstart) return;
		$_SESSION = array();
		if ($this->_iscookie) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
		}
		session_destroy();
		$this->_isstart = FALSE;
		$this->_session();
	}

	// セッションへの登録
	function set($key, $value)
	{
		$_SESSION[$key] = $value;
	}

	// セッションからの取得
	function get($key)
	{
		return isset($_SESSION[$key]) ? $_SESSION[$key] : NULL;
	}

	// セッションを有効にする
	function _session($param=array())
	{
		if (!$this->_isstart) {
			ini_set('session.hash_function', 1);
			ini_set('session.name', '_SID');
			ini_set('session.hash_bits_per_character', 6);
			$this->_cookie();
			if (!session_id()) {
				$sess_name = session_name();
				$session_id = $this->CI->input->get_post($sess_name);
				if ($session_id !== FALSE) $session_id = urldecode($session_id);
				if ($session_id !== FALSE) {
					session_id($session_id);
				}
			}
			$this->CI->gen_log->debug("$sess_name : $session_id");
			session_start();
			$this->_regenerate();
			$this->_isstart = TRUE;
		} else {
			show_error('Already Start Sessoin');
		}
	}

	// セッションIDの再生成
	function _regenerate()
	{
		if ($this->_isregenerate) {
			session_regenerate_id(TRUE);
		}
	}

	// Cookieを使うかどうかを設定
	function _cookie()
	{
		if ($this->_iscookie) {
			ini_set('session.use_cookies', 1);
			if ($this->_issecure) {
				ini_set('session.cookie_secure', 1);
			}
		} else {
			ini_set('session.use_cookies', 0);
		}
		ini_set('session.use_only_cookies', 0);
		ini_set('session.use_trans_sid', 1);
	}

}
