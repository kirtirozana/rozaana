<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/extension_rewards
 * @version   1.1.42
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



/**
 * @method Mirasvit_Rewards_Model_Earning_Behavior getFirstItem()
 * @method Mirasvit_Rewards_Model_Earning_Behavior getLastItem()
 * @method Mirasvit_Rewards_Model_Resource_Earning_Behavior_Collection|Mirasvit_Rewards_Model_Earning_Behavior[] addFieldToFilter
 * @method Mirasvit_Rewards_Model_Resource_Earning_Behavior_Collection|Mirasvit_Rewards_Model_Earning_Behavior[] setOrder
 */
class Mirasvit_Rewards_Model_Resource_Earning_Behavior_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('rewards/earning_behavior');
    }

    public function toOptionArray($emptyOption = false)
    {
        $arr = array();
        if ($emptyOption) {
            $arr[0] = array('value' => 0, 'label' => Mage::helper('rewards')->__('-- Please Select --'));
        }
        /** @var Mirasvit_Rewards_Model_Earning_Behavior $item */
        foreach ($this as $item) {
            $arr[] = array('value' => $item->getId(), 'label' => $item->getName());
        }

        return $arr;
    }

    public function getOptionArray($emptyOption = false)
    {
        $arr = array();
        if ($emptyOption) {
            $arr[0] = Mage::helper('rewards')->__('-- Please Select --');
        }
        /** @var Mirasvit_Rewards_Model_Earning_Behavior $item */
        foreach ($this as $item) {
            $arr[$item->getId()] = $item->getName();
        }

        return $arr;
    }

    protected function initFields()
    {
        $select = $this->getSelect();
        // $select->columns(array('is_replied' => new Zend_Db_Expr("answer <> ''")));
    }

    protected function _initSelect()
    {
        parent::_initSelect();
        $this->initFields();
    }

     /************************/
}
