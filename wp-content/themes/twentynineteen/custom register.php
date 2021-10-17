<?php 
/*

Template Name: Custom Register Page

*/
get_header(); 
global $wpdb;

if($_POST){
	if($_POST['add']){

	$username=$wpdb->escape($_POST['txtUsername']);
	$email=$wpdb->escape($_POST['txtEmail']);
	$password=$wpdb->escape($_POST['txtPassword']);
	wp_create_user($username, $password, $email);
	echo " {$username} user create successfully";


	//$password=$wpdb->escape($_POST['txtPassword']);
	//$user_id=2;
	//wp_set_password($password,$user_id );
	exit;
	}elseif($_POST['show']){

	$user = get_users();
	echo "<pre>";
	print_r($user);
	echo "</pre>";
	exit;
	}elseif($_POST['show_single']){
		$id= $_POST['num'];
		$user = get_userdata($id);
	echo "<pre>";
	print_r($user);
	echo "</pre>";
	exit;
	}elseif($_POST['delete']){
		$id= $_POST['num'];
		$user= wp_delete_user( $id );
	echo "<pre>";
	print_r($user);
	echo "</pre>";
	exit;
	}else{
	echo "wrong btn";
	}
}


?>



<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<div class="col-sm-12" style="padding: 50px 150px;">

<h1>Create User</h1>

<form method="post">
	Username: <input type="text" name="txtUsername"><br><br>
	Email &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <input type="text" name="txtEmail"><br><br>
	Password&nbsp;: <input type="text" name="txtPassword"><br><br>
	<input type="submit" name="add" value="add">
	<input type="submit" name="show" value="show">
	<input type="text" name="num" value="" id="num">
	<input type="submit" name="delete" value="delete">
	<input type="submit" name="update" value="update"><br><br>
	<input type="submit" name="show_single" value="show single">
	
</form>
</div>

<script type="text/javascript">
	
</script>
</body>
</html>



<?php get_footer() ?>


