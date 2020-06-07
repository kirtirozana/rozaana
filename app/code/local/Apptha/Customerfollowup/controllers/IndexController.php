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
class Apptha_Customerfollowup_IndexController extends Mage_Core_Controller_Front_Action {

    /**
     * fucntion to redirect cart page or account page
     */
    public function cartpageAction() {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {  // if not logged in
            Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getBaseurl() . "customer/account");
            //redirect to login page
            $this->_redirectUrl(Mage::helper('customer')->getLoginUrl());
        } else {
            $session = Mage::getSingleton('checkout/session');//get session
            $cart_items = $session->getQuote()->getAllItems();//get cart items in the session for logged in customer
            if ($cart_items) {
                //items in cart rediect to cart page
                $this->_redirectUrl(Mage::getBaseUrl() . "checkout/cart");
            } else {
                //no items in cart means redirect to login page
                $this->_redirectUrl(Mage::getBaseUrl() . "customer/account");
            }
        }
    }

}