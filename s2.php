<?PHP

include_once("definitions.php");
require_once("class_language.php");
require_once("class_estimate.php");
require_once("class_contact.php");
require_once("class_custom_task.php");
require_once("class_tempData.php");

//check to see if we're logged in
session_start();
require_once('uuid.php');

if (!isset($_SESSION['userID'])) {
    header('location:login.php?location=ballpark');
    exit;
} elseif ((!isset($_SESSION['appUUID'])) || ($_SESSION['appUUID'] != $app_UUID)) {
    header('location:login.php?err=6&location=ballpark');
    exit;
}


header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=quote.html");

$estimate = $_SESSION['estimate'];
$languageTbl = $_SESSION['languageTbl'];
$lingoContact = $_SESSION['lingoContact'];
$lingoPM = $_SESSION['pm'];

print "<html><head><link href=\"http://www.llts.com/estimator/main.css\" rel=\"stylesheet\" type=\"text/css\" /></head>";
print "<body>";

print("<fieldset><legend>Estimate</legend>\n");
print("<table border=0>\n");
print("<tr><td>Project ID:</td><td>" . $estimate->get_projectID() . "</td></tr>\n");
print("<tr><td>Client:</td><td>" . $estimate->get_clientName() . "</td></tr>\n");

print("<tr><td>Lingo Representative:</td><td>" . $lingoContact->get_name() . "</td></tr>\n");
print("<tr><td>Project Manager</td><td>" . $lingoPM->get_name() . "</td></tr>\n");
print("<tr><td>Date Prepared:</td><td>" . $estimate->get_estimateDate() . "</td></tr>\n");
print("<tr><td>Project:</td><td>" . $estimate->get_projectName() . "</td></tr>\n");
print("<tr><td>Project Type:</td><td>" . $estimate->get_projectType() . "</td></tr>\n");
print("<tr><td>File Type:</td><td>" . $estimate->get_fileType() . "</td></tr>\n");
print("<tr><td>Deliverable:</td><td>" . $estimate->get_deliverable() . "</td></tr>\n");
print("<tr><td>Estimated Delivery Date:</td><td>" . $estimate->get_deliveryDate() . "</td></tr>\n");
print("<tr><td valign=\"top\">Project Description:</td><td><textarea name=\"projDesc\" cols=\"70\" rows=\"4\" readonly>" . $estimate->get_projDesc() . "</textarea></td></tr>\n");
print("<tr><td>Billing Terms:</td><td>" . $estimate->get_billingTerms() . "</td></tr>\n");
print("<tr><td>Billing Cycle:</td><td>" . $estimate->get_billingCycle() . "</td></tr>\n");
print("</table></fieldset>\n");


print("<fieldset><legend>Requested Services</legend>\n");
print("<table border=0>\n");
print("<tr><td><ul>");
foreach ($estimate->get_services() as $service) {
    print("<li>$service</li>");
}
print("</ul></td></tr></table></fieldset>\n");


for ($lcv = 0; $lcv < count($estimate->get_targetLanguages()); $lcv++) {

    if ($languageTbl[$lcv]->get_error() == TRUE) {
        echo "<fieldset><legend>", $languageTbl[$lcv]->get_targetLang(), "</legend>\n",
        "<p>Error: Language does not exist in cost tables. Please enter appropriate cost data in the database first. Language skipped.</p>\n",
        "</fieldset>\n";
    } else {
        echo "<fieldset><legend>", $languageTbl[$lcv]->get_targetLang();
        if ($languageTbl[$lcv]->contains_custom()) {
            echo " - using custom pricing";
        }

        echo "</legend>";



        echo ("<div id='language$lcv' ><table border=1 bgcolor='#FFFFFF'>
				<tr>
				<th colspan=\"2\">Printable</th>\n
				<th>&nbsp;</th>\n
				<th># of Units</th>\n
				<th>Unit Type</th>\n
				<th>Price/Unit</th>\n
				<th>Cost</th>\n
				<th>% Margin</th>\n
				<th>Calculated<br />Sell Price<br />per Unit</th>\n
				<th>Actual<br />Sell Price<br />per Unit</th>\n
				<th>Actual<br />Sell Price</th>\n
				<th>Actual GM%</th>\n
				</tr>");


        for ($columns = 0; $columns < count($languageTbl[$lcv]->get_Tasks()); $columns++) {
            switch ($columns) {
                case NEWTEXT:
                    echo "<tr><td colspan=\"2\">&nbsp;</td><td colspan=10 style=\"background-color:#CC3300;color:#FFFFFF\"><b>Linguistic</b></td></tr>";
                    break;
                case FORMAT - 1:
                    echo "<tr><td colspan=\"2\" style=\"background-color:#AAAAAA\">&nbsp;</td><td>Total words:</td><td><span id=\"totalwords\">",
                    number_format($languageTbl[$lcv]->total_words()),
                    "</span></td><td colspan=9 style=\"background-color:#AAAAAA\">&nbsp;</td></tr>";
                    break;
                case FORMAT:
                    echo "<tr><td colspan=\"2\">&nbsp;<td colspan=10 style=\"background-color:#CC3300;color:#FFFFFF\"><b>DTP</b></td></tr>";
                    break;
                case TMWORK:
                    echo "<tr><td colspan=\"2\">&nbsp;<td colspan=10 style=\"background-color:#CC3300;color:#FFFFFF\"><b>Engineering</b></td></tr>";
                    break;
                case QA:
                    echo "<tr><td colspan=\"2\">&nbsp;<td colspan=10 style=\"background-color:#CC3300;color:#FFFFFF\"><b>Quality Assurance</b></td></tr>";
                    break;
                case ADD1:
                    echo "<tr><td colspan=\"2\">&nbsp;<td colspan=10 style=\"background-color:#CC3300;color:#FFFFFF\"><b>Additional Efforts</b></td></tr>";
                    break;
                case PM:
                    print("<tr><td colspan=\"2\">&nbsp;</td><td colspan=10 style=\"background-color:#CC3300;color:#FFFFFF\"><b>Project Management</b></td></tr>");
                    break;
                case PM + 1:
                    print("<tr><td colspan=\"2\">&nbsp;</td><td colspan=10 style=\"background-color:#CC3300;color:#FFFFFF\"><b>Custom Tasks</b></td></tr>");
                    break;
            }

            if ($columns == PM)
                $languageTbl[$lcv]->get_task($columns)->print_table_row($lcv, $columns, $languageTbl[$lcv], false);
            else
                $languageTbl[$lcv]->get_task($columns)->print_table_row($lcv, $columns, false);
        }

        print("<tr><td colspan=12 style=\"background-color:#AAAAAA\">&nbsp;</td></tr><tr><td colspan=6><strong>Total</strong></td>");
        $grandTotal[COST] = 0;
        $grandTotal[ACT_SELL] = 0;
        for ($row = 0; $row < count($languageTbl[$lcv]->get_Tasks()); $row++) {
            $grandTotal[COST] += $languageTbl[$lcv]->get_task($row)->get_cost();
            if ($languageTbl[$lcv]->get_task($row)->get_name() == "Project Management")
                $grandTotal[ACT_SELL] += $languageTbl[$lcv]->get_task($row)->get_actualSellPrice($languageTbl[$lcv]);
            else
                $grandTotal[ACT_SELL] += $languageTbl[$lcv]->get_task($row)->get_actualSellPrice();
        }


        $grandTotal[CALC_SELL] = ($languageTbl[$lcv]->total_words() == 0) ? 0 : (round(($grandTotal[ACT_SELL] / $languageTbl[$lcv]->total_words()), 2));
        $grandTotal[COST] = round($grandTotal[COST], 2);
        $grandTotal[ACT_SELL] = round($grandTotal[ACT_SELL], 2);

        print("<td><input class=\"noneditable\" name=\"costTotal-" . $lcv . "\" type=\"text\" readonly size=\"10\" id=\"costTotal-" . $lcv . "\" value=\"" . number_format($grandTotal[COST], 2) . "\" style=\"text-align:right\"/></td>\n");
        print("<td align=right class=\"instruction\">Sell Price<br>per word</td><td><input class=\"noneditable\" name=\"cspTotal-" . $lcv . "\" type=\"text\" readonly size=\"8\" id=\"cspTotal-" . $lcv . "\" value=\"" . number_format($grandTotal[CALC_SELL], 2) . "\" style=\"text-align:right\" /></td>\n<td>&nbsp;</td>\n");
        print("<td><input class=\"noneditable\" name=\"aspTotal-" . $lcv . "\" type=\"text\" readonly size=\"10\" id=\"aspTotal-" . $lcv . "\" value=\"" . number_format($grandTotal[ACT_SELL], 2) . "\" style=\"text-align:right\"/></td>\n");

        $abc = ($grandTotal[ACT_SELL] == 0) ? 0 : (round((($grandTotal[ACT_SELL] - $grandTotal[COST]) / $grandTotal[ACT_SELL]) * 100, 2));
        print("<td><input class=\"noneditable\" id=\"lang_total_GM-" . $lcv . "\" name=\"lang_total_GM-" . $lcv . "\" type=\"text\" readonly size=\"8\" value=\"" . $abc . "\"></div></tr>");
        print("</table></div></fieldset>\n");
    } // end of language table printing
} //end of language for loop
//print the sell price table
print("<fieldset><legend>Summary</legend>\n<div id='summary'>");
print("<fieldset id='sellPriceField'><legend id='sellPriceLegend'>Sell Price</legend><table border=1 bgcolor='#FFFFFF'>\n");
print("<tr>\n<th width=\"125px\">&nbsp;</th>");
for ($lcv = 0; $lcv < count($estimate->get_targetLanguages()); $lcv++) {
    if ($languageTbl[$lcv]->get_error() <> TRUE)
        echo "<th width=\"80px\">", $languageTbl[$lcv]->get_targetLang(), "<br />Sell Price</th>";
}
print("<th width=\"80px\">Total<br />Sell Price</th>\n</tr>\n");
$columns = count($estimate->get_targetLanguages()) + 2;

//to build the  sell price table, we loop through each task, and get the Language's task value
//to construct the table rows
$SumCol = array();
$SumCol[0] = 0;
for ($row = 0; $row < count($languageTbl[0]->get_Tasks()); $row++) {

    switch ($row) {
        case 0: print("<tr><td colspan==$columns><b>Wordcount</b></td></tr>");
            break;
        case FORMAT: print("<tr><td colspan==$columns><b>DTP</b></td></tr>");
            break;
        case TMWORK: print("<tr><td colspan==$columns><b>Engineering</b></td></tr>");
            break;
        case QA: print("<tr><td colspan==$columns><b>Quality Assurance</b></td></tr>");
            break;
        case ADD1: print("<tr><td colspan==$columns><b>Additional Efforts</b></td></tr>");
            break;
        case PM: print("<tr><td colspan==$columns><b>Project Management</b></td></tr>");
            break;
        case PM + 1: print("<tr><td colspan==$columns><b>Custom Tasks</b></td></tr>");
            break;
    }
    $SumRow = 0;
    print ("<tr>\n<td>" . $languageTbl[0]->get_task($row)->get_name() . "</td>");

    //loop through each language, and get the task items and build the row
    for ($col = 0; $col < count($estimate->get_targetLanguages()); $col++) {
        if ($languageTbl[$col]->get_error() <> TRUE) {
            $temp_sellprice = 0;
            if ($languageTbl[$col]->get_task($row)->get_name() == "Project Management") {
                $temp_sellprice = $languageTbl[$col]->get_task($row)->get_actualSellPrice($languageTbl[$col]);
                $SumRow += $languageTbl[$col]->get_task($row)->get_actualSellPrice($languageTbl[$col]);
                $SumCol[$col] += $languageTbl[$col]->get_task($row)->get_actualSellPrice($languageTbl[$col]);
            } else {
                $temp_sellprice = $languageTbl[$col]->get_task($row)->get_actualSellPrice();
                $SumRow += $languageTbl[$col]->get_task($row)->get_actualSellPrice();
                $asp = $languageTbl[$col]->get_task($row)->get_actualSellPrice();
                $SumCol[$col] += $asp;
            }

            print ("<td align=\"right\"><input name=\"SPT-" . $col . "-" . $row . "\" type=\"text\" readonly size=\"10\" id=\"SPT-" . $col . "-" . $row . "\" value=\"" . number_format($temp_sellprice, 2) . "\" style=\"text-align:right\"/></td>");
        }
    }
    print ("<td align=\"right\"><input name=\"SPT_TTOTAL-" . $row . "\" type=\"text\" readonly size=\"10\" id=\"SPT_TTOTAL-" . $row . "\" value=\"" . number_format($SumRow, 2) . "\" style=\"text-align:right\"/></td>\n</tr>\n");
}

print("<tr><td colspan=$columns>&nbsp;</td></tr>");

//calculate the grand total
$grandTotal = 0;
foreach ($languageTbl as $language) {
    if ($language->get_error() != TRUE)
        $grandTotal += $language->sellprice();
}

//check if rush fees apply, if so add them
$rushFeeAmount = 0;
if ($estimate->get_rushfee()) {
    print("<tr><td >Rush Fee</td>");
    $rushFeeAmount = $grandTotal * $estimate->get_rushFeeMultiplier();
    foreach ($languageTbl as $language)
        if ($language->get_error() != TRUE)
            print("<td>&nbsp;</td>");
    print("<td id='rushfee' align=\"right\">" . number_format($rushFeeAmount, 2) . "</td></tr>");
}

//check if discounts apply, if so subtract them
$discountAmount = 0;
if ($estimate->get_discountType() != "none") {
    print("<tr><td>Discount</td>");
    switch ($estimate->get_discountType()) {
        case "percent":
            $discountPercent = $estimate->get_discountPercent() / 100;
            $discountAmount = $grandTotal * $discountPercent;
            break;
        case "fixed":
            $discountAmount = $estimate->get_discountAmount();
            break;
    }
    foreach ($languageTbl as $language)
        if ($language->get_error() != TRUE)
            print("<td>&nbsp;</td>");
    print("<td id='discount' align=\"right\">" . number_format($discountAmount, 2) . "</td></tr>");
}




print("<tr><td><strong>Total</strong></td>");
for ($lang = 0; $lang < count($estimate->get_targetLanguages()); $lang++) {
    if ($languageTbl[$lang]->get_error() <> TRUE) {
        print("<td align=\"right\"><input name=\"SPT_LTOTAL-" . $lang . "\" type=\"text\" readonly size=\"10\" id=\"SPT_LTOTAL-" . $lang . "\" value=\"" . number_format($SumCol[$lang], 2) . "\" style=\"text-align:right\" /></td>");
    }
}
print("<td align=\"right\"><input name=\"SPT_GTOTAL\" type=\"text\" readonly size=\"10\" id=\"SPT_GTOTAL\" value=\"" . number_format($grandTotal + $rushFeeAmount - $discountAmount, 2) . "\" style=\"text-align:right\" /></td></tr>");

print("</table></fieldset>\n");

//print the cost table section
print ("<fieldset id='costField'><legend id='costLegend'>Cost</legend><table border=1 bgcolor='#FFFFFF'>\n");
print ("<tr>\n<th width=\"125px\">&nbsp;</th>");
for ($lcv = 0; $lcv < count($estimate->get_targetLanguages()); $lcv++) {
    if ($languageTbl[$lcv]->get_error() <> TRUE)
        echo "<th width=\"80px\">", $languageTbl[$lcv]->get_targetLang(), "<br />Cost</th>";
}
print("<th width=\"80px\">Total<br />Cost</th>\n</tr>\n");
$columns = count($estimate->get_targetLanguages()) + 2;


for ($row = 0; $row < count($languageTbl[0]->get_Tasks()); $row++) {
    switch ($row) {
        case 0: print("<tr><td colspan==$columns><b>Wordcount</b></td></tr>");
            break;
        case FORMAT: print("<tr><td colspan==$columns><b>DTP</b></td></tr>");
            break;
        case TMWORK: print("<tr><td colspan==$columns><b>Engineering</b></td></tr>");
            break;
        case QA: print("<tr><td colspan==$columns><b>Quality Assurance</b></td></tr>");
            break;
        case ADD1: print("<tr><td colspan==$columns><b>Additional Efforts</b></td></tr>");
            break;
        case PM: print("<tr><td colspan==$columns><b>Project Management</b></td></tr>");
            break;
        case PM + 1: print("<tr><td colspan==$columns><b>Custom Tasks</b></td></tr>");
            break;
    }


    $SumRow = 0;
    print ("<tr>\n<td>" . $languageTbl[0]->get_task($row)->get_name() . "</td>");

    for ($lang = 0; $lang < count($estimate->get_targetLanguages()); $lang++) {
        if ($row == 0)
            $SumCol[$lang] = 0;

        if ($languageTbl[$lang]->get_error() <> TRUE) {
            print ("<td align=\"right\"><div id=\"CT-" . $lang . "-" . $row . "\">" . number_format($languageTbl[$lang]->get_task($row)->get_cost(), 2) . "</div></td>");
            $SumRow += $languageTbl[$lang]->get_task($row)->get_cost();
            $SumCol[$lang] += $languageTbl[$lang]->get_task($row)->get_cost();
        }
    }
    print ("<td align=\"right\"><div id=\"CT_TTOTAL-" . $row . "\">" . number_format($SumRow, 2) . "</div></td>\n</tr>\n");
}

print("<tr><td colspan=$columns>&nbsp;</td></tr><tr><td><strong>Total</strong></td>");
$grandTotal = 0;
for ($lang = 0; $lang < count($estimate->get_targetLanguages()); $lang++) {
    if ($languageTbl[$lang]->get_error() <> TRUE) {
        print("<td align=\"right\"><div id=\"langCostTotal-" . $lang . "\">" . number_format($SumCol[$lang], 2) . "</div></td>");
        $grandTotal += $SumCol[$lang];
    }
}
print("<td id='totalCost' align=\"right\">" . number_format($grandTotal, 2) . "</td></tr>");

print("</table></fieldset>\n");


//print the gross margin %
print("<fieldset id='gmField'><legend id='gmLegend'>Gross Margin</legend><table border=1 bgcolor='#FFFFFF'>\n");
print("<tr>\n<td width=\"125px\">%</td>");

$gtSell = 0;
$gtCost = 0;
for ($lang = 0; $lang < count($estimate->get_targetLanguages()); $lang++) {
    if ($languageTbl[$lang]->get_error() != TRUE) {
        $langSell = $languageTbl[$lang]->sellprice();
        $langCost = $languageTbl[$lang]->language_cost();

        $gtSell += $langSell;
        $gtCost += $langCost;

        print("<td width=\"80px\" align=\"right\"><div id=\"GM-" . $lang . "\">" . round((($langSell - $langCost) / $langSell) * 100, 2) . "</div></td>");
    }
}



$gtSell += $rushFeeAmount;
$gtSell -= $discountAmount;
print("<td width=\"80px\" align=\"right\"><div id=\"GM_Grand_Total\">" . round((($gtSell - $gtCost) / $gtSell) * 100, 2) . "</div></td>");

print("\n</tr></table></fieldset>\n");

print("</div></fieldset>\n");

print "</body></html>";
?>
