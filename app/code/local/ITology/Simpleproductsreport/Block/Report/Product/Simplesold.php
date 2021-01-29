<?php


class ITology_Simpleproductsreport_Block_Report_Product_Simplesold extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'report_product_simplesold';
        $this->_blockGroup = 'itology_simpleproductsreport';
        $this->_headerText = Mage::helper('itology_simpleproductsreport')->__('Total by Simple Products');
        parent::__construct();
        $this->_removeButton('add');
    }


}