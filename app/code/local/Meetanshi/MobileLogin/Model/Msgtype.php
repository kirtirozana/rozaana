<?php

class Meetanshi_Mobilelogin_Model_Msgtype
{
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label' => Mage::helper('mobilelogin')->__('Promotional')),
            array('value' => 4, 'label' => Mage::helper('mobilelogin')->__('Transactional')),
        );
    }
}

