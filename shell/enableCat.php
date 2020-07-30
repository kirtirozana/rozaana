<?php
$mageFilename = '../app/Mage.php';
require_once $mageFilename;
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);
Mage::app('admin');
Mage::register('isSecureArea', 1);
$parentId = '2';

$_categories = Mage::getModel('catalog/category')->getCollection();

    foreach ($_categories as $_category){
       // if you have any issue then you many need to load the 
        $_category = Mage::getModel('catalog/category')->load($_category->getId());
       $_category->setIsActive(1);
    $_category->setDisplayMode('PRODUCTS');
    $_category->setIsAnchor(1); //for active anchor
            $_category->setIncludeInMenu(1);
		$_category->setPageLayout('two_columns_left');

       $_category->save();
    }
?>
