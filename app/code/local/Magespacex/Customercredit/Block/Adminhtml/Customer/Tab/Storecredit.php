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
class Magespacex_Customercredit_Block_Adminhtml_Customer_Tab_Storecredit extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    protected $_customerCredit;

    /**
     * @return mixed
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('customercredit_fieldset', array(
            'legend' => Mage::helper('customercredit')->__('Credit Information')
        ));

        $fieldset->addField('credit_balance', 'note', array(
            'label' => Mage::helper('customercredit')->__('Current Credit Balance'),
            'title' => Mage::helper('customercredit')->__('Current Credit Balance'),
            'text' => $this->getBalanceCredit(),
        ));
        $fieldset->addField('credit_value', 'text', array(
            'label' => Mage::helper('customercredit')->__('Add or subtract  a credit value'),
            'title' => Mage::helper('customercredit')->__('Add or subtract  a credit value'),
            'name' => 'credit_value',
            'note' => Mage::helper('customercredit')->__('You can add or subtract an amount from customer’s balance by entering a number. For example, enter “99” to add $99 and “-99” to subtract $99'),
        ));
        $fieldset->addField('description', 'textarea', array(
            'label' => Mage::helper('customercredit')->__('Comment'),
            'title' => Mage::helper('customercredit')->__('Comment'),
            'name' => 'description',
        ));
        $fieldset->addField('sendemail', 'checkbox', array(
            'after_element_html' => Mage::helper('customercredit')->__('Send email to customer'),
            'title' => Mage::helper('customercredit')->__('Send email to customer'),
            'type' => 'checkbox',
            'name' => 'send_mail',
            'onclick' => 'this.value = this.checked ? 1 : 0;'
        ));

        $form->addFieldset('balance_history_fieldset', array(
            'legend' => Mage::helper('customercredit')->__('Transaction History')
        ))->setRenderer($this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('customercredit/transactionhistory.phtml'));

        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * @return mixed
     */
    public function getCredit()
    {
        if (is_null($this->_customerCredit)) {
            $customerId = Mage::registry('current_customer')->getId();
            $this->_customerCredit = Mage::getModel('customer/customer')->load($customerId);
        }
        return $this->_customerCredit;
    }

    /**
     * @return mixed
     */
    public function getTabLabel()
    {
        return Mage::helper('customercredit')->__('Store Credit');
    }

    /**
     * @return mixed
     */
    public function getTabTitle()
    {
        return Mage::helper('customercredit')->__('Store Credit');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        if (Mage::registry('current_customer')->getId()) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        if (Mage::registry('current_customer')->getId()) {
            return false;
        }
        return true;
    }

    /**
     * @return mixed
     */
    public function getBalanceCredit()
    {
        $customerCredit = $this->getCredit()->getCreditValue();
        return Mage::helper('core')->currency($customerCredit);
    }

}
