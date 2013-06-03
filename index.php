<?php

include('allowed_ips.php');
require_once('recaptchalib.php');
$publickey = "6Lew7OASAAAAAOeVU4Dmh_I4UwPyUOwrElcj2GH1";


$ip = @$REMOTE_ADDR;
if(empty($ip))
    {
	   $ip=$_SERVER['REMOTE_ADDR'];
    }

$secure=false;
for($i=0;$i<count($ai);$i++){
	if($ip==$ai[$i]){
		$secure=true;
	}
}

//constants
$http_to_anchor_pattern = '/(http:\/\/[a-z0-9\.\_\-\?\=\%\&\/]+)/i';
$http_to_youtube_pattern = '/(youtube:[a-z0-9\.\_\-\?\=\%\&\/]+)/i';
$http_folder_path = "/";
date_default_timezone_set("Australia/Brisbane");

//if allowed IPs is missing, create empty file
if(!file_exists('allowed_ips.php'))
	{
		$filename = "allowed_ips.php";
		$fh = fopen($filename, 'w');
		fwrite($fh, "");
		fclose($fh);
	}

//if messages.php and messagesbb.php are missing, create empty files
if(!file_exists('messages.php'))
	{
		$filename = "messages.php";
		$fh = fopen($filename, 'w');
		fwrite($fh, "");
		fclose($fh);
	}
if(!file_exists('messagesbb.php'))
	{
		$filename = "messagesbb.php";
		$fh = fopen($filename, 'w');
		fwrite($fh, "");
		fclose($fh);
	}

//if control is not set, create it and set to default (active)
if(!file_exists('chat-online.php'))
	{
		$control = '<?php $chat_activated="1"; ?>';
		$filename = "chat-online.php";
		$fh = fopen($filename, 'w');
		fwrite($fh, $control);
		fclose($fh);
	}

//if control ON and control OFF files do not exist, create them
if(!file_exists('control-chat-on.php'))
	{
		$control = '
<?php
$filename = "chat-online.php";
$fh = fopen($filename, \'w\');
fwrite($fh, \'<?php $chat_activated=1; ?>\');
fclose($fh);
?>
		';
		
		
		$filename = "control-chat-on.php";
		$fh = fopen($filename, 'w');
		fwrite($fh, $control);
		fclose($fh);
	}
if(!file_exists('control-chat-off.php'))
	{
		
		$control = '
<?php
$filename = "chat-online.php";
$fh = fopen($filename, \'w\');
fwrite($fh, \'<?php $chat_activated=0; ?>\');
fclose($fh);
?>
		';
		$filename = "control-chat-off.php";
		$fh = fopen($filename, 'w');
		fwrite($fh, $control);
		fclose($fh);
	}

//if image path and thumb path are missing, creat them
$dirname = "images";
$dirname_f = "{$_SERVER["DOCUMENT_ROOT"]}{$http_folder_path}{$dirname}/";
if (file_exists($dirname_f))
	{
		//do nothing
	}
else
	{  
		mkdir("{$_SERVER["DOCUMENT_ROOT"]}{$http_folder_path}{$dirname}", 0777);
	}
$dirname = "thumbs";
$dirname_f = "{$_SERVER["DOCUMENT_ROOT"]}{$http_folder_path}images/{$dirname}/";
if (file_exists($dirname_f))
	{
		//do nothing
	}
else
	{  
		mkdir("{$_SERVER["DOCUMENT_ROOT"]}{$http_folder_path}images/{$dirname}", 0777);
	}

if(file_exists('chat-online.php'))
	{
		include('chat-online.php');
	}





if($secure==true){
	
//begin checking posted data
if(strlen($_POST["message-text"])>0)
	{
		$message=$_POST["message-text"];
		$message=strip_tags($message);
		$message=stripslashes($message);
		
		//convert message to html
		$time_now = date('Y-m-d H:i:s',time());
		
		//if there was an image upload
		if($_FILES["image_full_upload"]>"")
			{
				//assign unique file name
				
				
				$filename_full=$_SERVER["DOCUMENT_ROOT"]."{$http_folder_path}images/" . $_FILES["image_full_upload"]["name"];
				$filename_thumb=$_SERVER["DOCUMENT_ROOT"]."{$http_folder_path}images/thumbs/" . $_FILES["image_full_upload"]["name"];
				
				$filename_full_nopath="{$http_folder_path}images/".$_FILES["image_full_upload"]["name"];
				$filename_thumb_nopath="{$http_folder_path}images/thumbs/".$_FILES["image_full_upload"]["name"];
				
				if(file_exists($filename_full))
					{
						$file_unique_identifier=uniqid();
						$filename_full=$_SERVER["DOCUMENT_ROOT"]."{$http_folder_path}images/".$file_unique_identifier.$_FILES["image_full_upload"]["name"];
						$filename_thumb=$_SERVER["DOCUMENT_ROOT"]."{$http_folder_path}images/thumbs/".$file_unique_identifier.$_FILES["image_full_upload"]["name"];
						
						$filename_full_nopath="{$http_folder_path}images/".$file_unique_identifier.$_FILES["image_full_upload"]["name"];
						$filename_thumb_nopath="{$http_folder_path}images/thumbs/".$file_unique_identifier.$_FILES["image_full_upload"]["name"];
						
					}
				
				//Move Uploaded Files
				move_uploaded_file($_FILES["image_full_upload"]["tmp_name"],$filename_full);
				
				//check if move was successful
				$files_uploaded=0;
				if(file_exists($filename_full))
					{
						copy($filename_full, $filename_thumb);
						//resize copied file
						include('simpleimage.php');
						$image = new SimpleImage();
						$image->load($filename_thumb);
						$image->resizeToWidth(400);
						$image->save($filename_thumb);
						
						$files_uploaded=1;
						if(file_exists($filename_thumb))
							{
								$thumbs_uploaded=1;
							}
					}
				
			}
		
		//process message
		$message=str_replace('
', "<br />",$message);
		$message='
<div class="message">
	<p class="msgdate">'.$time_now.'</p>
	'.$message;
	
	if($thumbs_uploaded==1)
		{
			if($files_uploaded==1)
				{
					//Check for Alt Text
					$image_alt_text=$_POST["image-alt"];
					$image_alt_text=strip_tags($image_alt_text);
					$image_alt_text=stripslashes($image_alt_text);

					//add image section
					$message.='<div class="msg_image"><a href="'.$filename_full_nopath.'" target="_new"><img src="'.$filename_thumb_nopath.'" alt="'.$image_alt_text.'"></a></div>';
				}
		}
	else
		{
			if($files_uploaded==1)
				{
					//Check for Alt Text
					$image_alt_text=$_POST["image-alt"];
					$image_alt_text=strip_tags($image_alt_text);
					$image_alt_text=stripslashes($image_alt_text);
					
					//add image section
					$message.='<div class="msg_image"><a href="'.$filename_full_nopath.'" target="_new"><img src="'.$filename_full_nopath.'" alt="'.$image_alt_text.'" ></a></div>';
				}
		}
		
		//close message div tag	
		$message.='</div>';

		//wrap urls
		$source = $message;
		$replacement = '<a href="$1" target="_blank">$1</a>';
		$source = preg_replace($http_to_anchor_pattern, $replacement, $source); 
		$message=$source;
		
		//wrap youtube ids	
		$source = $message;
		$replacement = '<iframe width="420" height="315" src="http://www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>';
		$source = preg_replace($http_to_youtube_pattern, $replacement, $source);
		$source = str_replace('youtube:', '', $source);
		$message=$source;
		
		//write back-copy of messages
		$messagebb='<div class="drc">'.$ip.'</div>'.$message;
		$filename = "messagesbb.php";
		$fh = fopen($filename, 'r');
		$existing_message=fread($fh,filesize($filename));
		fclose($fh);
		
		if(file_exists($filename))
			{
				unlink($filename);
			}
		
		$fh = fopen($filename, 'x');
		
		fwrite($fh, $messagebb.$existing_message);
		fclose($fh);
		
		
		//write front copy of messages
		$filename = "messages.php";
		$fh = fopen($filename, 'r');
		$existing_message=fread($fh,filesize($filename));
		fclose($fh);
		
		if(file_exists($filename))
			{
				unlink($filename);
			}
		
		$fh = fopen($filename, 'x');
		
		fwrite($fh, $message.$existing_message);
		fclose($fh);
		
		header('Location: '.$http_folder_path);
		
	}
}
?>

<html>
<head>

<title>Anonymous Chat</title>

<script type="text/javascript">

</script>

<style type="text/css">
form {
	background:url('leave-me-a-message.png') no-repeat;
	width:440px;
	height:130px;
	padding:150px 30px 30px 30px;
	box-shadow:0px 0px 15px #000000, inset 0px 0px 15px #a8a8a8;
	border:4px solid #fefefe;
	border-radius:5px;
}

p.msgdate {
	font-style:italic;
	font-size:12px;
	margin-bottom:5px;
}

div.message {
	width:100%;
	border-bottom:1px solid black;
	padding-bottom:20px;
	word-wrap:break-word;
}
	div.message:hover {
		/*box-shadow:0px 0px 5px #cecece;*/
	}

div.messages {
	text-align:left;
	width:440px;
	padding:30px 30px 30px 30px;
	box-shadow:0px 0px 15px #000000, inset 0px 0px 15px #a8a8a8;
	border:4px solid #fefefe;
	border-radius:5px;
}

textarea, input.input-submit {
	margin-top:10px;
	border-radius:3px;
	box-shadow:0px 0px 3px #000000, inset 0px 0px 25px #d3d3d3;
	display:block;
	width:440px;
}

input#image-alt {
	border-radius: 3px;
	box-shadow: 0px 0px 0px black, inset 0px 0px 10px lightGrey;
	height: 20px;
	width: 200px;
	border: 1px solid #444;
	margin-bottom: 5px;
}

body {
	margin:0px;
	padding:20px;
	text-align:center;
	font-family:helvetica, arial, sans-serif;
}

div.formwrapper {
	margin-left:auto;
	margin-right:auto;
	width:500px;
}

div.msg_image {
	margin-top:12px;
}
	div.msg_image img {
		max-width:450px;
		height:auto;
		border-radius:3px;
	}

form label {
	font-size:12px;
}

label {
	margin-right:10px;
}

div.input-wrapper {
	text-align:left;
	width:100%;
	padding:10px 0px 10px 0px;
}



</style>
</head>

<body>
<div class="formwrapper">
<?php	

if($secure==true){
	echo '
<form id="new-chat-post" method="post" action="'.$http_folder_path.'verify.php" enctype="multipart/form-data">
	<textarea name="message-text"> </textarea>
	<div class="input-wrapper">
		<label>Alt Text</label>
		<input type="text" name="image-alt" id="image-alt" value="&nbsp;"/>
		<br/>
		<label>Image Attachment</label>
		<input type="file" name="image_full_upload" id="image_full_upload" />
		<br/>
		<input type="submit" class="input-submit">
		' . recaptcha_get_html($publickey) . '
	</div>
</form>
';
}
else{
	echo '
<form id="new-chat-post" method="post" action="'.$http_folder_path.'auth.php" enctype="multipart/form-data">
	<div class="input-wrapper">
		<input type="submit" class="input-submit" value="Unlock your IP and start posting!">
	</div>
</form>
';	
}

?>
<div class="messages">
<?php
if($chat_activated==1)
{
include('messages.php');

}
else {
	echo "Chat is currently offline";
	//include('messages.php');
}?>
</div>

</div>
<?php
include('ga.php');
?>

</body>
</html>