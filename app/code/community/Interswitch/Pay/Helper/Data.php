<?php
/**
 * Interswitch Extension
 *
 * DISCLAIMER
 * This file will not be supported if it is modified.
 */
class Interswitch_Pay_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_TEST_MODE        = 'payment/interswitch_pay/test_mode';
    const XML_PATH_USE_STANDARD        = 'payment/interswitch_pay/use_standard';

    const XML_PATH_MERCHANT_CODE  = 'payment/interswitch_pay/merchant_code';
    const XML_PATH_TERMINAL_ID ='payment/interswitch_pay/terminal_id';
    const XML_PATH_DOMAIN ='payment/interswitch_pay/domain';
    const XML_PATH_ICON_URL ='payment/interswitch_pay/icon_url';
    const XML_PATH_CLIENT_ID ='payment/interswitch_pay/client_id';
    const XML_PATH_CLIENT_SECRET ='payment/interswitch_pay/client_secret';


    const XML_PATH_ORDER_STATUS  = 'payment/interswitch_pay/order_status';
    


    function getGatewayMode(){
        if(Mage::getStoreConfig(Interswitch_Pay_Helper_Data::XML_PATH_TEST_MODE)){
            return 'Test';
        } else{
            return 'Live';
        }
    }

    function getJsScriptPath(){
        if(Mage::getStoreConfig(Interswitch_Pay_Helper_Data::XML_PATH_TEST_MODE)){
           return  'https://testmerchant.interswitch-ke.com/webpay/button/functions.js';
        } else{
            return  'https://merchant.interswitch-ke.com/webpay/button/functions.js';
        }
    }


    function generaterTransactionReference($orderId){
        $characters = '0abcd'.time().'efz1nrstu2o'.time().'123456'.time().'pqghijk'.time().'lm3456vwxy'.time().'789';
        $characters = strtoupper($characters);
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 12; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        
        return $orderId;
    }

    function genNonce($length) {
      
        $characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"; 
  
        $strlength = strlen($characters);
        
        $random = '';
        
        for ($i = 0; $i < $length; $i++) {
          $random .= $characters[rand(0, $strlength - 1)];
        }
  
        return $random;
        
  }

    function verifyTransaction($reference)
    {
        if(Mage::getStoreConfig(Interswitch_Pay_Helper_Data::XML_PATH_TEST_MODE)){
            $url =  'https://testids.interswitch.co.ke:9080/api/v1/merchant/cards/transactions/'.$reference;
           

        } else {
            $url =  'https://esb.interswitch-ke.com:18082/api/v1/merchant/cards/transactions/'.$reference;
        }
       

       
        $clientID=trim(Mage::getStoreConfig(Interswitch_Pay_Helper_Data::XML_PATH_CLIENT_ID));
        $clientSecret=trim(Mage::getStoreConfig(Interswitch_Pay_Helper_Data::XML_PATH_CLIENT_SECRET));
        $timestamp=time();
        $nonce=Interswitch_Pay_Helper_Data::genNonce(20).$timestamp;
        $signatureText =  ($httpMethod."&".rawurlencode($url)."&".$timestamp."&".$nonce."&".$clientID."&".$clientSecret);
        $signature=base64_encode(hash('sha512',$signatureText,true));
        $signatureMethod='SHA512';
        $merchantid=trim(Mage::getStoreConfig(Interswitch_Pay_Helper_Data::XML_PATH_MERCHANT_CODE));
        $domain=trim(Mage::getStoreConfig(Interswitch_Pay_Helper_Data::XML_PATH_DOMAIN));
        $httpmethod='GET';
        $authorization = 'InterswitchAuth '.base64_encode($clientID);
        $contentType='application/json';
        echo $authorization;


        $curl=curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Content-Type: $contentType",
            "Authorization: $authorization",
            "Timestamp: $timestamp",
            "Nonce: $nonce",
            "Signature: $signature",
            "SignatureMethod: $signatureMethod",
            "merchantid: $merchantid ",
            "domain: $domain"
        

        ));

        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);



        $response = curl_exec($curl);
        //var_dump($curl);
        echo $response;
        $err=curl_error($curl);
       // echo $err;
        curl_close($curl);


        $result =  json_decode($response);
       // echo $result;
        $order_status = Mage::getStoreConfig(Interswitch_Pay_Helper_Data::XML_PATH_ORDER_STATUS);
        $result->order_status = $order_status;

        return $result;
    }


    function getFormParams()
    {
        $order = new Mage_Sales_Model_Order();
        $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();

        // return blank params if no order is found
        if(!$orderId){
            return array();
        }
        $order->loadByIncrementId($orderId);

        // get an email for this transaction
        $billing  = $order->getBillingAddress();
        if ($order->getBillingAddress()->getEmail()) {
            $email = $order->getBillingAddress()->getEmail();
        } else {
            $email = $order->getCustomerEmail();
        }

        $customerInfor= $email.'|'.$billing->getFirstname().'|'.$billing->getLastname().'|'.$email.'|'.$billing->getTelephone().'|'.$billing->getCity().'|'.$billing->getCountry().'|'.$billing->getPostcode().'|'.$billing->getStreet()[0].'|'.$billing->getCity();
        $reference = Interswitch_Pay_Helper_Data::generaterTransactionReference($orderId);
        $params = array(
    
            'merchant_code' => trim(Mage::getStoreConfig(Interswitch_Pay_Helper_Data::XML_PATH_MERCHANT_CODE)),
            'gateway_script'         => Interswitch_Pay_Helper_Data::getJsScriptPath(),
            'reference'         => $reference,
            'orderId'     => $orderId,
            'nextUrl'     => Mage::getUrl('interswitch/payment/response?reference='.$reference),
            'cancelUrl'   => Mage::getUrl('interswitch/payment/cancel'),
            'amount'      => (round($order->getGrandTotal(), 2)*100),
            'currency'    => $order->getOrderCurrencyCode(),
            'narration'     => $this->__('Order ID: ') . $orderId,
            'merchant_code' => trim(Mage::getStoreConfig(Interswitch_Pay_Helper_Data::XML_PATH_MERCHANT_CODE)),
            'terminal_id' => trim(Mage::getStoreConfig(Interswitch_Pay_Helper_Data::XML_PATH_TERMINAL_ID)),
            'domain' => trim(Mage::getStoreConfig(Interswitch_Pay_Helper_Data::XML_PATH_DOMAIN)),
            'icon_url' => trim(Mage::getStoreConfig(Interswitch_Pay_Helper_Data::XML_PATH_ICON_URL)),
            'channel' => 'WEB',
            'client_id' => trim(Mage::getStoreConfig(Interswitch_Pay_Helper_Data::XML_PATH_CLIENT_ID)),
            'client_secret' => trim(Mage::getStoreConfig(Interswitch_Pay_Helper_Data::XML_PATH_CLIENT_SECRET)),
            'customerInfor' =>$customerInfor,
            'preauth' => "1",
            'fee' => "0"
            

        );

        return $params;
    }
}
