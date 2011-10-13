<?php
print <<< MSG
<?php
class <!--=base_model_name=/--> extends MY_Model {

	var \$is_apc;
	var \$mdb;
	var \$sdb;
	var \$key;
	var \$cache_key;
	var \$tbl_name;
	var \$tbl_item;
	var \$tbl_info;
	var \$use_part_cache;
	var \$cache_key_value;
	var \$default_order_by;
	var \$select_where;
	var \$delete_where;

	function __construct()
	{
		parent::__construct();
		\$this->is_apc = FALSE;
		\$this->mdb = 'master';
		\$this->sdb = 'slave';
		\$this->key = array(<!--=key=/-->);
		\$this->cache_key = array(<!--=cache_key=/-->);
		\$this->tbl_name = '<!--=tbl_name=/-->';
		\$this->tbl_item = array(<!--=tbl_item=/-->);
		\$this->tbl_info = array(
<!--~tbl_info~-->
			<!--=column=/--> => array('type' => <!--=type=/-->, 'default' => <!--=default=/-->),
<!--/~tbl_info~/-->
		);
		\$this->use_part_cache = FALSE;
		\$this->cache_key_value = array();
		\$this->default_order_by = array(
<!--~default_order_by~-->
			<!--=key=/--> => 'asc',
<!--/~default_order_by~/-->
		);
		\$this->select_where = array();
		\$this->delete_where = array();
	}

}
MSG;
