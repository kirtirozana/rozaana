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


class Mirasvit_Rewards_Block_Checkout_Earn extends Mirasvit_Rewards_Block_Checkout_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('mst_rewards/checkout/earn.phtml' );
    }

    public function _toHtml()
    {
        if (!Mage::getModel('customer/session')->isLoggedIn()) {
            return '';
        }
        return parent::_toHtml();
    }
}
