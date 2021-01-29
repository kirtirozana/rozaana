<?php

class ITology_Simpleproductsreport_Block_Report_Product_Price extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $price =  $row->getData($this->getColumn()->getIndex());
        $qty = $row->getData('ordered_qty');

        return '$' . $qty * $price;
    }
}