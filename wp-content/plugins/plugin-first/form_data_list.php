<?php 


	global $wpdb;
	global $table_prefix;
	$table=$table_prefix.'plugin_first';
	$sql="SELECT * FROM $table";
	$result=$wpdb->get_results($sql);
	//print_r($result); 


?>

<table border="2">
	<tr>
		<td>id</td><td>username</td>
	</tr>
	<?php foreach ($result as $list) { ?>
		<tr>
			<td><?php echo $list->id ?></td><td><?php echo $list->username ?></td>
		</tr>
	<?php	} ?>
</table>