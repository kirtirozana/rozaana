<?php
class Textlocal_SMSNotifications_Model_System_Config_Source_View
{
    /**
     * Options getter
     *
     * @return array
     */

    public function toOptionArray()
    {
        return array(
            array('value' => 'placeorder', 'label' => Mage::helper('adminhtml')->__('PlaceOrder')),
            array('value' => 'hold', 'label' => Mage::helper('adminhtml')->__('Hold')),
            array('value' => 'unhold', 'label' => Mage::helper('adminhtml')->__('Unhold')),
            array('value' => 'complete', 'label' => Mage::helper('adminhtml')->__('Complete')),
            array('value' => 'cancel', 'label' => Mage::helper('adminhtml')->__('Cancel')),
            array('value' => 'invoice' , 'label' => Mage::helper('adminhtml')->__('Invoice')),
            array('value' => 'shippment', 'label' => Mage::helper('adminhtml')->__('Shippment')),
            

        );
    }

    /**
     * Get options in "key-value" format
     *
      @return array
     */
    /*public function toArray()
    {
        return array(
            0 => Mage::helper('adminhtml')->__('Data1'),
            1 => Mage::helper('adminhtml')->__('Data2'),
            3 => Mage::helper('adminhtml')->__('Data3'),
        );
    }*/
}
