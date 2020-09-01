<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);

define('MAGENTO_ROOT', getcwd());
$mageFilename = '../app/Mage.php';
require_once $mageFilename;

Mage::setIsDeveloperMode(true);
Mage::app();
Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(0));

$categoryall = Mage::getModel('catalog/category');
$catTree = $categoryall->getTreeModel()->load();
$catIds = $catTree->getCollection()->getAllIds();


//foreach($categoryall as $category){
$category = Mage::getModel('catalog/category')->load(19);
$allProducts = $category->getProductsPosition();
foreach ($category->getChildrenCategories() as $child) {
   $childProducts = $child->getProductsPosition();
   $allProducts = array_merge($allProducts, $childProducts);
}
$category->setPostedProducts($allProducts)->save();
//}
?>
