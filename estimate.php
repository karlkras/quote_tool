<?PHP
include_once("definitions.php");
include_once("class_estimate.php");
include_once("class_contact.php");
include_once("class_tempData.php");
include_once("class_language.php");


//start the session
session_start();
require_once('uuid.php');

//check to make sure we're logged in
if (!isset($_SESSION['userID'])) {
    header('location:login.php?location=ballpark');
    exit;
} elseif ((!isset($_SESSION['appUUID'])) || ($_SESSION['appUUID'] != $app_UUID)) {
    header('location:login.php?err=6&location=ballpark');
    exit;
}



$taskNames = array("New Text", "Fuzzy Text", "Repetitions/100% Matches", "Proofread", "Formatting", "Graphic Design", "Formatting Coordination", "TM Work", "File Treatment", "Screen Capturing", "Quality Assurance", "QA Coordination", "Project Management");

$defaultTasks = array("Translate", "Copy Edit", "Proofread", "Formatting (DTP)", "Graphic Design", "Quality Assurance Review", "File Treatment", "Translation Memory creation, update, and/or maintenance", "Project Management");

$xmlDoc = true;



if ((isset($_SESSION['loadsaved'])) && ($_SESSION['loadsaved'] == 'true')) {

    if (isset($_SESSION['reloadedXML'])) {
        $filePath = $_SESSION['reloadedXML'];
        $xmlDoc = new DOMDocument;
        if ($xmlDoc->load($filePath)) {
            unlink($filePath);
            unset($_SESSION['loadsaved']);
            unset($_SESSION['reloadedXML']);
        } else {
            print "<h3>Error: Could not open XML file. Please try again.</h3>";
            unset($xmlDoc);
        }
    } else {
        print "<h3>Error: No file path found</h3>";
    }
} elseif (isset($_SESSION['languageTbl'])) {

    //if there is session data, then load the objects so we can populate the forms
    $languageTbl = $_SESSION['languageTbl'];
    $estimateObj = $_SESSION['estimate'];
    $lingoContact = $_SESSION['lingoContact'];
    $lingoPM = $_SESSION['pm'];
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http//www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http//www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Language Line Translation Solutions Estimator</title>
        <link href="main.css" rel="stylesheet" type="text/css" />

        <script language="javascript">
            function toggleID(elementID, imgID)
            {
                if (document.getElementById(elementID).style.display === 'none')
                {
                    document.getElementById(elementID).style.display = 'inline';
                    document.getElementById(imgID).src = 'minus.png';
                }
                else
                {
                    document.getElementById(elementID).style.display = 'none';
                    document.getElementById(imgID).src = 'plus.png';
                }

                return false;
            }

            function toggleDTP(formatUnits)
            {

                if ((document.getElementById("DTPCostunits").value === 'pages') || (document.getElementById("DTPSellunits").value === 'pages'))
                {
                    document.getElementById("fmtCostPer").disabled = false;
                }
                else
                {
                    document.getElementById("fmtCostPer").disabled = true;
                }
            }
            function updateCostUnit()
            {
                newPages = document.getElementById("pageNumber").value;
                pagesPerHour = document.getElementById("fmtPageHour").value;
                if ((newPages === 0) || (pagesPerHour === 0))
                    document.getElementById("fmtHours").value = 0;
                else
                    document.getElementById("fmtHours").value = Math.round((newPages / pagesPerHour) * 4) / 4;
            }

            function updateQA()
            {
                newPages = document.getElementById("pageNumber").value;
                pagesPerHour = document.getElementById("qaPagesHour").value;
                if ((newPages === 0) || (pagesPerHour === 0))
                    document.getElementById("qaHours").value = 0;
                else
                    document.getElementById("qaHours").value = Math.round((newPages / pagesPerHour) * 4) / 4;
            }

            function changeProjType(projType)
            {
                if (projType === "ptOther")
                {
                    document.getElementById("ptOtherTextDiv").style.display = 'inline';
                }
                else
                {
                    document.getElementById("ptOtherTextDiv").style.display = 'none';
                }

                return;
            }

            function changeFileType(docType)
            {
                var fmtRate = 1;

                switch (docType)
                {
                    case "ftWord":
                        fmtRate = 6;
                        document.getElementById('deliverable').value = "MS Word";
                        break;
                    case "ftQuark":
                        fmtRate = 4;
                        document.getElementById('deliverable').value = "QuarkXPress";
                        break;
                    case "ftInDesign":
                        fmtRate = 4;
                        document.getElementById('deliverable').value = "InDesign";
                        break;
                    case "ftPM":
                        fmtRate = 4;
                        document.getElementById('deliverable').value = "PageMaker";
                        break;
                    case "ftFM":
                        fmtRate = 4;
                        document.getElementById('deliverable').value = "FrameMaker";
                        break;
                    case "ftPPT":
                        fmtRate = 12;
                        document.getElementById('deliverable').value = "PowerPoint";
                        break;
                    case "ftPub":
                        fmtRate = 1;
                        document.getElementById('deliverable').value = "Publisher";
                        break;
                    case "ftExcel":
                        fmtRate = 1;
                        document.getElementById('deliverable').value = "Excel";
                        break;
                    case "ftFree":
                        fmtRate = 1;
                        document.getElementById('deliverable').value = "Freehand";
                        break;
                    case "ftPages":
                        fmtRate = 1;
                        document.getElementById('deliverable').value = "Pages";
                        break;
                    case "ftIllustrator":
                        fmtRate = 1;
                        document.getElementById('deliverable').value = "Illustrator";
                        break;
                    case "ftCD":
                        fmtRate = 1;
                        document.getElementById('deliverable').value = "CorelDraw";
                        break;
                    case "ftPhotoshop":
                        fmtRate = 1;
                        document.getElementById('deliverable').value = "Photoshop";
                        break;
                    case "ftAcrobat":
                        fmtRate = 1;
                        document.getElementById('deliverable').value = "Acrobat";
                        break;
                    case "ftWebworks":
                        fmtRate = 1;
                        document.getElementById('deliverable').value = "WebWorks";
                        break;
                    case "ftEmail":
                        fmtRate = 1;
                        document.getElementById('deliverable').value = "Email";
                        break;
                    case "ftFlash":
                        fmtRate = 1;
                        document.getElementById('deliverable').value = "Flash";
                        break;
                    case "ftHTML":
                        fmtRate = 1;
                        document.getElementById('deliverable').value = "HTML";
                        break;
                    case "ftPDF":
                        fmtRate = 1;
                        document.getElementById('deliverable').value = "Word Document";
                        break;
                    case "ftRoboHelp":
                        fmtRate = 1;
                        document.getElementById('deliverable').value = "RoboHelp";
                        break;
                    case "ftTxt":
                        fmtRate = 1;
                        document.getElementById('deliverable').value = "Text";
                        break;
                    case "ftTM":
                        fmtRate = 1;
                        document.getElementById('deliverable').value = "Translation Memory";
                        break;
                    case "ftResource":
                        fmtRate = 1;
                        document.getElementById('deliverable').value = "UI Resource File";
                        break;
                    case "ftXML":
                        fmtRate = 1;
                        document.getElementById('deliverable').value = "XML";
                        break;

                    default:
                        fmtRate = 1;
                        document.getElementById('deliverable').value = "";
                        break;


                }
                document.getElementById("fmtPageHour").value = fmtRate;

                return fmtRate;
            }


            function countLangs()
            {
                var txtSelectedValuesObj = document.getElementById('langNumber');
                var selectedArray = new Array();
                var selObj = document.getElementById('targetL');
                var langList = document.getElementById('languagelist');
                langList.innerHTML = "";
                var i;
                var count = 0;
                var first = true;
                for (i = 0; i < selObj.options.length; i++)
                {

                    if (selObj.options[i].selected)
                    {
                        langList.innerHTML = langList.innerHTML + selObj.options[i].value + "<br>";
                        count++;
                        if (first)
                        {
                            document.getElementById('languageID').innerHTML = "Enter values for " + selObj.options[i].value;
                            first = false;
                        }
                    }
                }
                txtSelectedValuesObj.value = count;

            }

            function countWords(form)
            {

                var a = (form.newText.value !== '') ? eval(form.newText.value) : 0;
                var b = (form.fuzzyText.value !== '') ? eval(form.fuzzyText.value) : 0;
                var c = (form.matchText.value !== '') ? eval(form.matchText.value) : 0;


                form.totalW.value = a + b + c;
            }

            function showBallpark()
            {
                document.getElementById('quoteForm').style.display = 'none';
                document.getElementById('archiveForm').style.display = 'none';
                document.getElementById('ballparkFields').style.display = 'inline';
                tbl = document.getElementById('miscTbl');
                //tbl.rows[2].style.display = '';


                return true;
            }

            function showEstimate()
            {
                document.getElementById('quoteForm').style.display = 'inline';
                document.getElementById('archiveForm').style.display = 'none';
                document.getElementById('ballparkFields').style.display = 'none';
                tbl = document.getElementById('miscTbl');
                //tbl.rows[2].style.display = '';
                return true;
            }

            function showArchive()
            {
                document.getElementById('quoteForm').style.display = 'none';
                document.getElementById('archiveForm').style.display = 'inline';

                return true;
            }

            function showClient()
            {
                document.getElementById('customer').style.display = 'inline';
                document.getElementById('prospect').style.display = 'none';

                return true;
            }

            function showProspect()
            {
                document.getElementById('customer').style.display = 'none';
                document.getElementById('prospect').style.display = 'inline';

                return true;
            }

            function toggleWordCount()
            {
                if (document.getElementById('tradosStyle').style.display == 'none')
                {
                    document.getElementById('tradosStyle').style.display = 'inline';
                    document.getElementById('totalStyle').style.display = 'none';
                }
                else
                {
                    document.getElementById('tradosStyle').style.display = 'none';
                    document.getElementById('totalStyle').style.display = 'inline';
                }

                return true;
            }

            function addService(serviceType)
            {
                if (document.getElementById(serviceType).checked === true)
                {
                    var _table = document.getElementById('addl_cost_table').insertRow(0);
                    var cell0 = _table.insertCell(0);
                    var cell1 = _table.insertCell(1);
                    cell0.align = "right";
                    cell0.innerHTML = serviceType.replace(/_/g, " ");
                    cell1.innerHTML = "<input type='text' name='" + serviceType + "' id='" + serviceType + "' value='0'/> Hours";

                }
                else
                {
                    var _table = document.getElementById("addl_cost_table");
                    var _rows = _table.getElementsByTagName("tr");

                    for (lcv = 0; lcv < _rows.length; lcv++)
                    {
                        if (_rows[lcv].getElementsByTagName("input")[0].name === serviceType)
                        {
                            document.getElementById('addl_cost_table').deleteRow(lcv);
                        }
                    }
                    //

                }


            }

            function changeTerms(terms)
            {
                if (terms === "Other")
                {
                    document.getElementById('termsOther').disabled = false;
                }
                else
                {
                    document.getElementById('termsOther').disabled = true;
                }
            }

            function otherLang(language)
            {

                if (language.options[160].selected == true)
                {
                    document.getElementById('otherLangContent').style.display = 'inline';
                }
                else
                {
                    document.getElementById('otherLangContent').style.display = 'none';
                }
            }

            function changeCycle(cycle)
            {
                if (cycle === "Progress")
                {
                    document.getElementById('cycleOther').disabled = false;
                }
                else
                {
                    document.getElementById('cycleOther').disabled = true;
                }
            }

            function changeAction(url)
            {
                document.estimate.action = url;
            }
        </script>

        <script language="javascript" src="libs/cal2.js">
            /*
             Xin's Popup calendar script-  Xin Yang (http://www.yxscripts.com/)
             Script featured on/available at http://www.dynamicdrive.com/
             This notice must stay intact for use
             */
        </script>
        <script language="javascript" src="libs/cal_conf2.js"></script>
    </head>

    <body>
        <div id="wrapper">
            <h1>&nbsp; <img src="images/languageline-logo.png" width="223" height="62" style="padding:2px"/> &nbsp;</h1>
            <hr />
            <span style="text-align:left"><form action="reset.php" method="post" name="reset"><input type="submit" name="reset" value="Start Over" /></form></span>
            <form action="process.php" method="post" name="estimate" >
<?PHP
if (is_object($xmlDoc)) {
    $quoteType = $xmlDoc->getElementsByTagName('quotetype')->item(0)->nodeValue;
} elseif (isset($_SESSION['temp'])) {

    $tempObj = $_SESSION['temp'];
    //unset($_SESSION['temp']);
    $quoteType = $tempObj->get_quoteType();
}
?>
                <div style="text-align:center; padding-bottom:25px; padding-top:15px;"><span style="font-weight:bold; font-size:1.5em">Ballpark Quote</span><br />
                    <h3 align="center"> &mdash; OnDemand Version &mdash; </h3>
                    <h5 align="center">Welcome <?PHP echo $_SESSION['userFirstName'] . " " . $_SESSION['userLastName']; ?> (<a href="./logout.php">logout</a>)</h5>

                    <input name="quoteType" value="qtBallpark" type="hidden"/>
                          <!-- <input name="quoteType" type="radio" value="qtRetrieval" onclick="showArchive()"/> Archive Retrieval &nbsp; -->
                </div>
                <div id="quoteForm"><fieldset><legend>Quote info</legend>
                        <table border="0" align="center" width="100%">
<?PHP
date_default_timezone_set('America/Los_Angeles');
$dateFormat = "M d, Y";
$todayDate = date($dateFormat);
?>
                            <tr>
                                <td width="30%" align="right" valign="top">Date of estimate</td>
                                <td width="70%" align="left" valign="top">
                                    <input name="estDate" type="text" readonly="true"  
                <?PHP
                if (is_object($xmlDoc)) {
                    //get date from XML file
                    print "value=\"" . $xmlDoc->getElementsByTagName("estdate")->item(0)->nodeValue . "\"";
                } elseif (isset($estimateObj)) {
                    print "value=\"" . $estimateObj->get_estimateDate() . "\"";
                } elseif (isset($tempObj)) {
                    echo "value=\"", $tempObj->get_estimateDate(), "\" ";
                } else {
                    print " value=\"" . $todayDate . "\" ";
                }
                ?>
                                           />
                                </td>
                            </tr>
                            <tr>
                                <td align="right">Project ID</td>
                                <td>
                                    <input type="text" name="projectid" 
                            <?PHP
                            if (is_object($xmlDoc)) {
                                print " value=\"" . $xmlDoc->getElementsByTagName('projectid')->item(0)->nodeValue . "\" ";
                            } elseif (isset($estimateObj)) {
                                print " value=\"" . $estimateObj->get_projectID() . "\" ";
                            } elseif (isset($tempObj)) {
                                echo " value=\"", $tempObj->get_projectID(), "\" ";
                            }
                            ?>
                                           />
                                </td>
                            </tr>
                            <tr>
                                <td align="right" valign="top">Representative </td>
                                <td align="left" valign="top">
                                    <select name="rep">
                                        <option value="">TBD</option>
                                    <?PHP
// get the list of reps from the database
                                    $myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
                                    if ($myDBConn->connect_errno) {
                                        echo "Failed to connect to MySQL: (" . $myDBConn->connect_errno . ") " . $myDBConn->connect_error;
                                        exit;
                                    }

                                    $query = "select name, id from lingocontacts order by name";
                                    $result = $myDBConn->query($query) or die($myDBConn->error);


                                    while ($res = $result->fetch_assoc()) {
                                        $name = $res['name'];
                                        $id = $res['id'];
                                        print("<option value=\"" . $id . "\"");
                                        if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('rep')->item(0)->nodeValue == $id)) {
                                            print " selected=\"selected\" ";
                                        } elseif ((isset($lingoContact)) && ($lingoContact->get_name() == $name)) {
                                            print " selected=\"selected\" ";
                                        } elseif ((isset($tempObj)) && ($tempObj->get_representative() == $name)) {
                                            echo " selected=\"selected\" ";
                                        }

                                        print(">" . $name . "</option>\n");
                                    }

                                    $result->free();
                                    ?>					

                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td align="right">Project Manager</td>
                                <td><select name="pm">
                                        <option value="TBD" 
<?PHP
if (is_object($xmlDoc)) {
    if ($xmlDoc->getElementsByTagName('pm')->item(0)->nodeValue == 'TBD')
        print " selected=\"selected\" ";
}
elseif (isset($tempObj) && ($tempObj->get_projectManager() == ""))
    echo " selected=\"selected\" ";
elseif (!isset($lingoPM))
    print " selected=\"selected\" ";
?>
                                                >TBD</option>
                                        <?PHP
// get the list of reps from the database

                                        $query = "select name, id from lingocontacts order by name";
                                        $result = $myDBConn->query($query) or die($myDBConn->error);


                                        while ($res = $result->fetch_assoc()) {
                                            $name = $res['name'];
                                            $id = $res['id'];
                                            print("<option value=\"" . $id . "\"");
                                            if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('pm')->item(0)->nodeValue == $id)) {
                                                print " selected=\"selected\" ";
                                            } elseif ((isset($lingoPM)) && ($lingoPM->get_name() == $name)) {
                                                print " selected=\"selected\" ";
                                            } elseif ((isset($tempObj)) && ($tempObj->get_projectManager() == $name)) {
                                                echo " selected=\"selected\" ";
                                            }
                                            print(">" . $name . "</option>\n");
                                        }

                                        $result->free();
                                        ?>						</select>
                                </td>
                            </tr>		
                        </table>
                    </fieldset>
                    <fieldset><legend>Client info</legend>
                        <table border="0" align="center" width="100%">
                            <tr>
                                <td colspan="2" align="center" valign="top">
                                    <input name="cusType" type="radio" value="cusClient" checked onclick="showClient()"
<?PHP
if (is_object($xmlDoc)) {
    if ($xmlDoc->getElementsByTagName('custype')->item(0)->nodeValue == 'cusClient') {
        print ' checked ';
    }
} elseif (isset($estimateObj)) {
    if ($estimateObj->get_clientType() == 'cusClient') {
        print ' checked ';
    }
} elseif (isset($tempObj)) {
    if ($tempObj->get_clientType() == 'cusClient') {
        echo ' checked ';
    }
}
?>
                                           /> Client &nbsp; <input name="cusType" type="radio" value="cusProspect" onclick="showProspect()"
                                        <?PHP
                                        if (is_object($xmlDoc)) {
                                            if ($xmlDoc->getElementsByTagName('custype')->item(0)->nodeValue == 'cusProspect') {
                                                print ' checked ';
                                            }
                                        } elseif (isset($estimateObj)) {
                                            if ($estimateObj->get_clientType() == 'cusProspect') {
                                                print ' checked ';
                                            }
                                        } elseif (isset($tempObj)) {
                                            if ($tempObj->get_clientType() == 'cusProspect') {
                                                print ' checked ';
                                            }
                                        }
                                        ?>

                                           /> Prospect
                                </td>
                            </tr>
                            <tr>
                                <td align="right" valign="top" width="30%">Client/Prospect Name</td>
                                <td width="70%" >
                                    <div id="customer"
                                                <?PHP
                                                if (is_object($xmlDoc)) {
                                                    if ($xmlDoc->getElementsByTagName('custype')->item(0)->nodeValue == 'cusClient') {
                                                        print (" style=\"display:inline\" ");
                                                    } else {
                                                        print (" style=\"display:none\" ");
                                                    }
                                                } elseif (isset($estimateObj)) {
                                                    if ($estimateObj->get_clientType() == 'cusClient') {
                                                        echo " style=\"display:inline\" ";
                                                    } else {
                                                        echo " style=\"display:none\" ";
                                                    }
                                                } elseif (isset($tempObj)) {
                                                    if ($tempObj->get_clientType() == 'cusClient') {
                                                        echo " style=\"display:inline\" ";
                                                    } else {
                                                        echo " style=\"display:none\" ";
                                                    }
                                                } else {
                                                    print (" style=\"display:inline\" ");
                                                }
                                                ?>
                                         >
                                    <?PHP
                                    //get customer names from the database
                                    $query = "select Name from clients order by Name";
                                    $result = $myDBConn->query($query) or die($myDBConn->error);
                                    while ($row = $result->fetch_assoc()) {
                                        $clients[] = $row['Name'];
                                    }
                                    $result->free();
                                    $myDBConn->close();
                                    ?>

                                        <select name="cusName" >
                                    <?PHP
                                    foreach ($clients as &$clientName) {
                                        print("<option value=\"$clientName\"");
                                        if ((is_object($xmlDoc)) && ($xmlDoc->getElementsByTagName('custype')->item(0)->nodeValue == 'cusClient') &&
                                                ($xmlDoc->getElementsByTagName('cusName')->item(0)->nodeValue == $clientName)) {
                                            print (" selected ");
                                        } elseif ((isset($estimateObj)) && ($estimateObj->get_clientType() == 'cusClient') && ($estimateObj->get_clientName() == $clientName)) {
                                            echo " selected ";
                                        } elseif ((isset($tempObj)) && ($tempObj->get_clientType() == 'cusClient') && ($tempObj->get_clientName() == $clientName)) {
                                            echo " selected ";
                                        }

                                        print(">$clientName</option>");
                                    }
                                    unset($clientName);
                                    ?>

                                        </select>
                                    </div>
                                    <div id="prospect" 
                                           <?PHP
                                           if ((is_object($xmlDoc)) && ($xmlDoc->getElementsByTagName('custype')->item(0)->nodeValue == 'cusProspect')) {
                                               print (" style=\"display:inline\" ");
                                           } elseif ((isset($estimateObj)) && ($estimateObj->get_clientType() == 'cusProspect')) {
                                               echo " style=\"display:inline\" ";
                                           } elseif ((isset($tempObj)) && ($tempObj->get_clientType() == 'cusProspect')) {
                                               echo " style=\"display:inline\" ";
                                           } else {
                                               print (" style=\"display:none\" ");
                                           }
                                           ?>
                                         >
                                        <input name="prosName" type="text" size="50" maxlength="100" 
                                    <?php
                                    if (is_object($xmlDoc))
                                        print "value=\"" . $xmlDoc->getElementsByTagName('prosname')->item(0)->nodeValue . "\" ";
                                    elseif (isset($estimateObj))
                                        echo "value=\"", $estimateObj->get_clientName(), "\" ";
                                    elseif (isset($tempObj))
                                        echo "value=\"", $tempObj->get_prospectName(), "\" ";
                                    ?>
                                               />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td align="right" valign="top">Project Name</td>
                                <td>
                                    <input name="projName" type="text" size="50" maxlength="50" 
                                    <?PHP
                                    if (is_object($xmlDoc))
                                        print "value=\"" . $xmlDoc->getElementsByTagName('projname')->item(0)->nodeValue . "\" ";
                                    elseif (isset($estimateObj))
                                        echo "value=\"", $estimateObj->get_projectName(), "\" ";
                                    elseif (isset($tempObj))
                                        echo "value=\"", $tempObj->get_projectName(), "\" ";
                                    ?>
                                           />
                                </td>
                            </tr>
                            <tr>
                                <td align="right" valign="top">Project Type </td>
                                <td>

                                    <select name="projType" onchange="changeProjType(this.value)">
                                        <option value="ptWeb"
                                             <?PHP
                                             if ((is_object($xmlDoc)) && ($xmlDoc->getElementsByTagName('projtype')->item(0)->nodeValue == 'ptWeb'))
                                                 print " selected ";
                                             elseif ((isset($estimateObj)) && ($estimateObj->get_projectType() == 'Website'))
                                                 print " selected ";
                                             elseif ((isset($tempObj)) && ($tempObj->get_projectType() == 'ptWeb'))
                                                 print " selected ";
                                             ?>
                                                >Website</option>
                                        <option value="ptUI"
                                            <?PHP
                                            if ((is_object($xmlDoc)) && ($xmlDoc->getElementsByTagName('projtype')->item(0)->nodeValue == 'ptUI'))
                                                print " selected ";
                                            elseif ((isset($estimateObj)) && ($estimateObj->get_projectType() == 'User Interface'))
                                                print " selected ";
                                            elseif ((isset($tempObj)) && ($tempObj->get_projectType() == 'ptUI'))
                                                print " selected ";
                                            ?>
                                                >User Interface</option>					
                                        <option value="ptHelp"
                                            <?PHP
                                            if ((is_object($xmlDoc)) && ($xmlDoc->getElementsByTagName('projtype')->item(0)->nodeValue == 'ptHelp'))
                                                print " selected ";
                                            elseif ((isset($estimateObj)) && ($estimateObj->get_projectType() == 'Help System'))
                                                print " selected ";
                                            elseif ((isset($tempObj)) && ($tempObj->get_projectType() == 'ptHelp'))
                                                print " selected ";
                                            ?>
                                                >Help System</option>
                                        <option value="ptSAP"
<?PHP
if ((is_object($xmlDoc)) && ($xmlDoc->getElementsByTagName('projtype')->item(0)->nodeValue == 'ptSAP'))
    print " selected ";
elseif ((isset($estimateObj)) && ($estimateObj->get_projectType() == 'SAP'))
    print " selected ";
elseif ((isset($tempObj)) && ($tempObj->get_projectType() == 'ptSAP'))
    print " selected ";
?>
                                                >SAP</option>
                                        <option value="ptDoc"
                                    <?PHP
                                    if ((is_object($xmlDoc)) && ($xmlDoc->getElementsByTagName('projtype')->item(0)->nodeValue == 'ptDoc'))
                                        print " selected ";
                                    elseif (isset($estimateObj)) {
                                        if ($estimateObj->get_projectType() == 'Documentation') {
                                            print " selected ";
                                        }
                                    } elseif ((isset($tempObj)) && ($tempObj->get_projectType() == 'ptDoc'))
                                        print " selected ";
                                    else
                                        echo " selected ";
                                    ?>
                                                >Document</option>
                                        <option value="ptAudio"
<?PHP
if ((is_object($xmlDoc)) && ($xmlDoc->getElementsByTagName('projtype')->item(0)->nodeValue == 'ptAudio'))
    print " selected ";
elseif ((isset($estimateObj)) && ($estimateObj->get_projectType() == 'Audio'))
    print " selected ";
elseif ((isset($tempObj)) && ($tempObj->get_projectType() == 'ptAudio'))
    print " selected ";
?>
                                                >Audio</option>
                                        <option value="ptXML"
                                    <?PHP
                                    if ((is_object($xmlDoc)) && ($xmlDoc->getElementsByTagName('projtype')->item(0)->nodeValue == 'ptXML'))
                                        print " selected ";
                                    elseif ((isset($estimateObj)) && ($estimateObj->get_projectType() == 'XML'))
                                        print " selected ";
                                    elseif ((isset($tempObj)) && ($tempObj->get_projectType() == 'ptXML'))
                                        print " selected ";
                                    ?>
                                                >XML</option>
                                        <option value="ptOther"
<?PHP
if ((is_object($xmlDoc)) && ($xmlDoc->getElementsByTagName('projtype')->item(0)->nodeValue == 'ptOther'))
    print " selected ";
elseif ((isset($estimateObj)) && ( ($estimateObj->get_projectType() != 'XML') && ($estimateObj->get_projectType() != 'Audio') && ($estimateObj->get_projectType() != 'Documentation') && ($estimateObj->get_projectType() != 'SAP') && ($estimateObj->get_projectType() != 'Help System') && ($estimateObj->get_projectType() != 'User Interface') && ($estimateObj->get_projectType() != 'Website') ))
    print " selected ";
elseif ((isset($tempObj)) && ($tempObj->get_projectType() == 'ptOther'))
    print " selected ";
?>
                                                >Other</option>
                                    </select>
                                    <div id="ptOtherTextDiv" 
                                        <?PHP
                                        if ((is_object($xmlDoc)) && ($xmlDoc->getElementsByTagName('projtype')->item(0)->nodeValue == 'ptOther'))
                                            print " style=\"display:inline\" ";
                                        elseif ((isset($estimateObj)) && ( ($estimateObj->get_projectType() != 'XML') && ($estimateObj->get_projectType() != 'Audio') && ($estimateObj->get_projectType() != 'Documentation') && ($estimateObj->get_projectType() != 'SAP') && ($estimateObj->get_projectType() != 'Help System') && ($estimateObj->get_projectType() != 'User Interface') && ($estimateObj->get_projectType() != 'Website') ))
                                            echo " style=\"display:inline\" ";
                                        elseif ((isset($tempObj)) && ($tempObj->get_projectType() == 'ptOther'))
                                            print " style=\"display:inline\" ";
                                        else
                                            print " style=\"display:none\" ";
                                        ?>
                                         > &nbsp; <input type="text" name="ptOtherText" id="ptOtherText"  
                                        <?PHP
                                        if (is_object($xmlDoc))
                                            print " value=\"" . $xmlDoc->getElementsByTagName('ptothertext')->item(0)->nodeValue . "\" ";
                                        elseif ((isset($estimateObj)) && ( ($estimateObj->get_projectType() != 'XML') && ($estimateObj->get_projectType() != 'Audio') && ($estimateObj->get_projectType() != 'Documentation') && ($estimateObj->get_projectType() != 'SAP') && ($estimateObj->get_projectType() != 'Help System') && ($estimateObj->get_projectType() != 'User Interface') && ($estimateObj->get_projectType() != 'Website') ))
                                            echo " value=\"", $estimateObj->get_projectType(), "\" ";
                                        elseif ((isset($tempObj)) && ($tempObj->get_projectType() == 'ptOther'))
                                            echo " value=\"", $tempObj->get_projectTypeOther(), "\" ";
                                        ?>/></div>
                                </td>
                            </tr>
                            <tr>
                                <td align="right" valign="top">File Type</td>
                                <td>
                                    <select name="fileType" onchange="changeFileType(this.value)">
                                        <option value="ftAcrobat"
                                        <?PHP
                                        if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('filetype')->item(0)->nodeValue == 'ftAcrobat'))
                                            print " selected=\"selected\" ";
                                        elseif ((isset($estimateObj)) && ($estimateObj->get_fileType() == 'Acrobat'))
                                            echo " selected=\"selected\" ";
                                        elseif ((isset($tempObj)) && ($tempObj->get_fileType() == 'ftAcrobat'))
                                            echo " selected=\"selected\" ";
                                        ?>
                                                >Acrobat</option>
                                        <option value="ftCD"
                                        <?PHP
                                        if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('filetype')->item(0)->nodeValue == 'ftCD'))
                                            print " selected=\"selected\" ";
                                        elseif ((isset($estimateObj)) && ($estimateObj->get_fileType() == 'CorelDraw'))
                                            echo " selected=\"selected\" ";
                                        elseif ((isset($tempObj)) && ($tempObj->get_fileType() == 'ftCD'))
                                            echo " selected=\"selected\" ";
                                        ?>
                                                >CorelDraw</option>
                                        <option value="ftEmail"
                                        <?PHP
                                        if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('filetype')->item(0)->nodeValue == 'ftEmail'))
                                            print " selected=\"selected\" ";
                                        elseif ((isset($estimateObj)) && ($estimateObj->get_fileType() == 'Email'))
                                            echo " selected=\"selected\" ";
                                        elseif ((isset($tempObj)) && ($tempObj->get_fileType() == 'ftEmail'))
                                            echo " selected=\"selected\" ";
                                        ?>
                                                >Email</option>
                                        <option value="ftExcel"
                                        <?PHP
                                        if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('filetype')->item(0)->nodeValue == 'ftExcel'))
                                            print " selected=\"selected\" ";
                                        elseif ((isset($estimateObj)) && ($estimateObj->get_fileType() == 'Excel'))
                                            echo " selected=\"selected\" ";
                                        elseif ((isset($tempObj)) && ($tempObj->get_fileType() == 'ftExcel'))
                                            echo " selected=\"selected\" ";
                                        ?>
                                                >Excel</option>
                                        <option value="ftFlash"
                                        <?PHP
                                        if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('filetype')->item(0)->nodeValue == 'ftFlash'))
                                            print " selected=\"selected\" ";
                                        elseif ((isset($estimateObj)) && ($estimateObj->get_fileType() == 'Flash'))
                                            echo " selected=\"selected\" ";
                                        elseif ((isset($tempObj)) && ($tempObj->get_fileType() == 'ftFlash'))
                                            echo " selected=\"selected\" ";
                                        ?>
                                                >Flash</option>
                                        <option value="ftFM"
                                        <?PHP
                                        if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('filetype')->item(0)->nodeValue == 'ftFM'))
                                            print " selected=\"selected\" ";
                                        elseif ((isset($estimateObj)) && ($estimateObj->get_fileType() == 'FrameMaker'))
                                            echo " selected=\"selected\" ";
                                        elseif ((isset($tempObj)) && ($tempObj->get_fileType() == 'ftFM'))
                                            echo " selected=\"selected\" ";
                                        ?>
                                                >FrameMaker</option>
                                        <option value="ftFree"
                                    <?PHP
                                    if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('filetype')->item(0)->nodeValue == 'ftFree'))
                                        print " selected=\"selected\" ";
                                    elseif ((isset($estimateObj)) && ($estimateObj->get_fileType() == 'FreeHand'))
                                        echo " selected=\"selected\" ";
                                    elseif ((isset($tempObj)) && ($tempObj->get_fileType() == 'ftFree'))
                                        echo " selected=\"selected\" ";
                                    ?>
                                                >FreeHand</option>
                                        <option value="ftHTML"
                                    <?PHP
                                    if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('filetype')->item(0)->nodeValue == 'ftHTML'))
                                        print " selected=\"selected\" ";
                                    elseif ((isset($estimateObj)) && ($estimateObj->get_fileType() == 'HTML'))
                                        echo " selected=\"selected\" ";
                                    elseif ((isset($tempObj)) && ($tempObj->get_fileType() == 'ftHTML'))
                                        echo " selected=\"selected\" ";
                                    ?>
                                                >HTML</option>
                                        <option value="ftIllustrator"
                                             <?PHP
                                             if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('filetype')->item(0)->nodeValue == 'ftIllustrator'))
                                                 print " selected=\"selected\" ";
                                             elseif ((isset($estimateObj)) && ($estimateObj->get_fileType() == 'Illustrator'))
                                                 echo " selected=\"selected\" ";
                                             elseif ((isset($tempObj)) && ($tempObj->get_fileType() == 'ftIllustrator'))
                                                 echo " selected=\"selected\" ";
                                             ?>
                                                >Illustrator</option>
                                        <option value="ftInDesign"
                                        <?PHP
                                        if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('filetype')->item(0)->nodeValue == 'ftInDesign'))
                                            print " selected=\"selected\" ";
                                        elseif ((isset($estimateObj)) && ($estimateObj->get_fileType() == 'InDesign'))
                                            echo " selected=\"selected\" ";
                                        elseif ((isset($tempObj)) && ($tempObj->get_fileType() == 'ftInDesign'))
                                            echo " selected=\"selected\" ";
                                        ?>
                                                >InDesign</option>
                                        <option value="ftPM"
                                        <?PHP
                                        if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('filetype')->item(0)->nodeValue == 'ftPM'))
                                            print " selected=\"selected\" ";
                                        elseif ((isset($estimateObj)) && ($estimateObj->get_fileType() == 'PageMaker'))
                                            echo " selected=\"selected\" ";
                                        elseif ((isset($tempObj)) && ($tempObj->get_fileType() == 'ftPM'))
                                            echo " selected=\"selected\" ";
                                        ?>
                                                >PageMaker</option>
                                        <option value="ftPages"
                                        <?PHP
                                        if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('filetype')->item(0)->nodeValue == 'ftPages'))
                                            print " selected=\"selected\" ";
                                        elseif ((isset($estimateObj)) && ($estimateObj->get_fileType() == 'Pages'))
                                            echo " selected=\"selected\" ";
                                        elseif ((isset($tempObj)) && ($tempObj->get_fileType() == 'ftPages'))
                                            echo " selected=\"selected\" ";
                                        ?>
                                                >Pages</option>
                                        <option value="ftPDF"
                                        <?PHP
                                        if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('filetype')->item(0)->nodeValue == 'ftPDF'))
                                            print " selected=\"selected\" ";
                                        elseif ((isset($estimateObj)) && ($estimateObj->get_fileType() == 'PDF'))
                                            echo " selected=\"selected\" ";
                                        elseif ((isset($tempObj)) && ($tempObj->get_fileType() == 'ftPDF'))
                                            echo " selected=\"selected\" ";
                                        ?>
                                                >PDF</option>
                                        <option value="ftPhotoshop"
                                        <?PHP
                                        if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('filetype')->item(0)->nodeValue == 'ftPhotoshop'))
                                            print " selected=\"selected\" ";
                                        elseif ((isset($estimateObj)) && ($estimateObj->get_fileType() == 'Photoshop'))
                                            echo " selected=\"selected\" ";
                                        elseif ((isset($tempObj)) && ($tempObj->get_fileType() == 'ftPhotoshop'))
                                            echo " selected=\"selected\" ";
                                        ?>
                                                >Photoshop</option>
                                        <option value="ftPPT"
                                        <?PHP
                                        if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('filetype')->item(0)->nodeValue == 'ftPPT'))
                                            print " selected=\"selected\" ";
                                        elseif ((isset($estimateObj)) && ($estimateObj->get_fileType() == 'PowerPoint'))
                                            echo " selected=\"selected\" ";
                                        elseif ((isset($tempObj)) && ($tempObj->get_fileType() == 'ftPPT'))
                                            echo " selected=\"selected\" ";
                                        ?>
                                                >PowerPoint</option>
                                        <option value="ftPub"
                                        <?PHP
                                        if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('filetype')->item(0)->nodeValue == 'ftPub'))
                                            print " selected=\"selected\" ";
                                        elseif ((isset($estimateObj)) && ($estimateObj->get_fileType() == 'Publisher'))
                                            echo " selected=\"selected\" ";
                                        elseif ((isset($tempObj)) && ($tempObj->get_fileType() == 'ftPub'))
                                            echo " selected=\"selected\" ";
                                        ?>
                                                >Publisher</option>
                                        <option value="ftQuark"
                                        <?PHP
                                        if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('filetype')->item(0)->nodeValue == 'ftQuark'))
                                            print " selected=\"selected\" ";
                                        elseif ((isset($estimateObj)) && ($estimateObj->get_fileType() == 'QuarkXPress'))
                                            echo " selected=\"selected\" ";
                                        elseif ((isset($tempObj)) && ($tempObj->get_fileType() == 'ftQuark'))
                                            echo " selected=\"selected\" ";
                                        ?>
                                                >QuarkXPress</option>
                                        <option value="ftRoboHelp"
                                        <?PHP
                                        if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('filetype')->item(0)->nodeValue == 'ftRoboHelp'))
                                            print " selected=\"selected\" ";
                                        elseif ((isset($estimateObj)) && ($estimateObj->get_fileType() == 'Robo Help'))
                                            echo " selected=\"selected\" ";
                                        elseif ((isset($tempObj)) && ($tempObj->get_fileType() == 'ftRoboHelp'))
                                            echo " selected=\"selected\" ";
                                        ?>
                                                >Robo Help</option>
                                        <option value="ftTxt"
                                        <?PHP
                                        if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('filetype')->item(0)->nodeValue == 'ftTxt'))
                                            print " selected=\"selected\" ";
                                        elseif ((isset($estimateObj)) && ($estimateObj->get_fileType() == 'Text'))
                                            echo " selected=\"selected\" ";
                                        elseif ((isset($tempObj)) && ($tempObj->get_fileType() == 'ftTxt'))
                                            echo " selected=\"selected\" ";
                                        ?>
                                                >Text</option>
                                        <option value="ftTM"
                                        <?PHP
                                        if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('filetype')->item(0)->nodeValue == 'ftTM'))
                                            print " selected=\"selected\" ";
                                        elseif ((isset($estimateObj)) && ($estimateObj->get_fileType() == 'Translation Memory'))
                                            echo " selected=\"selected\" ";
                                        elseif ((isset($tempObj)) && ($tempObj->get_fileType() == 'ftTM'))
                                            echo " selected=\"selected\" ";
                                        ?>
                                                >Translation Memory</option>
                                        <option value="ftResource"
                                        <?PHP
                                        if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('filetype')->item(0)->nodeValue == 'ftResource'))
                                            print " selected=\"selected\" ";
                                        elseif ((isset($estimateObj)) && ($estimateObj->get_fileType() == 'UI Resource File'))
                                            echo " selected=\"selected\" ";
                                        elseif ((isset($tempObj)) && ($tempObj->get_fileType() == 'ftResource'))
                                            echo " selected=\"selected\" ";
                                        ?>
                                                >UI Resource File</option>
                                        <option value="ftWebworks"
                                        <?PHP
                                        if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('filetype')->item(0)->nodeValue == 'ftWebworks'))
                                            print " selected=\"selected\" ";
                                        elseif ((isset($estimateObj)) && ($estimateObj->get_fileType() == 'WebWorks'))
                                            echo " selected=\"selected\" ";
                                        elseif ((isset($tempObj)) && ($tempObj->get_fileType() == 'ftWebworks'))
                                            echo " selected=\"selected\" ";
                                        ?>
                                                >WebWorks</option>
                                        <option value="ftWord" 
                                        <?PHP
                                        if (is_object($xmlDoc)) {
                                            if ($xmlDoc->getElementsByTagName('filetype')->item(0)->nodeValue == 'ftWord')
                                                print " selected=\"selected\" ";
                                        }
                                        else if (isset($estimateObj)) {
                                            if ($estimateObj->get_fileType() == 'Word')
                                                echo " selected=\"selected\" ";
                                        }
                                        elseif (isset($tempObj)) {
                                            if ($tempObj->get_fileType() == 'ftWord')
                                                echo " selected=\"selected\" ";
                                        } else
                                            print " selected=\"selected\" ";
                                        ?>
                                                >Word</option>
                                        <option value="ftXML"
                                        <?PHP
                                        if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('filetype')->item(0)->nodeValue == 'ftXML'))
                                            print " selected=\"selected\" ";
                                        elseif ((isset($estimateObj)) && ($estimateObj->get_fileType() == 'XML'))
                                            echo " selected=\"selected\" ";
                                        elseif ((isset($tempObj)) && ($tempObj->get_fileType() == 'ftXML'))
                                            echo " selected=\"selected\" ";
                                        ?>
                                                >XML</option>
                                        <option value="ftOther"
                                        <?PHP
                                        if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('filetype')->item(0)->nodeValue == 'ftOther'))
                                            print " selected=\"selected\" ";
                                        elseif ((isset($estimateObj)) && ( ($estimateObj->get_fileType() != "Acrobat") && ($estimateObj->get_fileType() != "CorelDraw") && ($estimateObj->get_fileType() != "Email") && ($estimateObj->get_fileType() != "Excel") && ($estimateObj->get_fileType() != "Flash") && ($estimateObj->get_fileType() != "FrameMaker") && ($estimateObj->get_fileType() != "FreeHand") && ($estimateObj->get_fileType() != "HTML") && ($estimateObj->get_fileType() != "Illustrator") && ($estimateObj->get_fileType() != "InDesign") && ($estimateObj->get_fileType() != "PageMaker") && ($estimateObj->get_fileType() != "Pages") && ($estimateObj->get_fileType() != "PDF") && ($estimateObj->get_fileType() != "Photoshop") && ($estimateObj->get_fileType() != "PowerPoint") && ($estimateObj->get_fileType() != "Publisher") && ($estimateObj->get_fileType() != "QuarkXPress") && ($estimateObj->get_fileType() != "Robo Help") && ($estimateObj->get_fileType() != "Text") && ($estimateObj->get_fileType() != "Translation Memory") && ($estimateObj->get_fileType() != "UI Resource File") && ($estimateObj->get_fileType() != "WebWorks") && ($estimateObj->get_fileType() != "Word") && ($estimateObj->get_fileType() != "XML") ))
                                            echo " selected=\"selected\" ";
                                        elseif ((isset($tempObj)) && ($tempObj->get_fileType() == 'ftOther'))
                                            echo " selected=\"selected\" ";
                                        ?>
                                                >Other</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td align="right" valign="top">Deliverable(s)</td>
                                <td>
                                    <input name="deliverable" id="deliverable" type="text" 
                                        <?PHP
                                        if (is_object($xmlDoc))
                                            echo " value=\"", $xmlDoc->getElementsByTagName('deliverable')->item(0)->nodeValue, "\" ";
                                        elseif (isset($estimateObj))
                                            echo " value=\"", $estimateObj->get_deliverable(), "\" ";
                                        elseif (isset($tempObj))
                                            echo " value=\"", $tempObj->get_deliverable(), "\" ";
                                        else
                                            echo " value=\"MS Word\" ";
                                        ?>
                                           />
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                    <fieldset><legend><A HREF="#" onClick="return toggleID('services', 'serviceimg');" ><img id="serviceimg" src="minus.png" border=0></a>Requested Services</legend>
                        <div id='services' style='display:inline'><table border="0" align="center" width="100%" id="miscTbl">
                                <tr>
                                    <td width="50%" align="left" valign="top">
                                        <strong>Linguistic:</strong> <br />
                                        <?PHP
                                        $services = array();
                                        if (is_object($xmlDoc)) {
                                            $reqServes = $xmlDoc->getElementsByTagName('requestedservices')->item(0);
                                            foreach ($reqServes->childNodes as $service) {
                                                $services[] = $service->nodeValue;
                                            }
                                        } elseif (isset($estimateObj)) {
                                            $services = $estimateObj->get_services();
                                        } elseif (isset($tempObj)) {
                                            $services = $tempObj->get_requestedServices();
                                        }
                                        ?>
                                        <input type="checkbox" name="requestedServices[]" value="Translate" checked/>Translate<br />
                                        <input type="checkbox" name="requestedServices[]" value="Copy Edit" checked/>Copy Edit<br />
                                        <input type="checkbox" name="requestedServices[]" value="Proofread" checked/>Proofread<br />
                                        <input type="checkbox" name="requestedServices[]" value="Online Review" id="OnlineReview" onClick="addService('OnlineReview');"
                                        <?PHP
                                        if ((((is_object($xmlDoc)) || (isset($estimateObj)) || (isset($tempObj))) && in_array('Online Review', $services)))
                                            print " checked ";
                                        ?>
                                               />Online Review<br />
                                        <input type="checkbox" name="requestedServices[]" value="Glossary Development" id="Glossary_Development" onClick="addService('Glossary_Development');"
                                        <?PHP
                                        if ((((is_object($xmlDoc)) || (isset($estimateObj)) || (isset($tempObj))) && in_array('Glossary Development', $services)))
                                            print " checked ";
                                        ?>
                                               />Glossary Development<br />
                                        <input type="checkbox" name="requestedServices[]" value="Review Leveraged Text" id="Review_Leveraged_Text" onClick="addService('Review_Leveraged_Text');"
                                        <?PHP
                                        if ((((is_object($xmlDoc)) || (isset($estimateObj)) || (isset($tempObj))) && in_array('Review Leveraged Text', $services)))
                                            print " checked ";
                                        ?>
                                               />Review Leveraged Text<br /><br />
                                        <br /><strong>DTP:</strong><br />
                                        <input type="checkbox" name="requestedServices[]" value="Formatting (DTP)" checked/>Formatting (DTP)<br />
                                        <input type="checkbox" name="requestedServices[]" value="EM Cleanup" id="EM_Cleanup" onClick="addService('EM_Cleanup');"
                                        <?PHP
                                        if ((((is_object($xmlDoc)) || (isset($estimateObj)) || (isset($tempObj))) && in_array('EM Cleanup', $services)))
                                            print " checked ";
                                        ?>
                                               />EM Cleanup<br />
                                        <input type="checkbox" name="requestedServices[]" value="Graphic Design" checked />Graphic Design<br />
                                        <input type="checkbox" name="requestedServices[]" value="PDF Creation" id="PDF_Creation" onClick="addService('PDF_Creation');"
                                        <?PHP
                                        if ((((is_object($xmlDoc)) || (isset($estimateObj)) || (isset($tempObj))) && in_array('PDF Creation', $services)))
                                            print " checked ";
                                        ?>
                                               />PDF Creation<br /><br />

                                        <br /><strong>QA:</strong><br />
                                        <input type="checkbox" name="requestedServices[]" value="Quality Assurance Review" checked/>Quality Assurance Review<br /><br />

                                        <br /><strong>Voiceover:</strong><br />
                                        <input type="checkbox" name="requestedServices[]" value="Voiceover Talent" id="Voiceover_Talent" onClick="addService('Voiceover_Talent');"
                                        <?PHP
                                        if ((((is_object($xmlDoc)) || (isset($estimateObj)) || (isset($tempObj))) && in_array('Voiceover Talent', $services)))
                                            print " checked ";
                                        ?>
                                               />Voiceover Talent<br />
                                        <input type="checkbox" name="requestedServices[]" value="Voiceover Recording" id="Voiceover_Recording" onClick="addService('Voiceover_Recording');"
                                        <?PHP
                                        if ((((is_object($xmlDoc)) || (isset($estimateObj)) || (isset($tempObj))) && in_array('Voiceover Recording', $services)))
                                            print " checked ";
                                        ?>
                                               />Voiceover Recording<br />
                                        <input type="checkbox" name="requestedServices[]" value="Voiceover Editing/Mixing" id="Voiceover_Editing/Mixing" onClick="addService('Voiceover_Editing/Mixing');"
                                        <?PHP
                                        if ((((is_object($xmlDoc)) || (isset($estimateObj)) || (isset($tempObj))) && in_array('Voiceover Editing/Mixing', $services)))
                                            print " checked ";
                                        ?>
                                               />Voiceover Editing/Mixing<br />
                                        <input type="checkbox" name="requestedServices[]" value="Voiceover Archiving" id="Voiceover_Archiving" onClick="addService('Voiceover_Archiving');"
                                        <?PHP
                                        if ((((is_object($xmlDoc)) || (isset($estimateObj)) || (isset($tempObj))) && in_array('Voiceover Archiving', $services)))
                                            print " checked ";
                                        ?>
                                               />Voiceover Archiving<br />
                                        <input type="checkbox" name="requestedServices[]" value="Voiceover Shipping" id="Voiceover_Shipping" onClick="addService('Voiceover_Shipping');"
<?PHP
if ((((is_object($xmlDoc)) || (isset($estimateObj)) || (isset($tempObj))) && in_array('Voiceover Shipping', $services)))
    print " checked ";
?>
                                               />Voiceover Shipping<br />
                                        <input type="checkbox" name="requestedServices[]" value="Voiceover Director" id="Voiceover_Director" onClick="addService('Voiceover_Director');"
<?PHP
if ((((is_object($xmlDoc)) || (isset($estimateObj)) || (isset($tempObj))) && in_array('Voiceover Director', $services)))
    print " checked ";
?>
                                               />Voiceover Director<br />
                                    </td>
                                    <td align="left" valign="top">
                                        <strong>Engineering:</strong><br />
                                        <input type="checkbox" name="requestedServices[]" value="File Treatment" checked/>File Treatment<br />
                                        <input type="checkbox" name="requestedServices[]" value="Translation Memory creation, update, and/or maintenance" checked/>TM Work<br />
                                        <input type="checkbox" name="requestedServices[]" value="Senior Engineering" id="Senior_Engineering" onClick="addService('Senior_Engineering');"
                                    <?PHP
                                    if ((((is_object($xmlDoc)) || (isset($estimateObj)) || (isset($tempObj))) && in_array('Senior Engineering', $services)))
                                        print " checked ";
                                    ?>
                                               />Senior Engineering<br />
                                        <input type="checkbox" name="requestedServices[]" value="UI Engineering" id="UI_Engineering" onClick="addService('UI_Engineering');"
<?PHP
if ((((is_object($xmlDoc)) || (isset($estimateObj)) || (isset($tempObj))) && in_array('UI Engineering', $services)))
    print " checked ";
?>
                                               />UI Engineering<br />
                                        <input type="checkbox" name="requestedServices[]" value="Website Engineering" id="Website_Engineering" onClick="addService('Website_Engineering');"
                                        <?PHP
                                        if ((((is_object($xmlDoc)) || (isset($estimateObj)) || (isset($tempObj))) && in_array('Website Engineering', $services)))
                                            print " checked ";
                                        ?>
                                               />Website Engineering<br />
                                        <input type="checkbox" name="requestedServices[]" value="Help Engineering" id="Help_Engineering" onClick="addService('Help_Engineering');"
                                        <?PHP
                                        if ((((is_object($xmlDoc)) || (isset($estimateObj)) || (isset($tempObj))) && in_array('Help Engineering', $services)))
                                            print " checked ";
                                        ?>
                                               />Help Engineering<br />
                                        <input type="checkbox" name="requestedServices[]" value="Flash Engineering" id="Flash_Engineering" onClick="addService('Flash_Engineering');"
                                        <?PHP
                                        if ((((is_object($xmlDoc)) || (isset($estimateObj)) || (isset($tempObj))) && in_array('Flash Engineering', $services)))
                                            print ' checked ';
                                        ?>
                                               />Flash Engineering<br />
                                        <input type="checkbox" name="requestedServices[]" value="Troubleshooting" id="Troubleshooting" onClick="addService('Troubleshooting');"
<?PHP
if ((((is_object($xmlDoc)) || (isset($estimateObj)) || (isset($tempObj))) && in_array('Troubleshooting', $services)))
    print " checked ";
?>
                                               />Troubleshooting<br />
                                        <input type="checkbox" name="requestedServices[]" value="Functional QA" id="Functional_QA" onClick="addService('Functional_QA');"
                                        <?PHP
                                        if ((((is_object($xmlDoc)) || (isset($estimateObj)) || (isset($tempObj))) && in_array('Functional QA', $services)))
                                            print " checked ";
                                        ?>
                                               />Functional QA<br />
                                        <input type="checkbox" name="requestedServices[]" value="CD/DVD Burning" id="CD/DVD_Burning" onClick="addService('CD/DVD_Burning');"
                                        <?PHP
                                        if ((((is_object($xmlDoc)) || (isset($estimateObj)) || (isset($tempObj))) && in_array('CD/DVD Burning', $services)))
                                            print " checked ";
                                        ?>
                                               />CD/DVD Burning<br />
                                        <input type="checkbox" name="requestedServices[]" value="Test Script Development" id="Test_Script_Development" onClick="addService('Test_Script_Development');"
                                        <?PHP
                                        if ((((is_object($xmlDoc)) || (isset($estimateObj)) || (isset($tempObj))) && in_array('Test Script Development', $services)))
                                            print " checked ";
                                        ?>
                                               />Test Script Development<br />
                                        <input type="checkbox" name="requestedServices[]" value="OLR - Lab" id="OLR_-_Lab" onClick="addService('OLR_-_Lab');" 
                                        <?PHP
                                        if ((((is_object($xmlDoc)) || (isset($estimateObj)) || (isset($tempObj))) && in_array('OLR - Lab', $services)))
                                            print " checked ";
                                        ?>
                                               />OLR - Lab<br />					
                                        <input type="checkbox" name="requestedServices[]" value="Screen Capturing" 
                                        <?PHP
                                        if ((((is_object($xmlDoc)) || (isset($estimateObj)) || (isset($tempObj))) && in_array('Screen Capturing', $services)))
                                            print " checked ";
                                        ?>
                                               />Screen Capturing<br />
                                        <input type="checkbox" name="requestedServices[]" value="Graphic Editing" id="Graphic_Editing" onClick="addService('Graphic_Editing');"
<?PHP
if ((((is_object($xmlDoc)) || (isset($estimateObj)) || (isset($tempObj))) && in_array('Graphic Editing', $services)))
    print " checked ";
?>
                                               />Graphic Editing<br /><br />

                                        <br /><strong>PDF:</strong><br />
                                        <input type="checkbox" name="requestedServices[]" value="PDF Engineering" id="PDF_Engineering" onClick="addService('PDF_Engineering');"
                                        <?PHP
                                        if ((((is_object($xmlDoc)) || (isset($estimateObj)) || (isset($tempObj))) && in_array('PDF Engineering', $services)))
                                            print " checked ";
                                        ?>
                                               />PDF Engineering<br />
                                        <input type="checkbox" name="requestedServices[]" value="PDF Annotation" id="PDF_Annotation" onClick="addService('PDF_Annotation');"
                                        <?PHP
                                        if ((((is_object($xmlDoc)) || (isset($estimateObj)) || (isset($tempObj))) && in_array('PDF Annotation', $services)))
                                            print " checked ";
                                        ?>
                                               />PDF Annotation<br />

                                        <br /><strong>Project Management:</strong><br />
                                        <input type="checkbox" name="requestedServices[]" value="Project Management" checked/>Project Management<br /><br />

                                        <br /><strong>Consulting:</strong><br />
                                        <input type="checkbox" name="requestedServices[]" value="Internationalization Consulting" id="Internationalization_Consulting" 
                                        <?PHP
                                        if ((((is_object($xmlDoc)) || (isset($estimateObj)) || (isset($tempObj))) && in_array('Internationalization Consulting', $services)))
                                            print " checked ";
                                        ?>
                                               onclick="addService('Internationalization_Consulting');" />Internationalization Consulting<br />
                                        <input type="checkbox" name="requestedServices[]" value="CMS Consulting" id="CMS_Consulting" onclick="addService('CMS_Consulting');" 
                                        <?PHP
                                        if ((((is_object($xmlDoc)) || (isset($estimateObj)) || (isset($tempObj))) && in_array('CMS Consulting', $services)))
                                            print " checked ";
                                        ?>
                                               />CMS Consulting<br />
                                        <input type="checkbox" name="requestedServices[]" value="Testing Services" id="Testing_Services" onclick="addService('Testing_Services');" 
                                        <?PHP
                                        if ((((is_object($xmlDoc)) || (isset($estimateObj)) || (isset($tempObj))) && in_array('Testing Services', $services)))
                                            print " checked ";
                                        ?>
                                               />Testing Services<br />

                                    </td>

                                </tr>

                            </table></div>
                    </fieldset>
                    <fieldset>
                        <legend>Languages</legend>	
                        <table border="0" width="100%" align="center" id='language'>
                            <tr>
                                <td width="30%" align="right" valign="top">Source Language </td>
                                <td width="70%" colspan="2">
                                    <select name="sourceL"><?PHP
                                        //get the language list from @task and print each one as an option
                                        require_once('attaskconn/LingoAtTaskService.php');
                                        $api = new LingoAtTaskService();

                                        $languageList = array();
                                        $g = new getLanguageService();
                                        $languageList = $api->getLanguageService($g)->return;
                                        sort($languageList->sourceLanguages);
                                        $sourceLanguages = $languageList->sourceLanguages;
                                        sort($languageList->targetLanguages);
                                        $targetLanguages = $languageList->targetLanguages;

                                        foreach ($sourceLanguages as $srcLang) {
                                            echo "<option value=\"", $srcLang, "\" ";
                                            if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('sourcelanguage')->item(0)->nodeValue == $srcLang))
                                                echo " selected ";
                                            elseif (isset($languageTbl) && (count($languageTbl) > 0) && ($languageTbl[0]->get_sourceLang() == $srcLang))
                                                echo " selected ";
                                            elseif ((!is_object($xmlDoc)) && ( (!isset($languageTbl)) || (count($languageTbl) < 1)) && ($srcLang == 'English (US)'))
                                                echo " selected ";

                                            echo ">", $srcLang, "</option>\n";
                                        }
                                        ?></select>
                                </td>

                            </tr>
                            <tr>
                                <td align="right" valign="top">Target Language</td>
                                <td>
                                        <?PHP
                                        $targLangs = array();
                                        if (is_object($xmlDoc)) {
                                            $targetLangs = $xmlDoc->getElementsByTagName('targetlanguages')->item(0);
                                            foreach ($targetLangs->childNodes as $language) {
                                                $targLangs[] = $language->nodeValue;
                                            }
                                        } elseif (isset($languageTbl) && (count($languageTbl) > 0)) {
                                            foreach ($languageTbl as $language) {
                                                $targLangs[] = $language->get_targetLang();
                                            }
                                        } elseif (isset($tempObj)) {
                                            $targLangs[] = $tempObj->get_targetLanguages();
                                        }
                                        ?>
                                    <select name="targetL[]" multiple size="5" onchange="countLangs();
                                            otherLang(this);" id="targetL" >
                                        <?PHP
                                        foreach ($targetLanguages as $tgtLang) {
                                            echo "<option value=\"", $tgtLang, "\" ";
                                            if (((is_object($xmlDoc)) || (isset($languageTbl) && (count($languageTbl) > 0)) || (isset($tempObj))) && (in_array($tgtLang, $targLangs)))
                                                echo "selected ";
                                            echo ">", $tgtLang, "</option>\n";
                                        }
                                        ?>
                                        <option value="Other"
                                        <?PHP
                                        if (is_object($xmlDoc) && in_array('Other', $targLangs))
                                            print " selected ";
                                        elseif (isset($languageTbl)) {
                                            foreach ($languageTbl as $language) {
                                                if (!in_array($language->get_targetLang(), $targLangs)) {
                                                    echo " selected ";
                                                    break;
                                                }
                                            }
                                        } elseif (isset($tempObj) && in_array('Other', $targLangs))
                                            echo " selected ";
                                        ?>
                                                >Other / Not Listed</option>


                                    </select> 				
                                    <br />* ctrl+click to select multiple 

                                </td>
                                <td valign="top" align="right">
                                    <span id="languagelist"><?PHP
                                        if (isset($languageTbl) && (count($languageTbl) > 0)) {
                                            foreach ($languageTbl as $lang) {
                                                echo $lang->get_targetLang(), "<br>";
                                            }
                                        } elseif (isset($tempObj)) {
                                            foreach ($tempObj->get_targetLanguages() as $target) {
                                                echo $target, "<br>";
                                            }
                                        }
                                        ?></span></td>
                            </tr>
                            <tr>
                                <td>&nbsp;

                                </td>
                                <td colspan="2">
                                    <div id='otherLangContent' 
                                        <?PHP
                                        if (is_object($xmlDoc) && in_array('Other', $targLangs))
                                            print " style=\"display:inline\" ";
                                        elseif (isset($languageTbl) && (count($languageTbl) > 0)) {
                                            $found = false;
                                            foreach ($languageTbl as $language) {
                                                if (!in_array($language->get_targetLang(), $targLangs)) {
                                                    echo " style=\"display:inline\" ";
                                                    $found = true;
                                                    break;
                                                }
                                            }
                                            if (!$found)
                                                echo " style=\"display:none\" ";
                                            unset($found);
                                        }
                                        elseif (isset($tempObj) && in_array('Other', $targLangs))
                                            echo " style=\"display:inline\" ";
                                        else
                                            print " style=\"display:none\" ";
                                        ?>
                                         >
                                        <strong>Other Language Details</strong>
                                        <table id='otherLang'>
                                            <tr>
                                                <td>Name</td>
                                                <td>
                                                    <input name="otherLangName" 
                                        <?PHP
                                        if (is_object($xmlDoc)) {
                                            $otherLangValues = $xmlDoc->getElementsByTagName('otherlanguage')->item(0);
                                            print " value=\"" . $otherLangValues->getElementsByTagName('langname')->item(0)->nodeValue . "\" ";
                                            $newtextcost = $otherLangValues->getElementsByTagName('newtextcost')->item(0)->nodeValue;
                                            $fuzzytextcost = $otherLangValues->getElementsByTagName('fuzzytextcost')->item(0)->nodeValue;
                                            $matchtextcost = $otherLangValues->getElementsByTagName('matchtextcost')->item(0)->nodeValue;
                                            $transhourlycost = $otherLangValues->getElementsByTagName('transhourlycost')->item(0)->nodeValue;
                                            $prhourlycost = $otherLangValues->getElementsByTagName('prhourlycost')->item(0)->nodeValue;
                                        } elseif (isset($languageTbl) && (count($languageTbl) > 0)) {
                                            foreach ($languageTbl as $language) {
                                                if (!in_array($language->get_targetLang(), $targLangs)) {
                                                    echo " value=\"", $language->get_targetName(), "\" ";
                                                    $newtextcost = $language->get_newTextRate();
                                                    $fuzzytextcost = $language->get_fuzzyTextRate();
                                                    $matchtextcost = $language->get_matchTextRate();
                                                    $transhourlycost = $language->get_transHourly();
                                                    $prhourlycost = $language->get_prHourly();
                                                }
                                            }
                                        } elseif (isset($tempObj)) {
                                            echo " value-\"", $tempObj->get_otherLangName(), "\" ";
                                            $newtextcost = $tempObj->get_otherLangNewTextCost();
                                            $fuzzytextcost = $tempObj->get_otherLangFuzzyTextCost();
                                            $matchtextcost = $tempObj->get_otherLangMatchTextCost();
                                            $transhourlycost = $tempObj->get_otherLangTransHourlyCost();
                                            $prhourlycost = $tempObj->get_otherLangPRHourlyCost();
                                        }
                                        ?>
                                                           />
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>New Text Cost:</td>
                                                <td>
                                                    <input name="otherLangNewTextCost" 
                                    <?PHP
                                    if (isset($newtextcost)) {
                                        echo " value=\"", $newtextcost, "\" ";
                                        unset($newtextcost);
                                    }
                                    ?>
                                                           />
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Fuzzy Text Cost:</td>
                                                <td><input name="otherLangFuzzyTextCost"  
                                        <?PHP
                                        if (isset($fuzzytextcost)) {
                                            echo " value=\"", $fuzzytextcost, "\" ";
                                            unset($fuzzytextcost);
                                        }
                                        ?>
                                                           /></td>
                                            </tr>
                                            <tr>
                                                <td>100% Match/Reps Cost:</td>
                                                <td><input name="otherLangMatchTextCost"  
                                        <?PHP
                                        if (isset($matchtextcost)) {
                                            echo " value=\"", $matchtextcost, "\" ";
                                            unset($matchtextcost);
                                        }
                                        ?>
                                                           /></td>
                                            </tr>
                                            <tr>
                                                <td>Translation Hourly Cost:</td>
                                                <td><input name="otherLangTransHourly"  
                                        <?PHP
                                        if (isset($transhourlycost)) {
                                            echo " value=\"", $transhourlycost, "\" ";
                                            unset($transhourlycost);
                                        }
                                        ?>
                                                           /></td>
                                            </tr>
                                            <tr>
                                                <td>Proofreading Hourly Cost:</td>
                                                <td><input name="otherLangPRHourly"  
                                        <?PHP
                                        if (isset($prhourlycost)) {
                                            echo " value=\"", $prhourlycost, "\" ";
                                            unset($prhourlycost);
                                        }
                                        ?>
                                                           /></td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td align="right" valign="top">Number of languages</td>
                                <td>
                                    <input name="langNumber" type="text" size="10" maxlength="3" readonly="true" id="langNumber" 
<?PHP
if (is_object($xmlDoc))
    print " value=\" " . $xmlDoc->getElementsByTagName('langnumber')->item(0)->nodeValue . "\" ";
elseif (isset($languageTbl))
    echo " value=\"", count($languageTbl), "\" ";
elseif (isset($tempObj))
    echo " value=\"", count($tempObj->get_targetLanguages()), "\" ";
else
    print " value=\"0\" ";
?>
                                           />
                                </td>
                            </tr>
                        </table>
                    </fieldset>

                    <fieldset><legend>Misc</legend>
                        <table border="0" align="center" width="100%" id="miscTbl">
                            <tr>
                                <td width="30%" align="right" valign="top">Requested Delivery  Date</td>
                                <td width="70%">
                                    <input name="estDeliveryDate" type="text" value="<?PHP
                                    if (is_object($xmlDoc))
                                        print $xmlDoc->getElementsByTagName('estdeliverydate')->item(0)->nodeValue;
                                    elseif (isset($estimateObj))
                                        echo $estimateObj->get_deliveryDate();
                                    elseif (isset($tempObj))
                                        echo $tempObj->get_deliveryDate();
                                    else
                                        print($todayDate);
                                    ?>"/> 
                                    <a href="javascript:showCal('Calendar1')"><img src="calendar.png" title="Enter with calendar" alt="Enter with calendar" border="0" /></a>
                                </td>
                            </tr>
                            <tr>
                                <td align="right" valign="top">Rush Fees </td>
                                <td>
                                    <input type="radio" name="rushFees" value="rf0" 
                                                    <?PHP
                                                    if (is_object($xmlDoc)) {
                                                        if ($xmlDoc->getElementsByTagName('rushfees')->item(0)->nodeValue == 'rf0')
                                                            print " checked ";
                                                    }
                                                    elseif (isset($estimateObj)) {
                                                        if ($estimateObj->get_rushFeeMultiplier() == 0)
                                                            echo " checked ";
                                                    }
                                                    elseif (isset($tempObj)) {
                                                        if ($tempObj->get_rushFee() == 'rf0')
                                                            echo " checked ";
                                                    } else
                                                        print " checked ";
                                                    ?>
                                           />None &nbsp; 
                                    <input type="radio" name="rushFees" value="rf25" 
                                                    <?PHP
                                                    if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('rushfees')->item(0)->nodeValue == 'rf25'))
                                                        print " checked ";
                                                    elseif (isset($estimateObj) && ($estimateObj->get_rushFeeMultiplier() == 0.25))
                                                        echo " checked ";
                                                    elseif (isset($tempObj) && ($tempObj->get_rushFee() == 'rf25'))
                                                        echo " checked ";
                                                    ?>
                                           />25% &nbsp;
                                    <input type="radio" name="rushFees" value="rf50"  
                                                    <?PHP
                                                    if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('rushfees')->item(0)->nodeValue == 'rf50'))
                                                        print " checked ";
                                                    elseif (isset($estimateObj) && ($estimateObj->get_rushFeeMultiplier() == 0.5))
                                                        echo " checked ";
                                                    elseif (isset($tempObj) && ($tempObj->get_rushFee() == 'rf50'))
                                                        echo " checked ";
                                                    ?>
                                           />50% &nbsp;
                                </td>
                            </tr>
                            <tr>
                                <td align="right" valign="top">Discount</td>
                                <td>
                                    <input type="text" name="discountAmount" 
                                                    <?PHP
                                                    if (is_object($xmlDoc)) {
                                                        $discounts = $xmlDoc->getElementsByTagName('discounts')->item(0);
                                                        print " value=\"" . $discounts->getElementsByTagName('amount')->item(0)->nodeValue . "\" ";
                                                    } elseif (isset($estimateObj)) {
                                                        switch ($estimateObj->get_discountType()) {
                                                            case 'percent':
                                                                echo " value=\"", $estimateObj->get_discountPercent(), "\" ";
                                                                break;
                                                            case 'fixed':
                                                                echo " value=\"", $estimateObj->get_discountAmount(), "\" ";
                                                                break;
                                                        }
                                                    } elseif (isset($tempObj)) {
                                                        echo " value=\"", $tempObj->get_discountAmount(), "\" ";
                                                    } else
                                                        print " value=\"0\" ";
                                                    ?>
                                           />
                                    <input type="radio" name="discountType" value="percent" 
<?PHP
if (is_object($xmlDoc)) {
    if ($discounts->getElementsByTagName('type')->item(0)->nodeValue == 'percent')
        print "checked";
}
elseif (isset($estimateObj)) {
    if ($estimateObj->get_discountType() == 'percent')
        echo " checked ";
}
elseif (isset($tempObj)) {
    if ($tempObj->get_discountType() == 'percent')
        echo " checked ";
} else
    print " checked ";
?>
                                           />Percent &nbsp; 

                                    <input type="radio" name="discountType" value="fixed" 
                                                    <?PHP
                                                    if (is_object($xmlDoc) && ($discounts->getElementsByTagName('type')->item(0)->nodeValue == 'fixed'))
                                                        print " checked ";
                                                    elseif (isset($estimateObj) && ($estimateObj->get_discountType() == 'fixed'))
                                                        echo " checked ";
                                                    elseif (isset($tempObj) && ($tempObj->get_discountType() == 'fixed'))
                                                        echo " checked ";
                                                    ?>
                                           />Fixed Amount
                                </td>
                            </tr>

<!--		<tr >
        <td align="right" valign="top">File Path</td>
        <td><input name="path" type="text" size="50" disabled="disabled"/></td>
</tr>  -->
                            <tr>
                                <td valign="top" align="right">Description of project </td>
                                <td valign="top" align="left">
                                    <textarea name="projDesc" cols="70" rows="4"><?PHP
                                                    if (is_object($xmlDoc))
                                                        print $xmlDoc->getElementsByTagName('projdesc')->item(0)->nodeValue;
                                                    elseif (isset($estimateObj))
                                                        echo $estimateObj->get_projDesc();
                                                    elseif (isset($tempObj))
                                                        echo $estimateObj->get_description();
                                                    ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="right">Notes for Operations </td>
                                <td valign="top" align="left">
                                    <textarea name="general_notes" cols="70" rows="4" ><?PHP
                                    if (is_object($xmlDoc))
                                        print $xmlDoc->getElementsByTagName('generalnotes')->item(0)->nodeValue;
                                    elseif (isset($estimateObj))
                                        echo $estimateObj->get_notes();
                                    elseif (isset($tempObj))
                                        echo $tempObj->get_notes();
                                    ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="right" style="padding-top:4px">Billing Terms</td>
                                <td><select name="terms" onchange="changeTerms(this.value);">
                                        <option value="COD"
<?PHP
if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('terms')->item(0)->nodeValue == 'COD'))
    print " selected=\"selected\" ";
elseif (isset($estimateObj) && ($estimateObj->get_billingTerms() == 'COD'))
    echo " selected=\"selected\" ";
elseif (isset($tempObj) && ($tempObj->get_billingTerms() == 'COD'))
    echo " selected=\"selected\" ";
?>
                                                >COD</option>
                                        <option value="Credit Card"
                                    <?PHP
                                           if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('terms')->item(0)->nodeValue == 'Credit Card'))
                                               print " selected=\"selected\" ";
                                           elseif (isset($estimateObj) && ($estimateObj->get_billingTerms() == 'Credit Card'))
                                               echo " selected=\"selected\" ";
                                           elseif (isset($tempObj) && ($tempObj->get_billingTerms() == 'Credit Card'))
                                               echo " selected=\"selected\" ";
                                           ?>
                                                >Credit Card</option>
                                        <option value="30 days" 
                                    <?PHP
                                    if (is_object($xmlDoc)) {
                                        if ($xmlDoc->getElementsByTagName('terms')->item(0)->nodeValue == '30 days')
                                            print " selected=\"selected\" ";
                                    }
                                    elseif (isset($estimateObj)) {
                                        if (($estimateObj->get_billingTerms() == '30 days'))
                                            echo " selected=\"selected\" ";
                                    }
                                    elseif (isset($tempObj)) {
                                        if (($tempObj->get_billingTerms() == '30 days'))
                                            echo " selected=\"selected\" ";
                                    } else
                                        print " selected=\"selected\" ";
                                    ?>
                                                >30 days</option>
                                        <option value="45 days"
                                    <?PHP
                                    if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('terms')->item(0)->nodeValue == '45 days'))
                                        print " selected=\"selected\" ";
                                    elseif (isset($estimateObj) && ($estimateObj->get_billingTerms() == '45 days'))
                                        echo " selected=\"selected\" ";
                                    elseif (isset($tempObj) && ($tempObj->get_billingTerms() == '45 days'))
                                        echo " selected=\"selected\" ";
                                    ?>
                                                >45 days</option>
                                        <option value="60 days"
                                    <?PHP
                                    if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('terms')->item(0)->nodeValue == '60 days'))
                                        print " selected=\"selected\" ";
                                    elseif (isset($estimateObj) && ($estimateObj->get_billingTerms() == '60 days'))
                                        echo " selected=\"selected\" ";
                                    elseif (isset($tempObj) && ($tempObj->get_billingTerms() == '60 days'))
                                        echo " selected=\"selected\" ";
                                    ?>
                                                >60 days</option>
                                        <option value="Other"
                                    <?PHP
                                    if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('terms')->item(0)->nodeValue == 'Other'))
                                        print " selected=\"selected\" ";
                                    elseif (isset($estimateObj) && ($estimateObj->get_billingTerms() == 'Other'))
                                        echo " selected=\"selected\" ";
                                    elseif (isset($tempObj) && ($tempObj->get_billingTerms() == 'Other'))
                                        echo " selected=\"selected\" ";
                                    ?>
                                                >Other</option>
                                    </select><br />
                                    <input type="text" name="termsOther" id="termsOther" style="margin-top:5px" 
                                    <?PHP
                                    if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('terms')->item(0)->nodeValue == 'Other'))
                                        print " value=\"" . $xmlDoc->getElementsByTagName('termsother')->item(0)->nodeValue . "\" ";
                                    elseif (isset($estimateObj) && ($estimateObj->get_billingTerms() == 'Other'))
                                        echo " value=\"", $estimateObj->get_billingTermsOther(), "\" ";
                                    elseif (isset($tempObj) && ($tempObj->get_billingTerms() == 'Other'))
                                        echo " value=\"", $tempObj->get_billingTermsOther(), "\" ";
                                    else
                                        print " disabled=\"disabled\" ";
                                    ?>
                                           />

                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="right" style="padding-top:4px">Billing Cycle</td>
                                <td>
                                    <select name="cycle" onchange="changeCycle(this.value);">
                                        <option value="On Delivery"
                                    <?PHP
                                    if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('cycle')->item(0)->nodeValue == 'On Delivery'))
                                        print " selected=\"selected\" ";
                                    elseif (isset($estimateObj) && ($estimateObj->get_billingCycle() == 'On Delivery'))
                                        echo " selected=\"selected\" ";
                                    elseif (isset($tempObj) && ($tempObj->get_billingCycle() == 'On Delivery'))
                                        echo " selected=\"selected\" ";
                                    ?>
                                                >On Delivery</option>
                                        <option value="Project Start"
                                    <?PHP
                                    if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('cycle')->item(0)->nodeValue == 'Project Start'))
                                        print " selected=\"selected\" ";
                                    elseif (isset($estimateObj) && ($estimateObj->get_billingCycle() == 'Project Start'))
                                        echo " selected=\"selected\" ";
                                    elseif (isset($tempObj) && ($tempObj->get_billingCycle() == 'Project Start'))
                                        echo " selected=\"selected\" ";
                                    ?>
                                                >Project Start</option>
                                        <option value="50-50"
                                    <?PHP
                                    if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('cycle')->item(0)->nodeValue == '50-50'))
                                        print " selected=\"selected\" ";
                                    elseif (isset($estimateObj) && ($estimateObj->get_billingCycle() == '50-50'))
                                        echo " selected=\"selected\" ";
                                    elseif (isset($tempObj) && ($tempObj->get_billingCycle() == '50-50'))
                                        echo " selected=\"selected\" ";
                                    ?>
                                                >50-50</option>
                                        <option value="Progress"
                                    <?PHP
                                    if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('cycle')->item(0)->nodeValue == 'Progress'))
                                        print " selected=\"selected\" ";
                                    elseif (isset($estimateObj) && ($estimateObj->get_billingCycle() == 'Progress'))
                                        echo " selected=\"selected\" ";
                                    elseif (isset($tempObj) && ($tempObj->get_billingCycle() == 'Progress'))
                                        echo " selected=\"selected\" ";
                                    ?>
                                                >Progress</option>
                                    </select><br />
                                    <input type="text" name="cycleOther" id="cycleOther" style="margin-top:5px" 
<?PHP
if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('cycle')->item(0)->nodeValue == 'Progress'))
    print " value=\"" . $xmlDoc->getElementsByTagName('cycleother')->item(0)->nodeValue . "\" ";
elseif (isset($estimateObj) && ($estimateObj->get_billingCycle() == 'Progress'))
    echo " value=\"", $estimateObj->get_billingCycleOther(), "\" ";
elseif (isset($tempObj) && ($tempObj->get_billingCycle() == 'Progress'))
    echo " value=\"", $tempObj->getBillingCycleOther(), "\" ";
else
    print " disabled=\"disabled\" ";
?>
                                           />
                                </td>
                            </tr>
                        </table>
                    </fieldset>


                    <div id="ballparkFields">
                        <hr />
                        <div id="languageID" style="text-align:center; font-size:1.5em; font-weight:bold"><?PHP
                                        if (isset($languageTbl) && (count($languageTbl) >= 1))
                                            echo "Enter values for ", $languageTbl[0]->get_targetLang();
                                        elseif (isset($tempObj))
                                            echo "Enter values for ", $tempObj->get_targetLanguage(0);
                                        else
                                            echo "&nbsp;";
                                        ?></div>
                        <fieldset ><legend>Linguistic</legend>

                            <table border="0" align="center" width="100%" >
                                <tr>
                                    <td colspan="2" align="center"> 
                                        <input name="wordCountStyle" type="radio" value="total" onclick="toggleWordCount()"
                                        <?PHP
                                        if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('wordcountstyle')->item(0)->nodeValue == 'total'))
                                            print " checked=\"checked\" ";
                                        elseif (isset($tempObj) && ($tempObj->get_wordCountStyle() == 'total'))
                                            print " checked=\"checked\" ";
                                        ?>
                                               />Total Word Count 
                                        <input name="wordCountStyle" type="radio" value="trados" onclick="toggleWordCount()" 
<?PHP
if (is_object($xmlDoc)) {
    if ($xmlDoc->getElementsByTagName('wordcountstyle')->item(0)->nodeValue == 'trados')
        print " checked=\"checked\" ";
}
elseif (isset($tempObj)) {
    if ($tempObj->get_wordCountStyle() == 'trados')
        echo " checked=\"checked\" ";
}
else {
    print " checked=\"checked\" ";
}
?>
                                               />Trados Analysis
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div id="tradosStyle" 
                                        <?PHP
                                        if (is_object($xmlDoc)) {
                                            if ($xmlDoc->getElementsByTagName('wordcountstyle')->item(0)->nodeValue == 'trados')
                                                print " style=\"display:inline\" ";
                                            else
                                                print " style=\"display:none\" ";
                                        }
                                        elseif (isset($tempObj))
                                            if ($tempObj->get_wordCountStyle() == 'trados')
                                                echo " style=\"display:inline\" ";
                                            else
                                                echo " style=\"display:none\" ";
                                        else
                                            print " style=\"display:inline\" ";
                                        ?>
                                             >
                                            <table>
                                                <tr>
                                                    <td>
                                                        New Text
                                                    </td>
                                                    <td>
                                                        <input name="new" type="text" size="20" maxlength="20" onchange="countWords(this.form);" id="newText" value="<?PHP
                                        if (is_object($xmlDoc))
                                            print $xmlDoc->getElementsByTagName('newtext')->item(0)->nodeValue;
                                        elseif (isset($languageTbl) && (count($languageTbl) > 0))
                                            echo $languageTbl[0]->get_newText();
                                        elseif (isset($tempObj))
                                            echo $tempObj->get_newText();
                                        else
                                            print '0';
                                        ?>"/>
                                                        <span class="instruction">(do not enter commas or periods)</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Fuzzy Text</td>
                                                    <td>
                                                        <input name="fuzzy" type="text" size="20" maxlength="20" onchange="countWords(this.form);" id="fuzzyText" value="<?PHP
                                        if (is_object($xmlDoc))
                                            print $xmlDoc->getElementsByTagName('fuzzytext')->item(0)->nodeValue;
                                        elseif (isset($languageTbl) && (count($languageTbl) > 0))
                                            echo $languageTbl[0]->get_fuzzyText();
                                        elseif (isset($tempObj))
                                            echo $tempObj->get_fuzzyText();
                                        else
                                            print '0';
                                        ?>"/>
                                                        <span class="instruction"> (do not enter commas or periods)</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>100% Matches/Repetitions</td>
                                                    <td>
                                                        <input name="100" type="text" size="20" maxlength="20" onchange="countWords(this.form);" id="matchText" value="<?PHP
                                    if (is_object($xmlDoc))
                                        print $xmlDoc->getElementsByTagName('matchtext')->item(0)->nodeValue;
                                    elseif (isset($languageTbl) && (count($languageTbl) > 0))
                                        echo $languageTbl[0]->get_matchText();
                                    elseif (isset($tempObj))
                                        echo $tempObj->get_matchText();
                                    else
                                        print '0';
                                        ?>"/>
                                                        <span class="instruction"> (do not enter commas or periods)</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Total Word Count</td>
                                                    <td>
                                                        <input name="totalW" type="text" readonly="true"  size="20" maxlength="20" value="<?PHP
                                        if (is_object($xmlDoc))
                                            print $xmlDoc->getElementsByTagName('totalwords')->item(0)->nodeValue;
                                        elseif (isset($languageTbl) && (count($languageTbl) > 0))
                                            echo $languageTbl[0]->get_newText() + $languageTbl[0]->get_fuzzyText() + $languageTbl[0]->get_matchText();
                                        elseif (isset($tempObj))
                                            echo $tempObj->get_newText() + $tempObj->get_fuzzyText() + $tempObj->get_matchText();
                                        else
                                            print '0';
                                        ?>" id="totalW"/>
                                                    </td>
                                                </tr>
                                            </table>


                                        </div>
                                        <div id="totalStyle"  
                                        <?PHP
                                        if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('wordcountstyle')->item(0)->nodeValue == 'total'))
                                            print " style=\"display:inline\" ";
                                        elseif (isset($tempObj) && ($tempObj->get_wordCountStyle() == 'total'))
                                            echo " style=\"display:inline\" ";
                                        else
                                            print " style=\"display:none\" ";
                                        ?>
                                             >
                                            <table>
                                                <tr>
                                                    <td>Total Text</td>
                                                    <td>
                                                        <input name="totalText" type="text" size="20" maxlength="20" 
<?PHP
if (is_object($xmlDoc))
    print " value=\"" . $xmlDoc->getElementsByTagName('totaltext')->item(0)->nodeValue . "\" ";
elseif (isset($tempObj))
    echo " value=\"", $tempObj->get_totalText(), "\" ";
else
    print " value=\"0\" ";
?>
                                                               /><span class="instruction"> (do not enter commas or periods)</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>% Leveraging</td>
                                                    <td>
                                                        <input name="percentLeverage" type="text" size="20" maxlength="20" value="<?PHP
                                    if (is_object($xmlDoc))
                                        print $xmlDoc->getElementsByTagName('percentleverage')->item(0)->nodeValue;
                                    elseif (isset($tempObj))
                                        echo $tempObj->get_percentLeverage();
                                    else
                                        print '0';
?>"/>
                                                        <span class="instruction"> (ex: 75)</span>
                                                    </td>
                                                </tr>


                                            </table>

                                        </div></td><td>&nbsp;</td></tr>

                                </td>
                                </tr>

                                <tr>
                                    <td>
                                        Trans/CE Cost Units: 
                                        <select name="linguisticCostType">
                                            <option value="Words"
                                        <?PHP
                                        if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('linguisticcosttype')->item(0)->nodeValue == 'Words'))
                                            print " selected=\"selected\" ";
                                        elseif (isset($languageTbl) && (count($languageTbl) > 0)) {
                                            foreach ($languageTbl[0]->get_tasks() as $task) {
                                                if ($task->get_name() == 'New Text') {
                                                    if ($task->get_costUnitType() == 'Words')
                                                        echo " selected=\"selected\" ";
                                                    break;
                                                }
                                            }
                                        }
                                        elseif (isset($tempObj) && ($tempObj->get_linguisticCostType() == 'Words'))
                                            echo " selected=\"selected\" ";
                                        ?>
                                                    >By Word</option>
                                            <option value="Hours"
                                        <?PHP
                                        if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('linguisticcosttype')->item(0)->nodeValue == 'Hours'))
                                            print " selected=\"selected\" ";
                                        elseif (isset($languageTbl) && (count($languageTbl) > 0)) {
                                            foreach ($languageTbl[0]->get_tasks() as $task) {
                                                if ($task->get_name() == 'New Text') {
                                                    if ($task->get_costUnitType() == 'Hours')
                                                        echo " selected=\"selected\" ";
                                                    break;
                                                }
                                            }
                                        }
                                        elseif (isset($tempObj) && ($tempObj->get_linguisticCostType() === 'Hours'))
                                            echo " selected=\"selected\" ";
                                        ?>
                                                    >Hourly</option>
                                        </select><br />
                                        Trans/CE Sell Units: 
                                        <select name="linguisticSellType">
                                            <option value="Words"
                                        <?PHP
                                        if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('linguisticselltype')->item(0)->nodeValue == 'Words'))
                                            print " selected=\"selected\" ";
                                        elseif (isset($languageTbl) && (count($languageTbl) > 0)) {
                                            foreach ($languageTbl[0]->get_tasks() as $task) {
                                                if ($task->get_name() == 'New Text') {
                                                    if ($task->get_sellUnitType() == 'Words')
                                                        echo " selected=\"selected\" ";
                                                    break;
                                                }
                                            }
                                        }
                                        if (isset($tempObj) && ($tempObj->get_linguisticSellType() == 'Words'))
                                            echo " selected=\"selected\" ";
                                        ?>
                                                    >By Word</option>
                                            <option value="Hours"
                                                        <?PHP
                                                        if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('linguisticselltype')->item(0)->nodeValue == 'Hours'))
                                                            print " selected=\"selected\" ";
                                                        elseif (isset($languageTbl) && (count($languageTbl) > 0)) {
                                                            foreach ($languageTbl[0]->get_tasks() as $task) {
                                                                if ($task->get_name() == 'New Text') {
                                                                    if ($task->get_sellUnitType() == 'Hours')
                                                                        echo " selected=\"selected\" ";
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        elseif (isset($tempObj) && ($tempObj->get_linguisticSellType() == 'Hours'))
                                                            echo " selected=\"selected\" ";
                                                        ?>
                                                    >Hourly</option>
                                        </select>
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>Proofreading/OLR
                                        <input name="proofreading" type="text" size="20" maxlength="20"  value="<?PHP
                                                        if (is_object($xmlDoc))
                                                            print $xmlDoc->getElementsByTagName('proofreading')->item(0)->nodeValue;
                                                        elseif (isset($languageTbl) && (count($languageTbl) > 0)) {
                                                            foreach ($languageTbl[0]->get_tasks() as $task) {
                                                                if ($task->get_name() == 'Proofread') {
                                                                    echo $task->get_costUnits();
                                                                    break;
                                                                }
                                                            }
                                                        } elseif (isset($tempObj))
                                                            echo $tempObj->get_proofreading();
                                                        else
                                                            print '0';
                                                        ?>"/> <span class="instruction">(hours)</span>
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">
                                        <input name="lockunits" type="checkbox" value="lockunits" 
<?PHP
if (is_object($xmlDoc)) {
    if ($xmlDoc->getElementsByTagName('lockunits')->item(0)->nodeValue == 'lockunits')
        print " checked ";
}
elseif (isset($languageTbl) && (count($languageTbl) > 0)) {
    if ($languageTbl[0]->get_task(0)->get_unitsLocked())
        echo " checked ";
}
elseif (isset($tempObj)) {
    if ($tempObj->get_unitsLocked())
        echo " checked ";
} else
    print " checked ";
?>
                                               />			     
                                        Tasks and units are the same for all languages</td>
                                </tr>


                            </table>
                        </fieldset>

                        <fieldset><legend>Formatting</legend>
                            <table border="0" align="center" width="100%">
                                <tr>
                                    <td width="30%" align="right" valign="top"># pages</td>
                                    <td width="70%">
                                        <input id="pageNumber" name="pageNumber" type="text" size="10" maxlength="10" onChange="updateCostUnit(this.value);
                                                updateQA(this.value);" value="<?PHP
if (is_object($xmlDoc))
    print $xmlDoc->getElementsByTagName('pagenumber')->item(0)->nodeValue;
elseif (isset($estimateObj))
    echo $estimateObj->get_pages();
elseif (isset($tempObj))
    echo $tempOjb->get_numberPages();
else
    print '0';
?>"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right" valign="top"># pages/hr</td>
                                    <td>
                                        <input id="fmtPageHour" name="pageHour" type="text" size="10" maxlength="10" value="<?PHP
                                                        if (is_object($xmlDoc))
                                                            print $xmlDoc->getElementsByTagName('pagehour')->item(0)->nodeValue;
                                                        elseif (isset($estimateObj))
                                                            echo $estimateObj->get_pagesPerHour();
                                                        elseif (isset($tempObj))
                                                            echo $tempObj->get_pagesPerHour();
                                                        else
                                                            print '6';
?>" onChange="updateCostUnit(this.value);"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">DTP hours</td>
                                    <td>
                                        <input id="fmtHours" name="fmtHours" type="text" size="10" value="<?PHP
                                                        if (is_object($xmlDoc))
                                                            print $xmlDoc->getElementsByTagName('fmthours')->item(0)->nodeValue;
                                                        elseif (isset($languageTbl) && (count($languageTbl) > 0)) {
                                                            foreach ($languageTbl[0]->get_tasks() as $task) {
                                                                if ($task->get_name() == 'Formatting') {
                                                                    echo $task->get_costUnits();
                                                                    break;
                                                                }
                                                            }
                                                        } elseif (isset($tempObj))
                                                            echo $tempObj->get_dtpHours();
                                                        else
                                                            print '0';
?>"/><span class="instruction">per language</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">Cost per page</td>
                                    <td><input id="fmtCostPer" name="fmtCostPer" type="text" size="10" value="<?PHP
                                            if (is_object($xmlDoc))
                                                print $xmlDoc->getElementsByTagName('fmtcostper')->item(0)->nodeValue;
                                            elseif (isset($languageTbl) && (count($languageTbl) > 0)) {
                                                $found = false;
                                                $costPerPage = 0;
                                                foreach ($languageTbl as $lang) {
                                                    foreach ($lang->get_tasks() as $task) {
                                                        if (($task->get_name() == 'Formatting') && ($task->get_CostUnitType() == 'Pages')) {
                                                            $found = true;
                                                            $costPerPage = $task->get_costPerUnit();
                                                            break;
                                                        }
                                                    }
                                                    if ($found)
                                                        break;
                                                }
                                                echo $costPerPage;
                                            }
                                            elseif (isset($tempObj))
                                                echo $tempObj->get_costPerPage();
                                            else
                                                print '0';
                                            ?>" disabled="disabled"/></td>
                                </tr>
                                <td align="right">Cost Units</td>
                                <td>
                                    <select id="DTPCostunits" name="DTPCostunits" onChange="toggleDTP(this.options[this.selectedIndex].value);">
                                        <option value="pages"
                                            <?PHP
                                            if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('dtpcostunits')->item(0)->nodeValue == 'pages'))
                                                print ' selected ';
                                            elseif (isset($languageTbl) && (count($languageTbl) > 0)) {
                                                foreach ($languageTbl[0]->get_tasks() as $task) {
                                                    if (($task->get_name() == 'Formatting') && ($task->get_costUnitType() == 'Pages')) {
                                                        echo ' selected ';
                                                        break;
                                                    }
                                                }
                                            } elseif (isset($tempObj) && ($tempObj->get_dtpCostType() == 'pages'))
                                                echo ' selected ';
                                            ?>
                                                >Pages</option>

                                        <option value="hours" 
                                            <?PHP
                                            if (is_object($xmlDoc)) {
                                                if ($xmlDoc->getElementsByTagName('dtpcostunits')->item(0)->nodeValue == 'hours')
                                                    print ' selected ';
                                            }
                                            elseif (isset($languageTbl) && (count($languageTbl) > 0)) {
                                                foreach ($languageTbl[0]->get_tasks() as $task) {
                                                    if (($task->get_name() == 'Formatting') && ($task->get_costUnitType() == 'Hours')) {
                                                        echo ' selected ';
                                                        break;
                                                    }
                                                }
                                            } elseif (isset($tempObj)) {
                                                if ($tempObj->get_dtpCostType() == 'hours')
                                                    echo ' selected ';
                                            } else
                                                print ' selected ';
                                            ?>
                                                >Hours</option>
                                    </select>

                                </td>
                                </tr>
                                <tr>
                                    <td align="right">Sell Units</td>
                                    <td>
                                        <select id="DTPSellunits" name="DTPSellunits" onChange="toggleDTP(this.options[this.selectedIndex].value);">
                                            <option value="pages"
                                            <?PHP
                                            if (is_object($xmlDoc) && ($xmlDoc->getElementsByTagName('dtpsellunits')->item(0)->nodeValue == 'pages'))
                                                print ' selected ';
                                            elseif (isset($languageTbl) && (count($languageTbl) > 0)) {
                                                foreach ($languageTbl[0]->get_tasks() as $task) {
                                                    if (($task->get_name() == 'Formatting') && ($task->get_sellUnitType() == 'Pages')) {
                                                        echo ' selected ';
                                                        break;
                                                    }
                                                }
                                            } elseif (isset($tempObj) && ($tempObj->get_dtpSellType() == 'pages'))
                                                echo ' selected ';
                                            ?>
                                                    >Pages</option>
                                            <option value="hours" 
                                        <?PHP
                                        if (is_object($xmlDoc)) {
                                            if ($xmlDoc->getElementsByTagName('dtpsellunits')->item(0)->nodeValue == 'hours')
                                                print ' selected ';
                                        }
                                        elseif (isset($languageTbl) && (count($languageTbl) > 0)) {
                                            foreach ($languageTbl[0]->get_tasks() as $task) {
                                                if (($task->get_name() == 'Formatting') && ($task->get_sellUnitType() == 'Hours')) {
                                                    echo ' selected ';
                                                    break;
                                                }
                                            }
                                        } elseif (isset($tempObj)) {
                                            if ($tempObj->get_dtpSellType() == 'hours')
                                                echo ' selected ';
                                        } else
                                            print ' selected ';
                                        ?>
                                                    >Hours</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr><td colspan="2">&nbsp;</td></tr>

                                <tr>
                                    <td align="right" valign="top">Graphics (#) </td>
                                    <td><input name="engGraphNum" type="text" size="10" maxlength="10"  value="<?PHP
                                        if (is_object($xmlDoc))
                                            print $xmlDoc->getElementsByTagName('enggraphnum')->item(0)->nodeValue;
                                        elseif (isset($estimateObj))
                                            echo $estimateObj->get_numberOfGraphics();
                                        elseif (isset($tempobj))
                                            echo $tempObj->get_numberGraphics();
                                        else
                                            print '0';
                                        ?>"/></td>
                                </tr>
                                <tr>
                                    <td align="right" valign="top">Graphics / hr </td>
                                    <td><input name="engGraphHour" type="text" size="10" maxlength="10" value="<?PHP
                                        if (is_object($xmlDoc))
                                            print $xmlDoc->getElementsByTagName('enggraphhour')->item(0)->nodeValue;
                                        elseif (isset($tempObj))
                                            echo $tempObj->get_graphicsPerHour();
                                        else
                                            print '6';
                                        ?>"/></td>
                                </tr>
                                <tr><td colspan="2">&nbsp;</td></tr>
                                <tr>
                                    <td align="right" valign="top">DTP Coordination</td>
                                    <td>
                                        <input id="fmtCoord" name="fmtCoord" type="text" size="10" maxlength="10" value="<?PHP
                                        if (is_object($xmlDoc))
                                            print $xmlDoc->getElementsByTagName('fmtcoord')->item(0)->nodeValue;
                                        elseif (isset($tempobj))
                                            echo $tempObj->get_dtpCoordPercent();
                                        else
                                            print '10';
                                        ?>"/> Percent
                                    </td>
                                </tr>

                            </table>
                        </fieldset>

                        <fieldset><legend>Engineering</legend>
                            <table border="0" align="center" width="100%">
                                <tr>
                                    <td width="30%" align="right" valign="top">TM Work </td>
                                    <td width="70%">
                                        <input name="engTM" type="text" size="10" maxlength="10"  value="<?PHP
                                        if (is_object($xmlDoc))
                                            print $xmlDoc->getElementsByTagName('engtm')->item(0)->nodeValue;
                                        elseif (isset($languageTbl) && (count($languageTbl) > 0)) {
                                            foreach ($languageTbl[0]->get_tasks() as $task) {
                                                if ($task->get_name() == 'TM Work') {
                                                    echo $task->get_costUnits();
                                                    break;
                                                }
                                            }
                                        } elseif (isset($tempobj))
                                            echo $tempObj->get_tmWork();
                                        else
                                            print '0';
                                        ?>"/>
                                        <span class="instruction">hours per language</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right" valign="top">File Treatment </td>
                                    <td>
                                        <input name="engineer" type="text" size="10" maxlength="10"  value="<?PHP
                                        if (is_object($xmlDoc))
                                            print $xmlDoc->getElementsByTagName('engineer')->item(0)->nodeValue;
                                        elseif (isset($languageTbl) && (count($languageTbl) > 0)) {
                                            foreach ($languageTbl[0]->get_tasks() as $task) {
                                                if ($task->get_name() == 'File Treatment') {
                                                    echo $task->get_costUnits();
                                                    break;
                                                }
                                            }
                                        } elseif (isset($tempobj))
                                            echo $tempObj->get_fileTreatment();
                                        else
                                            print '0';
                                        ?>"/>
                                        <span class="instruction">per language hours</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right" valign="top">Screen Captures (#) </td>
                                    <td><input name="engScap" type="text" size="10" maxlength="10"  value="<?PHP
                                        if (is_object($xmlDoc))
                                            print $xmlDoc->getElementsByTagName('engscap')->item(0)->nodeValue;
                                        elseif (isset($estimateObj))
                                            echo $estimateObj->get_numberOfScaps();
                                        elseif (isset($tempobj))
                                            echo $tempObj->get_numberScaps();
                                        else
                                            print '0';
                                        ?>"/></td>
                                </tr>
                                <tr>
                                    <td align="right" valign="top">Screen Captures / hr </td>
                                    <td><input name="engScapHour" type="text" size="10" maxlength="10" value="<?PHP
                                        if (is_object($xmlDoc))
                                            print $xmlDoc->getElementsByTagName('engscaphour')->item(0)->nodeValue;
                                        elseif (isset($tempObj))
                                            echo $tempObj->get_scapsPerHour();
                                        else
                                            print '8';
                                        ?>"/></td>
                                </tr>

                            </table>

                        </fieldset>

                        <fieldset><legend>Quality Assurance</legend>
                            <table border="0" align="center" width="100%">
                                <tr>
                                    <td width="30%" align="right" valign="top"># pages/hr</td>
                                    <td width="70%">
                                        <input name="qaPagesHour" id="qaPagesHour" type="text" size="10" maxlength="10" value="<?PHP
                                        if (is_object($xmlDoc))
                                            print $xmlDoc->getElementsByTagName('qapageshour')->item(0)->nodeValue;
                                        elseif (isset($tempObj))
                                            echo $tempObj->get_qaPagesPerHour();
                                        else
                                            print '12';
                                        ?>" onchange="updateQA();"/></td>
                                </tr>
                                <tr>
                                    <td align="right" valign="top">Hours</td>
                                    <td>
                                        <input name="qaHours" id="qaHours" type="text" size="10" maxlength="10" value="<?PHP
                                        if (is_object($xmlDoc))
                                            print $xmlDoc->getElementsByTagName('qahours')->item(0)->nodeValue;
                                        elseif (isset($languageTbl) && (count($languageTbl) > 0)) {
                                            foreach ($languageTbl[0]->get_tasks() as $task) {
                                                if ($task->get_name() == 'Quality Assurance') {
                                                    echo $task->get_costUnits();
                                                    break;
                                                }
                                            }
                                        } elseif (isset($tempObj))
                                            echo $tempObj->get_qaHours();
                                        else
                                            print '0';
                                        ?>"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="30%" align="right" valign="top">QA Coordination</td>
                                    <td width="70%">
                                        <input name="qaCoord" type="text" size="10" maxlength="10" value="<?PHP
                                        if (is_object($xmlDoc))
                                            print $xmlDoc->getElementsByTagName('qacoord')->item(0)->nodeValue;
                                        elseif (isset($tempObj))
                                            echo $tempObj->get_qaCoordPercent();
                                        else
                                            print '10';
                                        ?>"/>percent
                                    </td>
                                </tr>
                            </table>
                        </fieldset>

                        <fieldset><legend>Project Management</legend>
                            <table border="0" align="center" width="100%">
    <!--				<tr>
                                            <td width="30%" align="right" valign="top"># of hours </td>
                                            <td width="70%"><input name="pmHours" type="text" size="10" maxlength="10"  value="
<?PHP
/* 	if(is_object($xmlDoc))
  print $xmlDoc->getElementsByTagName('pmhours')->item(0)->nodeValue;
  else
  print '0'; */
?>
                                                    "/><span class="instruction"> Used to determine our cost</span></td>
                                    </tr>  -->
                                <tr>
                                    <td align="right" valign="top">% </td>
                                    <td><input name="pmPercentage" type="text" size="10" maxlength="10"  value="<?PHP
                                            if (is_object($xmlDoc))
                                                print $xmlDoc->getElementsByTagName('pmpercentage')->item(0)->nodeValue;
                                            elseif (isset($languageTbl) && (count($languageTbl) > 0)) {
                                                foreach ($languageTbl[0]->get_tasks() as $task) {
                                                    if ($task->get_name() == 'Project Management') {
                                                        echo $task->get_pmPercent();
                                                        break;
                                                    }
                                                }
                                            } elseif (isset($tempObj))
                                                echo $tempObj->get_pmPercent();
                                            else
                                                print '10';
                                            ?>"/><span class="instruction"> Percent of sales price charged to client</span></td>
                                </tr>
                            </table>
                        </fieldset>

                        <fieldset><legend>Additional Tasks</legend>
                            <table border="0" align="center" width="100%" id="addl_cost_table">
                                            <?PHP
                                            //need to see if any additional tasks are included, and if so print new table rows.
                                            $services = array();
                                            if (is_object($xmlDoc)) {
                                                $addServices = $xmlDoc->getElementsByTagName('additionalservices')->item(0);
                                                foreach ($addServices->childNodes as $service) {
                                                    if ($service->nodeName != "#text") {
                                                        print "<tr><td align=\"right\">";
                                                        print str_replace("_", " ", $service->nodeName);
                                                        print "</td><td><input name=\"" . $service->nodeName . "\" ";
                                                        print "id=\"" . $service->nodeName . "\" ";
                                                        print "value=\"" . $service->nodeValue . "\" type=\"text\"> Hours</td></tr>\n";
                                                    }
                                                }
                                            } elseif (isset($languageTbl) && (count($languageTbl) > 0)) {
                                                foreach ($languageTbl[0]->get_tasks() as $task) {
                                                    if ($task instanceof customTask) {
                                                        echo '<tr><td align="right">', $task->get_name(), '</td>';
                                                        echo '<td><input name="', str_replace(" ", "_", $task->get_name()), '" ';
                                                        echo 'id="', str_replace(" ", "_", $task->get_name()), '" ';
                                                        echo 'value="', $task->get_costUnits(), '" type="text"> Hours</td></tr>';
                                                    }
                                                }
                                            } elseif (isset($tempObj)) {
                                                foreach ($tempObj->get_requestedServices() as $key => $value) {
                                                    if (!in_array(str_replace("_", " ", $key), $defaultTasks)) {
                                                        echo '<tr><td align="right">', str_replace("_", " ", $key), '</td>';
                                                        echo '<td><input name="', $key, '" ';
                                                        echo 'id="', $key, '" ';
                                                        echo 'value="', $value, '" type="text"> Hours</td></tr>';
                                                    }
                                                }
                                            }
                                            ?>
                                <tr>
                                    <td width="30%" align="right" valign="top">Cost 1 </td>
                                    <td width="70%" valign="top" align="left">
                                        <input name="addTask1" type="text" size="10" maxlength="10" 
                                        <?PHP
                                        $additionalTasks_d = array();
                                        $additionalTasks_c = array();
                                        if (is_object($xmlDoc))
                                            print " value=\"" . $xmlDoc->getElementsByTagName('addtask1')->item(0)->nodeValue . "\" ";
                                        elseif (isset($languageTbl) && (count($languageTbl) > 0)) {
                                            foreach ($languageTbl[0]->get_tasks() as $task) {
                                                if (!($task instanceof customTask) && !(in_array($task->get_name(), $taskNames))) {
                                                    $additionalTasks_c[] = $task->get_costPerUnit();
                                                    $additionalTasks_d[] = $task->get_name();
                                                }
                                            }

                                            if (count($additionalTasks_c) > 0)
                                                echo " value=\"", $additionalTasks_c[0], "\" ";
                                        }
                                        elseif (isset($tempObj))
                                            echo " value=\"", $tempObj->get_addTask1_cost(), "\" ";
                                        ?>
                                               /> Description 
                                        <input name="addDesc1" type="text" size="30"  
<?PHP
if (is_object($xmlDoc))
    print " value=\"" . $xmlDoc->getElementsByTagName('adddesc1')->item(0)->nodeValue . "\" ";
elseif (isset($languageTbl) && (count($languageTbl) > 0)) {
    if (count($additionalTasks_d) > 0) {
        echo " value=\"", $additionalTasks_d[0], "\" ";
    }
} elseif (isset($tempObj))
    echo " value=\"", $tempObj->get_addTask1_description(), "\" ";
?>
                                               />
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" align="right">Cost 2 </td>
                                    <td valign="top" align="left">
                                        <input name="addTask2" type="text" size="10" maxlength="10"  
                                        <?PHP
                                        if (is_object($xmlDoc))
                                            print " value=\"" . $xmlDoc->getElementsByTagName('addtask2')->item(0)->nodeValue . "\" ";
                                        elseif (isset($languageTbl) && (count($languageTbl) > 0)) {
                                            if (count($additionalTasks_c) > 1)
                                                echo " value=\"", $additionalTasks_c[1], "\" ";
                                        }
                                        elseif (isset($tempObj))
                                            echo " value=\"", $tempObj->get_addTask2_cost(), "\" ";
                                        ?>
                                               /> Description 
                                        <input name="addDesc2" type="text" size="30"  
<?PHP
if (is_object($xmlDoc))
    print " value=\"" . $xmlDoc->getElementsByTagName('adddesc2')->item(0)->nodeValue . "\" ";
elseif (isset($languageTbl) && (count($languageTbl) > 0)) {
    if (count($additionalTasks_d) > 1) {
        echo " value=\"", $additionalTasks_d[1], "\" ";
    }
} elseif (isset($tempObj))
    echo " value=\"", $tempObj->get_addTask2_description(), "\" ";
?>
                                               />
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" align="right">Cost 3 </td>
                                    <td valign="top" align="left">
                                        <input name="addTask3" type="text" size="10" maxlength="10"  
                                        <?PHP
                                        if (is_object($xmlDoc))
                                            print " value=\"" . $xmlDoc->getElementsByTagName('addtask3')->item(0)->nodeValue . "\" ";
                                        elseif (isset($languageTbl) && (count($languageTbl) > 0)) {
                                            if (count($additionalTasks_c) > 2)
                                                echo " value=\"", $additionalTasks_c[2], "\" ";
                                        }
                                        elseif (isset($tempObj))
                                            echo " value=\"", $tempObj->get_addTask3_cost(), "\" ";
                                        ?>
                                               /> Description 
                                        <input name="addDesc3" type="text" size="30"  
                                        <?PHP
                                        if (is_object($xmlDoc))
                                            print " value=\"" . $xmlDoc->getElementsByTagName('adddesc3')->item(0)->nodeValue . "\" ";
                                        elseif (isset($languageTbl) && (count($languageTbl) > 0)) {
                                            if (count($additionalTasks_d) > 2) {
                                                echo " value=\"", $additionalTasks_d[2], "\" ";
                                            }
                                        } elseif (isset($tempObj))
                                            echo " value=\"", $tempObj->get_addTask3_description(), "\" ";
                                        ?>
                                               />
                                    </td>
                                </tr>
                            </table>
                        </fieldset></div>
                </div>
                <div id="archiveForm">
                    <fieldset><legend>Archive Retrieval</legend>
                        <table border="0" align="center" width="100%">
                            <tr>
                                <td width="30%" align="right" valign="top">@task number:</td>
                                <td width="70%"><input name="ret_attask" type="text"  /></td>
                            </tr>
                            <tr>
                                <td align="right" valign="top">Ballpark number:</td>
                                <td><input name="ret_ballpark" type="text" /></td>
                            </tr>
                            <tr>
                                <td align="right" valign="top">Client Name:</td>
                                <td><input name="ret_clientName" type="text" /></td>
                            </tr>
                            <tr>
                                <td align="right" valign="top">Client Number:</td>
                                <td><input name="ret_clientNumber" type="text" /></td>
                            </tr>
                            <tr>
                                <td align="right" valign="top">Project Number:</td>
                                <td><input name="ret_projNumber" type="text" /></td>
                            </tr>
                        </table>
                    </fieldset>
                </div>
                <div id="buttons"><input name="submit" type="submit" value="Proceed" /><br /><input name="save" type="submit" value="Save for later" onclick="changeAction('saveinprogress.php');"/></div>
            </form>
            <span style="text-align:center"><form action="uploader.php" method="post" name="loader"><input type="submit" name="load" value="Open Saved" /></form></span>
            <span style="text-align:center"><form action="reset.php" method="post" name="reset"><input type="submit" name="reset" value="Start Over" /></form></span>


        </div>
    </body>
</html>
