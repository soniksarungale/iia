<?php
require_once( 'simple_html_dom.php' );
if($_GET["email"]){
    $email=$_GET["email"];
    $prefix = substr($email, 0, strrpos($email, '@'));
    $username = str_replace( array( '\'', '"',
    ',' , ';', '<', '>', '.', '_' ), '', $prefix);
    
    $url="https://www.linkedin.com/in/".$username."/";
    $stock = file_get_html($url);

var_dump($stock);
}

?>