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
class Magespacex_Customercredit_Block_Payment_Form extends Mage_Payment_Block_Form
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('customercredit/payment/form.phtml');
    }

    /**
     * @return mixed
     */
    public function getUseCustomerCredit()
    {
        return Mage::getSingleton('checkout/session')->getUseCustomerCredit();
    }

    /**
     * @return bool
     */
    public function hasCustomerCreditItemOnly()
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $hasOnly = false;
        foreach ($quote->getAllItems() as $item) {
            if ($item->getProductType() == 'customercredit') {
                $hasOnly = true;
            } else {
                $hasOnly = false;
                break;
            }
        }
        return $hasOnly;
    }

    /**
     * @return bool
     */
    public function hasCustomerCreditItem()
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        foreach ($quote->getAllItems() as $item) {
            if ($item->getProductType() == 'customercredit') {
                return true;
            }
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getCustomerCredit()
    {
        return Mage::getModel('customercredit/customercredit')->getCustomerCredit();
    }

    /**
     * @return mixed
     */
    public function getCustomerCreditLabel()
    {
        return Mage::getModel('customercredit/customercredit')->getCustomerCreditLabel();
    }

    /**
     * @return mixed
     */
    public function getAvaiableCustomerCreditLabel()
    {
        return Mage::getModel('customercredit/customercredit')->getAvaiableCustomerCreditLabel();
    }

    /**
     * @return mixed
     */
    public function getCurrentCreditAmount()
    {
        $base_amount = Mage::getSingleton('checkout/session')->getBaseCustomerCreditAmount();
        return Mage::getModel('customercredit/customercredit')->getConvertedFromBaseCustomerCredit($base_amount);
    }

    /**
     * @return mixed
     */
    public function getCurrentCreditAmountLabel()
    {
        $base_amount = Mage::getSingleton('checkout/session')->getBaseCustomerCreditAmount();
        return Mage::getModel('customercredit/customercredit')
                ->getLabel(Mage::getModel('customercredit/customercredit')->getConvertedFromBaseCustomerCredit($base_amount));
    }

    /**
     * @return mixed
     */
    public function getUpdateUrl()
    {
        return $this->getUrl('customercredit/checkout/setAmountPost', array('_secure' => true));
    }

}
