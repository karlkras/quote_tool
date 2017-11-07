<?PHP
ob_start();
session_start();
include_once("..\definitions.php");
include_once("attask_functions.php");
require_once('../attaskconn/LingoAtTaskService.php');

require_once('uuid.php');

if (!isset($_SESSION['userID'])) {
    die('Fatal Error! You are not logged in correctly.');
} elseif ((!isset($_SESSION['appUUID'])) || ($_SESSION['appUUID'] != $app_UUID)) {
    die('Fatal Error! Application authentication is not correct');
}

//check for admin, if not send them to the 'view' portal
if ((!isset($_SESSION['isAdmin'])) || (!$_SESSION['isAdmin'])) {
    header('location:./index.php');
    exit;
}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Generating Backup file</title>


        <script src="csspopup.js"></script>
        <script src="taskfunctions.js"></script>

        <link href="admin.css" rel="stylesheet" type="text/css" />

    </head>

    <body>
        <p><a href="index.php" class="breadcrumbs">Index</a> &gt; <a href="customPricing.php" class="breadcrumbs">Custom Sell Pricing</a></p><br />
        <?PHP
        $filenames = array();

        $myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
        if ($myDBConn->connect_errno) {
            echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
            exit;
        }

        $query = "SHOW TABLES FROM " . DBName;
        $result = $myDBConn->query($query);


        while ($row = $result->fetch_row()) {

            $clientName = $row[0];
            if ((substr($clientName, 0, 7) == 'client_') && ($clientName != "client_template")) {
                $fileName = $clientName . ".csv";
                $fh = fopen($fileName, 'w') or die("can't open file");

                $query = "SELECT * FROM " . $clientName;
                $clientTableResult = $myDBConn->query($query);
                while ($ct_res = $clientTableResult->fetch_assoc()) {
                    $taskName = '';
                    $wordType = '';
                    $sourceLang = '';
                    $targetLang = '';


                    $dbTask = $ct_res['task_name'];
                    $pieces = explode("=", $dbTask);

                    if (count($pieces) > 1) { //then we have language pairs
                        $sourceLang = str_replace("_", " ", $pieces[1]);
                        $targetLang = str_replace("_", " ", $pieces[2]);
                    }

                    $tempName = $pieces[0];  //now explode this to check for word type
                    $pieces = explode("#", $tempName);

                    $taskName = str_replace("_", " ", $pieces[0]);
                    if (count($pieces) > 1) {
                        $wordType = str_replace("_", " ", $pieces[1]);
                    }

                    $outputString = $taskName . "," . $wordType . "," . $sourceLang . "," . $targetLang . "," . $ct_res['rate'] / 1000 . "," . $ct_res['units'] . "\n";
                    fwrite($fh, $outputString);
                }
                $clientTableResult->free();
                fclose($fh);

                if (file_exists($fileName)) {
                    $filenames[] = $fileName;
                }
            }
        }
        $result->free();

        if (count($filenames) > 0) {
            $zip = new ZipArchive();
            $zipFile = '';

            $dateStr = date('M_d_Y');

            //create the zip file
            if ($zip->open('backup_' . $dateStr . '.zip', ZipArchive::CREATE) === TRUE) {
                $zipFile = 'backup_' . $dateStr . '.zip';
            }

            //add files to the zip
            foreach ($filenames as $fileName) {

                if (!$zip->addFile($fileName, basename($fileName))) {
                    die("Could not add file " . basename($fileName) . ".");
                }
            }
            $zip->close();

            //delete the temporary files
            foreach ($filenames as $fileName) {
                unlink($fileName);
            }

            //send the zip file via browser
            if (file_exists($zipFile)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=' . basename($zipFile));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($zipFile));
                ob_clean();
                flush();
                readfile($zipFile);

                //clean up the  file from the server
                unlink($zipFile);
                exit;
            } else {
                echo "Could not create zip file.<br>";
            }
        } else {
            echo "No table files were created.<br>";
        }

        $myDBConn->close();
        ?>



    </body>
</html>
