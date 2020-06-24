<?php

class Meetanshi_Mobilelogin_Model_Apitype
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'msg91', 'label' => Mage::helper('mobilelogin')->__('Msg91')),
            array('value' => 'textlocal', 'label' => Mage::helper('mobilelogin')->__('Textlocal')),
            array('value' => 'twilio', 'label' => Mage::helper('mobilelogin')->__('Twilio')),
        );
    }
}

