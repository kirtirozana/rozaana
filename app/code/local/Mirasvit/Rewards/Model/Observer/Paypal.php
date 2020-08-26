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


class Mirasvit_Rewards_Model_Observer_Paypal
{
    public function onPrepareLineItems($observer) {
        $event = $observer->getEvent();

        if (!$paypalCart = $event->getPaypalCart()) {
        	return $this;
        }
        $quote = $paypalCart->getSalesEntity();
        $purchase = Mage::helper('rewards/purchase')->getByQuote($address->getQuote());

        if (!$purchase->getSpendAmount()) {
            return $this;
        }

        $paypalCart->updateTotal(Mage_Paypal_Model_Cart::TOTAL_DISCOUNT, $purchase->getSpendAmount());
        return $this;
    }
}