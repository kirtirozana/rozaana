<?php
/**
 * Uxmill
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the umxill.co license that is
 * available through the world-wide-web.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Uxmill
 * @package     Uxmill_WhatsappChat
 * @copyright   Copyright (c) Uxmill (http://www.uxmill.co)
 * @license     http://www.uxmill.co
 */

class Uxmill_WhatsappChat_Model_Source_Button
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'icon', 'label' => Mage::helper('whatsappchat')->__('Icon')),
            array('value' => 'chatbox', 'label' => Mage::helper('whatsappchat')->__('Box')),
        );
    }
}
