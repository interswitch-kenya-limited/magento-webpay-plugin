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
$installer = $this;

$helper = Mage::helper('interswitch_pay');

$text = 'The Interswitch Inline Extension has been successfully installed and is ready to be configured.';

Mage::getModel('adminnotification/inbox')->addMajor(
    $helper->__($text),
    $helper->__($text),
    '',
    true
);
