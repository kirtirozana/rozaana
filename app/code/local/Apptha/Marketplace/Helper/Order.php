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
/**
 * This class contains assign product collection for showing as main product
 * 
 */
class Apptha_Marketplace_Helper_Order extends Mage_Core_Helper_Abstract{
    /**
     * Function to send Order Mail
     * @params int $sellerId,array $productDetails,int $incrementId,string $sellerStore,string $customerEmail,string $customername
     * @return void
     */

	public function sendTelegramNotificationToRider($riderId,$orderId)
	{
		$sellerId=22;
		//$orderid=Mage::getModel('sales/order')->loadByIncrementId($incrementId)->getId();
		$incrementId=Mage::getModel('sales/order')->load($orderId)->getIncrementId();
		$msg="Attention!! You have received an order!! ";
		$msg=$msg."Click Here : ";
		$msg=$msg."<a href='https://rozaanaonline.com/marketplace/index/rider/order_id/$orderId/'>".$incrementId."</a>";
		$telegramCode='telegramid_'.$sellerId;
		$chat_id = Mage::getModel('core/variable')->loadByCode($telegramCode)->getPlainValue();
		$WebsiteURL = "https://api.telegram.org/bot1296688987:AAGSFTX6C3ARrtXBQQu5CMsJd8RJ_8-ok3I";
		//$Update = file_get_contents($WebsiteURL."/sendMessage?chat_id=$chat_id&text=$msg&parse_mode=html");
		$params=[
			'chat_id'=>$chat_id, 
			'text'=>$msg,
			'parse_mode'=>'html',
		];
		$ch = curl_init($WebsiteURL . '/sendMessage');
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);
		curl_close($ch);
	}
	public function sendTelegramNotification($sellerId,$incrementId)
	{
		//$sellerId=22;
		$orderid=Mage::getModel('sales/order')->loadByIncrementId($incrementId)->getId();
		$msg="Attention!! You have received an order!! ";
		$msg=$msg."Click Here : ";
		$msg=$msg."<a href='https://rozaanaonline.com/marketplace/order/vieworder/orderid/$orderid/'>".$incrementId."</a>";
		$telegramCode='telegramid_'.$sellerId;
		$chat_id = Mage::getModel('core/variable')->loadByCode($telegramCode)->getPlainValue();
		$WebsiteURL = "https://api.telegram.org/bot1296688987:AAGSFTX6C3ARrtXBQQu5CMsJd8RJ_8-ok3I";
		//$Update = file_get_contents($WebsiteURL."/sendMessage?chat_id=$chat_id&text=$msg&parse_mode=html");
		$params=[
			'chat_id'=>$chat_id, 
			'text'=>$msg,
			'parse_mode'=>'html',
		];
		$ch = curl_init($WebsiteURL . '/sendMessage');
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);
		curl_close($ch);
	}
    public function sendOrderEmailData($sellerId,$productDetails,$incrementId,$sellerStore,$customerEmail,$customerFirstname,$marketplaceData){
	Mage::helper('marketplace/order')->sendTelegramNotification($sellerId,$incrementId);
        if($marketplaceData['groupId']==$marketplaceData['productGroupId']){
            
            
        $productData=$productDetails;
        $sellerStoreData=$sellerStore;
        $orderIncrementId=$incrementId;
        $templateIdData = ( int ) Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/sales_notification_template_selection' );
        $adminEmailId = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
        $toName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
        $toMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
        if ($templateIdData) {
            $emailTemplate = Mage::helper ( 'marketplace/marketplace' )->loadEmailTemplate ( $templateIdData );
        } else {
            $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_admin_approval_seller_registration_sales_notification_template_selection' );
        }
        /**
         * Get seller information
         * Get name
         * and seller email id
         */
        $customer = Mage::helper ( 'marketplace/common' )->loadCustomerData ( $sellerId );
        $sellerName = $customer->getName ();
        $sellerEmail = $customer->getEmail ();
        $recipientSender = $toMailId;
        $sellerStore = Mage::app ()->getStore ()->getName ();
        $recipientSeller = $sellerEmail;
        /**
         * Set customer name in email templete
         * and customer email id.
         */
        $emailTemplate->setSenderName ( $customerFirstname );
        $emailTemplate->setSenderEmail ( "support@rozaanaonline.com" );
        $emailTemplateVariablesValueDatas = (array (
                'ownername' => $toName,
                'order_id' => $orderIncrementId,
                'seller_store' => $sellerStoreData,
                'productdetails' => $productData,
                'customer_email' => $customerEmail,
                'customer_firstname' => $customerFirstname
        ));
        $emailTemplate->setDesignConfig ( array (
                'area' => 'frontend'
        ) );
        $emailTemplate->getProcessedTemplate ( $emailTemplateVariablesValueDatas );
        $emailTemplate->send ( $recipientSender, $toName, $emailTemplateVariablesValueDatas );
        $emailTemplateVariablesValueDatas = (array (
                'ownername' => $sellerName,
                'productdetails' => $productData,
                'customer_email' => $customerEmail,
                'order_id' => $orderIncrementId,
                'seller_store' => $sellerStoreData,
                'customer_firstname' => $customerFirstname
        ));
        $emailTemplate->send ( $recipientSeller, $sellerName, $emailTemplateVariablesValueDatas );
     
        }
    }
    
    public function sendSellerShippingData($sellerId,$customerFirstname,$customerEmail,$productDetails,$sellerStore,$incrementId,$marketplaceGroupData){
if($marketplaceGroupData['groupId']==$marketplaceGroupData['productGroupId']){
 
        $templateId = ( int ) Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/sales_notification_template_selection' );
        $adminEmailId = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
        $toName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
        $toMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
        if ($templateId) {
            $emailTemplate = Mage::helper ( 'marketplace/marketplace' )->loadEmailTemplate ( $templateId );
        } else {
            $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_admin_approval_seller_registration_sales_notification_template_selection' );
        }
        $customer = Mage::helper ( 'marketplace/common' )->loadCustomerData ( $sellerId );
        $sellerName = $customer->getName ();
        $sellerEmail = $customer->getEmail ();
        $recipient = $toMailId;
        $sellerStore = Mage::app ()->getStore ()->getName ();
        $recipientSeller = $sellerEmail;
        $emailTemplate->setSenderName ( $customerFirstname );
        $emailTemplate->setSenderEmail ( $customerEmail );
        $emailTemplateVariablesValue = (array (
                'ownername' => $toName,
                'productdetails' => $productDetails,
                'order_id' => $incrementId,
                'seller_store' => $sellerStore,
                'customer_email' => $customerEmail,
                'customer_firstname' => $customerFirstname
        ));
        $emailTemplate->setDesignConfig ( array (
                'area' => 'frontend'
        ) );
        $emailTemplate->getProcessedTemplate ( $emailTemplateVariablesValue );
        $emailTemplate->send($recipient, $toName, $emailTemplateVariablesValue );
        $emailTemplateVariablesValue = (array (
                'ownername' => $sellerName,
                'productdetails' => $productDetails,
                'order_id' => $incrementId,
                'seller_store' => $sellerStore,
                'customer_email' => $customerEmail,
                'customer_firstname' => $customerFirstname
        ));
        $emailTemplate->send ( $recipientSeller, $sellerName, $emailTemplateVariablesValue );
}
    }
    /**
     * Function to get Shipping Id
     */
    public function getShippingIdFromOrder($isVirtual,$order){
        if($isVirtual==0){
            $shippingId = $order->getShippingAddress()->getCountry();
        }
        else{
            $shippingId = $order->getBillingAddress()->getCountry();
        }
        return $shippingId;
    }
    /**
     * Function to get National and international shipping price
     * 
     * @return array
     */
    public function getDeliveryScheduleprice($deliveryScheduleEnable,$deliveryScheleDetails,$currencySymbol){
        if ($deliveryScheduleEnable == 1) {
            $productDetailsData = '<tr><td colspan="1" style="padding:3px 9px;font-weight:bold;">' . Mage::helper ( 'deliveryschedule' )->__ ( 'Delivery Schedule' ) . '</td><td colspan="4" align="left" style="padding:3px 9px"><span>' . $deliveryScheleDetails['deliveryTypeInfo'] . '</span></td></tr>';
            $productDetailsData = '<tr><td colspan="1" style="padding:3px 9px;font-weight:bold;">' . Mage::helper ( 'deliveryschedule' )->__ ( 'Delivery Date' ) . '</td><td colspan="4" align="left" style="padding:3px 9px"><span>' . $deliveryScheleDetails['deliveryDateInfo'] . '  ' . $deliveryScheleDetails['deliveryTimeInfo'] . '</span></td></tr>';
            $productDetailsData = '<tr><td colspan="1" style="padding:3px 9px;font-weight:bold;">' . Mage::helper ( 'deliveryschedule' )->__ ( 'Delivery Comments' ) . '</td><td colspan="4" align="left" style="padding:3px 9px"><span>' .$deliveryScheleDetails['deliveryComment']  . '</span></td></tr>';
            $productDetailsData = '<tr><td colspan="1" style="padding:3px 9px;font-weight:bold;">' . Mage::helper ( 'deliveryschedule' )->__ ( 'Delivery Cost' ) . ' </td><td colspan="4" align="left" style="padding:3px 9px"><span>' . $currencySymbol . $deliveryScheleDetails['deliveryCost'] . '</span></td></tr>';
        
        return $productDetailsData;
        }
        
        
    }
}
