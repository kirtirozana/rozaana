<?php
require_once('app/Mage.php');
Mage::app();
$newStoreId = Mage_Core_Model_App::ADMIN_STORE_ID;
Mage::app()->setCurrentStore(Mage::getModel('core/store')->load($newStoreId));


$collection = Mage::getResourceModel('catalog/product_collection')
->addAttributeToSelect('*')
->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_DISABLED));
echo "total disabled products :". sizeof($collection);


?>
