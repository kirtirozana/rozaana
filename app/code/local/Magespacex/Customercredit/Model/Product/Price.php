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
 * Customercredit Model
 * 
 * @category    Magespacex
 * @package     Magespacex_Customercredit
 * @author      Magespacex Developer
 */
class Magespacex_Customercredit_Model_Product_Price extends Mage_Catalog_Model_Product_Type_Price
{

    const PRICE_TYPE_FIXED = 1;
    const PRICE_TYPE_DYNAMIC = 0;

    /**
     * @param null $product
     * @return mixed
     */
    public function getCreditAmount($product = null)
    {
        $creditAmount = Mage::helper('customercredit/creditproduct')->getCreditValue($product);
        switch ($creditAmount['type']) {
            case 'range':
                $creditAmount['min_price'] = $creditAmount['from'];
                $creditAmount['max_price'] = $creditAmount['to'];
                $creditAmount['price'] = $creditAmount['from'];

                if (isset($creditAmount['storecredit_rate'])) {
                    $creditAmount['price'] = $creditAmount['price'] * $creditAmount['storecredit_rate'];
                    $creditAmount['min_price'] = $creditAmount['from'] * $creditAmount['storecredit_rate'];
                    $creditAmount['max_price'] = $creditAmount['to'] * $creditAmount['storecredit_rate'];
                }

                if ($creditAmount['min_price'] == $creditAmount['max_price'])
                    $creditAmount['price_type'] = self::PRICE_TYPE_FIXED;
                else
                    $creditAmount['price_type'] = self::PRICE_TYPE_DYNAMIC;
                break;
            case 'dropdown':
                $creditAmount['min_price'] = min($creditAmount['prices']);
                $creditAmount['max_price'] = max($creditAmount['prices']);
                $creditAmount['price'] = $creditAmount['prices'][0];
                if ($creditAmount['min_price'] == $creditAmount['max_price'])
                    $creditAmount['price_type'] = self::PRICE_TYPE_FIXED;
                else
                    $creditAmount['price_type'] = self::PRICE_TYPE_DYNAMIC;
                break;
            case 'static':
                $creditAmount['price'] = $creditAmount['credit_price'];
                $creditAmount['price_type'] = self::PRICE_TYPE_FIXED;
                break;
            default:
                $creditAmount['min_price'] = 0;
                $creditAmount['price_type'] = self::PRICE_TYPE_DYNAMIC;
                $creditAmount['price'] = 0;
        }
//        Zend_debug::dump($creditAmount);die('zzz');
        return $creditAmount;
    }

    /**
     * @param $product
     * @return mixed
     */
    public function getPrice($product)
    {
        $creditAmount = $this->getCreditAmount($product);
        return $creditAmount['price'];
    }

    /**
     * @param $product
     * @param $qty
     * @param $finalPrice
     * @return mixed
     */
    protected function _applyOptionsPrice($product, $qty, $finalPrice)
    {
        if ($amount = $product->getCustomOption('credit_price_amount')) {
            $finalPrice = $amount->getValue();
        }
        return parent::_applyOptionsPrice($product, $qty, $finalPrice);
    }

    /**
     * @param $product
     * @param null $which
     * @return array
     */
    public function getPrices($product, $which = null)
    {
        return $this->getPricesDependingOnTax($product, $which);
    }

    /**
     * @param $product
     * @param null $which
     * @param null $includeTax
     * @return array
     */
    public function getPricesDependingOnTax($product, $which = null, $includeTax = null)
    {
        $creditAmount = $this->getCreditAmount($product);
        if (isset($creditAmount['min_price']) && isset($creditAmount['max_price'])) {
            $minimalPrice = Mage::helper('tax')->getPrice($product, $creditAmount['min_price'], $includeTax);
            $maximalPrice = Mage::helper('tax')->getPrice($product, $creditAmount['max_price'], $includeTax);
        } else {
            $minimalPrice = $maximalPrice = Mage::helper('tax')->getPrice($product, $creditAmount['price'], $includeTax);
        }

        if ($which == 'max')
            return $maximalPrice;
        elseif ($which == 'min')
            return $minimalPrice;
        return array($minimalPrice, $maximalPrice);
    }

    /**
     * @param $product
     * @return array
     */
    public function getMinimalPrice($product)
    {
        return $this->getPrices($product, 'min');
    }

    /**
     * @param $product
     * @return array
     */
    public function getMaximalPrice($product)
    {
        return $this->getPrices($product, 'max');
    }

}
