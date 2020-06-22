<?php

/**
 * Magespacex
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magespacex.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magespacex.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magespacex
 * @package     Magespacex_Storecredit
 * @module      Storecredit
 * @author      Magespacex Developer
 *
 * @copyright   Copyright (c) 2016 Magespacex (http://www.magespacex.com/)
 * @license     http://www.magespacex.com/license-agreement.html
 *
 */
/**
 * Customercredit Block
 *
 * @category    Magespacex
 * @package     Magespacex_Customercredit
 * @author      Magespacex Developer
 */

/**
 * Refund to customer balance functionality block
 *
 */
class Magespacex_Customercredit_Block_Order_Create_Credit extends Mage_Core_Block_Template
{
    /**
     * @return mixed
     */
    public function getCustomerCredit()
    {
        $store = $this->getStore();
        $customer_id = Mage::getSingleton('adminhtml/session_quote')->getCustomerId();
        $customer = Mage::getSingleton('customer/customer')->load($customer_id);
        $credit = $store->convertPrice($customer->getCreditValue());
        $session = Mage::getSingleton('checkout/session');
        if ($session->getBaseCustomerCreditAmount())
            $credit -= $session->getBaseCustomerCreditAmount();
        return $credit;
    }

    /**
     * @return bool
     */
    public function isAssignCredit()
    {
        $data = explode(",", Mage::helper('customercredit')->getGeneralConfig('assign_credit'));
        $customer_id = Mage::getSingleton('adminhtml/session_quote')->getCustomerId();
        $customer = Mage::getSingleton('customer/customer')->load($customer_id);
        foreach ($data as $group) {
            if ($customer->getGroupId() == $group) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function hasCustomerCreditItem()
    {
        $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
        $items = Mage::getSingleton('sales/quote_item')->getCollection();
        $items->addFieldToFilter('quote_id', $quote->getId());
        foreach ($items->getData() as $item) {

            if ($item['product_type'] == 'customercredit') {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function hasCustomerCreditItemOnly()
    {
        $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
        $items = Mage::getSingleton('sales/quote_item')->getCollection();
        $items->addFieldToFilter('quote_id', $quote->getId());
        $hasOnly = false;
        foreach ($items->getData() as $item) {
            if ($item['product_type'] == 'customercredit') {
                $hasOnly = true;
            } else {
                $hasOnly = false;
                break;
            }
        }
        return $hasOnly;
    }

    /**
     * @return mixed
     */
    public function getStore()
    {
        $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
        $store = Mage::app()->getStore($quote->getData('store_id'));
        return $store;
    }

    /**
     * @return mixed
     */
    public function getCustomerCreditLabel()
    {
        $store = $this->getStore();
        $credit = $this->getCustomerCredit();
        return $store->getCurrentCurrency()->format($credit);
    }
}
