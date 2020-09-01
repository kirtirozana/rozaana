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


require_once 'abstract.php';

class Mirasvit_Shell_Rewards extends Mage_Shell_Abstract
{
    /**
     * Run script
     *
     */
    public function run()
    {
        $cron = Mage::getModel('rewards/cron');
        $cron->run();
    }
}

$shell = new Mirasvit_Shell_Rewards();
$shell->run();
