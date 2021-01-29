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
 * @package     Apptha_Onestepcheckout
 * @version     0.1.9
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 * */

class Apptha_Onestepcheckout_Model_Observer extends Varien_Object
{
 public function save_newsletter_checkout($observer){
  if ((bool) Mage::getSingleton('checkout/session')->getCustomerIsSubscribed()){
            $quote = $observer->getEvent()->getQuote();
            $customer = $quote->getCustomer();
            switch ($quote->getCheckoutMethod()){
                case Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER:
                    $customer->setIsSubscribed(1);
                    break;
				case Mage_Sales_Model_Quote::CHECKOUT_METHOD_LOGIN_IN:
					$customer->setIsSubscribed(1);
					break;
                case Mage_Sales_Model_Quote::CHECKOUT_METHOD_GUEST:
                    $session = Mage::getSingleton('core/session');
                    try {
                        $status = Mage::getModel('newsletter/subscriber')->subscribe($quote->getBillingAddress()->getEmail());
                        if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE){
                            $session->addSuccess(Mage::helper('onestepcheckout')->__('Confirmation request has been sent regarding your newsletter subscription'));
                        }
                    }
                    catch (Mage_Core_Exception $e) {
                        $session->addException($e, Mage::helper('onestepcheckout')->__('There was a problem with the newsletter subscription: %s', $e->getMessage()));
                    }
                    catch (Exception $e) {
                        $session->addException($e, Mage::helper('onestepcheckout')->__('There was a problem with the newsletter subscription'));
                    }
                    break;
            }
            Mage::getSingleton('checkout/session')->setCustomerIsSubscribed(0);
        }  
        
            /* Inserts Comments value in sales order table*/
            $enable_comments = Mage::getStoreConfig('onestepcheckout/display_option/display_comments');
            if($enable_comments == 1)	
            {
                $orderComment = Mage::app()->getRequest()->getParam('onestepcheckout_comments');
                $orderComment = trim($orderComment);
                if ($orderComment != "")
                {
                    $observer->getEvent()->getOrder()->setOnestepcheckoutCustomercomment($orderComment);
                }
            }

            /* feedback data*/

             $enable_comments = Mage::getStoreConfig('onestepcheckout/feedback/enable_feedback');
            if($enable_comments == 1)
            {
                $orderFeedback = Mage::app()->getRequest()->getParam('onestepcheckout_feedback');

                $orderFeedback = trim($orderFeedback);
                if ($orderFeedback != "")
                {
                   $observer->getEvent()->getOrder()->setOnestepcheckoutCustomerfeedback($orderFeedback);
                }
            }


            $enable_comments = Mage::getStoreConfig('onestepcheckout/feedback/enable_feedback_freetext');
            if($enable_comments == 1)
            {
                $orderFeedback = Mage::app()->getRequest()->getParam('onestepcheckout_feedback_freetext');

                $orderFeedback = trim($orderFeedback);
                if ($orderFeedback != "")
                {
                   $observer->getEvent()->getOrder()->setOnestepcheckoutCustomerfeedback($orderFeedback);
                }
            }
 
 }
 public function checkfunction2($observer)
{
	if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'checkout_cart_add') {
		
		$productId = Mage::app()->getRequest()->getParam('product');
		$productQuantity = Mage::getModel("cataloginventory/stock_item")->loadByProduct($productId);
		$product = Mage::getModel('catalog/product')->load($productId);
		Mage::log("test");
		Mage::log($productQuantity->getMaxSaleQty());
        if (Mage::app()->getRequest()->getParam('qty') > $productQuantity->getMaxSaleQty()) {
            Mage::getSingleton('core/session')->addNotice('The maximum quantity allowed for purchase is 1.');
            Mage::app()->getResponse()->setRedirect($product->getProductUrl());
	}
	
    }

 }

 public function checkfunction($obj) {
	 //check if qty is more then 1
	 $productId=$obj->getProduct()->getId();
	 $productQuantity = Mage::getModel("cataloginventory/stock_item")->loadByProduct($productId);
	 $productQuantityMax=$productQuantity->getMaxSaleQty();
	 Mage::log($productId);
	 Mage::log($productQuantityMax);
    if($obj->getProduct()->getQty() >= $productQuantityMax/10) {
    //set notice to inform the visitor that there is a maximum
    Mage::getSingleton('core/session')->addNotice('The maximum quantity allowed for purchase isss 1.');

    //get the event
    $event = $obj->getEvent();

    //get the product we have added
    $product = $event->getProduct();

    //get the quote item
    $quoteItem = $event->getQuoteItem();

    //change the qty to 1 allowed
    $quoteItem->setQty(1);
    Mage:log('herhehere');

    //recalc totals
    $quoteItem->getQuote()->collectTotals();

    //save the item
    $quoteItem->getQuote()->save();
    }
 }
}
