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


class Mirasvit_Rewards_Model_Earning_Behavior_Tag_Observer extends Mirasvit_Rewards_Model_Earning_Behavior_Observer
{
    public function afterSaveTag($observer)
    {
        if ($observer->getObject() instanceof Mage_Tag_Model_Tag) {
            $tag         = $observer->getObject();
            $behavior    = Mage::getSingleton('rewards/earning_behavior')->getByActionCode(
                Mirasvit_Rewards_Model_Earning_Behavior_Tag_Action::ACTION_CODE);
            $transaction = Mage::getModel('rewards/earning_behavior_tag_transaction');

            if ($behavior) {
                $transaction->make($behavior, $tag);
            }
        }
    }
}