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
class Magespacex_Customercredit_Block_Sharecredit_Creditcode extends Mage_Core_Block_Template
{

    protected function _construct()
    {
        parent::_construct();
        $validate_config = Mage::helper('customercredit')->getGeneralConfig('validate', null);
        $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $collection = Mage::getModel('customercredit/creditcode')->getCollection()
            ->addFieldToFilter('main_table.customer_id', $customer_id);
        $collection->setOrder('transaction_time', 'DESC');
        if ($validate_config == 0) {
            $collection->addFieldToFilter('status', array('neq' => '4'));
        }
        $this->setCollection($collection);
    }

    /**
     * @return $this
     */
    public function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('page/html_pager', 'cutomercredit_pager')
            ->setTemplate('customercredit/html/pager.phtml')
            ->setCollection($this->getCollection());
        $this->setChild('cutomercredit_pager', $pager);

        $grid = $this->getLayout()->createBlock('customercredit/sharecredit_grid', 'customercredit_grid');
        // prepare column

        $grid->addColumn('credit_code', array(
            'header' => $this->__('Credit Code'),
            'index' => 'credit_code',
            'format' => 'medium',
            'align' => 'left',
            'render' => 'getCodeTxt',
            'searchable' => true,
        ));

        $grid->addColumn('recipient_email', array(
            'header' => $this->__('Recipient\'s Email'),
            'align' => 'left',
            'index' => 'recipient_email',
            'searchable' => true,
        ));

        $grid->addColumn('amount_credit', array(
            'header' => $this->__('Amount'),
            'align' => 'left',
            'type' => 'price',
            'index' => 'amount_credit',
            'render' => 'getBalanceFormat',
            'searchable' => true,
        ));


        $grid->addColumn('transaction_time', array(
            'header' => $this->__('Sending Date'),
            'index' => 'transaction_time',
            'type' => 'date',
            'format' => 'medium',
            'align' => 'left',
            'searchable' => true,
        ));
        $statuses = Mage::getSingleton('customercredit/status')->getOptionArray();
        $grid->addColumn('status', array(
            'header' => $this->__('Status'),
            'align' => 'left',
            'index' => 'status',
            'type' => 'options',
            'options' => $statuses,
            'searchable' => true,
        ));
        $grid->addColumn('action', array(
            'header' => $this->__('Action'),
            'align' => 'left',
            'type' => 'action',
            'width' => '50px',
            'render' => 'getActions',
        ));

        $this->setChild('customercredit_grid', $grid);
        return $this;
    }

    /**
     * @param $row
     * @return string
     */
    public function getNoNumber($row)
    {
        return sprintf('#%d', $row->getId());
    }

    /**
     * @param $row
     * @return string
     */
    public function getCodeTxt($row)
    {
        $input = '<input id="input-credit-code' . $row->getId() . '" readonly type="text" class="input-text" value="' . $row->getCreditCode() . '" onblur="hiddencode' . $row->getId() . '(this);">';
        $aelement = '<a href="javascript:void(0);" onclick="viewcreditcode' . $row->getId() . '()">' . Mage::helper('customercredit')->getHiddenCode($row->getCreditCode()) . '</a>';
        $html = '<div id="inputboxcustomercredit' . $row->getId() . '" >' . $aelement . '</div>
                <script type="text/javascript">
                        function viewcreditcode' . $row->getId() . '(){
                            $(\'inputboxcustomercredit' . $row->getId() . '\').innerHTML=\'' . $input . '\';
                            $(\'input-credit-code' . $row->getId() . '\').focus();
                        }
                        function hiddencode' . $row->getId() . '(el) {
                            $(\'inputboxcustomercredit' . $row->getId() . '\').innerHTML=\'' . $aelement . '\';
                        }
                </script>';
        return $html;
    }

    /**
     * @param $row
     * @return mixed
     */
    public function getBalanceFormat($row)
    {
        $amount = Mage::getModel('customercredit/customercredit')->getConvertedFromBaseCustomerCredit($row->getAmountCredit());
        return Mage::getModel('customercredit/customercredit')->getLabel($amount);
    }

    /**
     * @param $row
     * @return string
     */
    public function getActions($row)
    {
        $creditcode = $row->getCreditCode();
        $recipient_email = $row->getRecipientEmail();
        $credit_amount = $row->getAmountCredit();
        $confirmText = Mage::helper('customercredit')->__('If you do this, the recipient will not be able to use the code and the credit will be given back to your account. Are you sure you want to continue?');
        $cancelurl = $this->getUrl('customercredit/index/cancel', array('id' => $row->getId()));
        $verify_sender_url = $this->getUrl('customercredit/index/verifySender', array(
            'id' => $row->getId(),
            'customercredit_email_input' => $recipient_email,
            'customercredit_value_input' => $credit_amount
        ));

        $action = '';
        if ($row->getStatus() == Magespacex_Customercredit_Model_Status::STATUS_UNUSED) {

            $action .=' <a href="javascript:void(0);" onclick="remove' . $row->getId() . '()">' . $this->__('Cancel') . '</a>';
            $action .='<script type="text/javascript">
                        //<![CDATA[
                            function remove' . $row->getId() . '(){
                                if (confirm(\'' . $confirmText . '\')){
                                    setLocation(\'' . $cancelurl . '\');
                                }
                            }
                        //]]>
                    </script>';
        }
        if ($row->getStatus() == Magespacex_Customercredit_Model_Status::STATUS_AWAITING_VERIFICATION) {
            $action .=' <a href="javascript:void(0);" onclick="verify' . $row->getId() . '()">' . $this->__('Verify') . '</a>';
            $action .='<script type="text/javascript">
                        //<![CDATA[
                            function verify' . $row->getId() . '(){
                                    setLocation(\'' . $verify_sender_url . '\');
                            }
                        //]]>
                    </script>';
        }
        return $action;
    }

    /**
     * @return mixed
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('cutomercredit_pager');
    }

    /**
     * @return mixed
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('customercredit_grid');
    }

    /**
     * @return mixed
     */
    protected function _toHtml()
    {
        $this->getChild('customercredit_grid')->setCollection($this->getCollection());
        return parent::_toHtml();
    }

}
