<?php
/**
 * Interswitch Extension
 *
 * DISCLAIMER
 * This file will not be supported if it is modified.
 *
 */
class Interswitch_Pay_Model_Paymentmethod extends Mage_Payment_Model_Method_Abstract {
    protected $_code  = 'interswitch_pay';
    protected $_formBlockType = 'interswitch_pay/form_inline';
    protected $_infoBlockType = 'interswitch_pay/info_inline';

    public function assignData($data)
    {
        $info = $this->getInfoInstance();

        return $this;
    }

    public function validate()
    {
        parent::validate();
        $info = $this->getInfoInstance();

        return $this;
    }

    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('interswitch/payment/pay');
    }
}
