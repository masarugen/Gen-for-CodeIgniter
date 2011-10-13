<?php

if (!function_exists('hook_function')) {
	function hook_function()
	{
	}
}

if (!function_exists('ci_require')) {
	function ci_require($class_name, $folder_name = NULL, $file_name = NULL)
	{
		if (!class_exists($class_name)) {
			$path = APPPATH.'libraries/';
			if ($folder_name !== NULL) $path = $path.$folder_name.'/';
			if ($file_name === NULL) $file_name = $class_name;
			require_once($path.$file_name.'.php');
		}
	}
}

if (!function_exists('model_require')) {
	function model_require($class_name)
	{
		$base_class_name = ucfirst("base_$class_name");
		if (!class_exists($base_class_name)) {
			$path = APPPATH.'models/base/';
			require_once($path.strtolower($base_class_name).'.php');
		}
	}
}

if (!function_exists('ci_exists_model')) {
	function ci_exists_model($class_name)
	{
		$ret = FALSE;
		$path = APPPATH.'models/'.strtolower($class_name).'.php';
		if (file_exists($path)) {
			$ret = TRUE;
		}
		return $ret;
	}
}

