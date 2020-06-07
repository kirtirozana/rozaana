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
class Apptha_Advancedreports_Block_Adminhtml_Advancedreports_Edit_Tab_Product extends Mage_Adminhtml_Block_Widget_Form {

    // Assigning product report template
    protected function _construct() {
        parent::_construct();
        $this->setTemplate('advancedreports/product.phtml');
    }

    // Geting all products collection   
    public function advancedProductReportCollection($db_from, $db_to) {
       
        $orderTotals = Mage::getModel('sales/order_item')->getCollection();
        $orderTotals->getSelect()->joinInner(array('cp' => Mage::getSingleton('core/resource')->getTableName('sales/order_grid')), 'cp.entity_id = main_table.order_id AND cp.status IN ("complete","processing")', array('cp.created_at as sfog_created_at'));
        $orderTotals->addAttributeToFilter('cp.created_at', array('from' => $db_from, 'to' => $db_to));
        
        if(Mage::getSingleton('core/session')->getAdvancedReportStore() != 0)
        {
        $store_id = Mage::getSingleton('core/session')->getAdvancedReportStore();
        $orderTotals->addAttributeToFilter('cp.store_id',$store_id ); 
        }
        
        return $orderTotals;
    }    

    // Selected product name
    public function selectedProductName($selected_pid) {
        $obj_product = Mage::getModel('catalog/product');
        $_product = $obj_product->load($selected_pid);
        return $_product->getName();
    }

    // Selected product collection
    public function advancedIndividualProductReportCollection($db_from, $db_to, $individual_pid) {
        
        $orderTotals = Mage::getModel('sales/order_item')->getCollection();
        $orderTotals->addAttributeToFilter('main_table.product_id', $individual_pid);
        $orderTotals->getSelect()->joinInner(array('cp' => Mage::getSingleton('core/resource')->getTableName('sales/order_grid')), 'cp.entity_id = main_table.order_id AND cp.status IN ("complete","processing")', array('cp.created_at as sfog_created_at'));
        $orderTotals->addAttributeToFilter('cp.created_at', array('from' => $db_from, 'to' => $db_to));
        
        if(Mage::getSingleton('core/session')->getAdvancedReportStore() != 0)
        {
        $store_id = Mage::getSingleton('core/session')->getAdvancedReportStore();    
        $orderTotals->addAttributeToFilter('cp.store_id',$store_id);
        }         
        return $orderTotals;
    }

    // Getting all products name
    public function allProductName($db_from, $db_to, $selected_pid) {
        $orderTotals = Mage::getModel('sales/order_item')->getCollection();
        $orderTotals->getSelect()->joinInner(array('cp' => Mage::getSingleton('core/resource')->getTableName('sales/order_grid')), 'cp.entity_id = main_table.order_id AND cp.status IN ("complete","processing")', array('cp.created_at as sfog_created_at'));
        $orderTotals->addAttributeToFilter('cp.created_at', array('from' => $db_from, 'to' => $db_to));
        
        if(Mage::getSingleton('core/session')->getAdvancedReportStore() != 0)
        {
        $store_id = Mage::getSingleton('core/session')->getAdvancedReportStore();    
        $orderTotals->addAttributeToFilter('cp.store_id', $store_id);
        }
        

        $obj = Mage::getModel('catalog/product');
        $all_product_report = array();
        foreach ($orderTotals as $value) {
            $_product = $obj->load($value->getProductId());
            $_product_name = $_product->getName();

            if (empty($all_product_report[$_product_name]['name']) && $value->getProductId() != $selected_pid) {
                $all_product_report[$_product_name]['name'] = $_product->getName();
                $all_product_report[$_product_name]['id'] = $value->getProductId();
                $all_product_report[$_product_name]['qty'] = 0;
            }

            if ($value->getProductId() != $selected_pid) {
                if (array_key_exists($_product_name, $all_product_report)) {
                    $all_product_report[$_product_name]['qty'] = $all_product_report[$_product_name]['qty'] + round($value->getQtyOrdered());
                } else {
                    $all_product_report[$_product_name]['qty'] = round($value->getQtyOrdered());
                }
            }
        }
        return $all_product_report;
    }

    
}




