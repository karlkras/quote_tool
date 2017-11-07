<?PHP
require_once("class_language.php");
require_once("definitions.php");
require_once("class_estimate.php");

//open the session
session_start();
$languageTbl = $_SESSION['languageTbl'];
$estimateObj = $_SESSION['estimate'];


$new_sellprice_per = $_GET["spp"];
$langID = $_GET["lang"];
$taskID = $_GET["id"];
$rushFeeMarkup = $estimateObj->get_rushFeeMultiplier();


$languageTbl[$langID]->get_task($taskID)->set_customPrice($new_sellprice_per);

//find markup

if($languageTbl[$langID]->get_task($taskID)->isSplit() != "false")
	$new_markup = ($new_sellprice_per - $languageTbl[$langID]->get_task($taskID)->get_sellPerUnit() ) / $new_sellprice_per;
else
	$new_markup = ($new_sellprice_per - $languageTbl[$langID]->get_task($taskID)->get_costPerUnit() ) / $new_sellprice_per;
	
$new_markup = floor($new_markup*100);
$languageTbl[$langID]->get_task($taskID)->set_markup($new_markup);

//find calc sell price per
$new_calc_sellprice_per = $languageTbl[$langID]->get_task($taskID)->get_calculatedSellPricePerUnit();

//find act sell
$new_act_sellprice = $languageTbl[$langID]->get_task($taskID)->get_actualSellPrice();

//find gm
$new_grossmargin = $languageTbl[$langID]->get_task($taskID)->get_grossmargin();

//update the pm sell price
foreach ($languageTbl[$langID]->get_tasks() as $task)
{
	if ($task->get_name() == "Project Management")
		$new_pm_actual_sellprice = $task->get_actualSellPrice($languageTbl[$langID]);
}

$new_language_sellprice_per_word = $languageTbl[$langID]->sellprice_per_word();
$new_language_actual_sellprice = $languageTbl[$langID]->sellprice();
$new_language_gm = $languageTbl[$langID]->language_grossmargin();


$task_total_sellprice = 0;
$grand_total_sellprice = 0;
$grand_total_cost = 0;
foreach ($languageTbl as $language)
{
	if ($language->get_task($taskID)->get_name() == "Project Management")
		$task_total_sellprice += $language->get_task($taskID)->get_actualSellPrice($language);
	else
		$task_total_sellprice += $language->get_task($taskID)->get_actualSellPrice();
	$grand_total_sellprice += $language->sellprice();
	$grand_total_cost += $language->language_cost();
}

$grand_total_gm = ($grand_total_sellprice - $grand_total_cost)/$grand_total_sellprice;
$grand_total_gm = round(($grand_total_gm * 100),2);


$_SESSION['languageTbl'] = $languageTbl;

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="ISO-8859-1"?><update>';
echo "<langcode>". $langID ."</langcode>";
echo "<idnum>". $taskID ."</idnum>";
echo "<markup>". $new_markup ."</markup>";
echo "<newcspp>". $new_calc_sellprice_per ."</newcspp>";
echo "<newaspp>". $new_sellprice_per ."</newaspp>";
echo "<newasp>". $new_act_sellprice ."</newasp>";
echo "<newtaskgm>". $new_grossmargin ."</newtaskgm>";
echo "<newlanguageaspp>". $new_language_sellprice_per_word ."</newlanguageaspp>";
echo "<newlanguageasp>". $new_language_actual_sellprice ."</newlanguageasp>";
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