<?php
/**
 * Interswitch Extension
 *
 * DISCLAIMER
 * This file will not be supported if it is modified.
 *
 * @category   Interswitch
 * @author     Interswitch
 * @package    Interswitch
 * @copyright  Copyright (c) 2017 Interswitch. (https://www.interswitch.com/)
 * @license    https://raw.githubusercontent.com/InterswitchHQ/interswitch-magento/master/LICENSE   MIT License (MIT)
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
