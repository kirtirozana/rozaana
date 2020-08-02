<?php
class Textlocal_SMSNotifications_Model_System_Config_Source_Senderid
{
    /**
     * Options getter
     *
     * @return array
     */

    public function toOptionArray()
    { 
         $settings = Mage::helper('smsnotifications/data')->getSettings();
        require('Textlocal/SMSNotifications/Model/Textlocal.php');  
        $Textlocal = new Textlocal_SMSNotifications_Model_Textlocal(false, false, $settings['sms_auth_token']);
        $response = $Textlocal->getSenderNames()->sender_names;
         // Build Option Array
        $respons = array();
        foreach ($response as $key => $store) {
            $respons[] = array(
                'value' => $store,
                'label' => $store
            );
        }

        // Finished
        return $respons;   
   
    }

}
