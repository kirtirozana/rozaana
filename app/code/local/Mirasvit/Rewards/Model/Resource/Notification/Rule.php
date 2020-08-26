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



class Mirasvit_Rewards_Model_Resource_Notification_Rule extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('rewards/notification_rule', 'notification_rule_id');
    }

    protected function loadWebsiteIds(Mage_Core_Model_Abstract $object)
    {
        /* @var  Mirasvit_Rewards_Model_Notification_Rule $object */
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('rewards/notification_rule_website'))
            ->where('notification_rule_id = ?', $object->getId());
        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $array = array();
            foreach ($data as $row) {
                $array[] = $row['website_id'];
            }
            $object->setData('website_ids', $array);
        }

        return $object;
    }

    protected function saveWebsiteIds($object)
    {
        /* @var  Mirasvit_Rewards_Model_Notification_Rule $object */
        $condition = $this->_getWriteAdapter()->quoteInto('notification_rule_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('rewards/notification_rule_website'), $condition);
        foreach ((array) $object->getData('website_ids') as $id) {
            $objArray = array(
                'notification_rule_id' => $object->getId(),
                'website_id' => $id,
            );
            $this->_getWriteAdapter()->insert(
                $this->getTable('rewards/notification_rule_website'), $objArray);
        }
    }

    protected function loadCustomerGroupIds(Mage_Core_Model_Abstract $object)
    {
        /* @var  Mirasvit_Rewards_Model_Notification_Rule $object */
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('rewards/notification_rule_customer_group'))
            ->where('notification_rule_id = ?', $object->getId());
        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $array = array();
            foreach ($data as $row) {
                $array[] = $row['customer_group_id'];
            }
            $object->setData('customer_group_ids', $array);
        }

        return $object;
    }

    protected function saveCustomerGroupIds($object)
    {
        /* @var  Mirasvit_Rewards_Model_Notification_Rule $object */
        $condition = $this->_getWriteAdapter()->quoteInto('notification_rule_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('rewards/notification_rule_customer_group'), $condition);
        foreach ((array) $object->getData('customer_group_ids') as $id) {
            $objArray = array(
                'notification_rule_id' => $object->getId(),
                'customer_group_id' => $id,
            );
            $this->_getWriteAdapter()->insert(
                $this->getTable('rewards/notification_rule_customer_group'), $objArray);
        }
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        /** @var  Mirasvit_Rewards_Model_Notification_Rule $object */
        if (!$object->getIsMassDelete()) {
            $this->loadWebsiteIds($object);
            $this->loadCustomerGroupIds($object);
        }

        return parent::_afterLoad($object);
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        /** @var  Mirasvit_Rewards_Model_Notification_Rule $object */
        if (!$object->getId()) {
            $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
        }
        $object->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());

        return parent::_beforeSave($object);
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        /** @var  Mirasvit_Rewards_Model_Notification_Rule $object */
        if (!$object->getIsMassStatus()) {
            $this->saveWebsiteIds($object);
            $this->saveCustomerGroupIds($object);
        }

        return parent::_afterSave($object);
    }

    /************************/
}
