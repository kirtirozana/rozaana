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
 * Customercredit Adminhtml Controller
 * 
 * @category    Magespacex
 * @package     Magespacex_Customercredit
 * @author      Magespacex Developer
 */
class Magespacex_Customercredit_Adminhtml_ReporttransactionController extends Mage_Adminhtml_Controller_Action
{

    /**
     * init layout and set active for current menu
     *
     * @return Magespacex_Customercredit_Adminhtml_ReporttransactionController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('customercredit/transaction')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager')
        );
        return $this;
    }

    /**
     * index action
     */
    public function indexAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }

    public function dashboardAction()
    {
        $this->_title($this->__('Customer Credit Chart'));
        $this->loadLayout();
        $this->_setActiveMenu('customercredit');
        $this->_addBreadcrumb(Mage::helper('customercredit')->__('Customer Credit Chart'), Mage::helper('customercredit')->__('Customer Credit Chart'));
        $this->_addContent($this->getLayout()->createBlock('customercredit/adminhtml_report'));
        $this->renderLayout();
    }

    public function ajaxBlockAction()
    {
        $output = $this->getLayout()->createBlock("customercredit/adminhtml_report_customercredit")->toHtml();
        $this->getResponse()->setBody($output);
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('customercredit');
    }

}
