<?php
/**
 * Interswitch Extension
 *
 * DISCLAIMER
 * This file will not be supported if it is modified.

 */
class Interswitch_Pay_Block_Info_Inline extends Mage_Payment_Block_Info
{
  protected function _prepareSpecificInformation($transport = null)
  {
    if (null !== $this->_paymentSpecificInformation)
    {
      return $this->_paymentSpecificInformation;
    }

    return parent::_prepareSpecificInformation($transport);
  }
}
