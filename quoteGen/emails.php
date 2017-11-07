<?PHP

//error_reporting(0);
//ini_set("include_path", "c:/xampp/php/pear/includes");
ini_set("include_path", "c:/php/includes"); //ob
require("class.phpmailer.php");
include_once("D:\Systems\web_includes\mailer.inc");

//session_start();

$projectData = unserialize($_SESSION['projectData']);
$thisProject = unserialize($_SESSION['thisProject']);
$projectObj = unserialize($_SESSION['projectObj']);


//check to see if rates have been unlocked.
$ratesText = "";
if ($projectData->get_ratesUnlocked()) {
    $ratesText = "Cost rate fields were unlocked\n";
}

//check to see if minimum linguistic rate was overridden
$minimumText = "";
foreach ($thisProject as $srcLang => $bySource) {
    if ($srcLang != 'nonDistributed') {
        foreach ($bySource as $tgtLang => $byTarget) {
            foreach ($byTarget['linguistTasks'] as $lt) {
                if ($lt->totalWordCount() > 0) {
                    if (($lt->cost('new') + $lt->cost('fuzzy') + $lt->cost('match')) < $lt->get_minimum()) {
                        if (($lt->get_defaultToMinimum() == true) && ($lt->get_buyUnits() == 'words')) {
                            $minimumText .= $lt->get_name() . " was below defined minimum, but set to word based rate\n";
                        }
                    }
                }
            }
        }
    }
}

//pages used as a unit for DTP task
$dtpText = "";
foreach ($thisProject as $srcLang => $bySource) {
    if ($srcLang == 'nonDistributed') {
        foreach ($bySource as $nonDist) {
            if (get_class($nonDist) == 'dtpTask') {
                if (($nonDist->get_costUnits() == 'pages') || ($nonDist->get_sellUnits() == 'pages')) {
                    $dtpText .= $nonDist->get_name() . " uses pages for ";
                    if ($nonDist->get_costUnits() == 'pages')
                        $dtpText .= "(buy) ";
                    if ($nonDist->get_sellUnits() == 'pages')
                        $dtpText .= "(sell) ";
                    $dtpText .= "units\n";
                }
            }
        }
    }
    else {
        foreach ($bySource as $tgtLang => $byTarget) {
            foreach ($byTarget['billableTasks'] as $bt) {
                if (get_class($bt) == 'dtpTask') {
                    if (($bt->get_buyUnits() == 'pages') || ($bt->get_sellUnits() == 'pages')) {
                        $dtpText .= $tgtLang . " " . $bt->get_name() . " uses pages for ";
                        if ($bt->get_buyUnits() == 'pages')
                            $dtpText .= "(buy) ";
                        if ($bt->get_sellUnits() == 'pages')
                            $dtpText .= "(sell) ";
                        $dtpText .= "units\n";
                    }
                }
            }
        }
    }
}


//check if actuall sell price per is overridden
$asppText = "";
foreach ($thisProject as $srcLang => $bySource) {
    if ($srcLang == 'nonDistributed') {
        foreach ($bySource as $nonDist) {
            if ((get_class($nonDist) != 'pmTask') && ($nonDist->usesCustom())) {
                if ($nonDist->get_dbASPP() != -1) {
                    if ($nonDist->aspp() != $nonDist->get_dbASPP()) {
                        $asppText .= "User has overridden the custom sell price for " . $nonDist->get_name();
                        $asppText .= ". Database value: " . number_format($nonDist->get_dbASPP(), 2);
                        $asppText .= " Value used: " . number_format($nonDist->aspp(), 2) . "\n";
                    }
                } else {
                    $asppText .= "User has entered a custom sell price for " . $nonDist->get_name();
                    $asppText .= ". Calculated value: " . number_format($nonDist->calcAspp(), 2);
                    $asppText .= " Value used: " . number_format($nonDist->aspp(), 2) . "\n";
                }
            }
        }
    } else {
        foreach ($bySource as $tgtLang => $byTarget) {
            foreach ($byTarget['linguistTasks'] as $lingTask) {
                if ($lingTask->get_sellUnits() == 'words') {
                    if ($lingTask->usesCustom('new')) {
                        if ($lingTask->get_dbASPP('new') != -1) {
                            if ($lingTask->aspp('new') != $lingTask->get_dbASPP('new')) {
                                $asppText .= "User has overridden the custom sell price for " . $lingTask->get_name() . " - New Text";
                                $asppText .= ". Database value: " . number_format($lingTask->get_dbASPP('new'), 2);
                                $asppText .= " Value used: " . number_format($lingTask->aspp('new'), 2) . "\n";
                            }
                        } else {
                            $asppText .= "User has entered a custom sell price for " . $lingTask->get_name() . " - New Text";
                            $asppText .= ". Calculated value: " . number_format($lingTask->calcAspp('new'), 2);
                            $asppText .= " Value used: " . number_format($lingTask->aspp('new'), 2) . "\n";
                        }
                    }

                    if ($lingTask->usesCustom('fuzzy')) {
                        if ($lingTask->get_dbASPP('fuzzy') != -1) {
                            if ($lingTask->aspp('fuzzy') != $lingTask->get_dbASPP('fuzzy')) {
                                $asppText .= "User has overridden the custom sell price for " . $lingTask->get_name() . " - Fuzzy Text";
                                $asppText .= ". Database value: " . number_format($lingTask->get_dbASPP('fuzzy'), 2);
                                $asppText .= " Value used: " . number_format($lingTask->aspp('fuzzy'), 2) . "\n";
                            }
                        } else {
                            $asppText .= "User has entered a custom sell price for " . $lingTask->get_name() . " - Fuzzy Text";
                            $asppText .= ". Calculated value: " . number_format($lingTask->calcAspp('fuzzy'), 2);
                            $asppText .= " Value used: " . number_format($lingTask->aspp('fuzzy'), 2) . "\n";
                        }
                    }

                    if ($lingTask->usesCustom('match')) {
                        if ($lingTask->get_dbASPP('match') != -1) {
                            if ($lingTask->aspp('match') != $lingTask->get_dbASPP('match')) {
                                $asppText .= "User has overridden the custom sell price for " . $lingTask->get_name() . " - 100% Match / Reps Text";
                                $asppText .= ". Database value: " . number_format($lingTask->get_dbASPP('match'), 2);
                                $asppText .= " Value used: " . number_format($lingTask->aspp('match'), 2) . "\n";
                            }
                        } else {
                            $asppText .= "User has entered a custom sell price for " . $lingTask->get_name() . " - 100% Match / Reps Text";
                            $asppText .= ". Calculated value: " . number_format($lingTask->calcAspp('match'), 2);
                            $asppText .= " Value used: " . number_format($lingTask->aspp('match'), 2) . "\n";
                        }
                    }
                } else {
                    if ($lingTask->usesCustom('hourly')) {
                        if ($lingTask->get_dbASPP('hourly') != -1) {
                            if ($lingTask->aspp('hourly') != $lingTask->get_dbASPP('hourly')) {
                                $asppText .= "User has overridden the custom sell price for " . $lingTask->get_name();
                                $asppText .= ". Database value: " . number_format($lingTask->get_dbASPP('hourly'), 2);
                                $asppText .= " Value used: " . number_format($lingTask->aspp('hourly'), 2) . "\n";
                            }
                        } else {
                            $asppText .= "User has entered a custom sell price for " . $lingTask->get_name();
                            $asppText .= ". Calculated value: " . number_format($lingTask->calcAspp('hourly'), 2);
                            $asppText .= " Value used: " . number_format($lingTask->aspp('hourly'), 2) . "\n";
                        }
                    }
                }
            }
            foreach ($byTarget['billableTasks'] as $billTask) {
                if ((get_class($billTask) != 'pmTask') && ($billTask->usesCustom())) {
                    if ($billTask->get_dbASPP() != -1) {
                        if ($billTask->aspp() != $billTask->get_dbASPP()) {
                            $asppText .= "User has overridden the custom sell price for " . $billTask->get_name();
                            $asppText .= ". Database value: " . number_format($billTask->get_dbASPP(), 2);
                            $asppText .= " Value used: " . number_format($billTask->aspp(), 2) . "\n";
                        }
                    } else {
                        $asppText .= "User has entered a custom sell price for " . $billTask->get_name();
                        $asppText .= ". Calculated value: " . number_format($billTask->calcAspp(), 2);
                        $asppText .= " Value used: " . number_format($billTask->aspp(), 2) . "\n";
                    }
                }
            }
        }
    }
}

//prepare email to send.
if (($ratesText != "") || ($minimumText != "") || ($dtpText != "") || ($asppText != "")) {
    $mail = new PHPMailer();
    $mail->IsSMTP();                                   // send via SMTP
    $mail->Host = $Mail_Host; // SMTP servers
    $mail->SMTPAuth = false;     // turn on SMTP authentication
    $mail->Username = $Mail_User;  // SMTP username
    $mail->Password = $Mail_Pass; // SMTP password
    $mail->WordWrap = 50;
    $mail->IsHTML(true);


    $mail->From = 'lingoweb@llts.com';
    $mail->FromName = 'Quote Tool Emailer';
    $mail->AddAddress('djoyce@llts.com');
    $mail->AddAddress('emanning@llts.com');

    $mail->AddAddress('mvangrunsven@llts.com');
    $mail->Subject = 'Notice: Non-conforming project estimate saved';

    $mail->Body = "
	<style type='text/css'>
	<!--
	td{ 
	font-family:verdana;
	font-size:11;
	}
	
	--></style>
	<table bgcolor='#D7ECEA' border='1'>
		<tr>
			<td>Project ID: " . $projectObj->id . "</td>
		</tr>
		<tr>
			<td>Project Name: " . $projectObj->name . "</td>
		</tr>
		<tr>
			<td><pre>" . $ratesText . $minimumText . $dtpText . $asppText . "</pre></td>
		</tr>
	</table>";

    $mail->AltBody = "Project ID: " . $projectObj->id . "\n
Project Name: " . $projectObj->name . "\n
" . $ratesText . "\n
" . $minimumText . "\n
" . $dtpText . "\n
" . $asppText;

    //send email
    $result = $mail->Send();
}
