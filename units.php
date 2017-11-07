<?PHP
require_once("class_language.php");
require_once("definitions.php");
require_once("class_estimate.php");


//open the session
session_start();
$languageTbl = $_SESSION['languageTbl'];
$estimate = $_SESSION['estimate'];



$newUnits = $_GET["newUnits"];
$langCode = $_GET["lang"];
$idNum = $_GET["id"];
$costSell = $_GET['costsell'];


if ($costSell == "cost")
{
	$languageTbl[$langCode]->get_task($idNum)->set_costUnits($newUnits);
	if ( (($idNum <= MATCHES) || ($idNum == FORMAT)) &&  
	   ( $languageTbl[$langCode]->get_task($idNum)->get_sellUnitType() == $languageTbl[$langCode]->get_task($idNum)->get_costUnitType()))
	{
		$languageTbl[$langCode]->get_task($idNum)->set_sellUnits($newUnits);
	}
		
	if( $languageTbl[$langCode]->get_task($idNum)->get_costUnitType() == "Words" )
	{
		switch($idNum)
		{
			case NEWTEXT:
				$languageTbl[$langCode]->set_newText($newUnits);
				break;
			case FUZZY:
				$languageTbl[$langCode]->set_fuzzyText($newUnits);
				break;
			case MATCHES:
				$languageTbl[$langCode]->set_matchText($newUnits);
				break;
		}
	}
}
else
{
	$languageTbl[$langCode]->get_task($idNum)->set_sellUnits($newUnits);
		
	if( $languageTbl[$langCode]->get_task($idNum)->get_sellUnitType() == "Words" )
	{
		switch($idNum)
		{
			case NEWTEXT:
				$languageTbl[$langCode]->set_newText($newUnits);
				break;
			case FUZZY:
				$languageTbl[$langCode]->set_fuzzyText($newUnits);
				break;
			case MATCHES:
				$languageTbl[$langCode]->set_matchText($newUnits);
				break;
		}
	}
}


$new_cost = $newUnits * $languageTbl[$langCode]->get_task($idNum)->get_costPerUnit();
$new_cost = round($new_cost,2);

if ($idNum == PM)
	$markup = $languageTbl[$langCode]->get_task($idNum)->get_pmPercent();
else
	$markup = $languageTbl[$langCode]->get_task($idNum)->get_markup();

$totalWords = $languageTbl[$langCode]->total_words();

$split = $languageTbl[$langCode]->get_task($idNum)->isSplit();

if ( (($idNum <= MATCHES) ) && ($costSell == "cost"))
{
	if   ( $languageTbl[$langCode]->get_task($idNum)->get_sellUnitType() == $languageTbl[$langCode]->get_task($idNum)->get_costUnitType())
	{
		$sellUnits = $newUnits;

	}
	else
	{
		if ($languageTbl[$langCode]->get_task($idNum)->get_costUnitType() == "Hours")
		{	//we need to convert from hours to word
			switch($idNum)
			{
				case NEWTEXT:
					$sellUnits = $languageTbl[$langCode]->get_newText(); break;
				case FUZZY:
					$sellUnits = $languageTbl[$langCode]->get_fuzzyText(); break;
				case MATCHES:
					$sellUnits = $languageTbl[$langCode]->get_matchText(); break;
			}
		}
		else
		{	//we need to convert from words to hour
			switch($idNum)
			{
				case NEWTEXT:
					$sellUnits = round($languageTbl[$langCode]->get_newText() / 250,2);
					$sellUnits += round($languageTbl[$langCode]->get_newText() / 1000,2);
					break;
				case FUZZY:
					$sellUnits = round($languageTbl[$langCode]->get_fuzzyText() / 250,2);
					$sellUnits += round($languageTbl[$langCode]->get_fuzzyText() / 1000,2);
					break;
				case MATCHES:
					$sellUnits = round($languageTbl[$langCode]->get_matchText() / 250,2);
					$sellUnits += round($languageTbl[$langCode]->get_matchText() / 1000,2);
					break;
			}
					
		}
	}
	$languageTbl[$langCode]->get_task($idNum)->set_sellUnits($sellUnits);
}
else if (($idNum == FORMAT) && ($costSell == "cost"))
{
	if   ( $languageTbl[$langCode]->get_task($idNum)->get_sellUnitType() == $languageTbl[$langCode]->get_task($idNum)->get_costUnitType())
	{
		$sellUnits = $newUnits;

	}
}	
else
{
	$sellUnits = 0;
}

$_SESSION['languageTbl'] = $languageTbl;

if (floor($sellUnits) != $sellUnits)
	$sellUnits = number_format($sellUnits,2);
else
	$sellUnits = number_format($sellUnits);
	
	
$task_total_cost = 0;
$grand_total_cost = 0;
foreach ($languageTbl as $language)
{
	$task_total_cost += $language->get_task($idNum)->get_cost();
	$grand_total_cost += $language->language_cost();
}

	

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="ISO-8859-1"?><update>';
echo "<langCode>". $langCode ."</langCode>";
echo "<idNum>". $idNum ."</idNum>";
echo "<newcost>". number_format($new_cost,2) ."</newcost>";
echo "<markup>". $markup ."</markup>";
echo "<totalwords>". number_format($totalWords) ."</totalwords>";
echo "<costsell>". $costSell ."</costsell>";
echo "<langcost>". number_format($languageTbl[$langCode]->language_cost(),2) ."</langcost>";
echo "<sellunits>". $sellUnits ."</sellunits>";
echo "<split>". $split ."</split>";
echo "<dtpcoord>". $estimate->get_dtpCoordPercent() ."</dtpcoord>";
echo "<qacoord>". $estimate->get_qaCoordPercent() ."</qacoord>";
echo "<grandtotalcost>". number_format($grand_total_cost,2) ."</grandtotalcost>";
echo "<tasktotalcost>". number_format($task_total_cost,2) ."</tasktotalcost>";
if ( $idNum <= MATCHES )
{
	$temp = $languageTbl[$langCode]->get_task(0)->get_cost() + $languageTbl[$langCode]->get_task(1)->get_cost() + $languageTbl[$langCode]->get_task(2)->get_cost();
	if ( $temp < $languageTbl[$langCode]->get_transHourly() )
	{
		echo "<belowminimum>true</belowminimum>";
	}
}


echo "</update>";


