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
class Apptha_Advancedreports_Block_Adminhtml_Advancedreports_Edit_Tab_Timetopurchase extends Mage_Adminhtml_Block_Widget_Form {

    // Assigning timetopurchase report template
    protected function _construct() {
        parent::_construct();
        $this->setTemplate('advancedreports/timetopurchase.phtml');
    }

    // Getting timetopurchase report collection    
    public function advancedTimetopurchaseReportCollection($db_from, $db_to) {

        $sales_status = array('complete', 'processing');

        if(Mage::getSingleton('core/session')->getAdvancedReportStore() == 0)
        {         
        $carts = Mage::getResourceModel('sales/order_grid_collection')
                ->addAttributeToFilter('main_table.status', array('in' => $sales_status))
                ->addAttributeToFilter('main_table.created_at', array('from' => $db_from, 'to' => $db_to));

        $salesFlatQuote = (string) Mage::getConfig()->getTablePrefix() . 'sales_flat_quote';
        $carts->getSelect()->join(array('sales_flat_quote' => $salesFlatQuote), "(sales_flat_quote.reserved_order_id=main_table.increment_id)", array('sales_flat_quote.created_at as sfq_created_at', 'sales_flat_quote.updated_at as sfq_updated_at'));
        }
        else
        {
        $store_id = Mage::getSingleton('core/session')->getAdvancedReportStore();    
        $carts = Mage::getResourceModel('sales/order_grid_collection')
                ->addAttributeToFilter('main_table.status', array('in' => $sales_status))
                ->addAttributeToFilter('main_table.created_at', array('from' => $db_from, 'to' => $db_to))
                ->addAttributeToFilter('main_table.store_id', $store_id); 

        $salesFlatQuote = (string) Mage::getConfig()->getTablePrefix() . 'sales_flat_quote';
        $carts->getSelect()->join(array('sales_flat_quote' => $salesFlatQuote), "(sales_flat_quote.reserved_order_id=main_table.increment_id)", array('sales_flat_quote.created_at as sfq_created_at', 'sales_flat_quote.updated_at as sfq_updated_at'));                 
        }
        return $carts;
    }

}