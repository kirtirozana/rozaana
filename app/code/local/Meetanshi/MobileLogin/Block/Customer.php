<?php

class Meetanshi_Mobilelogin_Block_Customer extends Mage_Core_Block_Template
{
    public function getMobile()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $mobile=$customer->getMobileNumber();
            return $mobile;
        }
    }
}
