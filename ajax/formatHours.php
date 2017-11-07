<?php
require_once (__DIR__ . '/../database.php');


if (!isset($_GET['format'])) {
	die();
}

$keyword = $_GET['format'];
$data = getFormatHoursData($keyword);
ob_clean();
echo json_encode($data);

