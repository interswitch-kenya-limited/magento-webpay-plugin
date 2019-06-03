<?php
/**
 * Interswitch Extension
 *
 * DISCLAIMER
 * This file will not be supported if it is modified.
 *
 */
class Interswitch_Pay_Block_Form_Inline extends Mage_Payment_Block_Form
{
  protected function _construct()
  {
    parent::_construct();
    $this->setTemplate('interswitch/form/interswitch_pay.phtml');
  }
}
