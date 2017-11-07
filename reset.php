<?PHP
//first check to see that we are logged in
session_start();
require_once('uuid.php');

//check to make sure we're logged in
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

//must be logged in at this point, so grab the user session variables
//so that we can put them back into the new session
$tempUID = $_SESSION['userID'];
$tempF_Name = $_SESSION['userFirstName'];
$tempL_Name = $_SESSION['userLastName'];
$tempRoles = $_SESSION['userRoles'];

//make sure we got here legitimately, and destroy the session
if ( (isset($_POST['reset'])) && ($_POST['reset'] == "Start Over"))
{

	// Unset all of the session variables.
	$_SESSION = array();
	
		
	// Finally, destroy the session.
	session_destroy();
	
	//create a new session and add back the user variables
	session_start();
	$_SESSION['userID'] = $tempUID;
	$_SESSION['userFirstName'] = $tempF_Name;
	$_SESSION['userLastName'] = $tempL_Name;
	$_SESSION['userRoles'] = $tempRoles;
	
}

//send us back to the ballparker
header("Location:estimate.php");
exit;

?>
