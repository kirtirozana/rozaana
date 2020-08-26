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



class Mirasvit_RewardsSocial_TwitterController extends Mage_Core_Controller_Front_Action
{
    public function _getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    /**
     * @return void
     */
    public function tweetAction()
    {
        $url = $this->getRequest()->getParam('url');
        $transaction = Mage::helper('rewards/behavior')->processRule(
            Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_TWITTER_TWEET,
            $this->_getCustomer(),
            false,
            $url
        );
        if ($transaction) {
            echo Mage::helper('rewardssocial')->__(
                "You've earned %s for Tweet!", Mage::helper('rewards')->formatPoints($transaction->getAmount())
            );
        }
        die;
    }
}
