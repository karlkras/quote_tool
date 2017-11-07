<?PHP
require_once("class_language.php");
require_once("definitions.php");
require_once("class_estimate.php");

//open the session
session_start();
$languageTbl = $_SESSION['languageTbl'];
$estimateObj = $_SESSION['estimate'];

$markup = $_GET["markup"];
$langCode = $_GET["lang"];
$idNum = $_GET["id"];
$rushFeeMarkup = $estimateObj->get_rushFeeMultiplier();







if ($idNum == PM)
{
	//we're updating the PM percent, which has different rules
	$languageTbl[$langCode]->get_task($idNum)->set_pmPercent($markup);
	$new_calculated_sell_price_per = $languageTbl[$langCode]->get_task($idNum)->get_calculatedSellPricePerUnit($languageTbl[$langCode]);
	$new_actual_sell_price_per = $languageTbl[$langCode]->get_task($idNum)->get_actualSellPricePerUnit($languageTbl[$langCode]);
	$new_actual_sell_price = $languageTbl[$langCode]->get_task($idNum)->get_actualSellPrice($languageTbl[$langCode]);
	$new_task_gm = $languageTbl[$langCode]->get_task($idNum)->get_grossmargin($languageTbl[$langCode]);
	
	
	$new_pm_actual_sellprice = $new_actual_sell_price;
}
else
{
	$languageTbl[$langCode]->get_task($idNum)->set_markup($markup);
	
	$new_calculated_sell_price_per = $languageTbl[$langCode]->get_task($idNum)->get_calculatedSellPricePerUnit();
	$new_actual_sell_price_per = $languageTbl[$langCode]->get_task($idNum)->get_actualSellPricePerUnit();
	$new_actual_sell_price = $languageTbl[$langCode]->get_task($idNum)->get_actualSellPrice();
	$new_task_gm = $languageTbl[$langCode]->get_task($idNum)->get_grossmargin();
	
	foreach ($languageTbl[$langCode]->get_tasks() as $task)
	{
		if ($task->get_name() == "Project Management")
			$new_pm_actual_sellprice = $task->get_actualSellPrice($languageTbl[$langCode]);
	}
}	
	
$new_language_sellprice_per_word = $languageTbl[$langCode]->sellprice_per_word();
$new_language_actual_sellprice = $languageTbl[$langCode]->sellprice();
$new_language_gm = $languageTbl[$langCode]->language_grossmargin();


$task_total_sellprice = 0;
$grand_total_sellprice = 0;
$grand_total_cost = 0;
foreach ($languageTbl as $language)
{
	if ($language->get_task($idNum)->get_name() == "Project Management")
		$task_total_sellprice += $language->get_task($idNum)->get_actualSellPrice($language);
	else
		$task_total_sellprice += $language->get_task($idNum)->get_actualSellPrice();
	$grand_total_sellprice += $language->sellprice();
	$grand_total_cost += $language->language_cost();
}

$grand_total_gm = ($grand_total_sellprice - $grand_total_cost)/$grand_total_sellprice;
$grand_total_gm = round(($grand_total_gm * 100),2);
	

$_SESSION['languageTbl'] = $languageTbl;





header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="ISO-8859-1"?><update>';
echo "<langcode>". $langCode ."</langcode>";
echo "<idnum>". $idNum ."</idnum>";
echo "<markup>". $markup ."</markup>";
echo "<newcspp>". $new_calculated_sell_price_per ."</newcspp>";
echo "<newaspp>". $new_actual_sell_price_per ."</newaspp>";
echo "<newasp>". number_format($new_actual_sell_price,2) ."</newasp>";
echo "<newtaskgm>". $new_task_gm ."</newtaskgm>";
echo "<newlanguageaspp>". $new_language_sellprice_per_word ."</newlanguageaspp>";
echo "<newlanguageasp>". number_format($new_language_actual_sellprice,2) ."</newlanguageasp>";
echo "<newlanguagegm>". $new_language_gm ."</newlanguagegm>";
echo "<tasktotalasp>". $task_total_sellprice ."</tasktotalasp>";
echo "<grandtotalasp>". $grand_total_sellprice ."</grandtotalasp>";
echo "<grandtotalcost>". $grand_total_cost ."</grandtotalcost>";
echo "<grandtotalgm>". $grand_total_gm ."</grandtotalgm>";
echo "<newpmasp>". $new_pm_actual_sellprice ."</newpmasp>";
echo "<rushfee>". $rushFeeMarkup ."</rushfee>";
echo "<discounttype>". $estimateObj->get_discountType() ."</discounttype>";
echo "<discountpercent>". $estimateObj->get_discountPercent() ."</discountpercent>";
echo "<discountamount>". $estimateObj->get_discountAmount() ."</discountamount>";
echo "</update>";




?>