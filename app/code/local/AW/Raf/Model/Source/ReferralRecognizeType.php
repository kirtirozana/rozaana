<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Raf
 * @version    2.2.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Raf_Model_Source_ReferralRecognizeType
{
    const REFERRAL_LINK     = 1;
    const EMAIL_ADDRESS     = 2;

    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::REFERRAL_LINK,
                'label' => Mage::helper('awraf')->__("Referral link")
            ),
            array(
                'value' => self::EMAIL_ADDRESS,
                'label' => Mage::helper('awraf')->__("Email address")
            )
        );
    }
}