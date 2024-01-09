<?php
class PhonePe_ctrl
{
    function req($req=null)
    {
        // Replace these with your actual PhonePe API credentials
        $keyIndex = 1;
        $apiKey = 'b034a6df-e7f2-4aad-bf3e-882126b46212';
        $merchantId = 'M22SLNNE0AJ5Y';

        // Prepare the payment request data (you should customize this)
        $paymentData = array(
            'merchantId' => $merchantId,
            'merchantTransactionId' => uniqid('merchant'),
            "merchantUserId" => "pkarnTest",
            'amount' => 1000, // Amount in paisa (10 INR)
            'redirectUrl' => BASEURI."/phonepe-req",
            'redirectMode' => "POST",
            'callbackUrl' => BASEURI."/phonepe-res",
            "merchantOrderId" => uniqid('order'),
            "mobileNumber" => 8825137323,
            "message" => "This is test order",
            "email" => "mail2pkarn@gmail.com",
            "shortName" => "Pradeep",
            "paymentInstrument" => array(
                "type" => "PAY_PAGE",
            )
        );


        $jsonencode = json_encode($paymentData);
        $payloadMain = base64_encode($jsonencode);


        $salt_index = $keyIndex; //key index 1
        $payload = $payloadMain . "/pg/v1/pay" . $apiKey;
        $sha256 = hash("sha256", $payload);
        $final_x_header = $sha256 . '###' . $salt_index;
        $request = json_encode(array('request' => $payloadMain));


        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $request,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "X-VERIFY: " . $final_x_header,
                "accept: application/json"
            ],
        ]);
    }
    function res($req=null) {
        
    }
}