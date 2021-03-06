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
 * Customercredit Customer Block
 * 
 * @category    Magespacex
 * @package     Magespacex_Customercredit
 * @author      Magespacex Developer
 */
class Magespacex_Customercredit_Block_Adminhtml_Creditproduct extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    /**
     * Magespacex_Customercredit_Block_Adminhtml_Creditproduct constructor.
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_creditproduct';
        $this->_blockGroup = 'customercredit';
        $this->_headerText = Mage::helper('customercredit')->__('Credit Product Manager');
        $this->_addButtonLabel = Mage::helper('customercredit')->__('Add Credit Product');
        parent::__construct();
    }

}
