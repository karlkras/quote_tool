<?PHP
session_start();
require_once('../uuid.php');

//check to see if we're logged in
if (!isset($_SESSION['userID'])) {
    die('You are not properly logged in and are unable to use this application');
} elseif ((!isset($_SESSION['appUUID'])) || ($_SESSION['appUUID'] != $app_UUID)) {
    die('Fatal Error! Application authentication is not correct');
}

require_once('saveXML.php');

$taskNameArray = unserialize($_SESSION['taskNameArray']);
$rolledNameArray = unserialize($_SESSION['rolledNameArray']);
if (!isset($_SESSION['projectManager'])) {
    header('location: index.php?error=2');
    exit;
}


if (isset($_POST['rename']) && ($_POST['rename'] == 'Rename')) {

    foreach ($taskNameArray['name'] as $inx => $value) {
        if ($value != $_POST[$inx]) {
            $taskNameArray['name'][$inx] = $_POST[$inx];
        }
    }

    foreach ($rolledNameArray as $indx => $val) {
        $i = str_replace(" ", "_", $indx);
        if ($val != $_POST[$i]) {
            $rolledNameArray[$indx] = $_POST[$i];
        }
    }

    $_SESSION['taskNameArray'] = serialize($taskNameArray);
    $_SESSION['rolledNameArray'] = serialize($rolledNameArray);
}

//include(__DIR__ . '/emails.php');


save_to_xml($taskNameArray, $rolledNameArray, $_SESSION['updateAtTask'], unserialize($_SESSION['projectManager']));

