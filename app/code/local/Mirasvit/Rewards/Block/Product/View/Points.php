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


class Mirasvit_Rewards_Block_Product_View_Points extends Mage_Core_Block_Template
{

	public function getProduct()
	{
		return Mage::registry('current_product');
	}

	public function getProductPoints()
	{
		return Mage::helper('rewards/balance_earn')->getProductPoints($this->getProduct());
	}
}