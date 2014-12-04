<?php
  require_once('recaptchalib.php');
  $privatekey = "6Lew7OASAAAAAL1XaSi9xTT4sg5de8FbqKCV8Cye"; //Your Key Here
  $resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

  if (!$resp->is_valid) {
    // What happens when the CAPTCHA was entered incorrectly
    die ("The reCAPTCHA wasn't entered correctly. Go back and try it again." .
         "(reCAPTCHA said: " . $resp->error . ")");
  } else {
    // Your code here to handle a successful verification
    
    //extract data from the post
    $message_text = $_POST["message-text"];
    $image_alt = $_POST["image-alt"];
    $image_full_upload = $_POST["image_full_upload"];
    
    //set POST variables
    $url = 'http://gingercomet.com/chat/'; //Your URL Here
    $fields = array(
    'message-text' => urlencode($message_text),
    'image-alt' => urlencode($image_alt),
    'image_full_upload' => urlencode($image_full_upload)
    );
    
    //url-ify the data for the POST
    foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
    rtrim($fields_string, '&');
    
    //open connection
    $ch = curl_init();
    
    //set the url, number of POST vars, POST data
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_POST, count($fields));
    curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
    
    //execute post
    $result = curl_exec($ch);
    
    //close connection
    curl_close($ch);
    
    
    
    
  }
  ?>
  