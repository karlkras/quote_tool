<?PHP
session_start();
require_once('../uuid.php');
require_once(__DIR__ . "/functions/f_updateTaskService.php");

//check to see if we're logged in
if (!isset($_SESSION['userID'])) {
    die('You are not properly logged in and are unable to use this application');
} elseif ((!isset($_SESSION['appUUID'])) || ($_SESSION['appUUID'] != $app_UUID)) {
    die('Fatal Error! Application authentication is not correct');
}

require_once('saveXML.php');


if (isset($_POST['attask']) && ($_POST['attask'] == 'TRUE'))
    $_SESSION['updateAtTask'] = true;
else
    $_SESSION['updateAtTask'] = false;

$taskService = null;
$returnPageEntry = "<p><a href='index.php'>Return to Advanced Quote Tool</a></p>";

if (isset($_SESSION['projectManager'])) {
    $pMan = unserialize($_SESSION['projectManager']);
    update_taskService($pMan);
    $taskService = \unserialize($_SESSION['taskService']);
    if ($pMan->isBallparker()) {
        $returnPageEntry = "<p><a href='../estimate_1.php'>Return to the BallParker Quote Tool</a></p>";
    }
}


//create a list of "requested services" for editing
$taskNameArray = requestedServices($taskService, $pMan->getPackageEngineering(), $pMan->getPackageAllInternal());
$rolledNameArray = array('Linguistic Work' => 'Linguistic Work', 'Formatting' => 'Formatting', 'Engineering' => 'Engineering', 'Quality Assurance' => 'Quality Assurance', 'Other Services' => 'Other Services');

$_SESSION['taskNameArray'] = serialize($taskNameArray);
$_SESSION['rolledNameArray'] = serialize($rolledNameArray);
?>
<html>
    <head>

        <script language="javascript">
            function finishIt()
            {
                document.getElementById("AskIt").style.display = 'none';
                document.getElementById("mainDiv").style.display = 'none';
                document.getElementById("renameDiv").style.display = '';
            }
            function displayRename(change)
            {

                if (change != true)
                {
                    //redirect to the save to xml function; 
                    document.getElementById("AskIt").style.display = 'none';
                    document.getElementById("mainDiv").style.display = 'none';
                    document.getElementById("renameDiv").style.display = '';
                    window.open('rename.php', 'xml output');
                }
                else
                {
                    document.getElementById("AskIt").style.display = 'none';
                    document.getElementById("renameDiv").style.display = 'none';
                    document.getElementById("mainDiv").style.display = '';
                }
            }
        </script>


        <title>Rename tasks</title>
        <link href="../styles/main.css?ver=1.0" rel="stylesheet" type="text/css" />
        <link href="../styles/common.css?ver=1.0" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <div id="AskIt">
            <table border="1" align="center">
                <tr>
                    <th align="center" colspan="2">Do you wish to rename the default task names?</th>
                </tr>
                <tr>
                    <td align="center" >
                        <input type="button" name="RenameYes" value="Yes" onClick="displayRename(true);">
                    </td>
                    <td align="center" >
                        <input type="button" name="RenameNo" value="No" onClick="displayRename(false);">
                    </td>
                </tr>
            </table>
        </div>
        <div id="mainDiv" style="display:none">
            <form name="renameTasks" target="_blank" action="rename.php" method="post">
                <table align="center" border="1">
                    <tr>
                        <th colspan="2">Rename Individual Tasks</th>
                    </tr>
                    <tr>
                        <th align="center" width="50%">Current name</th>
                        <th align="center">New name</th>
                    </tr>
                    <?PHP
                    foreach ($taskNameArray['name'] as $index => $name) {
                        echo "<tr><td>" . $name . "</td><td><input type=\"text\" name=\"" . $index . "\" value=\"" . $name . "\" size=\"50\"></td></tr>";
                    }
                    ?>
                </table>
                <hr width="785px">
                <table align="center" border="1">
                    <tr>
                        <th colspan="2">Rename Bundled/Rolled-up Tasks</th>
                    </tr>
                    <tr>
                        <th align="center" width="50%">Current name</th>
                        <th align="center">New name</th>
                    </tr>
                    <?PHP
                    foreach ($rolledNameArray as $index => $name) {
                        echo "<tr><td>" . $name . "</td><td><input type=\"text\" name=\"" . $index . "\" value=\"" . $name . "\" size=\"50\"></td></tr>";
                    }
                    ?>
                </table>
                <div align="center">
                    <input type="submit" name="rename" value="Rename" onClick="return finishIt();">
                </div>


            </form>


        </div>
        <div id="renameDiv" style="display:none">
            <p>Exporting XML...</p>
            <?PHP echo $returnPageEntry ?>
            <p><a href="../index.php">Return to Quote Tool Suite main page</a></p>
        </div>

    </body>
</html>