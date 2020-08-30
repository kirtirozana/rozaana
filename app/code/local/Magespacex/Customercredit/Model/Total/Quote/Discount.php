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
 * Customercredit Model
 * 
 * @category    Magespacex
 * @package     Magespacex_Customercredit
 * @author      Magespacex Developer
 */
class Magespacex_Customercredit_Model_Total_Quote_Discount extends Mage_Sales_Model_Quote_Address_Total_Abstract {

    /**
     * Magespacex_Customercredit_Model_Total_Quote_Discount constructor.
     */
    public function __construct() {
        $this->setCode('customercredit_after_tax');
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this
     */
    public function collect(Mage_Sales_Model_Quote_Address $address) {
        parent::collect($address);
        $quote = $address->getQuote();
        $items = $address->getAllItems();
        $session = Mage::getSingleton('checkout/session');
        
        if (!count($items))
            return $this;
        if (Mage::getStoreConfig('customercredit/spend/tax', $quote->getStoreId()) == '0') {
            return $this;
        }

        if (!$quote->isVirtual() && $address->getAddressType() == 'billing') {
            return $this;
        }
        if ($quote->isVirtual() && $address->getAddressType() == 'shipping') {
            return $this;
        }
        if (!Mage::helper('customercredit/account')->customerGroupCheck()) {
            $session->setBaseCustomerCreditAmount(0);
            $session->setBaseCustomerCreditAmountPaypal(0);
            return $this;
        }
        $helper = Mage::helper('customercredit');
        
        $creditAmountEntered = $session->getBaseCustomerCreditAmount();
        if(!$creditAmountEntered)
            return $this;
        
        $baseDiscountTotal = 0;
        $baseCustomercreditDiscount = 0;
        $baseItemsPrice = 0;
        $baseCustomercreditForShipping = 0;
        
        foreach ($address->getAllItems() as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    if (!$child->isDeleted() && $child->getProduct()->getTypeId() != 'customercredit') {
                        $itemDiscount = $child->getBaseRowTotal() + $child->getBaseTaxAmount() - $child->getBaseDiscountAmount() - $child->getMagespacexBaseDiscount();
                        $baseDiscountTotal += $itemDiscount;
                    }
                }
            } else if ($item->getProduct()) {
                if (!$item->isDeleted() && $item->getProduct()->getTypeId() != 'customercredit') {
                    $itemDiscount = $item->getBaseRowTotal() + $item->getBaseTaxAmount() - $item->getBaseDiscountAmount() - $item->getMagespacexBaseDiscount();
                    $baseDiscountTotal += $itemDiscount;
                }
            }
        }
        $baseItemsPrice = $baseDiscountTotal;
        if ($helper->getSpendConfig('shipping')) {
            $shippingDiscount = $address->getBaseShippingAmount() + $address->getBaseShippingTaxAmount() - $address->getBaseShippingDiscountAmount() - $address->getMagespacexBaseDiscountForShipping()+$address->getDeliveryCost();
            $baseDiscountTotal += $shippingDiscount;
        }
        
        $customercreditBalance = Mage::getModel('customercredit/customercredit')->getBaseCustomerCredit();
        
        $baseCustomercreditDiscount = min($creditAmountEntered, $baseDiscountTotal, $customercreditBalance);
        $customercreditDiscount = Mage::getModel('customercredit/customercredit')
                ->getConvertedFromBaseCustomerCredit($baseCustomercreditDiscount);
        
        if ($baseCustomercreditDiscount < $baseItemsPrice)
            $rate = $baseCustomercreditDiscount / $baseItemsPrice;
        else {
            $rate = 1;
            $baseCustomercreditForShipping = $baseCustomercreditDiscount - $baseItemsPrice;
        }
        //update session
        $session->setBaseCustomerCreditAmount($baseCustomercreditDiscount);
        
        //update address
        $address->setGrandTotal($address->getGrandTotal() - $customercreditDiscount);
        $address->setBaseGrandTotal($address->getBaseGrandTotal() - $baseCustomercreditDiscount);
        $address->setCustomercreditDiscount($customercreditDiscount);
        $address->setBaseCustomercreditDiscount($baseCustomercreditDiscount);
        //distribute discount
        $this->_prepareDiscountCreditForAmount($address, $rate, $baseCustomercreditForShipping);
        return $this;
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address) {
        $quote = $address->getQuote();
        if (Mage::getStoreConfig('customercredit/spend/tax', $quote->getStoreId()) == 0) {
            return $this;
        }
        if (!$quote->isVirtual() && $address->getData('address_type') == 'billing')
            return $this;
        $session = Mage::getSingleton('checkout/session');
        $customer_credit_discount = $address->getCustomercreditDiscount();
        if ($session->getBaseCustomerCreditAmount())
            $customer_credit_discount = $session->getBaseCustomerCreditAmount();
        if ($customer_credit_discount > 0) {
            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => Mage::helper('customercredit')->__('Customer Credit'),
                'value' => -Mage::helper('core')->currency($customer_credit_discount,false,false)
            ));
        }

        return $this;
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @param $rate
     * @param $baseCustomercreditForShipping
     * @return $this
     */
    public function _prepareDiscountCreditForAmount(Mage_Sales_Model_Quote_Address $address, $rate, $baseCustomercreditForShipping) {
        // Update discount for each item
        $helper = Mage::helper('customercredit');
        foreach ($address->getAllItems() as $item) {
            if ($item->getParentItemId())
                continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    if(!$child->isDeleted() && $child->getProduct()->getTypeId() != 'customercredit') {
                        $baseItemPrice = $child->getBaseRowTotal() + $child->getBaseTaxAmount() - $child->getBaseDiscountAmount() - $child->getMagespacexBaseDiscount();
                        $itemBaseDiscount = $baseItemPrice * $rate;
                        $itemDiscount = Mage::app()->getStore()->convertPrice($itemBaseDiscount);
                        $child->setMagespacexBaseDiscount($child->getMagespacexBaseDiscount() + $itemBaseDiscount);
                        $child->setBaseCustomercreditDiscount($itemBaseDiscount)
                                ->setCustomercreditDiscount($itemDiscount);
                    }
                }
            } else if ($item->getProduct()) {
                if(!$item->isDeleted() && $item->getProduct()->getTypeId() != 'customercredit') {
                    $baseItemPrice = $item->getBaseRowTotal() + $item->getBaseTaxAmount() - $item->getBaseDiscountAmount() - $item->getMagespacexBaseDiscount();
                    $itemBaseDiscount = $baseItemPrice * $rate;
                    $itemDiscount = Mage::app()->getStore()->convertPrice($itemBaseDiscount);
                    $item->setMagespacexBaseDiscount($item->getMagespacexBaseDiscount() + $itemBaseDiscount);
                    $item->setBaseCustomercreditDiscount($itemBaseDiscount)
                            ->setCustomercreditDiscount($itemDiscount);
                }
            }
        }
        if ($helper->getSpendConfig('shipping') && $baseCustomercreditForShipping) {
            $baseShippingPrice = $address->getBaseShippingAmount() + $address->getBaseShippingTaxAmount() - $address->getBaseShippingDiscountAmount() - $address->getMagespacexBaseDiscountForShipping();
            $baseShippingDiscount = min($baseShippingPrice, $baseCustomercreditForShipping);
            $shippingDiscount = Mage::app()->getStore()->convertPrice($baseShippingDiscount);
            $address->setMagespacexBaseDiscountForShipping($address->getMagespacexBaseDiscountForShipping() + $baseShippingDiscount);
            $address->setBaseCustomercreditDiscountForShipping($baseShippingDiscount);
            $address->setCustomercreditDiscountForShipping($shippingDiscount);
        }
        return $this;
    }
}
