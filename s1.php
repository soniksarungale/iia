<?php
if(isset($_GET["token"])){
    header('Content-Type: application/json; charset=utf-8');
   $token=$_GET["token"];
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.linkedin.com/v2/userinfo',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer '.$token,
    'Content-Type: application/json',
  ),
));

$response = curl_exec($curl);

curl_close($curl);

echo json_encode(json_decode($response,true));



}

?>