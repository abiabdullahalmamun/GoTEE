<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
	//require_once ('newDBconnect.php');
// echo 'okkkk'; 
// exit(); 
date_default_timezone_set('Asia/Dhaka');

$webfile = $_GET['web'];
 
$query = http_build_query([
    // "webFileNumber" => "BGDDV4E3EF26"
    "webFileNumber" => $webfile , 
]);

$curl = curl_init(); curl_setopt_array($curl, 
array(   
CURLOPT_URL => 'https://pdp.ivacbd.com/api/v1/file/appointment/status?'. $query,
CURLOPT_RETURNTRANSFER => true, 
CURLOPT_ENCODING => '', 
CURLOPT_MAXREDIRS => 10, 
CURLOPT_TIMEOUT => 0, 
CURLOPT_FOLLOWLOCATION => true, 
CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, 
CURLOPT_CUSTOMREQUEST => 'POST', 
CURLOPT_POSTFIELDS => null,
CURLOPT_HTTPHEADER => array( 'Content-Type: application/x-www-form-urlencoded', 'Authorization: 35375202-51b2-463c-8be4-558f3e4df9bc' ), )); 
$response = curl_exec($curl); 

if (curl_errno($curl)) { echo "cURL Error #: " . curl_error($curl); } else { echo $response; } curl_close($curl); 

?>
