<?php
//error_reporting(E_ALL ^ E_DEPRECATED);
require_once('attaskconn/LingoAtTaskService.php');
include_once('db_functions.php');

$project = $_GET['project'];
$projectStub = array (
    'id' => $project,
    'fields' => 'companyID:extRefID'
);

try {
    set_time_limit(60);
    $api = new LingoAtTaskService();
    $g = new getProject($projectStub);
    $projectObj = $api->getProject($g)->return;
}
catch(exception $e) {
    echo "<h2>There was a problem with the @task service</h2>";
    echo "<strong>Overview:</strong> ". $e->getMessage(). "<br><br>";
    echo "<strong>Detail:</strong> ". $e->detail->ProcessFault->message ."<br><br><br><hr>";
    echo "Debug Data:<br><pre>";
    var_dump($e);
    echo "</pre>";
    exit;
}

$table = CheckCustomRushRate($projectObj->company->id);
echo json_encode($table);
