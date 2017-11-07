<?PHP
ob_start();
session_start();
require_once('../function.fatal_handler.php');
require_once('/functions/f_emailSession.php');
require_once(__DIR__ . '/uuid.php');

if (!isset($_SESSION['userID'])) {
    emailSession(serialize($_SESSION));
    exit;
    //die('Fatal Error! You are not logged in correctly.');
} elseif ((!isset($_SESSION['appUUID'])) || ($_SESSION['appUUID'] != $app_UUID)) {
    die('Fatal Error! Application authentication is not correct');
}

require_once('../attaskconn/LingoAtTaskService.php');
include_once("../definitions.php");

include_once('functions/f_displayAll.php');
include_once('functions/f_exportClient.php');
include_once('functions/f_getClientFromId.php');
include_once('functions/f_getClientsFromAtTask.php');
include_once('functions/f_parseTasks.php');
include_once('functions/f_processCSV.php');
include_once('functions/f_removeUsedClients.php');
include_once('functions/f_viewClient.php');

include_once('classes/c_languageTask.php');

ini_set("soap.wsdl_cache_enabled", "0");

//get the list of languages from @task
try {
    $api = new LingoAtTaskService();
    $g = new getLanguageService();
    $result = $api->getLanguageService($g);
    $sourceLangs = $result->return->sourceLanguages;
    sort($sourceLangs, SORT_STRING);

    $targetLangs = $result->return->targetLanguages;
    sort($targetLangs, SORT_STRING);
} catch (Exception $e) {
    echo "Could not retireve the language lists from @task<br>\n";
    echo "<pre>" . var_dump($e) . "</pre>";
    exit;
}

//get the task list
try {
    $g = new getLibraryTaskService();
    $result = $api->getLibraryTaskService($g);

    $billableTasks = $result->return->billableTasks;
    $linguisticTasks = $result->return->lingTasks;
} catch (Exception $e) {
    echo "couldn't get library tasks<br>\n";
    echo "<pre>", var_dump($e), "</pre>";
    exit;
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
        <meta http-equiv="Pragma" content="no-cache" />
        <meta http-equiv="Expires" content="0" />
        <title>Translation and Localization Services for the Multicultural Economy | Lingo Systems</title>
        <script src="csspopup.js"></script>
        <script src="taskfunctions.js"></script>
        <script src="ajax/getXMLHTTPRequest.js"></script>
        <script src="ajax/editNotes.js"></script>
        <script src="ajax/saveNotes.js"></script>
        <script src="ajax/editFileNotes.js"></script>
        <script src="ajax/saveFileNotes.js"></script>
        <script src="ajax/deleteFile.js"></script>

        <script language="javascript">
            function checkSub(typeValue)
            {
                if (typeValue == 'word')
                {
                    document.getElementById('subcat').disabled = false;
                }
                else
                {
                    document.getElementById('subcat').disabled = true;
                }

            }


            function checkSub2(typeValue)
            {
                if (typeValue == 'word')
                {
                    document.getElementById('subcat2').disabled = false;
                }
                else
                {
                    document.getElementById('subcat2').disabled = true;
                }

            }
        </script>

        <link href="admin.css" rel="stylesheet" type="text/css" />
    </head>

    <body>
        <img src="../images/languageline-logo.png" alt="Lingo Systems" width="223" height="62" class="logoimg"/>
        <p  class="breadcrumbs">
            <a href="index.php">Index</a> &gt; <a href="customPricing.php">Custom Sell Pricing</a><?PHP
if (isset($_GET['action']) && ($_GET['action'] == 'addtable')) {
    echo " &gt; Add Pricing Scheme";
} elseif (isset($_GET['action']) && ($_GET['action'] == 'view')) {
    echo " &gt; View Pricing Scheme";
} elseif (isset($_GET['action']) && ($_GET['action'] == 'edit')) {
    echo " &gt; Edit Pricing Scheme";
} elseif (isset($_GET['action']) && ($_GET['action'] == 'import')) {
    echo " &gt; Import Pricing Scheme";
}
?></p><br />

            <?PHP
            if (isset($_GET['action']) && ($_GET['action'] == 'edit')) {
                //first things first, check for admin, if not send them to the 'view' portal
                if ((!isset($_SESSION['isAdmin'])) || (!$_SESSION['isAdmin'])) {
                    header('location:./customPricing.php');
                    exit;
                }

                $defaultTasks = array();
                $languageTasks = array();


                $clientName = $_GET['target'];
                $tableName = 'client_' . $clientName;

                echo "<h1>", str_replace("_", " ", $clientName), " Custom Pricing</h1>";

                echo "<h2 style=\"margin-bottom:1px;padding-bottom:1px;\">Client Specific Information</h2>";
                $myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
                if ($myDBConn->connect_errno) {
                    echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
                    exit;
                }

                //check for discount
                $query = "SELECT discount, comments FROM clients WHERE table_name=\"" . $tableName . "\"";
                $result = $myDBConn->query($query) or die($myDBConn->error);

                if ($result->num_rows == 0) {
                    $discountString = '<td colspan="2" class="rightAligned admin">No client discount has been established</td>';
                    $clientDiscount = -1;
                } else {
                    $res = $result->fetch_assoc();
                    $clientDiscount = $res['discount'];
                    if ($clientDiscount == null){
                        $discountString = '<td colspan="2" class="rightAligned admin">No client discount has been established</td>';
                        $clientDiscount = -1;
                    }else{
                        $discountString = '<td class="rightAligned admin">' . $clientDiscount . '</td><td class="admin">percent</td>';
                        $discountString .= '<td><a href="editDiscount.php?target=' . $_GET['target'] . '">';
                        $discountString .= '<img src="images/Edit-icon.png" alt="Edit" title="Edit" border="0">';
                        $discountString .= '</a></td>';
                        $discountString .= '<td><a href="deleteDiscount.php?target=' . $_GET['target'] . '">';
                        $discountString .= '<img src="images/delete-icon.png" alt="Delete" title="Delete" border="0"></a></td>';
                    }
                    
                    echo '<table border=0 id="client-discount" class="inlineTable admin" style="min-width:200px">';
                    echo '<tr><th class="admin" style="padding-right:15px;" colspan="2"><b>Discount</b></th><th colspan="2" class="admin">&nbsp;</th></tr>';
                    echo '<tr>' . $discountString;
                    echo '</tr>';
                    echo '</table>';

                    //get any comments
                    if (is_null($res['comments'])) {
                        $clientNotes = 'No notes';
                        $noteID = -1;
                        $commentName = '&lt;none&gt;';
                        $commentDate = '&lt;unknown&gt;';
                    } else {
                        $result->free();
                        $noteID = $res['comments'];
                        $query = "SELECT updateDate, username, userNotes FROM comments WHERE id=$noteID";
                        $result = $myDBConn->query($query) or die($myDBConn->error);
                        if ($res = $result->fetch_assoc()) {
                            if (is_null($res['userNotes'])) {
                                $clientNotes = 'No notes';
                                $commentName = '&lt;none&gt;';
                                $commentDate = '&lt;unknown&gt;';
                            } else {
                                $clientNotes = $res['userNotes'];
                                is_null($res['updateDate']) ? $commentDate = '&lt;unknown&gt;' : $commentDate = $res['updateDate'];
                                if (is_null($res['username'])) { // if there's a userid then we need to get their name from attask for display
                                    $commentName = '&lt;none&gt;';
                                } else {
                                    //get the user's first and last name from attask
                                    $attaskUserName = $res['username'];

                                    set_time_limit(60);
                                    try {
                                        $api = new LingoAtTaskService();
                                        $g = new getUserByUsername;
                                        $g->accountName = $attaskUserName;
                                        $user = $api->getUserByUsername($g)->return;

                                        $commentName = $user->firstName . " " . $user->lastName;
                                    } catch (exception $e) {
                                        $commentName = '&lt;error&gt;';
                                    }
                                }
                            }
                            $result->free();
                        } else {
                            $clientNotes = 'No notes';
                            $commentName = '&lt;none&gt;';
                            $commentDate = '&lt;unknown&gt;';
                        }
                    }
                }


                echo "<table border=0 id=\"client-notes\" class=\"inlineTable admin\" style=\"min-width:400px\">\n";
                echo "\t<tr><th class=\"admin\" style=\"padding-right:15px;\" ><b>Notes</b></th></tr>\n";
                echo "\t<tr class=\"nohover\"><td class=\"admin note\" id=\"commentCell\" style=\"width:375px\">$clientNotes</td></tr>\n";
                echo "\t<tr class=\"nohover\"><td class=\"admin breadcrumbs\" id=\"commentDetails\">Comment by: $commentName on $commentDate</td></tr>\n";
                echo "\t<tr class=\"nohover\"><td class=\"admin\" style=\"text-align:center\" id=\"commentAction\">";
                echo "<a href=\"#\" onClick=\"editNotes($noteID,'$tableName')\" class=\"breadcrumbs\">Edit</a>";
                echo "</td></tr>\n";
                echo "</table>\n";


                //check for attached files.
                $fileName = '';
                $filePath = '';
                $fileDate = '';
                $fileUser = '';
                $fileComment = '';

                //first get the client attask id
                $query = "SELECT attask_id FROM clients WHERE table_name=\"" . $tableName . "\"";
                $result = $myDBConn->query($query) or die($myDBConn->error);

                if ($res = $result->fetch_assoc()) {
                    $clientAttaskId = $res['attask_id'];
                    $result->free();
                    $query = "SELECT ID, filePath, fileComments, uploadedBy, uploadDate FROM clientfiles WHERE attaskID=$clientAttaskId";
                    if ($result = $myDBConn->query($query)) {
                        if ($result->num_rows == 0) {
                            echo "<table border=0 id=\"client_files\" class=\"inlineTable admin\" style=\"min-width:550px\">\n";
                            echo "\t<tr><th class=\"admin\" colspan=3>Attached files</th></tr>\n";
                            echo "\t<tr class=\"nohover\"><td class=\"admin\">No attached files found</td></tr>\n";
                            echo "\t<tr class=\"nohover\">";
                            echo "<td class=\"admin centerAligned breadcrumbs\" colspan=3>";
                            echo "<a href=\"addFile.php?target=" . $_GET['target'] . "\">Add File</a>";
                            echo "</td></tr>\n";
                            echo "</table>\n\n";
                        } else {
                            echo "<table border=0 id=\"client_files\" class=\"inlineTable admin\" style=\"min-width:550px\">\n";
                            echo "\t<tr><th class=\"admin\" colspan=2>Attached files</th></tr>\n";
                            while ($res = $result->fetch_assoc()) {
                                $path_parts = pathinfo($res['filePath']);
                                $fileName = $path_parts['basename'];
                                $filePath = $path_parts['dirname'];
                                echo "\t<tr class=\"nohover\">\n";
                                echo "\t\t<td class=\"admin\">\n";
                                echo "\t\t\t<a href=\"." . $res['filePath'] . "\" target=\"_new\"><strong>" . $fileName . "</strong></a>\n";
                                echo "\t\t</td>\n";
                                echo "\t\t<td class=\"admin breadcrumbs\">\n";
                                echo "\t\t\t<a href=\"." . $res['filePath'] . "\" target=\"_new\">Download</a>\n";
                                echo "\t\t</td>\n";
                                echo "\t</tr>\n";
                                echo "\t<tr class=\"nohover\">\n";
                                echo "\t\t<td class=\"admin breadcrumbs\">\n";
                                echo "\t\t\t<em><span id=\"fileDetails-" . $res['ID'] . "\">Uploaded by " . $res['uploadedBy'] . " on " . $res['uploadDate'] . "</span></em>\n";
                                echo "\t\t</td>\n";
                                echo "\t\t<td class=\"admin breadcrumbs\">\n";
                                echo "\t\t\t<a href=\"#\" onclick=\"deleteFile(" . $res['ID'] . ",'$fileName','$tableName')\">Remove</a></td>\n";
                                echo "\t</tr>\n";
                                echo "\t<tr class=\"nohover last\">\n";
                                echo "\t\t<td class=\"admin last\" id=\"fileComment-" . $res['ID'] . "\">" . $res['fileComments'] . "</td>\n";
                                echo "\t\t<td class=\"admin breadcrumbs last\" id=\"fileCommentAction-" . $res['ID'] . "\">\n";
                                echo "\t\t\t<a href=\"#\" onClick=\"editFileNotes(" . $res['ID'] . ",'$tableName')\">Edit</a>\n";
                                echo "\t\t</td>\n";
                                echo "\t</tr>\n";
                            }
                            $result->free();
                            echo "\t<tr class=\"nohover\">";
                            echo "<td class=\"admin centerAligned breadcrumbs\" colspan=3>";
                            echo "<a href=\"addFile.php?target=" . $_GET['target'] . "\">Add File</a>";
                            echo "</td></tr>\n";
                            echo "</table>\n\n";
                        }
                    }
                }


                echo "<hr><H2 style=\"margin-bottom:1px;padding-bottom:1px\">Non-Language Tasks</H2>\n";
                echo "<p style=\"margin-top:0; margin-bottom:0\"><a href=\"#\" onclick=\"return popup('popUpDiv');\" class=\"breadcrumbs\">Add non-language task</a></p>";


                //get Non-language task info
                $query = "SELECT * FROM " . $tableName;
                $result = $myDBConn->query($query) or die($myDBConn->error);

                while ($row = $result->fetch_assoc()) {
                    $task = $row['task_name'];
                    $rate = $row['rate'] / 1000;
                    $units = $row['units'];



                    //parse out the task name to determine if there is a language pair attached
                    $first = strpos($task, '=');
                    if ($first === false) { //there is no language pair, so this is a default value
                        $tempTask = new default_task;
                        //strip out sub category if available
                        $subcat_start = strpos($task, "#");
                        if ($subcat_start === false) {
                            $tempTask->set_name(str_replace("_", " ", $task));
                        } else {
                            $subcat_stop = strpos($task, "#", $subcat_start + 1);
                            if ($subcat_stop === false)
                                $subcat_stop = strlen($task) + 2;
                            $tempTask->set_name(str_replace("_", " ", substr($task, 0, $subcat_start)));

                            $tempTask->set_subCategory(str_replace("_", " ", substr($task, $subcat_start + 1, $subcat_stop - $subcat_start - 1)));
                        }

                        $tempTask->set_rate($rate);
                        $tempTask->set_unit($units);

                        $defaultTasks[] = $tempTask;
                    }
                    else {
                        $taskName = substr($task, 0, $first);
                        $second = strpos($task, '=', $first + 1);
                        if ($second === false) { //there is no second language, so treat it as an error
                            $sourceLang = substr($task, $first + 1);
                            $targetLang = NULL;

                            $tempTask = new default_task;
                            $subcat_start = strpos($task, "#");
                            if ($subcat_start === false) {
                                $tempTask->set_name(str_replace("_", " ", $task));
                            } else {
                                $subcat_stop = strpos($task, "#", $subcat_start + 1);
                                if ($subcat_stop === false)
                                    $subcat_stop = strlen($task) + 2;
                                $tempTask->set_name(str_replace("_", " ", substr($task, 0, $subcat_start)));

                                $tempTask->set_subCategory(str_replace("_", " ", substr($task, $subcat_start + 1, $subcat_stop - $subcat_start - 1)));
                            }
                            $tempTask->set_rate($rate);
                            $tempTask->set_unit($units);

                            $defaultTasks[] = $tempTask;
                        }
                        else {
                            $sourceLang = substr($task, $first + 1, $second - $first - 1);
                            $targetLang = substr($task, $second + 1);

                            $tempTask = new language_task;
                            $subcat_start = strpos($task, "#");
                            if ($subcat_start === false) {
                                $tempTask->set_name(str_replace("_", " ", substr($task, 0, $first)));
                            } else {
                                $subcat_stop = strpos($task, "#", $subcat_start + 1);
                                if ($subcat_stop === false)
                                    $subcat_stop = strlen($task) + 2;
                                $tempTask->set_name(str_replace("_", " ", substr($task, 0, $subcat_start)));

                                $tempTask->set_subCategory(str_replace("_", " ", substr($task, $subcat_start + 1, $subcat_stop - $subcat_start - 1)));
                            }
                            $tempTask->set_rate($rate);
                            $tempTask->set_unit($units);
                            $tempTask->set_source($sourceLang);
                            $tempTask->set_target($targetLang);

                            $languageTasks[$sourceLang][$targetLang][] = $tempTask;
                        }
                    }
                }
                $result->free();

                if (count($defaultTasks) > 0) {
                    echo "<table border=0 id=\"non-language-tasks\" class=\"inlineTable admin\">\n";
                    echo "\t<tr><th class=\"admin\"><b>Task</b></th><th class=\"admin\"><b>Rate</b></th><th class=\"admin\"><b>Per</b></th><th class=\"admin\" colspan=\"2\">&nbsp;</th></tr>\n";

                    foreach ($defaultTasks as $t) {
                        echo "\t<tr><td class=\"admin\">", $t->get_name();
                        if ($t->get_subCategory() != "none") {
                            echo " ", $t->get_subCategory();
                        }
                        echo "</td><td class=\"admin\">", number_format($t->get_rate(), 3), "</td><td class=\"admin\">", $t->get_unit(), "</td>";
                        $temp_client = str_replace(" ", "_", $clientName);
                        $temp_client = urlencode($temp_client);
                        $temp_name = str_replace(" ", "_", $t->get_name());
                        $temp_name = urlencode($temp_name);
                        $temp_sub = str_replace(" ", "_", $t->get_subCategory());
                        $temp_sub = urlencode($temp_sub);

                        echo "<td><a href=\"edit-nonlanguage.php?client=", $temp_client, "&name=", $temp_name, "&sub=", $temp_sub, "&rate=", $t->get_rate(), "&unit=", $t->get_unit(), "\" >";
                        echo "<img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a></td>";
                        echo "<td><a href=\"#\" onClick=\"return deleteNonLanguage( this.parentNode.parentNode, '", $temp_client, "', '", $temp_name, "', '", $temp_sub, "');\"><img src=\"images/delete-icon.png\" alt=\"Delete\" border=0 title=\"Delete\"/></a></td></tr>\n";
                    }
                    echo "</table>\n";
                    echo "<p><a href=\"#\" onclick=\"return popup('popUpDiv');\" class=\"breadcrumbs\">Add non-language task</a></p>\n\n";
                } else {
                    echo "<p>No non-language tasks found</p>\n";
                }

                echo "<hr><H2 style=\"margin-bottom:1px;padding-bottom:1px\">Language Tasks</H2>";
                echo "<a href=\"#\" onclick=\"return popup('addLangTask');\" class=\"breadcrumbs\">Add Language Based task</a><br><br>";

                if (count($languageTasks) < 1) {
                    echo "<p style=\"margin-top:0\">No language tasks found</p>\n";
                }

                foreach ($languageTasks as $key1 => $value1) {

                    foreach ($value1 as $key2 => $value2) {
                        echo "<table border=0 class=\"inlineTable admin\" id=\"" . str_replace(" ", "_", $key1), "_", str_replace(" ", "_", $key2) . "\"><tr><td colspan=5 class=\"language admin\">", str_replace("_", " ", $key1), " to ", str_replace("_", " ", $key2), "</td></tr>";
                        echo "<tr><th class=\"admin\"><b>Task</b></th><th class=\"admin\"><b>Rate</b></th><th class=\"admin\"><b>Per</b></th><th class=\"admin\" colspan=\"2\">&nbsp;</th></tr>";
                        foreach ($value2 as $value3) {
                            echo "<tr><td class=\"admin\">", $value3->get_name();
                            if ($value3->get_subCategory() != "none") {
                                echo " ", $value3->get_subCategory();
                            }

                            $temp_client = str_replace(" ", "_", $clientName);
                            $temp_client = urlencode($temp_client);
                            $temp_name = str_replace(" ", "_", $value3->get_name());
                            $temp_name = str_replace("+", "%2B", $temp_name);
                            $temp_name = urlencode($temp_name);
                            $temp_sub = str_replace(" ", "_", $value3->get_subCategory());
                            $temp_sub = urlencode($temp_sub);
                            $temp_src = urlencode($key1);
                            $temp_tgt = urlencode($key2);

                            echo "</td><td align=right class=\"admin\">", number_format($value3->get_rate(), 3), "</td><td class=\"admin\">", $value3->get_unit(), "</td>";
                            echo "<td>";
                            echo "<a href=\"edit-language.php?c=", $temp_client, "&n=", $temp_name, "&s=", $temp_sub, "&r=", $value3->get_rate(), "&u=", $value3->get_unit(), "&src=", $temp_src, "&tgt=", $temp_tgt, "\" >";
                            echo "<img src=\"images/Edit-icon.png\" alt=\"Edit\" border=0 title=\"Edit\" /></a></td>";
                            echo "<td><a href=\"#\" onClick=\"return deleteLanguageTask( this.parentNode.parentNode, '", $temp_client, "', '", $temp_name, "', '", $temp_sub;
                            echo "', '", $temp_src, "', '", $temp_tgt, "');\">";
                            echo "<img src=\"images/delete-icon.png\" alt=\"Delete\" border=0 title=\"Delete\"/></a></td></tr>";
                        }

                        echo "</table>&nbsp;";
                    }
                }

                $myDBConn->close();

                echo "<div id=\"blanket\" style=\"display:none;\"></div>";

                //popup for non language task
                echo "<div id=\"popUpDiv\" style=\"display:none;\">";
                echo "<form action=\"#\" method=\"post\" name=\"addnonlanguage_form\" >";
                echo "<table width=\"480px\" border=\"1\" style=\"margin:10px\" class=\"admin\">";
                echo "<tr>";
                echo "	<td align=\"right\" colspan=\"2\"><a href=\"#\" onclick=\"return popup('popUpDiv');\">Close</a></td>";
                echo "</tr>";
                echo "<tr>";
                echo "	<td align=\"right\" class=\"admin\">Task Name</td>";
                echo "	<td><select name=\"name\" id=\"name\" />";
                echo "	<option value=\"na\">-----Linguistic-----</option>\n";
                foreach ($linguisticTasks as $task) {
                    if ($task->ltask != null) {
                        echo "		<option value=\"", str_replace(" ", "_", $task->ltask->name), "\">", $task->ltask->name, "</option>\n";
                    }
                }
                echo "	<option value=\"na\">------Internal--------</option>\n";
                foreach ($billableTasks as $task) {
                    if ($task->btask != null) {
                        echo "		<option value=\"", str_replace(" ", "_", $task->btask->name), "\">", $task->btask->name, "</option>\n";
                    }
                }
                echo "	</select></td>";
                echo "	</tr><tr>";
                echo "	<td align=\"right\" class=\"admin\">Rate</td>";
                echo "	<td><input type=\"text\" name=\"rate\" id=\"rate\" />";
                echo "	</tr><tr>";
                echo "	<td align=\"right\" class=\"admin\">Units</td>";
                echo "	<td class=\"admin\">per <select name=\"units\" onchange=\"checkSub(this.value)\"><option value=\"word\">Word</option>";
                echo "	<option value=\"hour\">Hour</option><option value=\"percent\">Percent</option></select>";
                echo " sub-category<select name=\"subcat\" id=\"subcat\"><option value=\"none\">-----</option><option value=\"New_Text\">New Text</option>";
                echo "<option value=\"Fuzzy_Text\">Fuzzy Text</option><option value=\"Match_Text\">100% Match/Repetitions</option></select></td>";
                echo "	</tr>";
                echo "	<tr>";
                echo "		<td colspan=\"2\" align=\"center\"><a href=\"#\" onclick=\"addNonLanguage('" . $clientName . "'); return popup('popUpDiv');\">Add Non-Language Task</a></td>";
                echo "	</tr>";
                echo "</table>";
                echo "</form>";
                echo "</div>";

                //popup for language tasks
                echo "<div id=\"addLangTask\" style=\"display:none;\">\n";
                echo "<form action=\"#\" method=\"post\" name=\"addlanguagetask_form\" >\n";
                echo "<table width=\"480px\" border=\"1\" style=\"margin:10px\" class=\"admin\">\n";
                echo "<tr>\n";
                echo "	<td align=\"right\" colspan=\"2\"><a href=\"#\" onclick=\"return popup('addLangTask');\">Close</a></td>\n";
                echo "</tr>\n";
                echo "<tr>\n";
                echo "	<td align=\"right\" class=\"admin\">Source Language</td>\n";

                echo "	<td><select name=\"sourcelang\" id=\"sourcelang\" />\n";
                foreach ($sourceLangs as $lang) {
                    echo "		<option value=\"", $lang, "\"";
                    if ($lang == "English (US)")
                        echo " selected=\"selected\" ";
                    echo ">", $lang, "</option>\n";
                }
                echo "	</select></td>\n";
                echo "	</tr><tr>\n";
                echo "	<td align=\"right\" class=\"admin\">Target Language</td>\n";
                echo "	<td><select name=\"targetlang\" id=\"targetlang\" />\n";
                foreach ($targetLangs as $lang) {
                    echo "		<option value=\"", $lang, "\">", $lang, "</option>\n";
                }
                echo "	</select></td>\n";
                echo "	</tr><tr>\n";
                echo "	<td align=\"right\" class=\"admin\">Task Name</td>";
                echo "	<td><select name=\"name\" id=\"name\" />";


                echo "	<option value=\"na\">-----Linguistic-----</option>\n";
                foreach ($linguisticTasks as $task) {
                    if ($task->ltask != null) {
                        echo "		<option value=\"", str_replace(" ", "_", $task->ltask->name), "\">", $task->ltask->name, "</option>\n";
                    }
                }
                echo "	<option value=\"na\">------Internal--------</option>\n";
                foreach ($billableTasks as $task) {
                    if ($task->btask != null) {
                        echo "		<option value=\"", str_replace(" ", "_", $task->btask->name), "\">", $task->btask->name, "</option>\n";
                    }
                }


                echo "	</select></td>";
                echo "	</tr><tr>";
                echo "	<td align=\"right\" class=\"admin\">Rate</td>\n";
                echo "	<td><input type=\"text\" name=\"rate\" id=\"rate\" />\n";
                echo "	</tr><tr>\n";
                echo "	<td align=\"right\" class=\"admin\">Units</td>\n";
                echo "	<td class=\"admin\">per <select name=\"units\" onchange=\"checkSub2(this.value)\"><option value=\"word\">Word</option>\n";
                echo "	<option value=\"hour\">Hour</option><option value=\"percent\">Percent</option></select><br>";
                echo " sub-category<select name=\"subcat2\" id=\"subcat2\"><option value=\"none\">-----</option><option value=\"New_Text\">New Text</option>";
                echo "<option value=\"Fuzzy_Text\">Fuzzy Text</option><option value=\"Match_Text\">100% Match/Repetitions</option></select></td>\n";
                echo "	</tr>\n";
                echo "	<tr>\n";
                echo "		<td colspan=\"2\" align=\"center\"><a href=\"#\" onclick=\" addLanguageTask('" . $clientName . "'); return popup('addLangTask');\">Add Language Task</a></td>\n";
                echo "	</tr>\n";
                echo "</table>\n";
                echo "</form>\n";
                echo "</div>\n";
            } elseif (isset($_GET['action']) && ($_GET['action'] == 'addtable')) {
                //first things first, check for admin, if not send them to the 'view' portal
                if ((!isset($_SESSION['isAdmin'])) || (!$_SESSION['isAdmin'])) {
                    header('location:./customPricing.php');
                    exit;
                }

                $allClients = getClientsFromAtTask($api);
                $clients = removeUsedClients($allClients);

                echo "\n\t<form action=\"?action=createtable\" method=\"post\" enctype=\"multipart/form-data\"  accept-charset=\"UTF-8\">\n";
                echo "\tClient: <select name=\"client\"/>\n";

                foreach ($clients as $client_id => $client_name) {
                    echo "\t\t<option value=\"", $client_id, "@", str_replace("\n", "", str_replace("\r", "", $client_name));
                    echo "\">", str_replace("\n", "", str_replace("\r", "", $client_name)), "</option>\n";
                }
                echo "\t</select>\n\n";
                echo "<p><strong>Optional:</strong><br />\n";
                echo "Choose a CSV file to upload: <input name=\"uploadedfile\" type=\"file\" /><br />\n";
                echo "<input type=\"submit\" name=\"submit\" value=\"Create\"></p>";
                echo "</form>";
            } elseif (isset($_GET['action']) && ($_GET['action'] == 'createtable')) {
                //first things first, check for admin, if not send them to the 'view' portal
                if ((!isset($_SESSION['isAdmin'])) || (!$_SESSION['isAdmin'])) {
                    header('location:./customPricing.php');
                    exit;
                }

                if (!isset($_POST['client'])) {
                    header("location: customPricing.php?action=addtable");
                    exit;
                } else {
                    $pieces = explode('@', $_POST['client']);
                    $client_id = $pieces[0];
                    $client_name = $pieces[1];
                    $client_name = iconv('UTF-8', "ISO-8859-1//IGNORE", $client_name);


                    $newTable = "client_" . $client_name;
                    $newTable = str_replace(' ', '_', $newTable);
                    $newTable = str_replace('-', '_', $newTable);
                    $newTable = str_replace('\'', '', $newTable);
                    $newTable = str_replace('`', '', $newTable);
                    $newTable = str_replace('’', '', $newTable);
                    $newTable = str_replace('"', '', $newTable);
                    $newTable = str_replace('.', '', $newTable);
                    $newTable = str_replace(',', '', $newTable);
                    $newTable = str_replace('&', '', $newTable);
                    $newTable = str_replace('(', '_', $newTable);
                    $newTable = str_replace(')', '_', $newTable);



                    if (strlen($newTable) >= 64)
                        $newTable = substr($newTable, 0, 63);

                    $newTable = strtolower($newTable);



                    $myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
                    if ($myDBConn->connect_errno) {
                        echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
                        exit;
                    }

                    $newTable = $myDBConn->real_escape_string($newTable);

                    //check if the table already exists
                    //check if table exists
                    $query = "SHOW TABLES LIKE '$newTable'";

                    $tbls = $myDBConn->query($query);

                    if ($tbls === FALSE) {
                        $string = "<p>Could not check for client table in database: " . $myDBConn->error . "</p>";
                        die($string);
                    }

                    if ($tbls->num_rows != 0) {
                        $string = "<p>A client pricing table is already setup for " . $client_name . ". <br>Please use the edit function to add to/alter this table.</p>";
                        die($string);
                    }

                    $query = "CREATE TABLE IF NOT EXISTS " . $newTable . " LIKE client_template";
                    if ($myDBConn->query($query)) {
                        //now we need to create the entry in the clients table
                        $query = "INSERT INTO clients (Name, attask_id, table_name) VALUES ('";
                        $query .= str_replace('\'', '', $client_name) . "'," . $client_id . ",'" . $newTable . "')";
                        if ($myDBConn->query($query)) {
                            echo "<p>Client pricing table successfully created for <b>", $client_name, "</b></p>";
                        } else {
                            echo "<p>Could not create entry in clients table</p>";
                            echo "<p>" . $myDBConn->error . "</p>";
                            exit;
                        }
                    } else {
                        echo "<p>Could not create table for: ", $client_name, ".<br>Error: ", $myDBConn->error, "</p>";
                        exit();
                    }


                    if ((count($_FILES['uploadedfile']['name']) >= 1) && ($_FILES['uploadedfile']['size'] > 0)) {
                        //process the uploaded csv file
                        if ($lines = file($_FILES['uploadedfile']['tmp_name'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)) {
                            foreach ($lines as $line_num => $line) {
                                $task = '';
                                $textType = '';
                                $sourceLang = '';
                                $targetLang = '';
                                $rate = '';
                                $unit = '';
                                list($task, $textType, $sourceLang, $targetLang, $rate, $unit) = explode(',', $line);

                                echo "<p>inserting <i>$task</i> into database... ";

                                if ($textType != '') {
                                    $task .= "#" . $textType . "#";
                                }
                                if ($sourceLang != '') {
                                    $task .= "=" . $sourceLang;
                                }
                                if ($targetLang != '') {
                                    $task .= "=" . $targetLang;
                                }
                                $task = str_replace(' ', '_', $task);

                                $rate = $rate * 1000;

                                $query = "INSERT INTO $newTable (task_name, rate, units) VALUES ('$task', $rate, '$unit')";


                                if (!$myDBConn->query($query)) {
                                    echo "Failed: <strong>", $myDBConn->error, "</strong></p>\n";
                                } else {
                                    echo "<strong>Success</strong>.</p>\n";
                                }
                            }
                        } else {
                            echo "<p>Unable to open file</p>";
                        }
                    }


                    $myDBConn->close();

                    echo "<a href=\"customPricing.php\" class=\"breadcrumbs\">Back</a>";
                }
            } elseif (isset($_GET['action']) && ($_GET['action'] == 'drop')) {
                if (!isset($_GET['target'])) {
                    //redirect here
                } else {
                    $dropTable = "client_" . $_GET['target'];
                    $dropTable = str_replace(" ", "_", $dropTable);

                    $myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
                    if ($myDBConn->connect_errno) {
                        echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
                        exit;
                    }

                    $query = "DROP TABLE " . $dropTable;
                    $result = $myDBConn->query($query) or die($myDBConn->error());
                    if (!$result) {
                        die('<p>Could not delete table: ' . $myDBConn->error . '</p>');
                    }

                    $query = "DELETE FROM clients WHERE table_name= '$dropTable'";
                    $result = $myDBConn->query($query) or die($myDBConn->error);
                    if (!$result) {
                        die('<p>Could not remove item from client table: ' . $myDBConn->error . '</p>');
                    }


                    echo "<p>Table deleted successfully</p>\n";
                    $myDBConn->close();
                }
            } elseif (isset($_GET['action']) && ($_GET['action'] == 'export')) {
                $clientID = $_GET['target'];
                exportClient($clientID);
            } elseif (isset($_GET['action']) && ($_GET['action'] == 'import')) {
                //first things first, check for admin, if not send them to the 'view' portal
                if ((!isset($_SESSION['isAdmin'])) || (!$_SESSION['isAdmin'])) {
                    header('location:./customPricing.php');
                    exit;
                }

                if (isset($_GET['verify']) && ($_GET['verify'] == 'verified')) {
                    if ((count($_FILES['uploadedfile']['name']) >= 1) && ($_FILES['uploadedfile']['size'] > 0)) {
                        $clientID = $_GET['target'];
                        $clientName = "";
                        $tableName = "";
                        getClientFromId($clientID, $clientName, $tableName);

                        //process the uploaded csv file
                        processCSV($clientID, $clientName, $tableName);
                    } else {
                        echo "<p>No file was uploaded</p>";
                    }


                    echo "<a href=\"customPricing.php\" class=\"breadcrumbs\">Back</a>";
                } else { //Not verified
                    $clientID = $_GET['target'];
                    $clientName = "";
                    $tableName = "";
                    getClientFromId($clientID, $clientName, $tableName);

                    echo "<h3 style=\"text-align:center\">Warning! This action will remove all data currently applied to the client ";
                    echo "<span style=\"color:#0000CC;\">";
                    echo $clientName . "</span> and overwrite it with new data!</h3>";
                    echo "<p style=\"text-align:center\">Are you <b>SURE</b> you want to do this?</p>";
                    echo "<p style=\"text-align:center\"><a href=\"customPricing.php\">No, I made a mistake, get me out of here</a></p>";
                    echo "<hr>";
                    echo "<h4 style=\"text-align:center\">Yes, I understand this will overwrite all data for:</h4>";
                    echo "<h1 style=\"text-align:center\">" . $clientName . "</h1>";
                    echo "<form action=\"?action=import&verify=verified&target=", $_GET['target'], "\" method=\"post\" enctype=\"multipart/form-data\">";

                    echo "Choose a CSV file to upload: <input name=\"uploadedfile\" type=\"file\" /><br />";
                    echo "<input type=\"submit\" name=\"submit\" value=\"Yes, upload my file and overwrite data\">";
                    echo "</form>";
                }
            } elseif (isset($_GET['action']) && ($_GET['action'] == 'view')) {
                $clientID = $_GET['target'];
                $clientName = "";
                $tableName = "";
                getClientFromId($clientID, $clientName, $tableName);

                echo "<h1>", $clientName, " Custom Pricing</h1>";

                viewClient($tableName);
            } else {
                echo "<h2 class=\"centerAligned\">Client Specific Pricing Schemes</h2>\n";
                displayAll();
            }
            ?>



    </body>
</html>
