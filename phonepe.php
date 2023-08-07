<?php

require_once(dirname(__FILE__) . '/phonepe-sdk/encdec_phonepe.php');

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

function phonepe_MetaData()
{
    return array(
        'DisplayName' => 'Phonepe Gateway Module',
        'APIVersion' => '1.0',
        'DisableLocalCreditCardInput' => true,
        'TokenisedStorage' => false,
    );
}

function phonepe_config()
{
    $config_array = array('FriendlyName' => array('Type' => 'System', 'Value' => 'Phonepe'),
                        'MerchantId' => array('FriendlyName' => 'Merchant ID', 'Type' => 'text', 'Size' => '30'),
                        'SaltKey' => array('FriendlyName' => 'Salt Key', 'Type' => 'text', 'Size' => '50'),
                        'SaltIndex' => array('FriendlyName' => 'Salt Index', 'Type' => 'text', 'Size' => '1'),
                        'ProductionUrl' => array('FriendlyName' => 'Production Url', 'Type' => 'text', 'Size' => '100'),
                         );
    return $config_array;
}

function phonepe_link($params)
{
    $MerchantId = $params['MerchantId'];
    $SaltKey = $params['SaltKey'];
    $SaltIndex = $params['SaltIndex'];
    $ProductionUrl = $params['ProductionUrl'];
    
    $systemUrl = $params['systemurl'];
    $returnUrl = $params['returnurl'];
    $moduleName = $params['paymentmethod'];

    $invoiceId = $params['invoiceid'] . '_' . time();
    $amount = (int)($params['amount'] * 100);

    $phone = $params['clientdetails']['phonenumber'];
    $userId = $params['clientdetails']['id'];

    $jsonData = array(
        'merchantId' => $MerchantId,
        'merchantTransactionId' => $invoiceId,
        'merchantUserId' => $userId,
        'amount' => $amount,
        'redirectUrl' => $returnUrl,
        'redirectMode' => 'POST',
        'callbackUrl' => "{$systemUrl}/modules/gateways/callback/{$moduleName}.php",
        'mobileNumber' => $phone,
        'paymentInstrument' => array('type' => 'PAY_PAGE')
    );

    $bepData = encode_data($jsonData);

    $apiSalt = array(
        'bepData' => $bepData,
        'saltKey' => $SaltKey,
        'saltIndex' => $SaltIndex,
        'type' => 'pay'
    );

    $checkSumValue = generateCheckSum($apiSalt);

    $apiData = array(
        'bepData' => $bepData,
        'checkSumValue' => $checkSumValue
    );

    $url = callApi($ProductionUrl.'/pay', $apiData);

    $htmlOutput = '<form method="post" action="' . $url . '">';
    $htmlOutput .= '<input type="submit" value="Pay Now" />';
    $htmlOutput .= '</form>';

    return $htmlOutput;
}


function phonepe_refund($params)
{
    $MerchantId = $params['MerchantId'];
    $SaltKey = $params['SaltKey'];
    $SaltIndex = $params['SaltIndex'];
    $ProductionUrl = $params['ProductionUrl'];
    
    $invoiceId = $params['invoiceid'] . '_' . time();
    $transactionIdToRefund = $params['transid'];
    $refundAmount = $params['amount'];
    $userId = $params['clientdetails']['id'];

    $systemUrl = $params['systemurl'];

    $jsonData = array(
        'merchantId' => $MerchantId,
        'merchantUserId' => $userId,
        'originalTransactionId' => $transactionIdToRefund,
        'merchantTransactionId' => $invoiceId,
        'amount' => $refundAmount,
        'callbackUrl' => "{$systemUrl}"
    );

    $bepData = encode_data($jsonData);

    $apiSalt = array(
        'bepData' => $bepData,
        'saltKey' => $SaltKey,
        'saltIndex' => $SaltIndex,
        'type' => 'refund'
    );

    $checkSumValue = generateCheckSum($apiSalt);

    $apiData = array(
        'bepData' => $bepData,
        'checkSumValue' => $checkSumValue
    );

    $url = callApi($ProductionUrl.'/refund', $apiData);


    return array(
        'status' => 'success',
        'rawdata' => 'Refund request raised',
        'transid' => $refundTransactionId,
        'fees' => 0,
    );
}

?>
