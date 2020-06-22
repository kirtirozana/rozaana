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
 * Class Magespacex_Customercredit_Helper_Payment
 */
class Magespacex_Customercredit_Helper_Payment extends Mage_Payment_Helper_Data
{

    /**
     * @param null $store
     * @param null $quote
     * @return array
     */
    public function getStoreMethods($store = null, $quote = null)
    {
        $res = array();
        foreach ($this->getPaymentMethods($store) as $code => $methodConfig) {
            if ($quote && $quote->getGrandTotal() == 0) {
                if ($code == 'free') {
                    $prefix = parent::XML_PATH_PAYMENT_METHODS . '/' . $code . '/';
                    if (!$model = Mage::getStoreConfig($prefix . 'model', $store)) {
                        continue;
                    }
                    $methodInstance = Mage::getModel($model);
                    if (!$methodInstance) {
                        continue;
                    }
                    $methodInstance->setStore($store);
                    if (!$methodInstance->isAvailable($quote)) {
                        /* if the payment method cannot be used at this time */
                        continue;
                    }
                    $sortOrder = (int)$methodInstance->getConfigData('sort_order', $store);
                    $methodInstance->setSortOrder($sortOrder);
                    $res[] = $methodInstance;
                }
            } else {
                $prefix = parent::XML_PATH_PAYMENT_METHODS . '/' . $code . '/';
                if (!$model = Mage::getStoreConfig($prefix . 'model', $store)) {
                    continue;
                }
                $methodInstance = Mage::getModel($model);
                if (!$methodInstance) {
                    continue;
                }
                $methodInstance->setStore($store);
                if (!$methodInstance->isAvailable($quote)) {
                    /* if the payment method cannot be used at this time */
                    continue;
                }
                $sortOrder = (int)$methodInstance->getConfigData('sort_order', $store);
                $methodInstance->setSortOrder($sortOrder);
                $res[] = $methodInstance;
            }
        }

        usort($res, array($this, '_sortMethods'));
        return $res;
    }

}
