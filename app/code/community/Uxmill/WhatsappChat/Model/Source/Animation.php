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

class Uxmill_WhatsappChat_Model_Source_Animation
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'none', 'label' => Mage::helper('whatsappchat')->__('None')),
            array('value' => 'pulse-grow', 'label' => Mage::helper('whatsappchat')->__('Pulse Grow')),
            array('value' => 'pulse-shrink', 'label' => Mage::helper('whatsappchat')->__('Pulse Shrink')),
            array('value' => 'slideUpReturn', 'label' => Mage::helper('whatsappchat')->__('Slide Up Return')),
            array('value' => 'slideDownReturn', 'label' => Mage::helper('whatsappchat')->__('Slide Down Return')),
            array('value' => 'slideLeftReturn', 'label' => Mage::helper('whatsappchat')->__('Slide Left Return')),
            array('value' => 'slideRightReturn', 'label' => Mage::helper('whatsappchat')->__('Slide Right Return')),
            array('value' => 'spaceInUp', 'label' => Mage::helper('whatsappchat')->__('Space In Up')),
            array('value' => 'spaceInDown', 'label' => Mage::helper('whatsappchat')->__('Space In Down')),
            array('value' => 'spaceInLeft', 'label' => Mage::helper('whatsappchat')->__('Space In Left')),
            array('value' => 'spaceInRight', 'label' => Mage::helper('whatsappchat')->__('Space In Right')),
            array('value' => 'tinUpIn', 'label' => Mage::helper('whatsappchat')->__('Tin Up In')),
            array('value' => 'tinDownIn', 'label' => Mage::helper('whatsappchat')->__('Tin Down In')),
            array('value' => 'tinLeftIn', 'label' => Mage::helper('whatsappchat')->__('Tin Left In')),
            array('value' => 'tinRightIn', 'label' => Mage::helper('whatsappchat')->__('Tin Right In')),
            array('value' => 'wobble-horizontal', 'label' => Mage::helper('whatsappchat')->__('Wobble Horizontal')),
            array('value' => 'wobble-vertical', 'label' => Mage::helper('whatsappchat')->__('Wobble Vertical')),
        );
    }
}
