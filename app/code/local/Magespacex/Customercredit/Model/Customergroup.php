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
 * Customercredit Model
 * 
 * @category    Magespacex
 * @package     Magespacex_Customercredit
 * @author      Magespacex Developer
 */
class Magespacex_Customercredit_Model_Customergroup
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $customergroup = Mage::getModel('customer/group')->getCollection();

        $array_list = array();
        $count = 0;
        foreach ($customergroup as $group) {
            if ($group->getCustomerGroupId()) {
                $array_list[$count] = array('value' => $group->getCustomerGroupId(), 'label' => $group->getCustomerGroupCode());
                $count++;
            }
        }
        return $array_list;
    }

}
