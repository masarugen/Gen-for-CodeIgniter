<div>
<em style="text-decoration:underline; font-weight:bold; font-size:large;"><!--=database=/-->.<!--=table_name=/--></em>
<table border="1" cellpadding="1" cellspacing="1">
<tr>
<th>table</th><td><!--=database=/-->.<!--=table_name=/--></td>
</tr>
<tr>
<th>engine</th><td><!--=engine=/--></td>
</tr>
<tr>
<th>charset</th><td><!--=charset=/--></td>
</tr>
<tr>
<th>collate</th><td><!--=collate=/--></td>
</tr>
<tr>
<th>primary</th><td><!--=primary=/--></td>
</tr>
<!--~unique~-->
<tr>
<th>unique</th><td><!--=index_name=/-->(<!--=fields=/-->)</td>
</tr>
<!--/~unique~/-->
<!--~index~-->
<tr>
<th>index</th><td><!--=index_name=/-->(<!--=fields=/-->)</td>
</tr>
<!--/~index~/-->
<!--~partition~-->
<tr>
<th>partition</th><td><!--=partition_name=/-->(<!--=range_field=/-->)</td>
</tr>
<!--/~partition~/-->
</table>
<table border="1" cellspacing="1" cellpadding="1">
<tr>
<th>&nbsp;</th>
<th>field_name</th>
<th>field_type</th>
<th>length</th>
<th>default</th>
<th>not_null</th>
<th>auto_increment</th>
</tr>
<!--~fields~-->
<tr>
<td><!--=no=/--></td>
<td><!--=field_name=/-->&nbsp;</td>
<td><!--=field_type=/-->&nbsp;</td>
<td><!--=length=/-->&nbsp;</td>
<td><!--|default|--><!--=default=/--><!--/|default|/-->&nbsp;</td>
<td><!--|not_null|-->○<!--/|not_null|/-->&nbsp;</td>
<td><!--|auto_increment|-->○<!--/|auto_increment|/-->&nbsp;</td>
</tr>
<!--/~fields~/-->
</table>
</div>
<br />
<hr />
<br />
