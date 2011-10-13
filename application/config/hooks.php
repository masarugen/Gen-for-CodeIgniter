<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

$hook['pre_system'][] = array(
	'class' => 'Gen_moble_hook',
	'function' => 'input',
	'filename' => 'Gen_mobile_hook.php',
	'file_path' => 'hooks',
);

$hook['pre_controller'][] = array(
	'class' => '',
	'function' => 'hook_function',
	'filename' => 'gen_function.php',
	'filepath' => 'hooks',
);

$hook['post_controller'][] = array(
	'class' => 'Gen_mobile_hook',
	'function' => 'output',
	'filename' => 'Gen_mobile_hook.php',
	'flepath' => 'hooks',
);

/* End of file hooks.php */
/* Location: ./application/config/hooks.php */
