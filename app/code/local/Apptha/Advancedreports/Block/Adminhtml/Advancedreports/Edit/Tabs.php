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
class Apptha_Advancedreports_Block_Adminhtml_Advancedreports_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    // Prepare construct for report tabs
    public function __construct() {
        parent::__construct();
        $this->setId('advancedreports_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('advancedreports')->__('All Reports'));
    }
  protected function _prepareLayout()
  { 
      
      // Assigning advanced reports tabs
        $sales_active = $product_active = $transaction_active = $timetopurchase_active =  $topsellingproducts_active = $gainer_active = $followupproducts_active = false;

        if ($this->getRequest()->getParam('page')) {
            switch ($this->getRequest()->getParam('page')) {
                case "product":
                    $product_active = true;
                    break;
                case "transactions":
                    $transaction_active = true;
                    break;
                case "timetopurchase":
                    $timetopurchase_active = true;
                    break;
                case "topsellingproducts":
                    $topsellingproducts_active = true;
                    break;
                case "gainer":
                    $gainer_active = true;
                    break;
                case "followupproducts":
                    $followupproducts_active = true;
                    break;

                default:
                    $sales_active = true;
            }
        } else {
            $sales_active = true;
        }
        
        // assigning sales report tab.
        $this->addTab('sales_section', array(
            'label' => Mage::helper('advancedreports')->__('Sales Report'),
            'title' => Mage::helper('advancedreports')->__('Sales Report'),
            'content' => $this->getLayout()->createBlock('advancedreports/adminhtml_advancedreports_edit_tab_sales')->toHtml(),
            'active' => $sales_active,
        ));


        // assigning product report tab.
        $this->addTab('product_section', array(
            'label' => Mage::helper('advancedreports')->__('Product Report'),
            'title' => Mage::helper('advancedreports')->__('Product Report'),
        //    'content' => $this->getLayout()->createBlock('advancedreports/adminhtml_advancedreports_edit_tab_product')->toHtml(),
            'active' => $product_active,
        ));

        //  assigning transactions report tab.        
        $this->addTab('transactions_section', array(
            'label' => Mage::helper('advancedreports')->__('Transactions Report'),
            'title' => Mage::helper('advancedreports')->__('Transactions Report'),
        //    'content' => $this->getLayout()->createBlock('advancedreports/adminhtml_advancedreports_edit_tab_transactions')->toHtml(),
            'active' => $transaction_active,
        ));

        // assigning timetopurchase report tab.
        $this->addTab('timetopurchase_section', array(
            'label' => Mage::helper('advancedreports')->__('Time to Purchase Report'),
            'title' => Mage::helper('advancedreports')->__('Time to Purchase Report'),
         //   'content' => $this->getLayout()->createBlock('advancedreports/adminhtml_advancedreports_edit_tab_timetopurchase')->toHtml(),
            'active' => $timetopurchase_active,
        ));

        // assigning topsellingproducts report tab.
        $this->addTab('topsellingproducts_section', array(
            'label' => Mage::helper('advancedreports')->__('Top Selling Products Report'),
            'title' => Mage::helper('advancedreports')->__('Top Selling Products Report'),
       //     'content' => $this->getLayout()->createBlock('advancedreports/adminhtml_advancedreports_edit_tab_topsellingproducts')->toHtml(),
            'active' => $topsellingproducts_active,
        ));

        // assigning gainer report tab.
        $this->addTab('gainer_section', array(
            'label' => Mage::helper('advancedreports')->__('Gainer / Loser Report'),
            'title' => Mage::helper('advancedreports')->__('Gainer / Loser Report'),
         //   'content' => $this->getLayout()->createBlock('advancedreports/adminhtml_advancedreports_edit_tab_gainer')->toHtml(),
            'active' => $gainer_active,
        ));

        // assigning followupproducts report tab.
        $this->addTab('followupproducts_section', array(
            'label' => Mage::helper('advancedreports')->__('Follow Up Products Report'),
            'title' => Mage::helper('advancedreports')->__('Follow Up Products Report'),
           // 'content' => $this->getLayout()->createBlock('advancedreports/adminhtml_advancedreports_edit_tab_followupproducts')->toHtml(),
            'active' => $followupproducts_active,
        ));

      
       return parent::_prepareLayout();
  }  

}

