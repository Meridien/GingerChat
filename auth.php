<?php
$http_folder_path = "/chat/";
include('allowed_ips.php');
$ip = @$REMOTE_ADDR;
if(empty($ip)){$ip=$_SERVER['REMOTE_ADDR'];}
$secure=false;
for($i=0;$i<=count($ai);$i++){if($ip==$ai[$i]){$secure=true;}}
if($secure==true){}
else{
array_push($ai, $ip);
$new_allowed_list="<?php"."\n";
for($i=0; $i<=count($ai); $i++){$new_allowed_list.='$ai['.$i.']="'.$ai[$i].'";'."\n";}
$new_allowed_list.="?>";
$filename = "allowed_ips.php";
$fh = fopen($filename, 'w');
fwrite($fh, $new_allowed_list);
fclose($fh);
}
header('Location: '.$http_folder_path);
?>