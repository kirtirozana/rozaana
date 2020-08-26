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



class Mirasvit_RewardsSocial_Helper_MailTest extends EcomDev_PHPUnit_Test_Case
{
    protected $helper;
    protected function getExpectedMail($code)
    {
        return file_get_contents(dirname(__FILE__)."/MailTest/expected/$code.html");
    }

    protected function setUp()
    {
        parent::setUp();
        $this->helper = Mage::helper('rewardssocial/mail');
        $this->helper->emails = array();
    }
}
