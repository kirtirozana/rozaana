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


class Mirasvit_Rewards_Helper_BehaviorTest extends EcomDev_PHPUnit_Test_Case
{
    protected $helper;

    protected function setUp()
    {
        parent::setUp();
        $this->helper = Mage::helper('rewards/behavior');
    }

    /**
     * @test
     * @loadFixture data
     */
    public function processRuleTest() {
        $customer = Mage::getModel('customer/customer')->load(2);
        $this->helper->processRule('customer_sign_up', 2, 2);
        $balance = Mage::helper('rewards/balance')->getBalancePoints(2);
        $this->assertEquals(20, $balance);
    }
}