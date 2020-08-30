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
 * @package     Apptha_Marketplace
 * @version     1.9.1
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2016 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 * 
 */
class Apptha_Marketplace_IndexController extends Mage_Core_Controller_Front_Action {
 /**
  * Retrieve customer session model object
  *
  * @return Mage_Customer_Model_Session
  */
 protected function _getSession() {
  return Mage::getSingleton ( 'customer/session' );
 }
 /**
  * Load phtml file layout
  *
  * @return void
  */
 public function indexAction() {
   if (! $this->_getSession ()->isLoggedIn ()) {
    Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You must have a Seller Account to access this page' ) );
    $this->_redirect ( 'marketplace/seller/login' );
    return;
   }
   $this->loadLayout ();
   $this->renderLayout ();
 }
 public function riderAction(){
	 $orderId= $this->getRequest()->getParam('order_id');
	 $order=Mage::getModel('sales/order')->load($orderId);
	 $product = [];
	 foreach ($order->getAllItems() as $item) {
		 $product[$item->getSku()]=$item->getQtyOrdered();
	 }
	 $msg='';
	 for($i=20;$i<30;$i++)
	 {
		 $items=Mage::helper('marketplace/vieworder')->getOrderProductIds($i,$orderId);
		 if(sizeof($items))
		 {
			 $msg=$msg. "<br>";
			 $msg=$msg. "-------------------------";
			 //$msg=$msg. "<br>";
			 //$msg=$msg. "-------------------------";
			 $msg=$msg. "<br><span style='font-weight:bold'>";
			 $msg=$msg. Mage::getModel ( 'marketplace/sellerprofile' )->collectprofile($i)->getStoreTitle();
			 $msg=$msg. "</span><br>";
			 $msg=$msg. "-------------------------";
			 $msg=$msg. "<br>";
			 //$msg=$msg. "-------------------------";
			 foreach($items as $item)
			 {
			 	 $msg=$msg. "<br>";
				 $p=Mage::getModel('catalog/product')->load($item);
				 $msg=$msg. "SKU : ".$p->getSku();
				 $msg=$msg. "<br>";
				 $msg=$msg. "Product Name : ".$p->getName();
				 $msg=$msg. "<br>";
				 $msg=$msg. "Grams : ".$p->getUnit();
				 $msg=$msg. "<br>";
				 $msg=$msg. "Price : ".$p->getPrice();
				 $msg=$msg. "<br>";
				 $msg=$msg. "Qty : ".$product[$p->getSku()];
				 $msg=$msg. "<br>";
				 $msg=$msg. "-------------------------";
			 }
			 //echo "<br>";
			 //echo "-------------------------";
		 }
	 }
			 //$msg=$msg. "<br>";
			 //$msg=$msg. "-------------------------";
			 $msg=$msg. "<br>";
			 $msg=$msg. "Customer Address";
			 $msg=$msg. "<br>";
			 $msg=$msg. "-------------------------";
			 $msg=$msg. "<br><span style='font-weight:bold;color:red'>";
			 $msg=$msg. $order->getShippingAddress()->getFormated(true,'html');
			 $msg=$msg. "</span><br>";
			 $msg=$msg. "-------------------------";
			 $msg=$msg. "<br>";
			 echo $msg;
			 Mage::helper('marketplace/order')->sendTelegramNotificationToRider('1175589276',$orderId);
 }
 /**
  * Display home page banner images
  *
  * @return void
  */
 public function bannerAction() {
  $this->loadLayout ();
  $this->renderLayout ();
 }
 /**
  * Display category listings
  *
  * @return void
  */
 public function categorydisplayAction() {
  $this->loadLayout ();
  $this->renderLayout ();
 }
}
