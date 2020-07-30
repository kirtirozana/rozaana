<?php
ini_set('display_errors','On');
ini_set('memory_limit','512M');
error_reporting(E_ALL);
require_once('abstract.php');

class Mage_Shell_Updater extends Mage_Shell_Abstract
{
    public function run()
    {
        $products = Mage::getResourceModel('catalog/product_collection');
                //->addAttributeToFilter('is_imported', 1); // attribute added by importer
        $c=0;
        foreach($products as $p) {
	echo $c;
            $pid = $p->getId();
            $product = Mage::getModel('catalog/product')->load($pid);
            $mediaGallery = $product->getMediaGallery();
            if (isset($mediaGallery['images'])){
                foreach ($mediaGallery['images'] as $image){
                    Mage::getSingleton('catalog/product_action')
                    //->updateAttributes(array($pid), array('image'=>$image['file']), 0);
                    //->updateAttributes(array($pid), array('base_image'=>$image['file']), 0);
                    //->updateAttributes(array($pid), array('small_image'=>$image['file']), 0);
                    ->updateAttributes(array($pid), array('thumbnail'=>$image['file']), 0);
                    $c++;
                    break;
                }
            }
        }
        echo($c . " product(s) updated.");
    }

}


$shell = new Mage_Shell_Updater();
$shell->run();

