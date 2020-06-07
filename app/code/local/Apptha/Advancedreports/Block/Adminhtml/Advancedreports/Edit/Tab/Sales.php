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
class Apptha_Advancedreports_Block_Adminhtml_Advancedreports_Edit_Tab_Sales extends Mage_Adminhtml_Block_Widget_Form {

    // Assigning sales report template
    protected function _construct() {
        parent::_construct();
        $this->setTemplate('advancedreports/sales.phtml');
    }

    // Getting sales report collection 
    public function advancedSalesReportCollection($db_from, $db_to) {
        $sales_status = array('complete', 'processing');
        
        if(Mage::getSingleton('core/session')->getAdvancedReportStore() == 0)
        {    
        $orderTotals = Mage::getResourceModel('sales/order_grid_collection')
                ->addAttributeToFilter('main_table.status', array('in' => $sales_status))
                ->addAttributeToFilter('main_table.created_at', array('from' => $db_from, 'to' => $db_to))
                ->setOrder('main_table.created_at', 'desc');
        $salesFlatOrder = (string) Mage::getConfig()->getTablePrefix() . 'sales_flat_order';
        $orderTotals->getSelect()->join(array('sales_flat_order' => $salesFlatOrder), "(sales_flat_order.entity_id=main_table.entity_id )", array('sales_flat_order.base_tax_amount', 'sales_flat_order.base_shipping_amount'));
        }
        else
        {
          $store_id = Mage::getSingleton('core/session')->getAdvancedReportStore();     
            
            $orderTotals = Mage::getResourceModel('sales/order_grid_collection')
                ->addAttributeToFilter('main_table.status', array('in' => $sales_status))
                ->addAttributeToFilter('main_table.created_at', array('from' => $db_from, 'to' => $db_to))
                ->addAttributeToFilter('main_table.store_id',$store_id)    
                ->setOrder('main_table.created_at', 'desc');
        $salesFlatOrder = (string) Mage::getConfig()->getTablePrefix() . 'sales_flat_order';
        $orderTotals->getSelect()->join(array('sales_flat_order' => $salesFlatOrder), "(sales_flat_order.entity_id=main_table.entity_id )", array('sales_flat_order.base_tax_amount', 'sales_flat_order.base_shipping_amount'));
                    
        }    
        return $orderTotals;
    } 

}