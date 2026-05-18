<?php 
require '../../config.php';
session_start();
if(isset($_POST['login'])){
	if($_POST['user']==PNL_USERNAME && $_POST['pass'] == PNL_PASSWORD){
		$_SESSION['user']=$_POST['user'];
		echo "SUCCESS";
	}else{
		echo "FAILED";
	}
}




?>