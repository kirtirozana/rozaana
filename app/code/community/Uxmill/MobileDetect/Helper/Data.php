<?php
/**
 * Uxmill
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the umxill.co license that is
 * available through the world-wide-web.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Uxmill
 * @package     Uxmill_MobileDetect
 * @copyright   Copyright (c) Uxmill (http://www.uxmill.co)
 * @license     http://www.uxmill.co
 */

$includePath = Mage::getBaseDir('lib') . "/uxmill/Mobile_Detect.php";
require_once $includePath;

/**
 * @author dsgupta
 *
 */
class Uxmill_MobileDetect_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * @var Mobile_Detect
     */
    protected $_detect;

    /**
     * Uxmill_MobileDetect_Helper_Data constructor.
     */
    public function __construct()
    {
        
        
        $this->_detect = new \Mobile_Detect();
    }

    /**
     * @return unknown
     */
    public function getDetact()
    {
        return $this->_detect;
    }
}
	 