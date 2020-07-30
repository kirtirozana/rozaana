<?php
$mageFilename = '../app/Mage.php';
require_once $mageFilename;
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);
Mage::app('admin');
Mage::register('isSecureArea', 1);
$parentId = '2';


$cats=array("Mobile & Laptop Accessories","Mobile Accessories","Chargers","Headphones","USB Cables","Dried Fruits","Imported","Indian","Speciality","Ambi, Kachhi Ambi & Amla","Bhindi, Beans & Brinjal ","Cabbage & Cauliflower","Carrot & Radish","Corn, Sprout & Zucchini","Cucumber & Capsicum","Cucumber, Capsicum & Kakdi","Lemon, Ginger & Garlic","Palak, Mint, Basil, Spring Onion & Lemon Grass","Potato, Onion & Tomato","Pumpkin, Lauki, Tori & Karela","Speciality","Idli & Appa Maker","Containers","Oil Dispensers","Forks & Spoons","Juicer & Blender");

foreach($cats as $catname){
 try{
    $category = Mage::getModel('catalog/category');
    $category->setName($catname);
    //$category->setUrlKey('your-cat-url-key');
    $category->setIsActive(1);
    $category->setDisplayMode('PRODUCTS');
    $category->setIsAnchor(1); //for active anchor
    $category->setStoreId(Mage::app()->getStore()->getId());
    $parentCategory = Mage::getModel('catalog/category')->load($parentId);
    $category->setPath($parentCategory->getPath());
    $category->save();
} catch(Exception $e) {
    print_r($e);
}
}
?>
