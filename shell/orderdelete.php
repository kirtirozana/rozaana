<?php
$mageFilename = '../app/Mage.php';
require_once $mageFilename;
Varien_Profiler::enable();
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);
Mage::app('default');
Mage::register('isSecureArea', 1);
//until here you gained access to the Magento models. The rest is custom code
for($i=0;$i<250;$i++)
{
	try{
	$orderId = $i;

$order = Mage::getModel('sales/order')->load($orderId);

$invoices = $order->getInvoiceCollection();
foreach ($invoices as $invoice){
   $invoice->delete();
}

$creditnotes = $order->getCreditmemosCollection();
foreach ($creditnotes as $creditnote){
   $creditnote->delete();
}

$shipments = $order->getShipmentsCollection();
foreach ($shipments as $shipment){
   $shipment->delete();
}

$order->delete();
	}
	catch(Exception $e)
	{
		echo "order not found $i";
	}
}
?>
