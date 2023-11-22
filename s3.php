<?php
if(isset($_GET["email"])){
  
  header('Content-Type: application/json; charset=utf-8');
  $email=$_GET["email"];

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.reversecontact.com/enrichment?apikey=sk_live_652523ab03a13c05ef2afa5a_key_493rje10jdb&email='.$email,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
  ));

  $response = curl_exec($curl);

  curl_close($curl);
  echo json_encode(json_decode($response,true));

}


?>