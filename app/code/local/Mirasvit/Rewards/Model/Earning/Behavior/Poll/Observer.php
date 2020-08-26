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


class Mirasvit_Rewards_Model_Earning_Behavior_Poll_Observer extends Mirasvit_Rewards_Model_Earning_Behavior_Observer
{
    public function afterPollVoteAdd($observer)
    {
        $poll        = $observer->getPoll();
        $behavior    = Mage::getSingleton('rewards/earning_behavior')->getByActionCode(
            Mirasvit_Rewards_Model_Earning_Behavior_Poll_Action::ACTION_CODE);
        $transaction = Mage::getModel('rewards/earning_behavior_poll_transaction');

        if ($behavior
            && $this->isCustomerLoggedIn()
            && !$this->isCustomerHasPoints($transaction->getCode($poll))) {
            // make new transaction
            $transaction->make($behavior, $poll);
        }
    }
}