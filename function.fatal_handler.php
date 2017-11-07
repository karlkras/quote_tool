<?PHP
register_shutdown_function("fatal_handler");

ini_set("include_path", "c:/php/includes"); 
require_once("class.phpmailer.php");

//include_once("D:\Systems\web_includes\mailer.inc");

function fatal_handler() {
    
    $projectName = "";
    $userName = "";

    if(isset($_SESSION['projectObj'] )) {
        $projectObj = unserialize($_SESSION['projectObj']);
        $projectName = $projectObj->name;
        $userName = $userFirstName = $_SESSION['userFirstName'] . " " . $_SESSION['userLastName'];
    }
    $errfile = "unknown file";
    $errstr = "shutdown";
    $errno = E_CORE_ERROR;
    $errline = 0;

    $error = error_get_last();

    if (($error !== NULL) && ($error['type'] == '1')) {
        $errno = $error["type"];
        $errfile = $error["file"];
        $errline = $error["line"];
        $errstr = $error["message"];

        echo "<h2>A fatal error has occurred in project: " . $projectName . "</h2>\n\n";
        echo "<p>$errstr</p>\n<br>";
        echo "<p style=\"font-size:10px\"><strong>File: </strong>" . basename($errfile) . "<br><strong>Line: </strong>$errline</p>";

        $mail = new PHPMailer();
        $mail->IsSMTP();                                   // send via SMTP
        $mail->Host = 'SATURN.llts.com'; // SMTP servers
        $mail->SMTPAuth = false;     // turn on SMTP authentication
        $mail->Username = 'lingomailer';  // SMTP username
        $mail->Password = 'puoDqdmOybt7S'; // SMTP password
        $mail->WordWrap = 50;
        $mail->IsHTML(true);


        $mail->From = 'lingoweb@llts.com';
        $mail->FromName = 'Quote Tool Emailer';
        $mail->AddAddress('mvangrunsven@llts.com');
        //$mail->AddAddress('kkrasnowsky_x@llts.com');
		$mail->AddAddress('emanning@llts.com');
        $mail->Subject = "Notice: fatal error in QT in use by " . $userName;

        $mail->Body = "<h2>A fatal error has occurred in project " . $projectName . "</h2>\n\n";
        $mail->Body .= "<p>$errstr</p>\n<br>";
        $mail->Body .= "<p style=\"font-size:10px\"><strong>File: </strong>" . basename($errfile) . "<br><strong>Line: </strong>$errline</p>";
        $mail->AltBody = "A fatal error occurred in the quote tool.\n\n";
        $mail->AltBody .= "$errstr\n\n";
        $mail->AltBody .= "File: " . basename($errfile) . "\nLine: $errline\n\n";

        $result = $mail->Send();
        if ($result) {
            echo "<p>An email has been sent to support</p>";
        } else {
            echo "<p>Additionally, there was an error sending an email to support.<br /></p>";
        }
    }
}
