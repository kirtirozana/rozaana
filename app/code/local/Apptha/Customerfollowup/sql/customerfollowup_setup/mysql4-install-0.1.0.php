<?php
/**
 * Apptha
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category    Apptha
 * @package     Apptha_Customer-Follow-Up
 * @version     1.1
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 * */
$installer = $this;
$installer->startSetup();
$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('customerfollowup')};
CREATE TABLE {$this->getTable('customerfollowup')} (
  `customerfollowup_id` int(11) unsigned NOT NULL auto_increment,
  `customer_name` varchar(255) NOT NULL default '',
  `customer_email` varchar(255) NOT NULL default '',
  `order_id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `amount_in_cart` int(11) NOT NULL,  
  `created_time` datetime NULL,
  `update_time` timestamp NULL,
  PRIMARY KEY (`customerfollowup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");
$installer->endSetup(); 