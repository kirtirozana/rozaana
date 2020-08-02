<?php

class Textlocal_SMSNotifications_Helper_Data extends Mage_Core_Helper_Abstract {

	
	public $app_name = 'Textlocal_SMSNotifications';

	// This method simply returns an array of all the extension specific settings
	public function getSettings()
	{
		// Create an empty array
		$settings = array();

		// Get the  settings
		$settings['sms_gateway_url'] = "https://api.textlocal.in/";//Mage::getStoreConfig('smsnotifications/sms_api_credentials/gateway_url');
		 $settings['sms_auth_token'] = Mage::getStoreConfig('smsnotifications/sms_api_credentials/auth_token');
		 $settings['sms_sender_name'] = Mage::getStoreConfig('smsnotifications/sms_api_credentials/sender_name');
        // Get the general settings
		$settings['country_code_filter'] = Mage::getStoreConfig('smsnotifications/general/country_code_filter');
        // Get the order notification settings
		$settings['order_noficication_recipients'] = Mage::getStoreConfig('smsnotifications/order_notification/recipients');
		$settings['order_noficication_recipients'] = explode(';', $settings['order_noficication_recipients']);
		$settings['order_notification_status'] = Mage::getStoreConfig('smsnotifications/order_notification/order_status');
        // Get the shipment notification settings
		$settings['shipment_notification_message'] = Mage::getStoreConfig('smsnotifications/shipment_notification/message');
        // Get the invoice notification settings
		$settings['invoice_notification_message'] = Mage::getStoreConfig('smsnotifications/invoice_notification/message');
		//get customer registration setting
		$settings['customer_notification_message'] =  Mage::getStoreConfig('smsnotifications/customer_notification/message');
		$settings['order_status']=  Mage::getStoreConfig('smsnotifications/customer_notification/order_allstatus');
		$settings['order_newstatus']=  Mage::getStoreConfig('smsnotifications/customer_notification/order_newstatus');
		// Return the settings
		return $settings;
	}

	// This method sends the specified message to the specified recipients
	public function sendSms($body, $recipients = array())
	{
	
        // Get the settings
		$settings = $this->getSettings();
        // If no recipients have been specified, don't do anything
		if(!count($recipients)) {
			return;
		}
        $errors = array();
        $apiurl = $settings['sms_gateway_url'];
        $a=strtolower(substr($apiurl,0,7));
			 
		if ($a=="http://") //checking if already contains http://
		 {
		 	$api_url=substr($apiurl,7,strlen($apiurl));
		 	$start="http://";
		}
	    elseif ($a=="https:/") //checking if already contains htps://
		{
		 	$api_url=substr($apiurl,8,strlen($apiurl));
		 	$start="http://";
		}
		else { 
		 	
		    $start="http://";
		    $api_url = $settings['sms_gateway_url'];
		}

		$uri = $start.$api_url."send?&apiKey=".urlencode($settings['sms_auth_token'])."&sender=".urlencode($settings['sms_sender_name'])."&numbers=".urlencode(implode(',',$recipients))."&message=".urlencode($body);
		
		

        $result = file_get_contents($uri);
        $rows = json_decode($result, true);
        if ($rows['status'] != 'success') {
    		return false;
	    } 
	    
	    return true;
		
	}

	// This method creates a log entry in the extension specific log file
	public function log($msg)
	{
		Mage::log($msg, null, 'smsnotifications.log', true);
	}
	public function generateMlnList()
    {
        if (!is_null($this->_list)) {
            $items = $this->_list->getItems();
            if (count($items) > 0) {

                $io = new Varien_Io_File();
                $path = Mage::getBaseDir('var') . DS . 'export' . DS;
                $name = md5(microtime());
                $file = $path . DS . $name . '.csv';
                $io->setAllowCreateFolders(true);
                $io->open(array('path' => $path));
                $io->streamOpen($file, 'w+');
                $io->streamLock(true);

                $io->streamWriteCsv($this->_getCsvHeaders($items));
                foreach ($items as $product) {
                    $io->streamWriteCsv($product->getData());
                }

                return array(
                    'type'  => 'filename',
                    'value' => $file,
                    'rm'    => true // can delete file after use
                );
            }
        }
    }

}