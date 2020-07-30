<?php
require_once('../app/Mage.php');
Mage::app();

$pro_ids = Mage::getModel('catalog/product')->getCollection()->getAllIds();
foreach($pro_ids as $pro_id){
    $product = Mage::getModel('catalog/product')->load($pro_id);
    $categories = $product->getCategoryIds(); $save = 0;
    foreach($categories as $categorie){
        $category = Mage::getModel('catalog/category')->load($categorie);
        foreach ($category->getParentCategories() as $parent) {
            if(!in_array($parent->getId(), $categories)){
                $categories[] = $parent->getId(); $save = 1;
            }
        }
    }
    if($save == 1){
        $product->setCategoryIds($categories);
        $product->save();
    }
}
?>
