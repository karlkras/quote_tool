<?php
require_once (__DIR__ . '/../database.php');


if (!isset($_GET['company'])) {
	die();
}

$keyword = $_GET['company'];
$data = getCompanyData($keyword);
ob_clean();
echo json_encode($data);

