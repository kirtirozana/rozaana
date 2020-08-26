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
 * @method Mirasvit_Rewards_Model_Spending_Rule getFirstItem()
 * @method Mirasvit_Rewards_Model_Spending_Rule getLastItem()
 * @method Mirasvit_Rewards_Model_Resource_Spending_Rule_Collection|Mirasvit_Rewards_Model_Spending_Rule[] addFieldToFilter
 * @method Mirasvit_Rewards_Model_Resource_Spending_Rule_Collection|Mirasvit_Rewards_Model_Spending_Rule[] setOrder
 */
class Mirasvit_Rewards_Model_Resource_Spending_Rule_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('rewards/spending_rule');
    }

    public function toOptionArray($emptyOption = false)
    {
        $arr = array();
        if ($emptyOption) {
            $arr[0] = array('value' => 0, 'label' => Mage::helper('rewards')->__('-- Please Select --'));
        }
        /** @var Mirasvit_Rewards_Model_Spending_Rule $item */
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
        /** @var Mirasvit_Rewards_Model_Spending_Rule $item */
        foreach ($this as $item) {
            $arr[$item->getId()] = $item->getName();
        }

        return $arr;
    }

    public function addWebsiteFilter($websiteId)
    {
        $this->getSelect()
            ->where("EXISTS (SELECT * FROM `{$this->getTable('rewards/spending_rule_website')}`
                AS `spending_rule_website_table`
                WHERE main_table.spending_rule_id = spending_rule_website_table.spending_rule_id
                AND spending_rule_website_table.website_id in (?))", array(-1, $websiteId));

        return $this;
    }

    public function addCustomerGroupFilter($customerGroupId)
    {
        $this->getSelect()
            ->where("EXISTS (SELECT * FROM `{$this->getTable('rewards/spending_rule_customer_group')}`
                AS `spending_rule_customer_group_table`
                WHERE main_table.spending_rule_id = spending_rule_customer_group_table.spending_rule_id
                AND spending_rule_customer_group_table.customer_group_id in (?))", array(-1, $customerGroupId));

        return $this;
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

    public function addIsActiveFilter()
    {
        $this->addFieldToFilter('is_active', true);

        return $this;
    }

    public function addCurrentFilter()
    {
        $this->addIsActiveFilter();
        $now = Mage::getSingleton('core/date')->gmtDate();
        $this->getSelect()->where("(main_table.active_from <= '$now' OR isnull(main_table.active_from)) AND ('$now' <= main_table.active_to OR isnull(main_table.active_to))");

        return $this;
    }
}
