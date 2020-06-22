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
 * Customercredit Controller
 * 
 * @category    Magespacex
 * @package     Magespacex_Customercredit
 * @author      Magespacex Developer
 */
class Magespacex_Customercredit_Adminhtml_CheckoutController extends Mage_Adminhtml_Controller_Action
{

    //binh.td 16/4/2015
    public function customercreditPostAction()
    {
        $request = $this->getRequest();
        $result = array();
        if ($request->isPost()) {
            $creditvalue = $request->getParam('credit_value');
            $session = Mage::getSingleton('checkout/session');
//            $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
            if ($creditvalue < 0.0001)
                $creditvalue = 0;
            $session->setBaseCustomerCreditAmount($creditvalue);
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * @return bool
     */
    protected function _isAllowed(){
         return true;
    }

}
