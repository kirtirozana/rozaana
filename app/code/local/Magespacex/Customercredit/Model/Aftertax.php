<?php
/**
 * Magespacex
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magespacex.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magespacex.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magespacex
 * @package     Magespacex_Storecredit
 * @module      Storecredit
 * @author      Magespacex Developer
 *
 * @copyright   Copyright (c) 2016 Magespacex (http://www.magespacex.com/)
 * @license     http://www.magespacex.com/license-agreement.html
 *
 */

/**
 * Class Magespacex_Customercredit_Model_Aftertax
 */
class Magespacex_Customercredit_Model_Aftertax extends Varien_Object {

    /**
     * @return array
     */
    static public function getOptionArray() {
        return array(
            0 => Mage::helper('customercredit')->__('Before tax'),
            1 => Mage::helper('customercredit')->__('After tax'),
        );
    }

    /**
     * @return array
     */
    public function toOptionArray() {
        $options = array();
        foreach (self::getOptionArray() as $value => $label)
            $options[] = array(
                'value' => $value,
                'label' => $label
            );
        return $options;
    }

}