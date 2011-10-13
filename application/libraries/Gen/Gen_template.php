<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gen_template {

	var $config = array();
	var $configs = '';
	var $bss = '';
	var $bse = '';
	var $bes = '';
	var $bee = '';
	var $cs  = '';
	var $ce  = '';
	var $cbs = '';
	var $cbe = '';
	var $ds  = '';
	var $de  = '';
	var $lss = '';
	var $lse = '';
	var $les = '';
	var $lee = '';
	var $iss = '';
	var $ise = '';
	var $ies = '';
	var $ied = '';
	var $else = '';
	var $mss = '';
	var $mse = '';
	var $mes = '';
	var $mee = '';

	function __construct()
	{
		// mark settings
		$config['mark_comment_start'] = '{{';
		$config['mark_comment_end'] = '}}';
		$config['mark_start'] = '<!--';
		$config['mark_end'] = '-->';
		$config['block_start'] = '';
		$config['block_end'] = '/';
		$config['main_block'] = '%';  // <!--%main%--> <!--/%main%/-->
		$config['mark_comment'] = '#';  // <!--#--> <!--/#/-->
		$config['mark_data'] = '=';  // <!--=a=/-->
		$config['mark_loop'] = '~';  // <!--~a~--> <!--/~a~/-->
		$config['mark_if'] = '|';    // <!--|calc|--> <!--/|calc|/-->
		$config['mark_else'] = '!'; // <!--!calc!/-->
		$this->config = $config;
		// configs
		$this->configs  = preg_quote($config['mark_comment'], '/');
		$this->configs .= '|'.preg_quote($config['mark_data'], '/');
		$this->configs .= '|'.preg_quote($config['mark_loop'], '/');
		$this->configs .= '|'.preg_quote($config['mark_if'], '/');
		$this->configs .= '|'.preg_quote($config['mark_else'], '/');
		// block
		$this->bss = preg_quote($config['mark_start'].$config['block_start'], '/');
		$this->bse = preg_quote($config['block_start'].$config['mark_end'], '/');
		$this->bes = preg_quote($config['mark_start'].$config['block_end'], '/');
		$this->bee = preg_quote($config['block_end'].$config['mark_end'], '/');
		// main
		$this->mss = $this->bss.preg_quote($config['main_block'], '/');
		$this->mse = preg_quote($config['main_block'], '/').$this->bse;
		$this->mes = $this->bes.preg_quote($config['main_block'], '/');
		$this->mee = preg_quote($config['main_block'], '/').$this->bee;
		// comment
		$this->cs = preg_quote($config['mark_comment_start'], '/');
		$this->ce = preg_quote($config['mark_comment_end'], '/');
		$this->cbs = $this->bss.preg_quote($config['mark_comment'], '/').$this->bse;
		$this->cbe = $this->bes.preg_quote($config['mark_comment'], '/').$this->bee;
		// data
		$this->ds = $this->bss.preg_quote($config['mark_data'], '/');
		$this->de = preg_quote($config['mark_data'], '/').$this->bee;
		// loop
		$this->lss = $this->bss.preg_quote($config['mark_loop'], '/');
		$this->lse = preg_quote($config['mark_loop'], '/').$this->bse;
		$this->les = $this->bes.preg_quote($config['mark_loop'], '/');
		$this->lee = preg_quote($config['mark_loop'], '/').$this->bee;
		// if
		$this->iss = $this->bss.preg_quote($config['mark_if'], '/');
		$this->ise = preg_quote($config['mark_if'], '/').$this->bse;
		$this->ies = $this->bes.preg_quote($config['mark_if'], '/');
		$this->iee = preg_quote($config['mark_if'], '/').$this->bee;
		// else
		$this->ess = $this->bss.preg_quote($config['mark_else'], '/');
		$this->ese = preg_quote($config['mark_else'], '/').$this->bse;
		$this->ees = $this->bes.preg_quote($config['mark_else'], '/');
		$this->eee = preg_quote($config['mark_else'], '/').$this->bee;
		$this->eol = "(?:r|\n|\r\n)?";
	}

	function create_view($template, $data, $class)
	{
		$template = $this->remove_comment($template);
		$template = $this->replace_block($template, $data, $class);
		$template = $this->replace_data($template, $data);
		return $template;
	}

	function main_block($template, $blockname)
	{
		$ismatch = preg_match("/$this->mss$blockname$this->mse$this->eol(.*)$this->mes$blockname$this->mee$this->eol/us", $template, $matches);
		if ($ismatch) {
			$template = $matches[1];
		} else {
			$ismatch = preg_match("/(.*)$this->mes$blockname$this->mee$this->eol/us", $template, $matches);
			if ($ismatch) {
				$template = $matches[1];
			} else {
				$ismatch = preg_match("/$this->mss$blockname$this->mse$this->eol(.*)/us", $template, $matches);
				if ($ismatch) {
					$template = $matches[1];
				}
			}
		}
		return $template;
	}

	function remove_comment($template)
	{
		$template = preg_replace("/$this->cs.*?$this->ce/us", '', $template);
		$template = preg_replace("/$this->cbs.*?$this->cbe$this->eol/us", '', $template);
		return $template;
	}

	function replace_block($template, $data, $class)
	{
		preg_match_all("/$this->bss($this->configs)(.*?)\\1$this->bse$this->eol(.*?)$this->bes\\1\\2\\1$this->bee$this->eol/us", $template, $matches);
		$matches_contents = $matches[0];
		$matches_kind = $matches[1];
		$matches_name = $matches[2];
		$matches_template = $matches[3];
		foreach ($matches_contents as $i=>$contents) {
			$temp = '';
			$replace = FALSE;
			switch ($matches_kind[$i]) {
				case $this->config['mark_loop']: // loop
					$temp = $this->replace_loop($matches_template[$i], $data[$matches_name[$i]], $class);
					$replace = TRUE;
					break;
				case $this->config['mark_if']: // if
					$temp = $this->replace_condition($matches_template[$i], $matches_name[$i], $data, $class, TRUE);
					$replace = TRUE;
					break;
				case $this->config['mark_else']: // else
					$temp = $this->replace_condition($matches_template[$i], $matches_name[$i], $data, $class, FALSE);
					$replace = TRUE;
					break;
			}
			if ($replace) {
				$template = preg_replace("/$this->bss(".preg_quote($matches_kind[$i], '/').")(".preg_quote($matches_name[$i], '/').")\\1$this->bse$this->eol(?:.*?)$this->bes\\1\\2\\1$this->bee$this->eol/us", $temp, $template, 1);
			}
		}
		return $template;
	}

	function replace_loop($template, $loop_data, $class)
	{
		$ret_template = '';
		foreach ($loop_data as $i=>$data) {
			$temp = '';
			$temp = $this->replace_block($template, $data, $class);
			$temp = $this->replace_data($temp, $data);
			$ret_template .= $temp;
		}
		return $ret_template;
	}

	function replace_condition($template, $name, $data, $class, $result)
	{
		$method = 'if_'.$name;
		if (!method_exists($class, $method)) {
			$method = 'if_pagedefault';
		}
		if ($class->$method($data) == $result) {
			$template = $this->replace_block($template, $data, $class);
			$template = $this->replace_data($template, $data);
		} else {
			$template = preg_replace("/.*/us", "", $template, 1);
		}
		return $template;
	}

	function replace_data($template, $data)
	{
		preg_match_all("/$this->ds(.*?)$this->de/us", $template, $matches);
		$matches_contents = $matches[0];
		$matches_name = $matches[1];
		foreach ($matches_contents as $i=>$contents) {
			if (isset($data[$matches_name[$i]])) {
				$template = preg_replace("/".preg_quote($contents, '/')."/us", $data[$matches_name[$i]], $template, 1);
			} else {
				$template = preg_replace("/".preg_quote($contents, '/')."/us", '', $template, 1);
			}
		}
		return $template;
	}

	function delete_rules($template, $rules)
	{
		foreach ($rules as $i => $rule) {
			if (!isset($rule['start'])) $rule['start'] = '';
			if (!isset($rule['end'])) $rule['end'] = '';
			$start_rule = $rule['start'];
			$end_rule = $rule['end'];
			$reg_start = preg_quote($start_rule, '/');
			$reg_end = preg_quote($end_rule, '/');
			if ($reg_start == '') {
				$template = preg_replace("/(.*)$reg_end/us", '', $template);
			} else if ($reg_end == '') {
				$template = preg_replace("/$reg_start(.*)/us", '', $template);
			} else {
				$template = preg_replace("/$reg_start(.*?)$reg_end/us", '', $template);
			}
		}
		return $template;
	}

	function use_rule($template, $rule, $isone = TRUE)
	{
		if (!isset($rule['start'])) $rule['start'] = '';
		if (!isset($rule['end'])) $rule['end'] = '';
		$start_rule = $rule['start'];
		$end_rule = $rule['end'];
		$reg_start = preg_quote($start_rule, '/');
		$reg_end = preg_quote($end_rule, '/');
		if ($reg_start == '') {
			preg_match_all("/(.*)$reg_end/us", $template, $matches);
		} else if ($reg_end == '') {
			preg_match_all("/$reg_start(.*)/us", $template, $matches);
		} else {
			preg_match_all("/$reg_start(.*?)$reg_end/us", $template, $matches);
		}
		if ($isone) {
			$use = array_shift($matches[1]);
		} else {
			$use = $matches[1];
		}
		return $use;
	}

}
