<?php

/**
 * Magespacex
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magespacex.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magespacex.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magespacex
 * @package     Magespacex_Storecredit
 * @module      Storecredit
 * @author      Magespacex Developer
 *
 * @copyright   Copyright (c) 2016 Magespacex (http://www.magespacex.com/)
 * @license     http://www.magespacex.com/license-agreement.html
 *
 */

/**
 * Customercredit Status Model
 *
 * @category    Magespacex
 * @package     Magespacex_Customercredit
 * @author      Magespacex Developer
 */
class Magespacex_Customercredit_Model_TransactionType extends Varien_Object
{

    const TYPE_UPDAT_BY_ADMIN = 1;
    const TYPE_SHARE_CREDIT_TO_FRIENDS = 2;
    const TYPE_RECEIVE_CREDIT_FROM_FRIENDS = 3;
    const TYPE_REDEEM_CREDIT = 4;
    const TYPE_REFUND_ORDER_INTO_CREDIT = 5;
    const TYPE_CHECK_OUT_BY_CREDIT = 6;
    const TYPE_CANCEL_SHARE_CREDIT = 7;
    const TYPE_BUY_CREDIT = 8;
    const TYPE_CANCEL_ORDER = 9;
    const TYPE_REFUND_CREDIT_PRODUCT = 10;

}
