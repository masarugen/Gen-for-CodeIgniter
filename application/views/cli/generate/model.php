<?php
print <<< MSG
<?php
model_require('<!--=tbl_name=/-->_model');
class <!--=model_name=/--> extends <!--=base_model_name=/--> {

	function __construct()
	{
		parent::__construct();
	}
}
MSG;
