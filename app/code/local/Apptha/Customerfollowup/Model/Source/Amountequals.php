<?php
/**
 * Apptha
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category    Apptha
 * @package     Apptha_Customer-Follow-Up
 * @version     1.1
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 * */
class Apptha_Customerfollowup_Model_Source_Amountequals extends Mage_Core_Model_Abstract
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'dm', 'label'=>Mage::helper('adminhtml')->__("Doesn't matter")),
            array('value' => 'eq', 'label'=>Mage::helper('adminhtml')->__('Equals to')),
            array('value' => 'gt', 'label'=>Mage::helper('adminhtml')->__('Greater than')),
            array('value' => 'gteq', 'label'=>Mage::helper('adminhtml')->__('Equals or greater than')),
            array('value' => 'lt', 'label'=>Mage::helper('adminhtml')->__('Less than')),
            array('value' => 'lteq', 'label'=>Mage::helper('adminhtml')->__('Equals or less than')),
            array('value' => 'neq', 'label'=>Mage::helper('adminhtml')->__('Not equals to')),

        );
    }

}