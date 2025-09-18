<!DOCTYPE HTML>
<html>  
<head>
<style>
.cent
{
   margin:auto;
   width:300px;
   height:300px;
   border: 2px solid black;
   padding:10px;
}


</style>


</head>

<body>


<?php

$flag='';

function SmsApi($ph,$sms,$port)
{
    global $flag;
    $szUrNmPswd = 'ApiUserAdmin'.':'.'123456';
    $strUrl ='http://192.168.0.160/API/TaskHandle';
    $strPost ='{"event":"txsms","userid":"0","num":"'.
              $ph.
              '","port":"'.
              $port.
              '","encoding":"0","smsinfo":"'.
              $sms.
              '"}';

    $headerArray =array("Content-type:application/json;charset='utf-8'","Accept:application/json");
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_ENCODING, "");
    curl_setopt($curl, CURLOPT_URL, $strUrl);
    //Add user authentication information (username and password) here
    curl_setopt($curl, CURLOPT_USERPWD, $szUrNmPswd);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,FALSE);
    curl_setopt($curl, CURLOPT_POST, 1);
    //strPost saves POST request behavior, such as: {"event":"getportinfo"}
    curl_setopt($curl, CURLOPT_POSTFIELDS, $strPost);
    curl_setopt($curl,CURLOPT_HTTPHEADER,$headerArray);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($curl);
    //echo $response;

    $opt = json_decode($response,true);
    $flag = $opt['result'];   
}


if ($_SERVER["REQUEST_METHOD"] == "POST")
{
  //global $flag;
  $ph = $_POST["number"];
  $sms = $_POST["sms"];
  $port = $_POST["port"];
  SmsApi($ph,$sms,$port);
}

?>

<div class="cent" >
    <form action="sms.php" method="post" >
       <div style="margin:5px;" >Ph. No.: <br> <input type="text" name="number"> <br><br> </div>
       <div style="margin:5px;"> SMS: <br>     <textarea name="sms" rows="3" cols="20"></textarea><br><br> </div>
       <div style="margin:5px;"> PORT: <br>
            <select id="port" name="port">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4" selected>4</option>
            </select>
            <br><br>
        <input type="submit" value=" SEND SMS ">
    </form>

  <p>
    <?php
      global $flag;
      echo 'Status:'.$flag;
    ?>
 </p>

</div>
</body>
</html>

