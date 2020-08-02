<?php

class Textlocal_SMSNotifications_Model_Observer
{

public function NewOrderPlace($observer)
{
        $settings = Mage::helper('smsnotifications/data')->getSettings();
         $arr = $settings['order_status'];
        $a = explode(',', $settings['order_status']);
        $b = explode(',', $settings['order_status']);
        $final_array = array_combine($a, $b);
        /*Get order */
        $order_id = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        $firstname = $order->getCustomerFirstname();
        $lastname  = $order->getCustomerLastname();

        $order_amount = $order->getBaseCurrencyCode();
        $order_id        = $order->getIncrementId();
        /*get telephone number*/
        $telephone= $order->getBillingAddress()->getTelephone();
     if(in_array('placeorder', $final_array)){
     if ($telephone) {
            $text = $settings['order_notification_status'];


            $text = str_replace('{{firstname}}', $firstname, $text);
            $text = str_replace('{{lastname}}',  $lastname, $text);
            $text = str_replace('{{amount}}', $order_amount , $text);
            $text = str_replace('{{order}}', $order_id   , $text);

            array_push($settings['order_noficication_recipients'], $telephone);

            $result = Mage::helper('smsnotifications/data')->sendSms($text, $settings['order_noficication_recipients']);
           return ;
                    }
     } 
}

public function salesOrderSaveAfter($observer)
{

        $settings = Mage::helper('smsnotifications/data')->getSettings();
        /* array for default order status */
         $new_order= $settings['order_newstatus'];
          $x = explode(',', $settings['order_newstatus']);
          $y = explode(',', $settings['order_newstatus']);
          $new_orderarray = array_combine($x, $y);

        /* array  for shipping,invoice, hold, unhold,cancle order status */
        $arr = $settings['order_status'];
        $a = explode(',', $settings['order_status']);
        $b = explode(',', $settings['order_status']);
        $final_array = array_combine($a, $b);
    
         // Get the new order object
        $order = $observer->getEvent()->getOrder();

        // Get the old order data
        $oldOrder = $order->getOrigData();
       
        $order_notification_status = [];
        
        //$order_notification_status = array('pending','holded','closed','canceled');
        foreach (Mage::getSingleton('sales/order_config')->getStatuses() as $_code => $_label) {
            if ($_code != 'processing' || $_code != 'complete') {
                $order_notification_status[] = $_code;
            }
        }

        if (!in_array($order->getStatus(), $order_notification_status)) {
            return;
        }

        // If the order status hasn't changed, don't do anything
        if ($oldOrder['status'] === $order->getStatus()) {
            return;
        }

        if (Mage::app()->getStore()->isAdmin() && $order->getStatus() == 'pending') {
            $resource       = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');
            $table          = $resource->getTableName('sales/order_status_history');

            $query = 'SELECT count(status) as status FROM '.$table.' WHERE status = "pending" and parent_id = '
                     .(int) $order->getId().' LIMIT 1';
            $pendingStatus = $readConnection->fetchOne($query);
        } else {
            $pendingStatus = 1;
        }

        // Generate the body for the notification
        $store_name = Mage::app()->getStore()->getFrontendName();
        /*Get all order status in multiselect*/

        $billingAdress  = $order->getBillingAddress();
        $shippingAdress = $order->getShippingAddress();

        if ($order->getCustomerFirstname()) {
            $customer_name = $order->getCustomerFirstname();
            $customer_name .= ' '.$order->getCustomerLastname();
        } elseif ($billingAdress->getFirstname()) {
            $customer_name = $billingAdress->getFirstname();
            $customer_name .= ' '.$billingAdress->getLastname();
        } else {
            $customer_name = $ShippingAddress->getFirstname();
            $customer_name .= ' '.$ShippingAddress->getLastname();
        }

        $order_amount = $order->getBaseCurrencyCode();
        $order_amount  .= ' '.$order->getBaseGrandTotal();
        $order_id        = $order->getIncrementId();
        $telephoneNumber = trim($shippingAdress->getTelephone());

        $ordermethod = ($order->getRemoteIp()) ?  1 : 0;
       //if (in_array("placeorder", $final_array))
        
        if ($ordermethod == 1 && $order->getStatus() == 'pending' && $pendingStatus <= 1 && in_array('placeorder', $final_array)) {
            $text = Mage::getStoreConfig('smsnotifications/order_notification/order_status');
            $text        = str_replace('{{name}}', $customer_name, $text);
            $text        = str_replace('{{amount}}', $order_amount, $text);
            $text        = str_replace('{{order}}', $order_id, $text);
            $orderStatus = str_replace('_', ' ', $order->getStatus());
            $text        = str_replace('{{orderStatus}}', $orderStatus, $text);

            // If no recipients have been set, we can't do anything
            if (!count($settings['order_noficication_recipients'])) {
                return;
            }

            // Send the order notification by SMS
            array_push($settings['order_noficication_recipients'], $telephoneNumber);
            /*Get all multiselect order status */
           

            $result = Mage::helper('smsnotifications/data')->sendSms($text, $settings['order_noficication_recipients']);

            // Display a success or error message
            if ($result) {
                $sendNumber1 = implode(',', $settings['order_noficication_recipients']);
                Mage::getSingleton('adminhtml/session')->addSuccess(sprintf('The order notification has been sent via SMS to: %s', $sendNumber1));
            } else {
                Mage::getSingleton('adminhtml/session')->addError('There has been an error sending the order notification SMS.');
            }
        } elseif ($ordermethod == 0 && $order->getStatus() == 'processing' && $pendingStatus <= 1) {
            $text = Mage::getStoreConfig('smsnotifications/order_notification/order_status');
            $text        = str_replace('{{name}}', $customer_name, $text);
            $text        = str_replace('{{amount}}', $order_amount, $text);
            $text        = str_replace('{{order}}', $order_id, $text);
            $orderStatus = str_replace('_', ' ', $order->getStatus());
            $text        = str_replace('{{orderStatus}}', $orderStatus, $text);

            // If no recipients have been set, we can't do anything
            if (!count($settings['order_noficication_recipients'])) {
                return;
            }
            // Send the order notification by SMS
            array_push($settings['order_noficication_recipients'], $telephoneNumber);
            /*Get all multiselect order status */
           

            $result = Mage::helper('smsnotifications/data')->sendSms($text, $settings['order_noficication_recipients']);

            // Display a success or error message
            if ($result) {
                $sendNumber1 = implode(',', $settings['order_noficication_recipients']);
                Mage::getSingleton('adminhtml/session')->addSuccess(sprintf('The order notification has been sent via SMS to: %s', $sendNumber1));
            } else {
                Mage::getSingleton('adminhtml/session')->addError('There has been an error sending the order notification SMS.');
            }
        } elseif ($order->getStatus() == 'pending' && in_array('unhold', $final_array)) {
            $text = Mage::getStoreConfig('smsnotifications/order_notification/order_unhold_status');
            $text        = str_replace('{{name}}', $customer_name, $text);
            $text        = str_replace('{{amount}}', $order_amount, $text);
            $text        = str_replace('{{order}}', $order_id, $text);
            $orderStatus = str_replace('_', ' ', $order->getStatus());
            $text        = str_replace('{{orderStatus}}', $orderStatus, $text);

            // If no recipients have been set, we can't do anything
            if (!count($settings['order_noficication_recipients'])) {
                return;
            }
            // Send the order notification by SMS
            array_push($settings['order_noficication_recipients'], $telephoneNumber);
            /*Get all multiselect order status */
           $result = Mage::helper('smsnotifications/data')->sendSms($text, $settings['order_noficication_recipients']);

            // Display a success or error message
            if ($result) {
                $sendNumber1 = implode(',', $settings['order_noficication_recipients']);
                Mage::getSingleton('adminhtml/session')->addSuccess(sprintf('The order notification has been sent via SMS to: %s', $sendNumber1));
            } else {
                Mage::getSingleton('adminhtml/session')->addError('There has been an error sending the order notification SMS.');
            }
        } elseif ($order->getStatus() == 'holded' && in_array('hold', $final_array)) {
            $text = Mage::getStoreConfig('smsnotifications/order_notification/order_hold_status');
            $text        = str_replace('{{name}}', $customer_name, $text);
            $text        = str_replace('{{amount}}', $order_amount, $text);
            $text        = str_replace('{{order}}', $order_id, $text);
            $orderStatus = str_replace('_', ' ', $order->getStatus());
            $text        = str_replace('{{orderStatus}}', $orderStatus, $text);

            // If no recipients have been set, we can't do anything
            if (!count($settings['order_noficication_recipients'])) {
                return;
            }

            // Send the order notification by SMS
            array_push($settings['order_noficication_recipients'], $telephoneNumber);
            /*Get all multiselect order status */
           

            $result = Mage::helper('smsnotifications/data')->sendSms($text, $settings['order_noficication_recipients']);

            // Display a success or error message
            if ($result) {
                $sendNumber1 = implode(',', $settings['order_noficication_recipients']);
                Mage::getSingleton('adminhtml/session')->addSuccess(sprintf('The order notification has been sent via SMS to: %s', $sendNumber1));
            } else {
                Mage::getSingleton('adminhtml/session')->addError('There has been an error sending the order notification SMS.');
            }
        } elseif ($order->getStatus() == 'canceled' && in_array('cancel', $final_array)) {
            $text = Mage::getStoreConfig('smsnotifications/order_notification/order_canceled_status');
            $text        = str_replace('{{name}}', $customer_name, $text);
            $text        = str_replace('{{amount}}', $order_amount, $text);
            $text        = str_replace('{{order}}', $order_id, $text);
            $orderStatus = str_replace('_', ' ', $order->getStatus());
            $text        = str_replace('{{orderStatus}}', $orderStatus, $text);

            // If no recipients have been set, we can't do anything
            if (!count($settings['order_noficication_recipients'])) {
                return;
            }

            // Send the order notification by SMS
            array_push($settings['order_noficication_recipients'], $telephoneNumber);
            /*Get all multiselect order status */
           

            $result = Mage::helper('smsnotifications/data')->sendSms($text, $settings['order_noficication_recipients']);

            // Display a success or error message
            if ($result) {
                $sendNumber1 = implode(',', $settings['order_noficication_recipients']);
                Mage::getSingleton('adminhtml/session')->addSuccess(sprintf('The order notification has been sent via SMS to: %s', $sendNumber1));
            } else {
                Mage::getSingleton('adminhtml/session')->addError('There has been an error sending the order notification SMS.');
            }
        }elseif ($order->getStatus() == 'fraud'  && in_array('fraud', $new_orderarray)) {
            $text        = Mage::getStoreConfig('smsnotifications/order_notification/order_suspected_fraud');
            $text        = str_replace('{{name}}', $customer_name, $text);
            $text        = str_replace('{{amount}}', $order_amount, $text);
            $text        = str_replace('{{order}}', $order_id, $text);
            $orderStatus = str_replace('_', ' ', $order->getStatus());
            $text        = str_replace('{{orderStatus}}', $orderStatus, $text);

            // If no recipients have been set, we can't do anything
            if (!count($settings['order_noficication_recipients'])) {
                return;
            }
            // Send the order notification by SMS
            array_push($settings['order_noficication_recipients'], $telephoneNumber);
            /*Get all multiselect order status */
           $result = Mage::helper('smsnotifications/data')->sendSms($text, $settings['order_noficication_recipients']);

            // Display a success or error message
            if ($result) {
                $sendNumber1 = implode(',', $settings['order_noficication_recipients']);
                Mage::getSingleton('adminhtml/session')->addSuccess(sprintf('The order notification has been sent via SMS to: %s', $sendNumber1));
            } else {
                Mage::getSingleton('adminhtml/session')->addError('There has been an error sending the order notification SMS.');
            }
        }elseif ($order->getStatus() == 'paypal_reversed' && in_array('paypal_reversed', $new_orderarray)) {
            $text        = Mage::getStoreConfig('smsnotifications/order_notification/order_paypal_reversed');
            $text        = str_replace('{{name}}', $customer_name, $text);
            $text        = str_replace('{{amount}}', $order_amount, $text);
            $text        = str_replace('{{order}}', $order_id, $text);
            $orderStatus = str_replace('_', ' ', $order->getStatus());
            $text        = str_replace('{{orderStatus}}', $orderStatus, $text);

            // If no recipients have been set, we can't do anything
            if (!count($settings['order_noficication_recipients'])) {
                return;
            }
            // Send the order notification by SMS
            array_push($settings['order_noficication_recipients'], $telephoneNumber);
            /*Get all multiselect order status */
           $result = Mage::helper('smsnotifications/data')->sendSms($text, $settings['order_noficication_recipients']);

            // Display a success or error message
            if ($result) {
                $sendNumber1 = implode(',', $settings['order_noficication_recipients']);
                Mage::getSingleton('adminhtml/session')->addSuccess(sprintf('The order notification has been sent via SMS to: %s', $sendNumber1));
            } else {
                Mage::getSingleton('adminhtml/session')->addError('There has been an error sending the order notification SMS.');
            }
        }elseif ($order->getStatus() == 'pending_payment' && in_array('pending_payment', $new_orderarray)) {
            $text        = Mage::getStoreConfig('smsnotifications/order_notification/order_pending_payment');
            $text        = str_replace('{{name}}', $customer_name, $text);
            $text        = str_replace('{{amount}}', $order_amount, $text);
            $text        = str_replace('{{order}}', $order_id, $text);
            $orderStatus = str_replace('_', ' ', $order->getStatus());
            $text        = str_replace('{{orderStatus}}', $orderStatus, $text);

            // If no recipients have been set, we can't do anything
            if (!count($settings['order_noficication_recipients'])) {
                return;
            }
            // Send the order notification by SMS
            array_push($settings['order_noficication_recipients'], $telephoneNumber);
            /*Get all multiselect order status */
           $result = Mage::helper('smsnotifications/data')->sendSms($text, $settings['order_noficication_recipients']);

            // Display a success or error message
            if ($result) {
                $sendNumber1 = implode(',', $settings['order_noficication_recipients']);
                Mage::getSingleton('adminhtml/session')->addSuccess(sprintf('The order notification has been sent via SMS to: %s', $sendNumber1));
            } else {
                Mage::getSingleton('adminhtml/session')->addError('There has been an error sending the order notification SMS.');
            }
        }elseif ($order->getStatus() == 'pending_paypal' && in_array('pending_paypal', $new_orderarray)) {
            $text        = Mage::getStoreConfig('smsnotifications/order_notification/order_pending_paypal');
            $text        = str_replace('{{name}}', $customer_name, $text);
            $text        = str_replace('{{amount}}', $order_amount, $text);
            $text        = str_replace('{{order}}', $order_id, $text);
            $orderStatus = str_replace('_', ' ', $order->getStatus());
            $text        = str_replace('{{orderStatus}}', $orderStatus, $text);

            // If no recipients have been set, we can't do anything
            if (!count($settings['order_noficication_recipients'])) {
                return;
            }
            // Send the order notification by SMS
            array_push($settings['order_noficication_recipients'], $telephoneNumber);
            /*Get all multiselect order status */
           $result = Mage::helper('smsnotifications/data')->sendSms($text, $settings['order_noficication_recipients']);

            // Display a success or error message
            if ($result) {
                $sendNumber1 = implode(',', $settings['order_noficication_recipients']);
                Mage::getSingleton('adminhtml/session')->addSuccess(sprintf('The order notification has been sent via SMS to: %s', $sendNumber1));
            } else {
                Mage::getSingleton('adminhtml/session')->addError('There has been an error sending the order notification SMS.');
            }
        }elseif ($order->getStatus() == 'paypal_canceled_reversal' && in_array('paypal_canceled_reversal', $new_orderarray)) {
            $text        = Mage::getStoreConfig('smsnotifications/order_notification/order_paypal_canceled');
            $text        = str_replace('{{name}}', $customer_name, $text);
            $text        = str_replace('{{amount}}', $order_amount, $text);
            $text        = str_replace('{{order}}', $order_id, $text);
            $orderStatus = str_replace('_', ' ', $order->getStatus());
            $text        = str_replace('{{orderStatus}}', $orderStatus, $text);

            // If no recipients have been set, we can't do anything
            if (!count($settings['order_noficication_recipients'])) {
                return;
            }
            // Send the order notification by SMS
            array_push($settings['order_noficication_recipients'], $telephoneNumber);
            /*Get all multiselect order status */
           $result = Mage::helper('smsnotifications/data')->sendSms($text, $settings['order_noficication_recipients']);

            // Display a success or error message
            if ($result) {
                $sendNumber1 = implode(',', $settings['order_noficication_recipients']);
                Mage::getSingleton('adminhtml/session')->addSuccess(sprintf('The order notification has been sent via SMS to: %s', $sendNumber1));
            } else {
                Mage::getSingleton('adminhtml/session')->addError('There has been an error sending the order notification SMS.');
            }
        }elseif ($order->getStatus() == 'payment_review' && in_array('payment_review', $new_orderarray)) {
            $text        = Mage::getStoreConfig('smsnotifications/order_notification/order_Payment_reivew');
            $text        = str_replace('{{name}}', $customer_name, $text);
            $text        = str_replace('{{amount}}', $order_amount, $text);
            $text        = str_replace('{{order}}', $order_id, $text);
            $orderStatus = str_replace('_', ' ', $order->getStatus());
            $text        = str_replace('{{orderStatus}}', $orderStatus, $text);

            // If no recipients have been set, we can't do anything
            if (!count($settings['order_noficication_recipients'])) {
                return;
            }
            // Send the order notification by SMS
            array_push($settings['order_noficication_recipients'], $telephoneNumber);
            /*Get all multiselect order status */
           $result = Mage::helper('smsnotifications/data')->sendSms($text, $settings['order_noficication_recipients']);

            // Display a success or error message
            if ($result) {
                $sendNumber1 = implode(',', $settings['order_noficication_recipients']);
                Mage::getSingleton('adminhtml/session')->addSuccess(sprintf('The order notification has been sent via SMS to: %s', $sendNumber1));
            } else {
                Mage::getSingleton('adminhtml/session')->addError('There has been an error sending the order notification SMS.');
            }
        }elseif ($order->getStatus() == 'complete'  && in_array('complete', $final_array)) {
            $text = Mage::getStoreConfig('smsnotifications/order_notification/order_complete_status');
            $text        = str_replace('{{name}}', $customer_name, $text);
            $text        = str_replace('{{amount}}', $order_amount, $text);
            $text        = str_replace('{{order}}', $order_id, $text);
            $orderStatus = str_replace('_', ' ', $order->getStatus());
            $text        = str_replace('{{orderStatus}}', $orderStatus, $text);

            // If no recipients have been set, we can't do anything
            if (!count($settings['order_noficication_recipients'])) {
                return;
            }

            // Send the order notification by SMS
            array_push($settings['order_noficication_recipients'], $telephoneNumber);
            /*Get all multiselect order status */
           

            $result = Mage::helper('smsnotifications/data')->sendSms($text, $settings['order_noficication_recipients']);

            // Display a success or error message
            if ($result) {
                $sendNumber1 = implode(',', $settings['order_noficication_recipients']);
                Mage::getSingleton('adminhtml/session')->addSuccess(sprintf('The order notification has been sent via SMS to: %s', $sendNumber1));
            } else {
                Mage::getSingleton('adminhtml/session')->addError('There has been an error sending the order notification SMS.');
            }
        } else {
            $text = Mage::getStoreConfig('smsnotifications/order_notification/order_custom_status');
        }
        
    }

    // This method is called whenever a new shipment is created for an order
    public function salesOrderInvoiceSaveAfter($observer)
    {

        // Get the settings
        $settings = Mage::helper('smsnotifications/data')->getSettings();
        $arr = $settings['order_status'];
        $a = explode(',', $settings['order_status']);
        $b = explode(',', $settings['order_status']);
        $final_array = array_combine($a, $b);

        $text = Mage::getStoreConfig('smsnotifications/invoice_notification/message');
        // If no invoice notification has been specified, no notification can be sent
        if (!$text) {
            return;
        }

        $order    = $observer->getEvent()->getInvoice()->getOrder();
        $order_id = $order->getIncrementId();

        $invoice   = $order->getInvoiceCollection()->getFirstItem();
        $invoiceId = $invoice->getIncrementId();

        $store_name = Mage::app()->getStore()->getFrontendName();

        $billingAdress  = $order->getBillingAddress();
        $shippingAdress = $order->getShippingAddress();

        if ($order->getCustomerFirstname()) {
            $customer_name = $order->getCustomerFirstname();
            $customer_name .= ' '.$order->getCustomerLastname();
        } elseif ($billingAdress->getFirstname()) {
            $customer_name = $billingAdress->getFirstname();
            $customer_name .= ' '.$billingAdress->getLastname();
        } else {
            $customer_name = $shippingAdress->getFirstname();
            $customer_name .= ' '.$shippingAdress->getLastname();
        }

        $order_amount = $order->getBaseCurrencyCode();
        $order_amount  .= ' '.$order->getBaseGrandTotal();

        $telephoneNumber = trim($shippingAdress->getTelephone());

        // Check if a telephone number has been specified
        if(in_array('invoice', $final_array)){
        if ($telephoneNumber) {
            $text = Mage::getStoreConfig('smsnotifications/invoice_notification/message');
            $text = $settings['invoice_notification_message'];
            $text = str_replace('{{name}}', $customer_name, $text);
            $text = str_replace('{{order}}', $order_id, $text);
            $text = str_replace('{{amount}}', $order_amount, $text);
            $text = str_replace('{{invoiceno}}', $invoiceId, $text);

            array_push($settings['order_noficication_recipients'], $telephoneNumber );

            $result = Mage::helper('smsnotifications/data')->sendSms($text, $settings['order_noficication_recipients']);
            if ($result) {
                $recipients_string = implode(',', $settings['order_noficication_recipients']);
                Mage::getSingleton('adminhtml/session')->addSuccess(sprintf('The invoice notification has been sent via SMS to: %s', $recipients_string));
            } else {
                Mage::getSingleton('adminhtml/session')->addError('There has been an error sending the invoice notification SMS.');
            }
        }
    }
}


    // This method is called whenever a new shipment is created for an order
    public function salesOrderShipmentSaveAfter($observer)
    {

          // Get the settings
        $settings = Mage::helper('smsnotifications/data')->getSettings();
         $arr = $settings['order_status'];
         $a = explode(',', $settings['order_status']);
         $b = explode(',', $settings['order_status']);
         $final_array = array_combine($a, $b);
 
        if (!$settings['shipment_notification_message']) {
            return;
        }
        $order = $observer->getEvent()->getShipment()->getOrder();
        
        $billingAdress  = $order->getBillingAddress();
        $shippingAdress = $order->getShippingAddress();

        if ($order->getCustomerFirstname()) {
            $customer_name = $order->getCustomerFirstname();
            $customer_name .= ' '.$order->getCustomerLastname();
        } elseif ($billingAdress->getFirstname()) {
            $customer_name = $billingAdress->getFirstname();
            $customer_name .= ' '.$billingAdress->getLastname();
        } else {
            $customer_name = $shippingAdress->getFirstname();
            $customer_name .= ' '.$shippingAdress->getLastname();
        }

        $order_id     = $order->getIncrementId();
        $order_amount = $order->getBaseCurrencyCode();
        $order_amount  .= ' '.$order->getBaseGrandTotal();

        $telephoneNumber = trim($shippingAdress->getTelephone());

        $shipment = $order->getShipmentsCollection()->getFirstItem();
        $shipId   = $shipment->getIncrementId();

        // Check if a telephone number has been specified
         if(in_array('shippment', $final_array)){
        if ($telephoneNumber) {
            // Send the shipment notification to the specified telephone number
            $text = $settings['shipment_notification_message'];
            $text = str_replace('{{name}}', $customer_name, $text);
            $text = str_replace('{{order}}', $order_id, $text);
            $text = str_replace('{{amount}}', $order_amount, $text);
            $text = str_replace('{{shipmentno}}', $shipId, $text);

            array_push($settings['order_noficication_recipients'], $telephoneNumber);

            $result = Mage::helper('smsnotifications/data')->sendSms($text, $settings['order_noficication_recipients']);
           // Display a success or error message
            if ($result) {
                $recipients_string = implode(',', $settings['order_noficication_recipients']);
                Mage::getSingleton('adminhtml/session')->addSuccess(sprintf('The shipment notification has been sent via SMS to: %s', $recipients_string));
            } else {
                Mage::getSingleton('adminhtml/session')->addError('There has been an error sending the shipment notification SMS.');
            }
        }
    } 
}

    // This method is called whenever the application's setting in the
    // adminhtml are changed
 public function configSaveAfter($observer)
    {
       /* ?>
        <script type="text/javascript">
            alert('aaaa');
        </script>
        <?php*/

        $settings = Mage::helper('smsnotifications/data')->getSettings();

          if (!count($settings['order_noficication_recipients'])) {
            return;
            }

            // Verify the settings by sending a test message
            $result = Mage::helper('smsnotifications/data')->sendSms('Congratulations, you have configured the extension correctly!', $settings['order_noficication_recipients']);
            // Display a success or error message
            if ($result) {
        	// If everything has worked, let the user know that a test message has been sent to the recipients
            $recipients_string = implode(',', $settings['order_noficication_recipients']);
            Mage::getSingleton('adminhtml/session')->addNotice(sprintf('A test message has been sent to the following recipient(s): %s. Please verify that all recipients received this test message. If not, correct the number(s) below.', $recipients_string));
        } else {
            Mage::getSingleton('adminhtml/session')->addError('Unable to send test message. Please verify that all your settings are correct and try again.');
        }
    }

/* Customer After Registration sms function */
public function customerRegisterSuccess($observer)
{

        $settings = Mage::helper('smsnotifications/data')->getSettings();
        $customer = $observer->getEvent()->getCustomer();
        $fname=$customer->getFirstname();
        $lname=$customer->getLastname();
        $email= $customer->getEmail();

        $telephone= $customer->getAddressesCollection()->getFirstitem()->getTelephone();

          if ($telephone) {
            $text =$settings['customer_notification_message'];
            $text = str_replace('{{firstname}}', $fname, $text);
            $text = str_replace('{{lastname}}',  $lname, $text);
            $text = str_replace('{{emailid}}',  $email, $text);
        }
        
        array_push($settings['order_noficication_recipients'], $telephone);
        $result = Mage::helper('smsnotifications/data')->sendSms($text, $settings['order_noficication_recipients']);
        return;
}       
/*Customer After change password sms function */ 
public function passwordChange($observer)
{

      $settings = Mage::helper('smsnotifications/data')->getSettings();
      $customer = $observer->getEvent()->getCustomer();
        $fname=$customer->getFirstname();
        $lname=$customer->getLastname();
        $email= $customer->getEmail();
        $telephone= $customer->getAddressesCollection()->getFirstitem()->getTelephone();
          if ($telephone) {
            $text = Mage::getStoreConfig('smsnotifications/customer_notification/change_password');
             $text = str_replace('{{firstname}}', $fname, $text);
            $text = str_replace('{{lastname}}',  $lname, $text);
            $text = str_replace('{{emailid}}',  $email, $text);
          //  $text = str_replace('{{name}}', $customer_name, $text);
       }
         array_push($settings['order_noficication_recipients'], $telephone);
         $result = Mage::helper('smsnotifications/data')->sendSms($text, $settings['order_noficication_recipients']);
           return $this;
}

/* Customer After product review submited sms function*/
public function customerProductReview($observer)
{ 
   
     $settings = Mage::helper('smsnotifications/data')->getSettings();
     $customer = Mage::getSingleton('customer/session')->getCustomer();
       $fname=$customer->getFirstname();
        $lname=$customer->getLastname();
        $email= $customer->getEmail();

     $telephone= $customer->getAddressesCollection()->getFirstitem()->getTelephone();
       if ($telephone){
            $text = Mage::getStoreConfig('smsnotifications/customer_notification/customer_review');
             $text = str_replace('{{firstname}}', $fname, $text);
            $text = str_replace('{{lastname}}',  $lname, $text);
            $text = str_replace('{{emailid}}',  $email, $text);
           //  $text = str_replace('{{name}}', $customer_name, $text);
        }
         array_push($settings['order_noficication_recipients'], $telephone);

          $result = Mage::helper('smsnotifications/data')->sendSms($text, $settings['order_noficication_recipients']);
          return($result);
}

/*Customer product After tag sms function */
public function customerProductTag($observer)
{
       $settings = Mage::helper('smsnotifications/data')->getSettings();
       $customer = Mage::getSingleton('customer/session')->getCustomer();
         $fname=$customer->getFirstname();
        $lname=$customer->getLastname();
        $email= $customer->getEmail();

        $telephone= $customer->getAddressesCollection()->getFirstitem()->getTelephone();
        if ($telephone) {
            $text = Mage::getStoreConfig('smsnotifications/customer_notification/product_tag');
             $text = str_replace('{{firstname}}', $fname, $text);
            $text = str_replace('{{lastname}}',  $lname, $text);
            $text = str_replace('{{emailid}}',  $email, $text);
           //  $text = str_replace('{{name}}', $customer_name, $text);
          }
             array_push($settings['order_noficication_recipients'], $telephone);
             $result = Mage::helper('smsnotifications/data')->sendSms($text, $settings['order_noficication_recipients']);
                return($result );
}

/*Customer Review approval status sms function*/
   public  function customerForgotpassword($observer)
   {
    
 $settings = Mage::helper('smsnotifications/data')->getSettings();

 $customer_email =$_POST["email"];
    $customer = Mage::getModel("customer/customer");
    $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
    $customer->loadByEmail($customer_email);
       $fname=$customer->getFirstname();
        $lname=$customer->getLastname();
        $email= $customer->getEmail();
$telephone= $customer->getAddressesCollection()->getFirstitem()->getTelephone();

if ($telephone) {
            $text = Mage::getStoreConfig('smsnotifications/customer_notification/password_status');
             $text = str_replace('{{firstname}}', $fname, $text);
            $text = str_replace('{{lastname}}',  $lname, $text);
            $text = str_replace('{{emailid}}',  $email, $text);
            //$text = str_replace('{{name}}', $customer_name, $text);
            $link=Mage::helper('customer')->getForgotPasswordUrl();
            $text = str_replace('{{link}}', $link, $text);
         array_push($settings['order_noficication_recipients'], $telephone);

          $result = Mage::helper('smsnotifications/data')->sendSms($text, $settings['order_noficication_recipients']);
          return;
       }

    }
/*Newsletter Subscription*/
public function newsletterSubscription($observer)
{
     echo 'hello ';   
     $settings = Mage::helper('smsnotifications/data')->getSettings();
     $text = Mage::getStoreConfig('smsnotifications/customer_notification/newslettre_sub');
       if (!$text) {
            return;
        }
     $customer = Mage::getSingleton('customer/session')->getCustomer();
        $fname=$customer->getFirstname();
        $lname=$customer->getLastname();
        $email= $customer->getEmail();
     $telephone= $customer->getAddressesCollection()->getFirstitem()->getTelephone();
     if ($telephone) {
            $text = Mage::getStoreConfig('smsnotifications/customer_notification/newslettre_sub');
            $text = str_replace('{{firstname}}', $fname, $text);
            $text = str_replace('{{lastname}}',  $lname, $text);
            $text = str_replace('{{emailid}}',  $email, $text);
            
        }     
         array_push($settings['order_noficication_recipients'], $telephone);

          $result = Mage::helper('smsnotifications/data')->sendSms($text, $settings['order_noficication_recipients']);
          return($result );
        }

       protected function _construct()
    {
        $this->_init("smsnotifications/smsnotifications");
    }
    



      
}
