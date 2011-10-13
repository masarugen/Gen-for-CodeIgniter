<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['time']      = time();
$config['microtime'] = microtime(TRUE);
$config['raw_now']   = date('YmdHis',      $config['time']);
$config['raw_date']  = date('Ymd',         $config['time']);
$config['raw_time']  = date('His',         $config['time']);
$config['now']       = date('Y/m/d H:i:s', $config['time']);
$config['now_date']  = date('Y/m/d',       $config['time']);
$config['now_time']  = date('H:i:s',       $config['time']);
$config['now_y']     = date('Y',           $config['time']);
$config['now_m']     = date('m',           $config['time']);
$config['now_d']     = date('d',           $config['time']);
$config['now_h']     = date('H',           $config['time']);
$config['now_i']     = date('i',           $config['time']);
$config['now_s']     = date('s',           $config['time']);

