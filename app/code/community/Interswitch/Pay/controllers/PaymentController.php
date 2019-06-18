<?php
/**
 * interswitch_pay Extension
 *
 * DISCLAIMER
 * This file will not be supported if it is modified.
 *
 */

class Interswitch_Pay_PaymentController extends Mage_Core_Controller_Front_Action
{
    public function cancelAction()
    {
        Mage::getSingleton('core/session')->addError(
            Mage::helper('interswitch_pay')->__("Payment cancelled."));

        $session = Mage::getSingleton('checkout/session');
        if ($session->getLastRealOrderId())
        {
            $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
            if ($order->getId())
            {
                //Cancel order
                if ($order->getState() != Mage_Sales_Model_Order::STATE_CANCELED)
                {
                    $order->registerCancellation("Canceled by Customer")->save();
                }
                $quote = Mage::getModel('sales/quote')->load($order->getQuoteId());
                //Return quote
                if ($quote->getId())
                {
                    $quote->setIsActive(1)
                        ->setReservedOrderId(NULL)
                        ->save();
                    $session->replaceQuote($quote);
                }

                //Unset data
                $session->unsLastRealOrderId();
            }
        }

        return $this->getResponse()->setRedirect( Mage::getUrl('checkout/onepage'));
    }

    public function payAction()
    {
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('Mage_Core_Block_Template','interswitch_pay',array('template' => 'interswitch/pay.phtml'));
        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
    }

    public function responseAction()
    {
      
        $success = false;
    

       // $orderId = $this->getRequest()->get("orderId");
        $reference = $this->getRequest()->get("reference");
        $refLen=strlen($reference);
        $reference=substr($reference,0, $refLen-1);

        // Both are required
        if( !$reference){
            Mage::getSingleton('core/session')->addError(
                Mage::helper('interswitch_pay')->__("No reference defined."));
    
            $session = Mage::getSingleton('checkout/session');
            if ($session->getLastRealOrderId())
            {
                $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
                if ($order->getId())
                {
                    //Cancel order
                    if ($order->getState() != Mage_Sales_Model_Order::STATE_CANCELED)
                    {
                        $order->registerCancellation("Cancelled because no reference")->save();
                    }
                    $quote = Mage::getModel('sales/quote')->load($order->getQuoteId());
                    //Return quote
                    if ($quote->getId())
                    {
                        $quote->setIsActive(1)
                            ->setReservedOrderId(NULL)
                            ->save();
                        $session->replaceQuote($quote);
                    }
    
                    //Unset data
                    $session->unsLastRealOrderId();
                }
            }
    
            return $this->getResponse()->setRedirect( Mage::getUrl('checkout/onepage'));
         
        }

  
        $order = Mage::getModel('sales/order')->loadByIncrementId($reference);
 
        if(!$order){
            Mage::getSingleton('core/session')->addError(
                Mage::helper('interswitch_pay')->__("No orders shown."));
            return $this->getResponse()->setRedirect( Mage::getUrl('checkout/onepage'));
        }
        
        // verify transaction with interswitch_pay
        $amount = (int)$order->grand_total;
        $result =  Mage::helper('interswitch_pay')->verifyTransaction($reference);
        //var_dump($result);
        //return;
        if($result->error)
        {
            Mage::getModel('adminnotification/inbox')->addMajor(
                Mage::helper('interswitch_pay')->__("Error while attempting to verify transaction: reference: " . $reference),
                Mage::helper('interswitch_pay')->__($result->ResponseDescription),
                '',
                true
            );
        }
        elseif($result->transactionRemoteResponseCode === "0")
        {
            $description = '';
            $order->setData('state', Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, true);
            $order->save();
            
            $description = 'Payment was successful. <br> Transaction Reference:'.$reference;
           
            $history = $order->addStatusHistoryComment($description, false);
          
            $history->setIsCustomerNotified(true);
            $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'Payment Success.');
            $order->save();

            
            Mage::getSingleton('checkout/session')->unsQuoteId();
            
            Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/success');
            $success = true;
        }
        else
        {
           $order->setState(Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW, true, $result->errors[0]->message);
        $order->save();

        Mage::getSingleton('checkout/session')->unsQuoteId();
        }


        if(!$success){
           
           Mage::getSingleton('core/session')->addError(
            Mage::helper('interswitch_pay')->__($result->errors[0]->message));
            Mage_Core_Controller_Varien_Action::_redirect('checkout/cart');
        }

    }
}
