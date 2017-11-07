<?php
require_once (__DIR__ . '/../database.php');


if (!isset($_GET['term'])) {
	die();
}

$keyword = $_GET['term'];
$data = searchForKeyword($keyword);
ob_clean();
echo json_encode($data);

