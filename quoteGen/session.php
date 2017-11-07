<?PHP
// path for cookies
$cookie_path = "/";

// timeout value for the cookie
$cookie_timeout = 8 * 60 * 60; // in seconds

// timeout value for the garbage collector
//   we add 300 seconds, just in case the user's computer clock
//   was synchronized meanwhile; 600 secs (10 minutes) should be
//   enough - just to ensure there is session data until the
//   cookie expires
$garbage_timeout = $cookie_timeout + 600; // in seconds

// set the PHP session id (PHPSESSID) cookie to a custom value
session_set_cookie_params($cookie_timeout, $cookie_path);

// set the garbage collector - who will clean the session files -
//   to our custom timeout
ini_set('session.gc_maxlifetime', $garbage_timeout);

// we need a distinct directory for the session files,
//   otherwise another garbage collector with a lower gc_maxlifetime
//   will clean our files aswell - but in an own directory, we only
//   clean sessions with our "own" garbage collector (which has a
//   custom timeout/maxlifetime set each time one of our scripts is
//   executed)

$sessdir = "/quote_sessions/localization/";
if (!is_dir($sessdir)) { mkdir($sessdir, 0777); }
ini_set('session.save_path', $sessdir);

// now we're ready to start the session
if (!session_start())
{
	echo "Could not start session";
}

