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
 * Customercredit Block
 * 
 * @category    Magespacex
 * @package     Magespacex_Customercredit
 * @author      Magespacex Developer
 */
class Magespacex_Customercredit_Block_History extends Mage_Core_Block_Template
{

    /**
     * prepare block's layout
     *
     * @return Magespacex_Customercredit_Block_Customercredit
     */
    public function _construct()
    {
        parent::_construct();
        $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $collection = Mage::getModel('customercredit/transaction')->getCollection()
            ->addFieldToFilter('customer_id', $customer_id);
        $collection->setOrder('transaction_time', 'DESC');
        $this->setCollection($collection);
    }

    /**
     * @return $this
     */
    public function _prepareLayout()
    {
        $pager = $this->getLayout()->createBlock('page/html_pager', 'customercredit.history.pager')
            ->setTemplate('customercredit/html/pager.phtml')
            ->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @param $trans_type_id
     * @return mixed
     */
    public function getTransactionType($trans_type_id)
    {
        return Mage::getModel('customercredit/typetransaction')->load($trans_type_id)->getTransactionName();
    }

    /**
     * @param $credit
     * @return mixed
     */
    public function getCurrencyLabel($credit)
    {
        $credit = Mage::getModel('customercredit/customercredit')->getConvertedFromBaseCustomerCredit($credit);
        return Mage::getModel('customercredit/customercredit')->getLabel($credit);
    }

}
