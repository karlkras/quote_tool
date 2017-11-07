<?PHP

ob_start();
ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 1);

require_once("../../attaskconn/LingoAtTaskService.php");
require_once(__DIR__ . '/../classes/PricingMySql.php');


session_start();


$error = 'false';
$errString = 'no err';
$schemeApplied = '';
$pmPercent = 10;
$customPM = 'false';
$customDiscount = 'false';
$clientDiscount = 0;

if (!isset($_GET['p'])) {
    $error = 'true';
    $errString = 'no project value was set';
} else {
    $healthCareListPricingSchemaName = "Healthcare List Pricing";
    $healthCareListPricingSchemaTable = "healthcare_list_pricing";

    $llsPricing = "LLS Pricing";

    try {
        set_time_limit(60);
        $api = new LingoAtTaskService();

        $quotableProjects = unserialize($_SESSION['quotableProjects']);

        foreach ($quotableProjects as $qp) {
            if ($qp->id == $_GET['p']) {
                $projectStub = $qp;
                break;
            }
        }

        $g = new getProject();
        $g->projStub = $projectStub;
        $projectObj = $api->getProject($g)->return;

        if ($projectObj->company->docTransPricingScheme == $healthCareListPricingSchemaName) {
            $_SESSION['pricing'] = $healthCareListPricingSchemaName;
            $schemeApplied = $healthCareListPricingSchemaName;
            $_SESSION['pricingTable'] = $healthCareListPricingSchemaTable;
        } elseif ($projectObj->company->docTransPricingScheme == $llsPricing) {
            $_SESSION['pricing'] = $llsPricing;
            $schemeApplied = $llsPricing;
        } elseif ($projectObj->company->docTransPricingScheme != 'Client-Specific Pricing'){
            $schemeApplied = $projectObj->company->docTransPricingScheme;
            $clientDiscount = 0;
            $_SESSION['pricing'] = 'none';
        } else {
            $client_id = $projectObj->company->id;

            $query = "SELECT * FROM clients WHERE attask_id = " . $client_id;

            $myDBConn = new PricingMySql();

            $result = $myDBConn->query($query);

            if ($result->num_rows > 0) {
                $res = $result->fetch_assoc();
                $schemeApplied = $res['Name'];
                $_SESSION['pricing'] = $schemeApplied;
                if (is_null($res['discount'])) {
                    $clientDiscount = 0;
                    $customDiscount = 'false';
                } else {
                    $clientDiscount = $res['discount'];
                    $customDiscount = 'true';
                }

                //now check for and get the PM rate for this client, if exists
                $table = $res['table_name'];
                $rateRush = 1;
                $result->free();
                $query = "SELECT rate FROM $table WHERE task_name='Project_Management' AND units='percent'";
                $result = $myDBConn->query($query);

                $pmPricing = $result->fetch_assoc();

                if (!is_null($pmPricing) && $pmPricing !== false) {
                    $pmPercent = $pmPricing['rate'] / 1000;
                    $custom = 'true';
                } else {
                    $pmPercent = 10;
                    $custom = 'false';
                }
            } else {
                $schemeApplied = 'No client pricing found';
                $clientDiscount = 0;
                $_SESSION['pricing'] = 'none';
            }
            if (isset($result) && (is_a($result, 'mysqli_result'))) {
                $result->free();
            }
            //$myDBConn->close();
        }
    } catch (exception $e) {
        $error = 'true';
        $errString = $e->getMessage();
    }
}



ob_end_clean();
header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="ISO-8859-1"?><scheme>\n';
echo "<error>" . $error . "</error>\n";
echo "<errstring>" . $errString . "</errstring>\n";
echo "<schemeapplied>" . htmlentities($schemeApplied) . "</schemeapplied>\n";
echo "<pmpercent>" . $pmPercent . "</pmpercent>\n";
echo "<clientdiscount>$clientDiscount</clientdiscount>\n";
echo "<customPM>" . $customPM . "</customPM>\n";
echo "<customDiscount>$customDiscount</customDiscount>\n";
echo "</scheme>\n";
?>