<?php
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../includes/gatewayfunctions.php';
require_once __DIR__ . '/../../../includes/invoicefunctions.php';

require_once(dirname(__FILE__) . '/../phonepe-sdk/encdec_phonepe.php');

$gatewayModuleName = basename(__FILE__, '.php');

$gatewayParams = getGatewayVariables("phonepe");

if (!$gatewayParams['type']) {
    die("Module Not Activated");
}

$checkSumValue = $_SERVER['HTTP_X_VERIFY'];

$body = file_get_contents('php://input');
$responseData = json_decode($body, true);

$response = $responseData['response'];

$SaltKey = $gatewayParams['SaltKey'];
$SaltIndex = $gatewayParams['SaltIndex'];

$data = decode_data($response);

$invoiceId = $data['data']['merchantTransactionId'];
$transactionId = $data['data']['transactionId'];

$paymentAmount = $data['data']['amount'];

$transactionStatus = $data['code'] == "PAYMENT_SUCCESS" ? "success" : "failed";

$invoice_arr  = explode('_',$invoiceId);
$invoiceIdR = $invoice_arr[0];

$apiData = array('bepData' => $response,
                    'saltKey' => $SaltKey,
                    'saltIndex' => $SaltIndex,
                    'type' => 'response');
$checkSumValueNew = generateCheckSum($apiData);

if ($checkSumValue != $checkSumValueNew) {
    $transactionStatus = 'failed';
}

if ($transactionStatus=="success") {

    $invoiceIdR = checkCbInvoiceID($invoiceIdR, $gatewayParams['name']);

    checkCbTransID($transactionId);

    logTransaction($gatewayParams['name'], $data, $transactionStatus);

    addInvoicePayment(
        $invoiceIdR,
        $transactionId,
        $paymentAmount/100,
        0,
        $gatewayModuleName
    );

}

