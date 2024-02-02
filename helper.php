<?php

function generateRandomString($length = 40)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function EncryptDataWithPublicKey($data)
{
    $pgPublicKey = "";
    $public_key = "-----BEGIN PUBLIC KEY-----\n" . $pgPublicKey . "\n-----END PUBLIC KEY-----";

    $key_resource = openssl_get_publickey($public_key);
    openssl_public_encrypt($data, $cryptText, $key_resource);
    return base64_encode($cryptText);
}



function SignatureGenerate($data)
{
    $merchantPrivateKey = "";
    $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . $merchantPrivateKey . "\n-----END RSA PRIVATE KEY-----";
 
    openssl_sign($data, $signature, $private_key, OPENSSL_ALGO_SHA256);
    return base64_encode($signature);
}



function HttpPostMethod($PostURL, $PostData)
{
    $url = curl_init($PostURL);
    $postToken = json_encode($PostData);
    $header = array(
        'Content-Type:application/json',
        'X-KM-Api-Version:v-0.2.0',
        'X-KM-IP-V4:' . get_client_ip(),
        'X-KM-Client-Type:PC_WEB'
    );
    
    curl_setopt($url, CURLOPT_HTTPHEADER, $header);
    curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($url, CURLOPT_POSTFIELDS, $postToken);
    curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($url, CURLOPT_SSL_VERIFYPEER, false); 
    // curl_setopt($url, CURLOPT_HEADER, 1); 
    
    $resultData = curl_exec($url);
    $ResultArray = json_decode($resultData, true);
    $header_size = curl_getinfo($url, CURLINFO_HEADER_SIZE);
    curl_close($url);
        $headers = substr($resultData, 0, $header_size);
        $body = substr($resultData, $header_size);
        // print_r($body);
        // print_r($headers);
    return $ResultArray;

}

function get_client_ip()
{
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

function DecryptDataWithPrivateKey($cryptText)
{
    $merchantPrivateKey = "";
    $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . $merchantPrivateKey . "\n-----END RSA PRIVATE KEY-----";
    openssl_private_decrypt(base64_decode($cryptText), $plain_text, $private_key);
    return $plain_text;
}