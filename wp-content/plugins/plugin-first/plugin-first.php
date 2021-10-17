<?php

/*

Plugin Name: Plugin First
Plugin URI: www/https/abc.com
Description: My first description plugin
Author: Akash Verma
Author Uri:www.df.com
Version: 1/2


*/

register_activation_hook(__FILE__,'plugin_first_activate');
register_deactivation_hook(__FILE__,'plugin_first_deactivate');

function plugin_first_activate(){
	global $wpdb;
	global $table_prefix;
	$table=$table_prefix.'plugin_first';
	$sql= "CREATE TABLE $table (
	 `id` INT NOT NULL AUTO_INCREMENT , `username` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB";
	$wpdb->query($sql);

}

function plugin_first_deactivate(){
	global $wpdb;
	global $table_prefix;
	$table=$table_prefix.'plugin_first';
	$sql="DROP TABLE $table";
	$wpdb->query($sql);


}

add_action('admin_menu','p_menu');
function p_menu(){
	add_menu_page('Form Data','Form Data',8,__FILE__,'form_data_list');
}

function form_data_list(){
	include('form_data_list.php');
}

?>