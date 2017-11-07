<?PHP
//check to see if we're logged in
session_start();

if (!isset($_SESSION['userID']))
{
	header('location:../login.php?location=admin');
	exit;
	
}
?>
