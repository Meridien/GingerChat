
<?php
$filename = "chat-online.php";
$fh = fopen($filename, 'w');
fwrite($fh, '<?php $chat_activated=1; ?>');
fclose($fh);
?>
		