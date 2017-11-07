<?PHP
//check for any error conditions
$errorCondition = 0;

if (isset($_GET['err'])) {
    $errorCondition = $_GET['err'];
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Login - LLTS Quote Tool</title>
        <link href="styles/main.css" rel="stylesheet" type="text/css" />
        <link href="styles/new.css" rel="stylesheet" type="text/css" />
    </head>

    <body>
        <div class="container">
            <header>
                <img src="images/languageline-logo.png" alt="Lingo Systems Logo" width="223" height="62" align="middle"><br /><br />
            </header>
            <div class="maincontent">
                <div class="border">
                    <form name="login_form" method="post" action="checklogin.php">
<?PHP
//check to see if there's a location variable set, and if so
//then put it into a hidden field to pass to the processing script
if (isset($_GET['location'])) {
    echo "<input type=\"hidden\" name=\"location\" value=\"" . $_GET['location'] . "\">\n";
}
?>
                        <table width="100%" border="0">
                            <tr>
                                <td colspan="2" class="banner">Log in</td>
                            </tr>
<?PHP
if ($errorCondition != 0) {
    echo "				<tr>\n";
    if ($errorCondition == 1) {
        echo "					<td colspan=\"2\" align=\"center\"><div class=\"error\">Both username and password fields must be entered.<br>Please try again.</div></td>\n";
    } elseif ($errorCondition == 2) {
        echo "					<td colspan=\"2\" align=\"center\"><div class=\"error\">The username and/or password entered do not match our records.<br>Please try again.</div></td>\n";
    } elseif ($errorCondition == 3) {
        echo "					<td colspan=\"2\" align=\"center\"><div class=\"error\">You have successfully been logged out. You may log in again if you desire.</div></td>\n";
    } elseif ($errorCondition == 4) {
        echo "					<td colspan=\"2\" align=\"center\"><div class=\"error\">You do not have sufficient privledges to access that area.</div></td>\n";
    } elseif ($errorCondition == 5) {
        echo "					<td colspan=\"2\" align=\"center\"><div class=\"error\">You do not have the proper role assigned to access that area.</div></td>\n";
    } elseif ($errorCondition == 6) {
        echo "					<td colspan=\"2\" align=\"center\"><div class=\"error\">Application ID is incorrect. Please log in again.</div></td>\n";
    } else {
        echo "					<td colspan=\"2\" align=\"center\"><div class=\"error\">An unknown error has occurred.<br>Please try again.</div></td>\n";
    }
    echo "				</tr>\n";
}
?>
                            <tr>
                                <td width="35%" align="right" valign="top">@Task Username</td>
                                <td width="65%">
                                    <input type="text" name="username" id="username_field" tabindex="1"/> &nbsp;
                                    <input type="submit" value="Log in" name="Submit" id="submit_button" tabindex="3"/>
                                </td>
                            </tr>
                            <tr>
                                <td align="right" valign="top">@Task Password</td>
                                <td>
                                    <input type="password" name="password" id="password_field" tabindex="2"/>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" align="center" style="font-size:.75em">&nbsp;</td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>