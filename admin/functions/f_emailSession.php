<?php
//ini_set("include_path", "c:/php/includes");
//require("class.phpmailer.php");

function emailSession($sessionInfo){
    
    $sessionData = unserialize($sessionInfo);
    echo "<h2>A fatal error has occurred</h2>\n\n";
    echo "<p>You are not logged in correctly</p>\n<br>";
    
    $mail = new PHPMailer();
    $mail->IsSMTP();                 // send via SMTP
    $mail->Host = 'SATURN.llts.com'; // SMTP servers
    $mail->SMTPAuth = false;     // turn on SMTP authentication
    $mail->Username = 'lingomailer';  // SMTP username
    $mail->Password = 'puoDqdmOybt7S'; // SMTP password
    $mail->WordWrap = 50;
    $mail->IsHTML(true);
    $mail->From = 'lingoweb@llts.com';
    $mail->FromName = 'Quote Tool Emailer';
    $mail->AddAddress('mvangrunsven@llts.com');
    $mail->Subject = "Error in QT Admin tool: customPricing";
    
    $mail->Body = "<p>Session variables were not set when trying to access customPricing.php</h2>\n\n";
    if (isset($_SERVER['HTTP_REFERER'])){
        $mail->Body .= "<p><strong>Calling page: </strong>" . $_SERVER['HTTP_REFERER'] . "</p>";
    }
    if (isset($_SERVER['QUERY_STRING'])){
        $mail->Body .= "<p><strong>Query: </strong>" . $_SERVER['QUERY_STRING'] . "</p>";
    }
    $mail->Body .= "<p><strong>Vardump: </strong>";
    ob_start();
    var_dump($sessionData);
    $vdump = ob_get_clean();
    $mail->Body .= str_replace('<pre class="xdebug-var-dump" dir="ltr">', '<pre>', $vdump) . "</p>";
        
    $mail->Send();
}