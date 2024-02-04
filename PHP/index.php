<?php

require_once ('./helper.php');

session_start();

date_default_timezone_set('Asia/Dhaka');

$MerchantID = "";
$DateTime = Date('YmdHis');
$amount = "10.7";
$OrderId = 'TEST'.strtotime("now").rand(1000, 10000);
$random = generateRandomString();

$PostURL = "http://sandbox.mynagad.com:10080/remote-payment-gateway-1.0/api/dfs/check-out/initialize/" . $MerchantID . "/" . $OrderId;

$_SESSION['orderId'] = $OrderId;

$merchantCallbackURL = "http://localhost/gentle/merchant-callback-website.php";

$SensitiveData = array(
    'merchantId' => $MerchantID,
    'datetime' => $DateTime,
    'orderId' => $OrderId,
    'challenge' => $random
);
//var_dump($SensitiveData); exit;
$PostData = array(
    'accountNumber' => '01711428036', //Replace with Merchant Number (not mandatory)
    'dateTime' => $DateTime,
    'sensitiveData' => EncryptDataWithPublicKey(json_encode($SensitiveData)),
    'signature' => SignatureGenerate(json_encode($SensitiveData))
);

$Result_Data = HttpPostMethod($PostURL, $PostData);


if (isset($Result_Data['sensitiveData']) && isset($Result_Data['signature'])) {
    if ($Result_Data['sensitiveData'] != "" && $Result_Data['signature'] != "") {

        $PlainResponse = json_decode(DecryptDataWithPrivateKey($Result_Data['sensitiveData']), true);
        // echo $PlainResponse;

        if (isset($PlainResponse['paymentReferenceId']) && isset($PlainResponse['challenge'])) {


            $paymentReferenceId = $PlainResponse['paymentReferenceId'];


            $randomServer = $PlainResponse['challenge'];

            $SensitiveDataOrder = array(
                'merchantId' => $MerchantID,
                'orderId' => $OrderId,
                'currencyCode' => '050',
                'amount' => $amount,
                'challenge' => $randomServer
            );

            
            $logo = "https://my-brand.be/wp-content/uploads/2021/08/my-brand-logo.jpg";
            
            $merchantAdditionalInfo = '{"serviceName":"Brand Name", "serviceLogoURL": "'.$logo.'", "additionalFieldNameEN": "Type", "additionalFieldNameBN": "টাইপ","additionalFieldValue": "Payment"}';

            $PostDataOrder = array(
                'sensitiveData' => EncryptDataWithPublicKey(json_encode($SensitiveDataOrder)),
                'signature' => SignatureGenerate(json_encode($SensitiveDataOrder)),
                'merchantCallbackURL' => $merchantCallbackURL,
                'additionalMerchantInfo' => json_decode($merchantAdditionalInfo)
            );

                      
            $OrderSubmitUrl = "http://sandbox.mynagad.com:10080/remote-payment-gateway-1.0/api/dfs/check-out/complete/" . $paymentReferenceId;

            $Result_Data_Order = HttpPostMethod($OrderSubmitUrl, $PostDataOrder);
            
                if ($Result_Data_Order['status'] == "Success") {
                    $url = json_encode($Result_Data_Order['callBackUrl']);   
                    echo "<script>window.open($url, '_self')</script>";  
                            
                }
                else {
                    echo json_encode($Result_Data_Order);
                     
                }
        } else {
            echo json_encode($PlainResponse);
                
        }
    }
}

?>