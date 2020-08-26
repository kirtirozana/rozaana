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



class Mirasvit_RewardsSocial_Block_Buttons_Googleplus_One extends Mirasvit_RewardsSocial_Block_Buttons_Abstract
{
	public function getOneUrl()
	{
		return Mage::getUrl('rewardssocial/googleplus/one');
	}

	public function isLiked()
	{
		if (!$customer = $this->_getCustomer()) {
			return false;
		}
		$url = $this->getCurrentUrl();
		if ($earnedTransaction = Mage::helper('rewardssocial/balance')->getEarnedPointsTransaction($customer, Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_GOOGLEPLUS_ONE.'-'.$url)) {
			return true;
		}
	}

	public function getEstimatedEarnPoints()
	{
		$url = $this->getCurrentUrl();
		return Mage::helper('rewards/behavior')->getEstimatedEarnPoints(Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_GOOGLEPLUS_ONE, $this->_getCustomer(), false, $url);
	}

	/**
	 * @deprecated
	 */
	public function getEstimatedPointsAmount()
	{
		return $this->getEstimatedEarnPoints();
	}
}