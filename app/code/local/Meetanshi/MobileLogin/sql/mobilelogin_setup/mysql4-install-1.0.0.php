<?php
$installer = $this;
$installer->startSetup();
$setup = Mage::getModel('customer/entity_setup', 'core_setup');
$setup->addAttribute(
    'customer', 'mobile_number', array(
    'type' => 'text',
    'input' => 'text',
    'label' => 'Mobile Number',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'is_unique' => 1,
    )
);


$attribute = Mage::getSingleton("eav/config")->getAttribute("customer", "mobile_number");
$usedinForms = array();
$usedinForms[] = "adminhtml_customer";
$usedinForms[] = "checkout_register";
$usedinForms[] = "customer_account_create";
$usedinForms[] = "customer_account_edit";
$usedinForms[] = "adminhtml_checkout";
$attribute->setData("used_in_forms", $usedinForms)
    ->setData("is_used_for_customer_segment", true)
    ->setData("is_system", 0)
    ->setData("is_user_defined", 1)
    ->setData("is_visible", 1)
    ->setData("sort_order", 100);
$attribute->save();

$installer->run(
    "
CREATE TABLE `{$this->getTable('mobilelogin')}` (
	`mobilelogin_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`mobilenumber` varchar(13) NOT NULL default '0', 
	`login_otp` varchar(255) NOT NULL,
	`login_verify` tinyint(1) NOT NULL DEFAULT 0,
	`register_otp` varchar(255) NOT NULL,
	`register_verify` tinyint(1) NOT NULL DEFAULT 0,
	`forgot_otp` varchar(255) NOT NULL,
	`forgot_verify` tinyint(1) NOT NULL DEFAULT 0,
	`update_otp` varchar(255) NOT NULL,
	`update_verify` tinyint(1) NOT NULL DEFAULT 0,
	PRIMARY KEY (`mobilelogin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
"
);
$installer->endSetup();



