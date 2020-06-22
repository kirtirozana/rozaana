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
 * Customercredit Block
 * 
 * @category    Magespacex
 * @package     Magespacex_Customercredit
 * @author      Magespacex Developer
 */
class Magespacex_Customercredit_Block_Order_Totals extends Mage_Core_Block_Template
{

    public function initTotals()
    {
        $order = $this->getParentBlock()->getOrder();
        // Zend_debug::dump($order->getCustomercreditDiscount());die();
        if ($order->getCustomercreditDiscount() > 0) {
            $this->getParentBlock()->addTotal(new Varien_Object(array(
                'code' => $this->getCode(),
                'value' => -$order->getCustomercreditDiscount(),
                'base_value' => -$order->getBaseCustomercreditDiscount(),
                'label' => $this->helper('customercredit')->__('Customer Credit'),
                )), 'subtotal');
        }
    }

}
