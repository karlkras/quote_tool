<?PHP
session_start();
require_once('../uuid.php');

//check to see if we're logged in
if (!isset($_SESSION['userID'])) {
    die('You are not properly logged in and are unable to use this application');
} elseif ((!isset($_SESSION['appUUID'])) || ($_SESSION['appUUID'] != $app_UUID)) {
    die('Fatal Error! Application authentication is not correct');
}


if ((isset($_POST['upload'])) && ($_POST['upload'] == "Upload File")) {
    $output = "<p><strong>Processing file:</strong> " . $_FILES['uploadedfile']['name'] . "</p>\n";
    if ($_FILES['uploadedfile']['error'] > 0) {
        switch ($_FILES['uploadedfile']['error']) {
            case UPLOAD_ERR_INI_SIZE:
                $output .= "The uploaded file exceeds the maximum size for PHP<br />\n";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $output .= "The uploaded file exceeds the maximum size that was specified in the HTML form<br />\n";
                break;
            case UPLOAD_ERR_PARTIAL:
                $output .= "The uploaded file was only partially uploaded<br />\n";
                break;
            case UPLOAD_ERR_NO_FILE:
                $output .= "No file was uploaded<br />\n";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $output .= "Missing a temporary folder<br />\n";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $output .= "Failed to write file to disk<br />\n";
                break;
            case UPLOAD_ERR_EXTENSION:
                $output .= "File upload stopped by extension<br />\n";
                break;
            default:
                $output .= "Unknown upload error<br />\n";
                break;
        }
    } else if ($_FILES['uploadedfile']['type'] != 'text/xml') {
        $output .= "Error: File is not XML file <br />\n";
    } else if ($_FILES['uploadedfile']['size'] > 5242880) {
        $output .= "Error: File is too large. 5 MB maximum<br />\n";
    } else {

        $xmlDoc = new DOMDocument();
        if ($xmlDoc->load($_FILES['uploadedfile']['tmp_name'])) {
            //check if it's a valid file
            if ($xmlDoc->documentElement->nodeName != 'form1') {

                $output .= "<p><strong>Error:</strong> Invalid XML document</p>\n";
                $output .= "<p><strong>Expected root element:</strong> &lt;form1&gt;. <strong>Found:</strong> &lt;" . $xmlDoc->documentElement->nodeName . "&gt;<br>\n";
            } else {
                $dataDump = $xmlDoc->getElementsByTagName("datadump")->item(0);
                $_SESSION['projectObj'] = str_replace("{[{NULL}]}", "\0", $dataDump->getElementsByTagName("projectobj")->item(0)->textContent);
                $_SESSION['projectData'] = str_replace("{[{NULL}]}", "\0", $dataDump->getElementsByTagName("projectdata")->item(0)->textContent);
                //$_SESSION['langCount'] = str_replace("{[{NULL}]}", "\0", $dataDump->getElementsByTagName("langcount")->item(0)->textContent);
                $_SESSION['taskService'] = str_replace("{[{NULL}]}", "\0", $dataDump->getElementsByTagName("taskservice")->item(0)->textContent);
                $_SESSION['projectManager'] = str_replace("{[{NULL}]}", "\0", $dataDump->getElementsByTagName("projectManager")->item(0)->textContent);

                header("Location: editQuote.php");
                /* echo "<pre>";
                  var_dump(unserialize($_SESSION['taskService']));
                  echo "</pre>"; */
            }
        } else {
            $output .= "Error: Could not parse XML document<br />\n";
        }
    }

    $output .= "<form enctype=\"multipart/form-data\" action=\"reload.php\" method=\"POST\">\n";
    $output .= "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"5242880\" />\n";
    $output .= "Choose a file to upload: <input name=\"uploadedfile\" type=\"file\" /><br />\n";
    $output .= "<input type=\"submit\" name=\"upload\" value=\"Upload File\" />\n";
    $output .= "</form>\n";
} else {
    $output = "<form enctype=\"multipart/form-data\" action=\"reload.php\" method=\"POST\">\n";
    $output .= "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"5242880\" />\n";
    $output .= "Choose a file to upload: <input name=\"uploadedfile\" type=\"file\" /><br />\n";
    $output .= "<input type=\"submit\" name=\"upload\" value=\"Upload File\" />\n";
    $output .= "</form>\n";
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Upload saved file</title>
    </head>

    <body>
<?PHP print $output; ?>

    </body>
</html>