<?php
/**
 * Interswitch Extension
 *
 * DISCLAIMER
 * This file will not be supported if it is modified.
 *
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
