<?PHP
//clear any existing session and start a new one, just to be sure
//there's no information left over from a previous session
session_start();	//get the current session

$_SESSION = array();	//clear out all existing session variables

//delete the session cookie so that we get an entirely new session when
//we login again
if (ini_get("session.use_cookies")) 
{
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}

// Finally, destroy the session.
session_destroy();

//send the user back to the login page, with an
//error code to display a logout message.
header('location:login.php?err=3');
exit;

?>