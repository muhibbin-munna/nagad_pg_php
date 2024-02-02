<?php

function HttpGet($url)
{
    $ch = curl_init();
    $timeout = 10;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/0 (Windows; U; Windows NT 0; zh-CN; rv:3)");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $file_contents = curl_exec($ch);
    echo curl_error($ch);
    curl_close($ch);
    return $file_contents;
}

$Query_String  = explode("&", explode("?", $_SERVER['REQUEST_URI'])[1] );
$payment_ref_id = substr($Query_String[2], 15); 
$url = "http://sandbox.mynagad.com:10080/remote-payment-gateway-1.0/api/dfs/verify/payment/".$payment_ref_id;
$json = HttpGet($url);
$arr = json_decode($json, true);

echo json_encode($arr);          
?>


