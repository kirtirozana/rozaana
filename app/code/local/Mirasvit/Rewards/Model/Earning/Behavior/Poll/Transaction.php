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


class Mirasvit_Rewards_Model_Earning_Behavior_Poll_Transaction extends Mirasvit_Rewards_Model_Transaction
{
    public function make($behavior, $poll)
    {
        $this->setAmount($behavior->getPointsAmount())
            ->setComment('Vote in poll "'.$poll->getPollTitle().'"')
            ->setStatus(self::STATUS_APPROVED)
            ->setExpiresDays($behavior->getPointsExpiresAfter())
            ->setCode($this->getCode($poll));

        return $this->save();
    }

    public function getCode($poll)
    {
        return Mirasvit_Rewards_Model_Earning_Behavior_Poll_Action::ACTION_CODE.'_'.$poll->getId();
    }
}