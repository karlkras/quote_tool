<?PHP

//define some constants
//definitions for testing on a local environment.
//define('DBServerName', 'localhost');
//define('UserName', 'root');
//define('Password', '');
//define('DBName', 'clientpricing');


//definitions for testing DB on Mike's pc
//define ('DBServerName', 'tapa.llts.com');
//define ('UserName', 'root');
//define ('Password', 'GVIkuUoonMLIYBwezbMK');
//define ('DBName', 'clientpricing');


//definitions for production DB
define ("DBServerName", 'neptune.llts.com');
define ("UserName", 'attaskdbuser');
define ("Password", 'hTudVU5XGT7rUk');
define ("DBName", 'clientpricing');

// qt_karl for mike development on TAPA,
// qt_v2 for production on hedron  
// qt_dashboard for Eric's testing locally on TAPA. 
// Adjust accordingly.
// 
//define ('QTROOT', '/qt_karl');
define ('QTROOT', '/qt_v2');
//define ('QTROOT', '/qt_dashboard');

define("NEWTEXT", 0);
define("FUZZY", 1);
define("MATCHES", 2);
define("PROOF", 3);
define("FORMAT", 4);
define("GRAPHICS", 5);
define("DTPCOORD", 6);
define("TMWORK", 7);
define("ENGINEERING", 8);
define("SCAPS", 9);
define("QA", 10);
define("QACOORD", 11);
define("ADD1", 12);
define("ADD2", 13);
define("ADD3", 14);
define("PM", 15);
define("FLAGS", 16);
define('MAXTASK', 16);

define('LINGUIST_TASK', 'linguistTask');
define('BILLABLE_TASK', 'billableTask');



define("NUM_UNITS", 0);
define("COST_PER", 1);
define("COST", 2);
define("MARKUP", 3);
define("CALC_SELL", 4);
define("SELL_PER", 5);
define("ACT_SELL", 6);
define("GM", 7);

//definitions for flags
define("ERRORS", 0);
define("CUSTOMPRICE", 1);

define("ABSPATH", dirname(__FILE__));


