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
 * Customercredit Helper
 * 
 * @category    Magespacex
 * @package     Magespacex_Customercredit
 * @author      Magespacex Developer
 */
class Magespacex_Customercredit_Helper_Account extends Mage_Core_Helper_Abstract
{

    /**
     * @return mixed
     */
    public function getNavigationLabel()
    {
        return $this->__('Store Credit');
    }

    /**
     * @return mixed
     */
    public function getDashboardLabel()
    {
        return $this->__('Account Dashboard');
    }

    /**
     * @return bool
     */
    public function accountNotLogin()
    {
        return !$this->isLoggedIn();
    }

    /**
     * @return mixed
     */
    public function isLoggedIn()
    {
        return Mage::getSingleton('customercredit/session')->isLoggedIn();
    }

    //check customer can use store credit or not
    /**
     * @return bool
     */
    public function customerGroupCheck()
    {
        if (Mage::app()->getStore()->isAdmin())
            $customer = Mage::getSingleton('adminhtml/session_quote')->getCustomer();
        else
            $customer = Mage::getSingleton('customer/session')->getCustomer();
        $customer_group = $customer->getGroupId();
        $group = Mage::getStoreConfig('customercredit/general/assign_credit');
        $group = explode(',', $group);
        if (in_array($customer_group, $group)) {
            return true;
        } else {
            return false;
        }
    }

}
