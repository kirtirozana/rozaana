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
class Apptha_Customerfollowup_Model_Observer extends Mage_Core_Model_Abstract {
    /**
     * XML configuration paths
     */
    const EMAIL_TO_RECIPIENT_TEMPLATE_XML_PATH = 'customerfollowup/general/email_template';
    const XML_PATH_EMAIL_IDENTITY = 'customerfollowup/general/sender_email_id';
    const XML_PATH_EMAIL_RECIPIENT = 'contacts/email/recipient_email';

    /**
     * fucntion to find customer details and product details in cart page
     * and send mail to that customers
     */
    public function SendMailtocustomer() {
        
        if ((Mage::helper('customerfollowup')->isCustomerfollowupEnabled())) {
            $status = Mage::getStoreConfig('customerfollowup/config/order_status');
            $statusValues = explode(',', $status);
            $customerGroup = Mage::getStoreConfig('customerfollowup/config/customer_groups');
            $groupValues = explode(',', $customerGroup);
            $orderAmount = Mage::getStoreConfig('customerfollowup/config/amount_in_cart');
            $amountEquals = Mage::getStoreConfig('customerfollowup/config/amount_equals');

            /**
             * getting product details in cart page who given place order in the checkout page
             * and send mail to that customers
             */
            $this->getOrderdetails($statusValues, $groupValues, $orderAmount, $amountEquals);

            /**
             * getting product details in cart page who logged in to the site
             * and send mail to that customers
             */
            $this->getCartdetails($statusValues, $groupValues, $orderAmount, $amountEquals);
        }
        return $this;
    }

    /**
     * fucntion to get order details
     */
    public function getOrderdetails($statusValues, $groupValues, $orderAmount, $amountEquals) {

        $prevDate = date("Y-m-d", strtotime("-1 day"));
        $followupTable = $tPrefix . 'customerfollowup'; //get customerfollowup table
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $selectResult = $write->query("select update_time from $followupTable order by `update_time` desc limit 1");
        $result = $selectResult->fetch(PDO::FETCH_ASSOC);
        if($result) {
            $startDate = $result;
        } else {
            $startDate = $prevDate;
        }
        /* getting orders for today based on order status,customer group and amout in cart */
        if ($amountEquals == 'dm' || $orderAmount == '') {
            $orderCollection = Mage::getModel('sales/order')
                            ->getCollection()
                            ->addAttributeToFilter('status', array('in' => $statusValues))
                            ->addAttributeToFilter('customer_group_id', array('in' => $groupValues))
                            ->addFieldToFilter('updated_at',array('from'=>$startDate,'to'=>  now()));
                            //->addAttributeToFilter('updated_at', array('like' => date('Y-m-d') . "%"));
        } else {
            $orderCollection = Mage::getModel('sales/order')
                            ->getCollection()
                            ->addAttributeToFilter('status', array('in' => $statusValues))
                            ->addAttributeToFilter('customer_group_id', array('in' => $groupValues))
                            ->addAttributeToFilter('base_grand_total', array($amountEquals => $orderAmount))
                            ->addFieldToFilter('updated_at',array('from'=>$startDate,'to'=>  now()));
                            //->addAttributeToFilter('updated_at', array('like' => date('Y-m-d') . "%"));
        }
        //getting sales order collection
        $currencySumbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->
                          getCurrentCurrencyCode())->getSymbol();//get currency symbol
        foreach ($orderCollection as $item) {
            $orderId = $item->getEntityId(); //get order id
            $customerId = $item->getCustomerId(); //get customer id
            $createdDate = $item->getCreatedAt();//get created date
            $tPrefix = (string) Mage::getConfig()->getTablePrefix(); //get table prefix
            $followupTable = $tPrefix . 'customerfollowup'; //customerfollowup table
            $salesTable = $tPrefix . 'sales_flat_order'; //sales_flat_order table
            $salesitemTable = $tPrefix . 'sales_flat_order_item'; //sales_flat_order_item table
            $write = Mage::getSingleton('core/resource')->getConnection('core_write'); //get db connection
            //check whether the follow up mail sent to customer for this order id or not
            $selectResult = $write->query("select customerfollowup_id from $followupTable where order_id = $orderId");
            $result = $selectResult->fetch(PDO::FETCH_ASSOC);
            //check reorder
            $search = $write->query("SELECT order_id FROM $salesitemTable WHERE product_id in (select c.product_id from $salesTable d left join $salesitemTable c on d.entity_id = c.order_id where (d.status='complete' || d.status='processing') and d.customer_id=$customerId)");
            $reorderIds = $search->fetchAll(PDO::FETCH_COLUMN);            
            if (empty($result) && !in_array($orderId,$reorderIds)) {
                $productCollection = $item->getAllItems(); //get product collection for order id
                /* customer followup cart page content */                
                $i = 1;
                $grandTotal = 0;
                /* content part of cart table */
                foreach ($productCollection as $product) {
                    $productId = $product->getProductId();
                    $model = Mage::getModel('catalog/product');
                    $_product = $model->load($productId);
                    $rowTotal = $product->getPrice() * $product->getQtyOrdered();
                    $productDetails .= '<tr style="background:#F8F7F5;">
                                     <td valign="top" style="border-width: 0; text-align:left; border: 1px solid #CCC; border-right:0; border-top:0; color: #0A263C; font:normal 13px arial; padding:5px 0 5px 10px; border-bottom: 0;">' . $i . '</td>
                                     <td valign="top" style="border-width: 0; text-align:left; border: 1px solid #CCC; border-right:0; border-top:0; color: #0A263C; font:bold 13px arial; padding:5px 0 5px 10px; border-bottom: 0;"><a href="' . $_product->getProductUrl() . '">' . $product->getName() . '</a></td>
                                     <td valign="top" style="border-width: 0; text-align:left; border: 1px solid #CCC; border-right:0; border-top:0; color: #0A263C; font:normal 13px arial; padding:5px 0 5px 10px; border-bottom: 0;"><a href="' . $_product->getProductUrl() . '"><img src="' . Mage::helper('catalog/image')->init($_product, 'image') . '" width="75" height="75"/></a></td>
                                     <td valign="top" style="border-width: 0; text-align:left; border: 1px solid #CCC; border-right:0; border-top:0; color: #0A263C; font:normal 13px arial; padding:5px 0 5px 10px; border-bottom: 0;">' . $product->getSku() . '</td>
                                     <td valign="top" style="border-width: 0; text-align:left; border: 1px solid #CCC; border-right:0; border-top:0; color: #0A263C; font:normal 13px arial; padding:5px 0 5px 10px; border-bottom: 0;">' . $currencySumbol.number_format($product->getPrice(), 2, '.', '') . '</td>
                                     <td valign="top" style="border-width: 0; text-align:left; border: 1px solid #CCC; border-right:0; border-top:0; color: #0A263C; font:normal 13px arial; padding:5px 0 5px 10px; border-bottom: 0;">' . number_format($product->getQtyOrdered()) . '</td>
                                     <td valign="top" style="border-width: 0; text-align:left; border: 1px solid #CCC;  border-top:0; color: #0A263C; font:normal 13px arial; padding:5px 0 5px 10px; border-bottom: 0;">' . $currencySumbol.number_format($rowTotal, 2, '.', '') . '</td>
                                     </tr>';
                    $i++;
                    $grandTotal = $grandTotal + $rowTotal;
                }
                $grandTotal = $currencySumbol.number_format($grandTotal, 2, '.', '');
                $template = self::EMAIL_TO_RECIPIENT_TEMPLATE_XML_PATH;
                $email = $item->getCustomerEmail(); //get customer mail
                $name = $item->getCustomerName(); //get customer name
                $amount = $item->getGrandTotal(); //get grand total
                $entityId = '';
                $date = date("F j, Y",strtotime($createdDate));
                //To create new object
                $postObject = new Varien_Object();
                //set data to send in the email template
                $postObject->setData('product_details', $productDetails);
                $postObject->setData('subtotal', $grandTotal);
                $postObject->setData('order_id', $orderId);
                $postObject->setData('name', $name);
                $postObject->setData('date', $date);
                //function call for sens mail transaction
                $this->_sendEmailTransaction($email, $name, $template, $postObject->getData());
                //function call to save customer
                $this->saveCustomer($email, $name, $entityId, $orderId, $amount);
            }
        }
    }

    /**
     * fucntion to get cart page details who abandoned their cart
     */
    public function getCartdetails($statusValues, $groupValues, $orderAmount, $amountEquals) {

        $prevDate = date("Y-m-d", strtotime("-1 day"));
        $followupTable = $tPrefix . 'customerfollowup'; //get customerfollowup table
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $selectResult = $write->query("select update_time from $followupTable order by `update_time` desc limit 1");
        $result = $selectResult->fetch(PDO::FETCH_ASSOC);
        if($result) {
            $startDate = $result;
        } else {
            $startDate = $prevDate;
        }

        /* getting cart page details for today who abandoned their cart based on customer group and amout in cart */
        if ($amountEquals == 'dm' || $orderAmount == '') {
            $quoteCollection = Mage::getModel('sales/quote')
                            ->getCollection()
                            ->addFieldToFilter('customer_group_id', array('in' => $groupValues))
                            ->addFieldToFilter('updated_at',array('from'=>$startDate,'to'=>  now()));
                            //->addFieldToFilter('updated_at', array('like' => date('Y-m-d') . "%"));
        } else {
            $quoteCollection = Mage::getModel('sales/quote')
                            ->getCollection()
                            ->addFieldToFilter('customer_group_id', array('in' => $groupValues))
                            ->addFieldToFilter('base_grand_total', array($amountEquals => $orderAmount))
                            ->addFieldToFilter('updated_at',array('from'=>$startDate,'to'=>  now()));
                            //->addFieldToFilter('updated_at', array('like' => date('Y-m-d') . "%"));
        }
        //getting sales quote collection
        foreach ($quoteCollection as $items) {
            $reservedOrderid = $items->getReservedOrderId(); //get reserved order id
            //if reserved id null follow up mail sent for customer
            if ($reservedOrderid == '') {
                $entityId = $items->getEntityId(); //get entity id
                $tPrefix = (string) Mage::getConfig()->getTablePrefix();
                $followupTable = $tPrefix . 'customerfollowup'; //get customerfollowup table
                $quoteTable = $tPrefix . 'sales_flat_quote'; //get sales_flat_quote table
                $write = Mage::getSingleton('core/resource')->getConnection('core_write');
                /* check whether the customer updating his cart or not */
                $selectResult = $write->query("select a.customerfollowup_id from $followupTable a join $quoteTable b on a.cart_id = b.entity_id where a.cart_id = $entityId and a.update_time > b.updated_at");
                $result = $selectResult->fetch(PDO::FETCH_ASSOC);
                if (empty($result)) { //if the customer updating his cart send mail to customer
                    $email = $items->getCustomerEmail(); //get customer email
                    $createdDate = $items->getCreatedAt();//get created date
                    $customerFirstname = $items->getCustomerFirstname();
                    $customerLastname = $items->getCustomerLastname();
                    $name = $customerFirstname . ' ' . $customerLastname; //get customer name
                    $productCollection = $items->getAllItems(); //get product collection
                    $currencySumbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->
                                        getCurrentCurrencyCode())->getSymbol();//get currency symbol
                    $i = 1;
                    $grandTotal = 0;
                    /* content part of the cart */
                    if($productCollection) {
                    foreach ($productCollection as $product) {
                        $productId = $product->getProductId();
                        //get product details for a product id
                        $model = Mage::getModel('catalog/product');
                        $_product = $model->load($productId);
                        $rowTotal = $product->getPrice() * $product->getQty();
                        $productDetails .= '<tr style="background:#F8F7F5;">
                                     <td valign="top" style="border-width: 0; text-align:left; border: 1px solid #CCC; border-right:0; border-top:0; color: #0A263C; font:normal 13px arial; padding:5px 0 5px 10px; border-bottom: 0;">' . $i . '</td>
                                     <td valign="top" style="border-width: 0; text-align:left; border: 1px solid #CCC; border-right:0; border-top:0; color: #0A263C; font:bold 13px arial; padding:5px 0 5px 10px; border-bottom: 0;"><a href="' . $_product->getProductUrl() . '">' . $product->getName() . '</a></td>
                                     <td valign="top" style="border-width: 0; text-align:left; border: 1px solid #CCC; border-right:0; border-top:0; color: #0A263C; font:normal 13px arial; padding:5px 0 5px 10px; border-bottom: 0;"><a href="' . $_product->getProductUrl() . '"><img src="' . Mage::helper('catalog/image')->init($_product, 'image') . '" width="75" height="75"/></a></td>
                                     <td valign="top" style="border-width: 0; text-align:left; border: 1px solid #CCC; border-right:0; border-top:0; color: #0A263C; font:normal 13px arial; padding:5px 0 5px 10px; border-bottom: 0;">' . $product->getSku() . '</td>
                                     <td valign="top" style="border-width: 0; text-align:left; border: 1px solid #CCC; border-right:0; border-top:0; color: #0A263C; font:normal 13px arial; padding:5px 0 5px 10px; border-bottom: 0;">' . $currencySumbol.number_format($product->getPrice(), 2, '.', '') . '</td>
                                     <td valign="top" style="border-width: 0; text-align:left; border: 1px solid #CCC; border-right:0; border-top:0; color: #0A263C; font:normal 13px arial; padding:5px 0 5px 10px; border-bottom: 0;">' . number_format($product->getQty()) . '</td>
                                     <td valign="top" style="border-width: 0; text-align:left; border: 1px solid #CCC;  border-top:0; color: #0A263C; font:normal 13px arial; padding:5px 0 5px 10px; border-bottom: 0;">' . $currencySumbol.number_format($rowTotal, 2, '.', '') . '</td>
                                     </tr>';
                        $i++;
                        $grandTotal = $grandTotal + $rowTotal;
                    }
                    $grandTotal = $currencySumbol.number_format($grandTotal, 2, '.', '');
                    $template = self::EMAIL_TO_RECIPIENT_TEMPLATE_XML_PATH;
                    $email = $items->getCustomerEmail(); //get customer email
                    $amount = $items->getGrandTotal(); //get grand total
                    $orderId = '';
                    $date = date("F j, Y",strtotime($createdDate));
                    $postObject = new Varien_Object();
                    //set data to send in the email template
                    $postObject->setData('product_details', $productDetails);
                    $postObject->setData('subtotal', $grandTotal);
                    $postObject->setData('order_id', $entityId);
                    $postObject->setData('name', $name);
                    $postObject->setData('date', $date);
                    //function call for send mail transaction
                    $this->_sendEmailTransaction($email, $name, $template, $postObject->getData());
                    //check if the follow up mail sent to customer or not
                    $selectResult = $write->query("select customerfollowup_id from $followupTable where cart_id = $entityId");
                    $result = $selectResult->fetch(PDO::FETCH_ASSOC);
                    $followupId = $result['customerfollowup_id'];
                    //insert or update follow up customer
                    $this->saveCustomer($email, $name, $entityId, $orderId, $amount, $followupId);
                    }
                }
            }
        }
    }

    /**
     * fucntion to send followup mail to customers
     */
    public function _sendEmailTransaction($emailto, $name, $template, $data) {
        $templateId = Mage::getStoreConfig($template); //get template id
        /* set sender given by admin */
        $sender = Mage::getStoreConfig('customerfollowup/general/sender_email_id');
        $senderEmail = Mage::getStoreConfig("trans_email/ident_$sender/email"); //get sender email
        $senderName = Mage::getStoreConfig("trans_email/ident_$sender/name"); //get sender name

        $sender = array('name' => $senderName, 'email' => $senderEmail);
        try {
            /* @var $mailTemplate Mage_Core_Model_Email_Template */
            $mailTemplate = Mage::getModel('core/email_template');
            /* Send Transactional Email */
            $mailTemplate->sendTransactional(
                    $templateId,
                    $sender,
                    $emailto,
                    $name,
                    $data
            );
        } catch (Exception $e) {
            $this->_getSession()->addError(Mage::helper('customerfollowup')->__("Email can not send !"));
        }
    }

    /**
     * fucntion to save customers on to customer followup table
     */
    public function saveCustomer($email, $name, $entityId, $orderId, $amount, $followupId=null) {
        //set data to insert follow up customer into table
        $data = array('customer_name' => $name, 'customer_email' => $email, 'order_id' => $orderId, 'cart_id' => $entityId, 'amount_in_cart' => $amount);
        $model = Mage::getModel('customerfollowup/customerfollowup');
        /* inserting and updating follow up customer */
        $model->setData($data)
                ->setId($followupId);
        if ($model->getCreatedTime() == NULL || $model->getUpdateTime() == NULL) {
            $model->setCreatedTime(now());
            $model->setUpdateTime(now());
        } else {
            $model->setUpdateTime(now());
        }
        $model->save();
    }

}