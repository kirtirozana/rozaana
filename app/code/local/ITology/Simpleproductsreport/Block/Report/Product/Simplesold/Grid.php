<?php


class ITology_Simpleproductsreport_Block_Report_Product_Simplesold_Grid extends Mage_Adminhtml_Block_Report_Product_Sold_Grid
{
    /**
     * Setting up proper product collection name for a report
     *
     * @return ITology_Simpleproductsreport_Block_Report_Product_Simplesold_Grid
     */
    protected function _prepareCollection()
    {
        Mage_Adminhtml_Block_Report_Grid::_prepareCollection();
        $this->getCollection()
            ->initReport('itology_simpleproductsreport/simpleproduct_sold_collection');
        return $this;
    }

    protected function _prepareColumns()
    {

        $this->addColumn('entity_id', array(
            'header'    =>Mage::helper('reports')->__('ID'),
            'index'     =>'entity_id',
            'align'     => 'left'
        ));

        $this->addColumn('name', array(
            'header'    =>Mage::helper('reports')->__('Product Name'),
            'index'     =>'name',
            'align'     => 'left'
        ));

        $this->addColumn('sku', array(
            'header'    =>Mage::helper('reports')->__('Product SKU'),
            'index'     =>'sku',
            'align'     => 'left'
        ));

        $this->addColumn('ordered_qty', array(
            'header'    =>Mage::helper('reports')->__('Quantity Ordered'),
            'index'     =>'ordered_qty',
            'align'     => 'left'
        ));

        $this->addColumn('price', array(
            'header'        => Mage::helper('reports')->__('Total Price'),
            'index'         => 'price',
            'align'          => 'left',
            'renderer'      => 'ITology_Simpleproductsreport_Block_Report_Product_Price'
        ));

        $this->addColumn('cost', array(
            'header'        => Mage::helper('reports')->__('Total Cost'),
            'index'         => 'cost',
            'align'         => 'left',
            'renderer'      => 'ITology_Simpleproductsreport_Block_Report_Product_Cost'
        ));



        return parent::_prepareColumns();
    }
}