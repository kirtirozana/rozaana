<?php

/**
 * Magespacex
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magespacex.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magespacex.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magespacex
 * @package     Magespacex_Storecredit
 * @module      Storecredit
 * @author      Magespacex Developer
 *
 * @copyright   Copyright (c) 2016 Magespacex (http://www.magespacex.com/)
 * @license     http://www.magespacex.com/license-agreement.html
 *
 */

/**
 * Customercredit Adminhtml Block
 * 
 * @category    Magespacex
 * @package     Magespacex_Customercredit
 * @author      Magespacex Developer
 */
class Magespacex_Customercredit_Block_Adminhtml_Report_Customercredit 
    extends Magespacex_Customercredit_Block_Adminhtml_Report_Graph
{

    /**
     * Magespacex_Customercredit_Block_Adminhtml_Report_Customercredit constructor.
     */
    public function __construct()
    {
        $currency = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
        $this->_googleChartParams = array(
            'cht' => 'lc',
            'chf' => 'bg,s,f4f4f4|c,lg,90,ffffff,0.1,ededed,0',
            'chdl' => $this->__('Used credit') . '|' . $this->__('Received credit'),
            'chco' => '2424ff,db4814',
            'chxt' => 'x,y,y',
            'chxlexpend' => '|2:||' . $this->__('# Credit(' . $currency . ')')
        );

        $this->setHtmlId('customer-credit');
        parent::__construct();
    }

    protected function _prepareData()
    {
        $this->setDataHelperName('customercredit/report_customercredit');
        $this->getDataHelper()->setParam('store', $this->getRequest()->getParam('store'));
        $data = $this->setDataRows(array('spent_credit', 'received_credit'));
        $this->_axisMaps = array(
            'x' => 'range',
            'y' => 'received_credit'
        );
        parent::_prepareData();
    }

    /**
     * @return mixed
     */
    public function getCommentContent()
    {
        return $this->__('This report shows the <b>used Credit </b> and <b>reveived Credit</b> of Customer');
    }

}
