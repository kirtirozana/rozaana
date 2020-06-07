<?php

/**
 * Apptha
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category    Apptha
 * @package     Apptha_Advancedreports
 * @version     0.2.1
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 * 
 */
?>
<?php
class Apptha_Advancedreports_Helper_Data extends Mage_Core_Helper_Abstract {

    public function domainKey($tkey) {

        $message = "EM-AREPORTMP0EFIL9XEV8YZAL7KCIUQ6NI5OREH4TSEB3TSRIF2SI1ROTAIDALG-JW";

        for ($i = 0; $i < strlen($tkey); $i++) {
            $key_array[] = $tkey[$i];
        }
        $enc_message = "";
        $kPos = 0;
        $chars_str = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
        for ($i = 0; $i < strlen($chars_str); $i++) {
            $chars_array[] = $chars_str[$i];
        }
        for ($i = 0; $i < strlen($message); $i++) {
            $char = substr($message, $i, 1);

            $offset = $this->getOffset($key_array[$kPos], $char);
            $enc_message .= $chars_array[$offset];
            $kPos++;
            if ($kPos >= count($key_array)) {
                $kPos = 0;
            }
        }

        return $enc_message;
    }

    public function getOffset($start, $end) {

        $chars_str = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
        for ($i = 0; $i < strlen($chars_str); $i++) {
            $chars_array[] = $chars_str[$i];
        }

        for ($i = count($chars_array) - 1; $i >= 0; $i--) {
            $lookupObj[ord($chars_array[$i])] = $i;
        }

        $sNum = $lookupObj[ord($start)];
        $eNum = $lookupObj[ord($end)];

        $offset = $eNum - $sNum;

        if ($offset < 0) {
            $offset = count($chars_array) + ($offset);
        }

        return $offset;
    }
    // Sorting by key
    function sort_array_by_key($arr, $key) {
        $keys = array();
        foreach ($arr as $k => $value) {
            $keys[$k] = strtolower($value[$key]);
        }
        asort($keys);
        $result = array();
        foreach ($keys as $k => $value) {
            $result[] = $arr[$k];
        }

        $result = array_reverse($result);
        return $result;
    }

    // Getting followup product ids
    
    public function getFollowupproductsCollection() {
         
        
        
        if (Mage::getSingleton('core/session')->getAdvancedReportStore() != 0) {
        $store_id = Mage::getSingleton('core/session')->getAdvancedReportStore();
        $collections = Mage::getModel('advancedreports/advancedreports')->getCollection()
                       ->addFieldToFilter('store_id', $store_id) 
                       ->addFieldToFilter('status_id', 1);  
        }
        else {
        $collections = Mage::getModel('advancedreports/advancedreports')->getCollection()
                       ->addFieldToFilter('status_id', 1);    
        } 
        
        
        $products = array();
        foreach ($collections as $value) {
            $products[] = $value->getProductId();
        }

        return $products;
    }

}