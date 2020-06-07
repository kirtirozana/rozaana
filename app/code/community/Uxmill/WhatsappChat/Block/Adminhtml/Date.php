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

/**
 * Class Uxmill_WhatsappChat_Block_Adminhtml_Date
 */
class Uxmill_WhatsappChat_Block_Adminhtml_Date extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    /**
     * {@inheritDoc}
     * @see Mage_Adminhtml_Block_System_Config_Form_Field::_getElementHtml()
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $date = new Varien_Data_Form_Element_Date();
        $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        $data = array(
            'name'    => $element->getName(),
            'html_id' => $element->getId(),
            'image'   => $this->getSkinUrl('images/grid-cal.gif')
        );
        $date->setData($data);
        $date->setValue($element->getValue(), $format);
        $date->setFormat(Mage::app()->getLocale()
                             ->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
        $date->setForm($element->getForm());

        return $date->getElementHtml();
    }
}
