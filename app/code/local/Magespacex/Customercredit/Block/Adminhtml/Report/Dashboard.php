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
class Magespacex_Customercredit_Block_Adminhtml_Report_Dashboard extends Mage_Adminhtml_Block_Template
{

    /**
     * Magespacex_Customercredit_Block_Adminhtml_Report_Dashboard constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('customercredit/report/dashboard.phtml');
    }

    protected function _prepareLayout()
    {
        $this->setChild('customer-credit', 
            $this->getLayout()->createBlock('customercredit/adminhtml_report_customercredit'));
        parent::_prepareLayout();
    }

}
