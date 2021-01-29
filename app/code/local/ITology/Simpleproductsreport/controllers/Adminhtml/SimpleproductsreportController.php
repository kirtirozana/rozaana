<?php


require_once(Mage::getModuleDir('controllers','Mage_Adminhtml') . DS . 'Report' . DS . 'ProductController.php');

class ITology_Simpleproductsreport_Adminhtml_SimpleproductsreportController extends Mage_Adminhtml_Report_ProductController
{
    public function indexAction()
    {
        $this->_title($this->__('Total by Simple Products'));

        $this->_initAction()
            ->_setActiveMenu('report/product/simple_sold')
            ->_addBreadcrumb(Mage::helper('itology_simpleproductsreport')->__('Total by Simple Products'), Mage::helper('itology_simpleproductsreport')->__('Total by Simple Products'))
            ->_addContent($this->getLayout()->createBlock('itology_simpleproductsreport/report_product_simplesold'))
            ->renderLayout();
    }

    /**
     * Export Sold Simple Products report to CSV format action
     *
     */
    public function exportSoldCsvAction()
    {
        $fileName   = 'simple_products_ordered.csv';
        $content    = $this->getLayout()
            ->createBlock('itology_simpleproductsreport/report_product_simplesold_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export Sold Simple Products report to XML format action
     *
     */
    public function exportSoldExcelAction()
    {
        $fileName   = 'simple_products_ordered.xml';
        $content    = $this->getLayout()
            ->createBlock('itology_simpleproductsreport/report_product_simplesold_grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }
}