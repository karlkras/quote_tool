<?PHP

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(1);

ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 1);
ini_set("include_path", "c:/php/includes");
require_once('attaskconn/LingoAtTaskService.php');


if ((!isset($_POST['username'])) || ($_POST['username'] == "") ||
        (!isset($_POST['password'])) || ($_POST['password'] == "")) {
    $redirectURL = 'location:login.php?err=1';
    if (isset($_POST['location'])) {
        $redirectURL .= "&location=" . $_POST['location'];
    }
    header($redirectURL);
    exit;
}

//load function to display more user friendly error message and send email to Mike vG on fatal errors.
require_once('./function.fatal_handler.php');


//clear any existing session and start a new one, just to be sure
//there's no information left over from a previous session
session_start(); //get the current session

$_SESSION = array(); //clear out all existing session variables
// Finally, destroy the session.
session_destroy();

$isAdmin = false;
$result = false;

//attempt to validate the user's login with @task
set_time_limit(60);
try {
    $api = new LingoAtTaskService();
    $g = new validateUserPassword;
    $g->UserName = $_POST['username'];
    $g->Password = $_POST['password'];
    $result = $api->validateUserPassword($g)->return;
} catch (exception $e) {
    echo "<h2>There was a problem validating your login with the @task service</h2>";
    echo "<strong>Overview:</strong> " . $e->getMessage() . "<br><br>";

    echo "<strong>Detail:</strong> " . $e->detail->ProcessFault->message . "<br><br><br><hr>";


    echo "Debug Data:<br><pre>";
    var_dump($e);
    echo "</pre>";

    exit;
}


//check the result that we get back, if it's false we
//have a bad login, so send them back to the login screen
//with an error code so we can display the appropriate message
if (!$result) {
    $redirectURL = 'location:login.php?err=2';
    if (isset($_POST['location'])) {
        $redirectURL .= "&location=" . $_POST['location'];
    }
    header($redirectURL);
    exit;
}



//if we made it this far then the user must have logged in correctly.
//So, let's get some user information about them from @task to store
//in the session for later use
try {

    $g = new getUserByUsername;
    $g->accountName = $_POST['username'];
    $user = $api->getUserByUsername($g)->return;

    $g = new getAccessLevelByUsername;
    $g->accountName = $_POST['username'];
    $userAccessLevel = $api->getAccessLevelByUsername($g)->return;
} catch (exception $e) {
    echo "<h2>There was a problem getting the user list</h2>";
    echo "<strong>Overview:</strong> " . $e->getMessage() . "<br><br>";

    echo "<strong>Detail:</strong> " . $e->detail->ProcessFault->message . "<br><br><br><hr>";


    echo "Debug Data:<br><pre>";
    var_dump($e);
    echo "</pre>";

    exit;
}



if (!$user) { //no user with that username was found
    $redirectURL = 'location:login.php?err=5';
    if (isset($_POST['location'])) {
        $redirectURL .= "&location=" . $_POST['location'];
    }
    header($redirectURL);
    exit;
} else {
    $userID = $user->id;
    $userName = $user->userName;
    $userRoles = $user->roles;
    $userFirstName = $user->firstName;
    $userLastName = $user->lastName;

    if (($userAccessLevel) && ($userAccessLevel == '5470e5f400035be417158c5077344831')) {
        $isAdmin = true;
    } else {
        $isAdmin = false;
    }
}


//start a new session
session_start();

//put the user info into the session variable
$_SESSION['userID'] = $userID;
$_SESSION['userName'] = $userName;
$_SESSION['userRoles'] = $userRoles;
$_SESSION['userFirstName'] = $userFirstName;
$_SESSION['userLastName'] = $userLastName;
$_SESSION['isAdmin'] = $isAdmin;

//add the app id to the session
if (isset($_POST['location']) && ($_POST['location'] == 'admin')) {
    require_once('./admin/uuid.php');
} else {
    require_once('uuid.php');
}
$_SESSION['appUUID'] = $app_UUID;

//check to see if we have a redirect location, otherwise
//send them back to the main index page
if (isset($_POST['location'])) {
    switch ($_POST['location']) {
        case 'ballpark': header('location:estimate_1.php');
            exit;
        case 'standard': header('location:docTrans/index.php');
            exit;
        case 'advanced': header('location:quoteGen/index.php');
            exit;
        case 'admin': header('location:admin/index.php');
            exit;
        case 'upload': header('location:pdfUpload/index.php');
            exit;
        default: header('location:index.php');
            exit;
    }
} else {
    header('location:index.php');
    exit;
}
?>