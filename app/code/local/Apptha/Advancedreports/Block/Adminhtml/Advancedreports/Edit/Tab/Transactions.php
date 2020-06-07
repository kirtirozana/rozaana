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
class Apptha_Advancedreports_Block_Adminhtml_Advancedreports_Edit_Tab_Transactions extends Mage_Adminhtml_Block_Widget_Form {

    // Assigning transactions report template
    protected function _construct() {
        parent::_construct();
        $this->setTemplate('advancedreports/transactions.phtml');
    }

    // Getting transactions report collection   
    public function advancedTransactionsReportCollection($db_from, $db_to) {

        $sales_status = array('complete', 'processing');
        
        if(Mage::getSingleton('core/session')->getAdvancedReportStore() == 0)
        {        
        $orderTotals = Mage::getResourceModel('sales/order_grid_collection')
                ->addAttributeToFilter('main_table.status', array('in' => $sales_status))
                ->addAttributeToFilter('main_table.created_at', array('from' => $db_from, 'to' => $db_to))
                ->setOrder('main_table.created_at', 'desc');
        $salesFlatOrder = (string) Mage::getConfig()->getTablePrefix() . 'sales_flat_order';
        $orderTotals->getSelect()->join(array('sales_flat_order' => $salesFlatOrder), "(sales_flat_order.entity_id=main_table.entity_id )", array('sales_flat_order.base_tax_amount', 'sales_flat_order.base_shipping_amount', 'sales_flat_order.total_qty_ordered'));
    
        } 
        else
        { 
        $store_id = Mage::getSingleton('core/session')->getAdvancedReportStore();        
        $orderTotals = Mage::getResourceModel('sales/order_grid_collection')
                ->addAttributeToFilter('main_table.status', array('in' => $sales_status))
                ->addAttributeToFilter('main_table.created_at', array('from' => $db_from, 'to' => $db_to))
                ->addAttributeToFilter('main_table.store_id', $store_id)
                ->setOrder('main_table.created_at', 'desc');
        $salesFlatOrder = (string) Mage::getConfig()->getTablePrefix() . 'sales_flat_order';
        $orderTotals->getSelect()->join(array('sales_flat_order' => $salesFlatOrder), "(sales_flat_order.entity_id=main_table.entity_id )", array('sales_flat_order.base_tax_amount', 'sales_flat_order.base_shipping_amount', 'sales_flat_order.total_qty_ordered'));
        
        }    
        
        return $orderTotals;
    }

    // Getting order url  
    public function getAdvancedReportOrderUrl($advanced_increment_id) {
        $order = Mage::getModel('sales/order')->loadByIncrementId($advanced_increment_id);
        $order_id = $order->getId();
        return Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/view", array('order_id' => $order_id));
    }

    // Getting product info for selected order
    public function getAdvancedProductInfo($advanced_increment_id) {
        $advanced_order_info = Mage::getModel('sales/order')->loadByIncrementId($advanced_increment_id);
        $items = $advanced_order_info->getAllVisibleItems();
        $advanced_item_info = '';
        $transactions_items_count=0;
        $transactions_items_count = count($items);         
        $transactions_sno_count=1;       
        
        foreach ($items as $item) {
            $advanced_item_info .= $item->getName();
            if($transactions_items_count > 1 && $transactions_items_count != $transactions_sno_count )
            {
             $advanced_item_info .= ' || ';    
            }
            
            $transactions_sno_count = $transactions_sno_count + 1;
        }
        return $advanced_item_info;
    }

}