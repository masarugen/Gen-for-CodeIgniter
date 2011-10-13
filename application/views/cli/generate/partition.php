ALTER TABLE <!--=database=/-->.<!--=table_name=/--> PARTITION BY RANGE(TO_DAYS(<!--=range_field=/-->)) (
PARTITION <!--=partition_name=/-->_<!--=partition_date=/--> VALUES LESS THAN (<!--=partition_time=/-->)
);
ALTER TABLE <!--=database=/-->.<!--=table_name=/--> ADD PARTITION (
<!--~partitions~-->
	PARTITION <!--=partition_name=/-->_<!--=partition_date=/--> VALUES LESS THAN (<!--=partition_time=/-->)<!--!end!-->,<!--/!end!/-->

<!--/~partitions~/-->
);
