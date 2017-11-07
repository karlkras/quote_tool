<?php
require_once(__DIR__ . '/attaskconn/LingoAtTaskService.php');
define("VerificationCode","ijumpsgetRTH");

function processLogin($authentication, $WFUsername, $app_UUID){
    set_time_limit(60);
    try {
        $api = new LingoAtTaskService();
        $g = new getUserByUsername;
        $g->accountName = $WFUsername;
        $user = $api->getUserByUsername($g)->return;
    } catch (exception $e) {
        echo "<h2>There was a problem getting user data from Work Front</h2>";
        echo "<strong>Overview:</strong> " . $e->getMessage() . "<br><br>";
        echo "<strong>Detail:</strong> " . $e->detail->ProcessFault->message . "<br><br><br><hr>";
        echo "Debug Data:<br><pre>";
        var_dump($e);
        echo "</pre>";
        exit;
    }
    
    if (!$user) {
        die("Error: no WorkFront user with login $WFUsername exists");
    }
    
    if ($authentication != VerificationCode) {
        die ("Fatal Error: application verification is incorrect");
    }
    
    $_SESSION['userID'] = $user->id;
    $_SESSION['userName'] = $user->userName;
    $_SESSION['userRoles'] = $user->roles;
    $_SESSION['userFirstName'] = $user->firstName;
    $_SESSION['userLastName'] = $user->lastName;
    $_SESSION['appUUID'] = $app_UUID;
}