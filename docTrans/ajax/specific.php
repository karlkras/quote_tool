<?PHP

ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 1);
require_once("../../attaskconn/LingoAtTaskService.php");

$api = new LingoAtTaskService();

$g = new getQuotableProjects();
$quotableProjects = $api->getQuotableProjects($g)->return;

$showOptions = 'false';
foreach ($quotableProjects as $qp) {
    if ($qp->id == $_GET['v']) {
        if ($qp->pricingScheme == 'Client-Specific Pricing') {
            $showOptions = 'true';
        } else {
            $showOptions = 'false';
        }
        break;
    }
}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="ISO-8859-1"?><update>';
echo "<showoptions>" . $showOptions . "</showoptions>";
echo "<scheme>" . $qp->pricingScheme . "</scheme>";
echo "</update>";
?>