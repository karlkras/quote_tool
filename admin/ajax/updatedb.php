<?PHP

include_once("../../definitions.php");

$newValue = $_GET['value'];
$editType = $_GET['editType'];
$sourceLang = $_GET['sourceLang'];
$targetLang = $_GET['targetLang'];
$editID = $_GET['editID'];

$error = "FALSE";


switch ($editType) {
    case 'newtext':
        $query = "UPDATE linguisticcost SET newTextRate=" . ($newValue * 1000) . " WHERE srcLang='" . $sourceLang . "' AND targetLang='" . $targetLang . "'";
        break;

    case 'fuzzy':
        $query = "UPDATE linguisticcost SET fuzzyTextRate=" . ($newValue * 1000) . " WHERE srcLang='" . $sourceLang . "' AND targetLang='" . $targetLang . "'";
        break;

    case 'match':
        $query = "UPDATE linguisticcost SET matchTextRate=" . ($newValue * 1000) . " WHERE srcLang='" . $sourceLang . "' AND targetLang='" . $targetLang . "'";
        break;

    case 'trans':
        $query = "UPDATE linguisticcost SET transHourly=" . ($newValue * 1000) . " WHERE srcLang='" . $sourceLang . "' AND targetLang='" . $targetLang . "'";
        break;

    case 'proof':
        $query = "UPDATE linguisticcost SET prHourly=" . ($newValue * 1000) . " WHERE srcLang='" . $sourceLang . "' AND targetLang='" . $targetLang . "'";
        break;
}

$myDBConn = new mysqli(DBServerName, UserName, Password, DBName);
if ($myDBConn->connect_errno) {
    $error = "TRUE";
} else {

    $result = $myDBConn->query($query);
}

if (!$result) {
    $error = "TRUE";
} else {
    $error = "FALSE";
}

$myDBConn->close();


header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="ISO-8859-1"?><update>';
echo "<errorflag>" . $error . "</errorflag>";
echo "<updatedvalue>" . $newValue . "</updatedvalue>";
echo "<editid>" . $editID . "</editid>";
echo "</update>";
?>
