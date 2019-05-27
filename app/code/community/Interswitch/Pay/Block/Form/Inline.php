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
class Interswitch_Pay_Block_Form_Inline extends Mage_Payment_Block_Form
{
  protected function _construct()
  {
    parent::_construct();
    $this->setTemplate('interswitch/form/interswitch_pay.phtml');
  }
}
