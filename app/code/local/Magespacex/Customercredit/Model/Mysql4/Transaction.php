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
 * Customercredit Resource Model
 * 
 * @category    Magespacex
 * @package     Magespacex_Customercredit
 * @author      Magespacex Developer
 */
class Magespacex_Customercredit_Model_Mysql4_Transaction extends Mage_Core_Model_Mysql4_Abstract
{

    public function _construct()
    {
        $this->_init('customercredit/transaction', 'transaction_id');
    }

    /**
     * @return mixed
     */
    public function getCreditUsed()
    {
        $table = $this->getMainTable();
        $select = $this->_getReadAdapter()
            ->select()->from($table)
            ->reset('columns')
            ->columns(new Zend_Db_Expr('SUM(spent_credit)'));
        $spent_credit = $this->_getReadAdapter()->fetchOne($select);
        return Mage::helper('core')->currency($spent_credit);
    }

}
