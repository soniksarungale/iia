<?php
require_once "../etl/api/autoload/init.php";

$db = Database::getInstance();
APIMaster::constructStatic($db);
API::constructStatic($db);
SchemaMapping::constructStatic($db);

$s1 = APIMaster::fetchByGroup("1");
$s2 = APIMaster::fetchByGroup("2");

$s1Mapping = APIMaster::fetchMapping($s1["api_master_id"]);
$s2Mapping = APIMaster::fetchMapping($s2["api_master_id"]);

$email="";
$inValid=""; 
$isPrivate=false;
$yesData=false;

if(isset($_POST["username"])){
    $email=$_POST["email"];
    $s3 = APIMaster::fetchByGroup("3");
    $s3Mapping = APIMaster::fetchMapping($s3["api_master_id"]);

    $link="https://linkedin.com/in/".$_POST["username"]."/";


    $s3data = API::FetchData($s3["api_code"],$s3["api_bearer_token"],array($s3["api_db_key"]=>$link)); 
   
   
        $db3Array = Functions::materializedArr($s3Mapping,$s3data);
if($db3Array){
    $customer = SchemaMapping::fetchDataByEmail($email);


    if($db3Array["full_name"]==$customer["full_name"]){
        $sql3 = Functions::materializedSql($db3Array);
        
        $sql3.=', username = "'.$_POST["username"].'",  url = "'.$link.'"';
        $condition = 'email = "'.$email.'"';

        SchemaMapping::updateMaterializedView($sql3, $condition);    
        $yesData=true;

        $full_name=$db3Array["full_name"];
        
    }else{
        $inValid=true;
    }
   

}else{
    $inValid=true;
}


}elseif(isset($_GET["code"])){

    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.linkedin.com/oauth/v2/accessToken',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => 'grant_type=authorization_code&code='.$_GET["code"].'&client_id=775on1i1wmpzyv&client_secret=UdsGnjDl6wOEbNn7&redirect_uri=http%3A%2F%2Flocalhost%2Fiia%2Fdata.php',
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/x-www-form-urlencoded',
        'Cookie: bcookie="v=2&3bf8fba6-2d2a-4522-81f0-a35172952309"; lang=v=2&lang=en-us; lidc="b=VB89:s=V:r=V:a=V:p=V:g=3508:u=60:x=1:i=1699209527:t=1699214655:v=2:sig=AQEdJUDI4kkb3HXk2UoGu2J8E97RihEm"; bscookie="v=1&202308271001059664dd9b-b288-4aac-8ce5-2b42ad87ab6bAQFiQhEHhvKWd6RWufxBi_gA5sM3oqXv"'
    ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $response=json_decode($response,true);




   $token=$response["access_token"];

   $s1data = API::FetchData($s1["api_code"],$token,array());  
   
   $dbArray = Functions::materializedArr($s1Mapping,$s1data);
   $full_name=$dbArray["full_name"];
   $sql1 = Functions::materializedSql($dbArray);

   if(SchemaMapping::fetchDataByEmail($dbArray["email"])){
        $condition = 'email = "'.$dbArray["email"].'"';
        SchemaMapping::updateMaterializedView($sql1, $condition);
    }else{
        $sql1.=', auth_token = "'.$token.'"';
        SchemaMapping::insertMaterializedView($sql1);
    }

    $customer = SchemaMapping::fetchDataByEmail($dbArray["email"]);
    $customer_arr=array();
    foreach($customer as $key => $val){ $customer_arr[$key] = $val; }


    $s2data = API::FetchData($s2["api_code"],"",array($s2["api_db_key"]=>$customer_arr[$s2["api_db_val"]])); 
   

    if(isset($s2data["person"])){
        if(!$s2data["person"]){
            $isPrivate=true;
        }
    }
    if(!$isPrivate){
        $db2Array = Functions::materializedArr($s2Mapping,$s2data);
        $sql2 = Functions::materializedSql($db2Array);
        
        $condition = 'email = "'.$db2Array["email"].'"';
        SchemaMapping::updateMaterializedView($sql2, $condition);    
        $yesData=true;
    }

    
    $email = $dbArray["email"];

}
$userData=array();
if($yesData){
    $userData=SchemaMapping::fetchDataByEmail($email);
}

//Proxy curl
//Nx0-POBoBXf5rqM2oQ_lqw 


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S1</title>
</head>
<body>
    <a href="https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=775on1i1wmpzyv&redirect_uri=http%3A%2F%2Flocalhost%2Fiia%2Fdata.php&state=foobar&scope=openid%20profile%20email"><img src="https://i.stack.imgur.com/mKpeu.png" alt="" style="width: 200px;"></a>
<br>

<?php if(!$isPrivate && !$inValid){ ?>
<table>
    <thead>
        <tr>
            <th>Field</th>
            <th>Value</th>
        </tr>
    </thead>
    <tbody>
        <?php if($userData){ foreach($userData as $key => $val){ ?>
            <tr>
                <td><?php echo $key; ?></td>
                <td><?php echo $val; ?></td>
            </tr>
        <?php } } ?>

    </tbody>
</table>
<?php }else{ ?>
    <form action="data.php" method="POST">
    Username: <input type="text" value="<?php echo Functions::possibleUsername($full_name); ?>" name="username">
    <input type="hidden" value="<?php echo $email; ?>" name="email">
    </form>
    <?php if ($inValid){ echo "Invalid username"; } ?>
<?php } ?>
</body>
</html>