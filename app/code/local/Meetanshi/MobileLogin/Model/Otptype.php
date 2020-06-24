<?php

class Meetanshi_Mobilelogin_Model_Otptype
{
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label' => Mage::helper('mobilelogin')->__('Number')),
            array('value' => 1, 'label' => Mage::helper('mobilelogin')->__('Alphabets')),
            array('value' => 2, 'label' => Mage::helper('mobilelogin')->__('alphanumeric')),
        );
    }
}

