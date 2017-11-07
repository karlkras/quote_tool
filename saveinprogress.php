<?PHP

//check to see if we're logged in
session_start();
require_once('uuid.php');

if (!isset($_SESSION['userID']))
{
	header('location:login.php?location=ballpark');
	exit;
}
elseif ((!isset($_SESSION['appUUID'])) || ($_SESSION['appUUID'] != $app_UUID))
{
	header('location:login.php?err=6&location=ballpark');
	exit;
}


//create a DOM document to represent our XML data
$doc = new DOMDocument();
$doc->formatOutput = true;


//create root element
$root = $doc->createElement("estimateform");
$doc->appendChild($root);



$quoteType = $_POST['quoteType'];

$qtNode = $doc->createElement("quotetype");
$qtNode->appendChild($doc->createTextNode($quoteType));
$root->appendChild($qtNode);


$estDate = $_POST['estDate'];

$estDateNode = $doc->createElement("estdate");
$estDateNode->appendChild($doc->createTextNode($estDate));
$root->appendChild($estDateNode);

$projectID = $_POST['projectid'];

$projIDNode = $doc->createElement("projectid");
$projIDNode->appendChild($doc->createTextNode($projectID));
$root->appendChild($projIDNode);


$rep = $_POST['rep'];

$repNode = $doc->createElement("rep");
$repNode->appendChild($doc->createTextNode($rep));
$root->appendChild($repNode);


$pm = $_POST['pm'];

$pmNode = $doc->createElement("pm");
$pmNode->appendChild($doc->createTextNode($pm));
$root->appendChild($pmNode);

$cusType = $_POST['cusType'];

$custTypeNode = $doc->createElement("custype");
$custTypeNode->appendChild($doc->createTextNode($cusType));
$root->appendChild($custTypeNode);


$cusName = $_POST['cusName'];

$cusNameNode = $doc->createElement("cusName");
$cusNameNode->appendChild($doc->createTextNode($cusName));
$root->appendChild($cusNameNode);


$prosName = $_POST['prosName'];

$prosNameNode = $doc->createElement("prosname");
$prosNameNode->appendChild($doc->createTextNode($prosName));
$root->appendChild($prosNameNode);


$projName = $_POST['projName'];

$projNameNode = $doc->createElement("projname");
$projNameNode->appendChild($doc->createTextNode($projName));
$root->appendChild($projNameNode);


$projType = $_POST['projType'];

$projTypeNode = $doc->createElement("projtype");
$projTypeNode->appendChild($doc->createTextNode($projType));
$root->appendChild($projTypeNode);


$ptOtherText = $_POST['ptOtherText'];
$ptOtherNode = $doc->createElement('ptothertext');
$ptOtherNode->appendChild($doc->createTextNode($ptOtherText));
$root->appendChild($ptOtherNode);


$fileType = $_POST['fileType'];

$fileTypeNode = $doc->createElement("filetype");
$fileTypeNode->appendChild($doc->createTextNode($fileType));
$root->appendChild($fileTypeNode);


$deliverable = $_POST['deliverable'];

$deliverableNode = $doc->createElement("deliverable");
$deliverableNode->appendChild($doc->createTextNode($deliverable));
$root->appendChild($deliverableNode);


$reqServicesNode = $doc->createElement("requestedservices");
$root->appendChild($reqServicesNode);

$addlServicesNode = $doc->createElement("additionalservices");
$root->appendChild($addlServicesNode);

foreach ($_POST['requestedServices'] as $service)
{
	$temp = $doc->createElement('service');
	$temp->appendChild($doc->createTextNode($service));
	$reqServicesNode->appendChild($temp);
	
	if ( !(($service == "Translate") || ($service == "Copy Edit") || ($service == "Proofread") ||
			($service == "Formatting (DTP)") || ($service == "Quality Assurance Review") ||
			($service == "File Treatment") || ($service == "Translation Memory creation, update, and/or maintenance") ||
			($service == "Project Management")))
	{
		$id = str_replace(" ", "_", $service);
		$temp = $doc->createElement($id);
		$temp->appendChild($doc->createTextNode($_POST[$id]));
		$addlServicesNode->appendChild($temp);
	}
}


$sourceL = $_POST['sourceL'];

$sourceLangNode = $doc->createElement("sourcelanguage");
$sourceLangNode->appendChild($doc->createTextNode($sourceL));
$root->appendChild($sourceLangNode);


$targetLanguagesNode = $doc->createElement('targetlanguages');
$root->appendChild($targetLanguagesNode);

foreach ($_POST['targetL'] as $language)
{
	$temp = $doc->createElement('language');
	$temp->appendChild($doc->createTextNode($language));
	$targetLanguagesNode->appendChild($temp);
}




$otherLanguageNode = $doc->createElement('otherlanguage');
$root->appendChild($otherLanguageNode);


$otherLangName = $_POST['otherLangName'];
$langNameNode = $doc->createElement('langname');
$langNameNode->appendChild($doc->createTextNode($otherLangName));
$otherLanguageNode->appendChild($langNameNode);



$otherLangNewTextCost = $_POST['otherLangNewTextCost'];
$newTextCostNode = $doc->createElement('newtextcost');
$newTextCostNode->appendChild($doc->createTextNode($otherLangNewTextCost));
$otherLanguageNode->appendChild($newTextCostNode);


$otherLangFuzzyTextCost = $_POST['otherLangFuzzyTextCost'];
$fuzzyTextCostNode = $doc->createElement('fuzzytextcost');
$fuzzyTextCostNode->appendChild($doc->createTextNode($otherLangFuzzyTextCost));
$otherLanguageNode->appendChild($fuzzyTextCostNode);


$otherLangMatchTextCost = $_POST['otherLangMatchTextCost'];
$matchTextCostNode = $doc->createElement('matchtextcost');
$matchTextCostNode->appendChild($doc->createTextNode($otherLangMatchTextCost));
$otherLanguageNode->appendChild($matchTextCostNode);


$otherLangTransHourly = $_POST['otherLangTransHourly'];
$transHourlyCostNode = $doc->createElement('transhourlycost');
$transHourlyCostNode->appendChild($doc->createTextNode($otherLangTransHourly));
$otherLanguageNode->appendChild($transHourlyCostNode);


$otherLangPRHourly = $_POST['otherLangPRHourly'];
$prHourlyCostNode = $doc->createElement('prhourlycost');
$prHourlyCostNode->appendChild($doc->createTextNode($otherLangPRHourly));
$otherLanguageNode->appendChild($prHourlyCostNode);


$langNumber = $_POST['langNumber'];
$langNumberNode = $doc->createElement("langnumber");
$langNumberNode->appendChild($doc->createTextNode($langNumber));
$root->appendChild($langNumberNode);


$estDeliveryDate = $_POST['estDeliveryDate'];
$estDeliveryDateNode = $doc->createElement("estdeliverydate");
$estDeliveryDateNode->appendChild($doc->createTextNode($estDeliveryDate));
$root->appendChild($estDeliveryDateNode);


$rushFees = $_POST['rushFees'];
$rushFeesNode = $doc->createElement("rushfees");
$rushFeesNode->appendChild($doc->createTextNode($rushFees));
$root->appendChild($rushFeesNode);


$discountNode = $doc->createElement("discounts");
$root->appendChild($discountNode);

$discountAmount = $_POST['discountAmount'];
$discountAmountNode = $doc->createElement("amount");
$discountAmountNode->appendChild($doc->createTextNode($discountAmount));
$discountNode->appendChild($discountAmountNode);

$discountType = $_POST['discountType'];
$discountTypeNode = $doc->createElement("type");
$discountTypeNode->appendChild($doc->createTextNode($discountType));
$discountNode->appendChild($discountTypeNode);



$projDesc = $_POST['projDesc'];
$projDescNode = $doc->createElement("projdesc");
$projDescNode->appendChild($doc->createTextNode($projDesc));
$root->appendChild($projDescNode);


$general_notes = $_POST['general_notes'];
$generalNotesNode = $doc->createElement("generalnotes");
$generalNotesNode->appendChild($doc->createTextNode($general_notes));
$root->appendChild($generalNotesNode);


$terms = $_POST['terms'];
$termsNode = $doc->createElement("terms");
$termsNode->appendChild($doc->createTextNode($terms));
$root->appendChild($termsNode);


$termsOther = $_POST['termsOther'];
$termsOtherNode = $doc->createElement("termsother");
$termsOtherNode->appendChild($doc->createTextNode($termsOther));
$root->appendChild($termsOtherNode);


$cycle = $_POST['cycle'];
$cycleNode = $doc->createElement("cycle");
$cycleNode->appendChild($doc->createTextNode($cycle));
$root->appendChild($cycleNode);


$cycleOther = $_POST['cycleOther'];
$cycleOtherNode = $doc->createElement("cycleother");
$cycleOtherNode->appendChild($doc->createTextNode($cycleOther));
$root->appendChild($cycleOtherNode);


$wordCountStyle = $_POST['wordCountStyle'];
$wordCountStyleNode = $doc->createElement("wordcountstyle");
$wordCountStyleNode->appendChild($doc->createTextNode($wordCountStyle));
$root->appendChild($wordCountStyleNode);


$newText = $_POST['new'];
$newTextNode = $doc->createElement("newtext");
$newTextNode->appendChild($doc->createTextNode($newText));
$root->appendChild($newTextNode);


$fuzzyText = $_POST['fuzzy'];
$fuzzyTextNode = $doc->createElement("fuzzytext");
$fuzzyTextNode->appendChild($doc->createTextNode($fuzzyText));
$root->appendChild($fuzzyTextNode);


$matchText = $_POST['100'];
$matchTextNode = $doc->createElement("matchtext");
$matchTextNode->appendChild($doc->createTextNode($matchText));
$root->appendChild($matchTextNode);


$totalW = $_POST['totalW'];
$totalWordsNode = $doc->createElement("totalwords");
$totalWordsNode->appendChild($doc->createTextNode($totalW));
$root->appendChild($totalWordsNode);


$totalText = $_POST['totalText'];
$totalTextNode = $doc->createElement("totaltext");
$totalTextNode->appendChild($doc->createTextNode($totalText));
$root->appendChild($totalTextNode);


$percentLeverage = $_POST['percentLeverage'];
$percentLeverageNode = $doc->createElement("percentleverage");
$percentLeverageNode->appendChild($doc->createTextNode($percentLeverage));
$root->appendChild($percentLeverageNode);


$linguisticCostType = $_POST['linguisticCostType'];
$linguisticCostTypeNode = $doc->createElement("linguisticcosttype");
$linguisticCostTypeNode->appendChild($doc->createTextNode($linguisticCostType));
$root->appendChild($linguisticCostTypeNode);


$linguisticSellType = $_POST['linguisticSellType'];
$linguisticSellTypeNode = $doc->createElement("linguisticselltype");
$linguisticSellTypeNode->appendChild($doc->createTextNode($linguisticSellType));
$root->appendChild($linguisticSellTypeNode);


$proofreading = $_POST['proofreading'];
$proofreadingNode = $doc->createElement("proofreading");
$proofreadingNode->appendChild($doc->createTextNode($proofreading));
$root->appendChild($proofreadingNode);


$lockunits = $_POST['lockunits'];
$lockunitsNode = $doc->createElement("lockunits");
$lockunitsNode->appendChild($doc->createTextNode($lockunits));
$root->appendChild($lockunitsNode);


$pageNumber = $_POST['pageNumber'];
$pageNumberNode = $doc->createElement("pagenumber");
$pageNumberNode->appendChild($doc->createTextNode($pageNumber));
$root->appendChild($pageNumberNode);


$pageHour = $_POST['pageHour'];
$pageHourNode = $doc->createElement("pagehour");
$pageHourNode->appendChild($doc->createTextNode($pageHour));
$root->appendChild($pageHourNode);


$fmtHours = $_POST['fmtHours'];
$fmtHoursNode = $doc->createElement("fmthours");
$fmtHoursNode->appendChild($doc->createTextNode($fmtHours));
$root->appendChild($fmtHoursNode);


$fmtCostPer = $_POST['fmtCostPer'];
$fmtCostPerNode = $doc->createElement("fmtcostper");
$fmtCostPerNode->appendChild($doc->createTextNode($fmtCostPer));
$root->appendChild($fmtCostPerNode);


$DTPCostunits = $_POST['DTPCostunits'];
$DTPCostunitsNode = $doc->createElement("dtpcostunits");
$DTPCostunitsNode->appendChild($doc->createTextNode($DTPCostunits));
$root->appendChild($DTPCostunitsNode);


$DTPSellunits = $_POST['DTPSellunits'];
$DTPSellunitsNode = $doc->createElement("dtpsellunits");
$DTPSellunitsNode->appendChild($doc->createTextNode($DTPSellunits));
$root->appendChild($DTPSellunitsNode);


$fmtCoord = $_POST['fmtCoord'];
$fmtCoordNode = $doc->createElement("fmtcoord");
$fmtCoordNode->appendChild($doc->createTextNode($fmtCoord));
$root->appendChild($fmtCoordNode);


$engGraphNum = $_POST['engGraphNum'];
$engGraphNumNode = $doc->createElement("enggraphnum");
$engGraphNumNode->appendChild($doc->createTextNode($engGraphNum));
$root->appendChild($engGraphNumNode);


$engGraphHour = $_POST['engGraphHour'];
$engGraphHourNode = $doc->createElement("enggraphhour");
$engGraphHourNode->appendChild($doc->createTextNode($engGraphHour));
$root->appendChild($engGraphHourNode);


$engTM = $_POST['engTM'];
$engTMNode = $doc->createElement("engtm");
$engTMNode->appendChild($doc->createTextNode($engTM));
$root->appendChild($engTMNode);


$engineer = $_POST['engineer'];
$engineerNode = $doc->createElement("engineer");
$engineerNode->appendChild($doc->createTextNode($engineer));
$root->appendChild($engineerNode);


$engScap = $_POST['engScap'];
$engScapNode = $doc->createElement("engscap");
$engScapNode->appendChild($doc->createTextNode($engscap));
$root->appendChild($engScapNode);


$engScapHour = $_POST['engScapHour'];
$engScapHourNode = $doc->createElement("engscaphour");
$engScapHourNode->appendChild($doc->createTextNode($engScapHour));
$root->appendChild($engScapHourNode);


$qaPagesHour = $_POST['qaPagesHour'];
$qaPagesHourNode = $doc->createElement("qapageshour");
$qaPagesHourNode->appendChild($doc->createTextNode($qaPagesHour));
$root->appendChild($qaPagesHourNode);


$qaHours = $_POST['qaHours'];
$qaHoursNode = $doc->createElement("qahours");
$qaHoursNode->appendChild($doc->createTextNode($qaHours));
$root->appendChild($qaHoursNode);


$qaCoord = $_POST['qaCoord'];
$qaCoordNode = $doc->createElement("qacoord");
$qaCoordNode->appendChild($doc->createTextNode($qaCoord));
$root->appendChild($qaCoordNode);


$pmHours = $_POST['pmHours'];
$pmHoursNode = $doc->createElement("pmhours");
$pmHoursNode->appendChild($doc->createTextNode($pmHours));
$root->appendChild($pmHoursNode);


$pmPercentage = $_POST['pmPercentage'];
$pmPercentageNode = $doc->createElement("pmpercentage");
$pmPercentageNode->appendChild($doc->createTextNode($pmPercentage));
$root->appendChild($pmPercentageNode);


$addTask1 = $_POST['addTask1'];
$addTask1Node = $doc->createElement("addtask1");
$addTask1Node->appendChild($doc->createTextNode($addTask1));
$root->appendChild($addTask1Node);


$addDesc1 = $_POST['addDesc1'];
$addDesc1Node = $doc->createElement("adddesc1");
$addDesc1Node->appendChild($doc->createTextNode($addDesc1));
$root->appendChild($addDesc1Node);


$addTask2 = $_POST['addTask2'];
$addTask2Node = $doc->createElement("addtask2");
$addTask2Node->appendChild($doc->createTextNode($addTask2));
$root->appendChild($addTask2Node);


$addDesc2 = $_POST['addDesc2'];
$addDesc2Node = $doc->createElement("adddesc2");
$addDesc2Node->appendChild($doc->createTextNode($addDesc2));
$root->appendChild($addDesc2Node);


$addTask3 = $_POST['addTask3'];
$addTask3Node = $doc->createElement("addtask3");
$addTask3Node->appendChild($doc->createTextNode($addTask3));
$root->appendChild($addTask3Node);


$addDesc3 = $_POST['addDesc3'];
$addDesc3Node = $doc->createElement("adddesc3");
$addDesc3Node->appendChild($doc->createTextNode($addDesc3));
$root->appendChild($addDesc3Node);






$filename = "estimate-" . date('M-d-Y-Gi') . ".xml";
$filename = str_replace(" ","_",$filename);
/*$foldername = "logs";
$relativepath = $foldername . "/" . $filename;
$fullpath = "http://www.llts.com/estimator/" . $foldername . "/" . $filename;
*/

header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=$filename");

//$result = $doc->save($relativepath);

echo $doc->saveXML() . "\n";


?>
