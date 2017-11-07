<?PHP
//error_reporting(1);

ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 1);

include_once (__DIR__ . '/classes/ProjectManager.php');

session_start();
require_once(__DIR__ . '/../uuid.php');

//check to see if we're logged in
if (!isset($_SESSION['userID'])) {
    die('You are not properly logged in and are unable to use this application');
} elseif ((!isset($_SESSION['appUUID'])) || ($_SESSION['appUUID'] != $app_UUID)) {
    die('Fatal Error! Application authentication is not correct');
}

if (!isset($_SESSION['projectManager'])) {
    $_SESSION['projectManager'] = serialize(new ProjectManager());
    header('location: index.php?error=2');
    exit;
}

$projectManager = \unserialize($_SESSION['projectManager']);
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=iso-8859-1" />
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js" type="text/javascript" ></script>
        <script src="../libs/common.js" type="text/javascript"></script>
        <script src="../libs/JQueryDriver.js?ver=1.0" type="text/javascript"></script>
        <script src="ajax/uberbundle.js" type="text/javascript"></script>

        <title>Edit Quote - LLS Quote Tool</title>
        <link href="../styles/main.css?ver=1.0" rel="stylesheet" type="text/css" />
        <link href="../styles/common.css?ver=1.0" rel="stylesheet" type="text/css" />
    </head>


    <body>
        <?PHP
            $projectManager->renderProjectInfo();
        ?>
        
    <table align="center">
        <tbody>
            
            <tr>
                <?PHP if (!$projectManager->isBallparker()): ?>
                <td align="center">
                    <form action="s.php" method="post" name="export">
                        <input type="hidden" name="attask" value="TRUE"/>
                        <input type="submit" name="submit" value="Export to Workfront and XML"/>
                    </form>
                </td>
                <?PHP endif; ?>
                <td align="center">
                    <form action="s.php" method="post" name="save">
                        <input type="hidden" name="attask" value="FALSE"/>
                        <input type="submit" name="submit" value="Save XML file for later use"/>
                    </form>
                </td>
            </tr>        
        </tbody>
    </table>

        <?PHP
        $projectManager->renderUnlockRateButton();
        $projectManager->renderBundleEfforts();
        $projectManager->renderLinguistFrames();
        $projectManager->renderOtherServicesFrame();
        $projectManager->renderAdditionalFeesAndDiscountFrame();
        $projectManager->renderSummaryPanel();
        ?>

        <table align="center">
            <tbody>
                <tr>
                    <td>
                        <form action="s.php" method="post" name="save">
                            <input type="hidden" name="attask" value="FALSE" />
                            <input type="submit" name="submit" value="Save XML file for later use" />
                        </form>
                    </td>
                </tr>
                <?PHP if (!$projectManager->isBallparker()): ?>
                <tr>
                    <td>
                        <form action="s.php" method="post" name="export">
                            <input type="hidden" name="attask" value="TRUE" />
                            <input type="submit" name="submit" value="Export to @task and XML" />
                        </form>
                    </td>
                </tr>
                <?PHP endif; ?>
                
                <?PHP
                    $callingPage = $projectManager->getCallingPage();
                    echo "\n<tr>\n";
                    echo "<td align=\"center\" >\n";
                    echo "<a href=\"" . $callingPage . "\"><button >Return to calling Page</button></a>\n";
                    echo "</td>\n";
                    echo "</tr>\n";
                ?>
            </tbody>
        </table>
    </body>
</html>