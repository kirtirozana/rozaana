<?php

include_once '../app/Mage.php';
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$updates_file="skudata.csv";
$sku_entry=array();
$updates_handle=fopen($updates_file, 'r');
if($updates_handle) {
    while($sku_entry=fgetcsv($updates_handle, 1000, ",")) {
        $old_sku=$sku_entry[0];
        $new_sku=$sku_entry[1];
        echo "<br>Updating ".$old_sku." to ".$new_sku." - ";
        try {
            $get_item = Mage::getModel('catalog/product')->loadByAttribute('sku', $old_sku);
            if ($get_item) {
                $get_item->setSku($new_sku)->save();
                echo "Sku updat successfully";
            } else {
                echo "Product Item not found";
            }
        } catch (Exception $e) {
            echo "Product can't getting from Magento: ".$e->getMessage()."<br>";
            return;
        }
    }
}
fclose($updates_handle);
?>
