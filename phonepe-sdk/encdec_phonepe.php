<?php

function encode_data($jsonArray)
{
    $jsonData = json_encode($jsonArray);
    $base64Encoded = base64_encode($jsonData);
    return $base64Encoded;
}

function decode_data($jsonArray)
{
    $base64Encoded = base64_decode($jsonArray);
    $jsonData = json_decode($base64Encoded, true);
    return $jsonData;
}

function generateCheckSum($data)
{
    if ($data['type'] == 'pay')
    {
        $payload = $data['bepData'] . "/pg/v1/pay" . $data['saltKey'];
    }
    elseif ($data['type'] == 'response') {
        $payload = $data['bepData'] . $data['saltKey'];
    }
    elseif ($data['type'] == 'refund') {
        $payload = $data['bepData'] . '/pg/v1/refund' . $data['saltKey'];
    }
    $hash = hash('sha256', $payload);
    $checksum = $hash . "###" . $data['saltIndex'];
    return $checksum;
}

function callApi($prodUrl, $data)
{
    $curl = curl_init();

    $jsonData = json_encode(['request' => $data['bepData']]);

    curl_setopt_array($curl, [
        CURLOPT_URL => $prodUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $jsonData,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "accept: application/json",
            "X-VERIFY: " . $data['checkSumValue']
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
        return null;
    } else {
        $responseData = json_decode($response, true);
        if (isset($responseData['data']['instrumentResponse']['redirectInfo']['url'])) {
            return $responseData['data']['instrumentResponse']['redirectInfo']['url'];
        }
        else {
            return null;
        }
    }
}

?>
