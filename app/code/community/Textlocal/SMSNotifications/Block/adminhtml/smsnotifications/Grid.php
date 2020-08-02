<?php
class Textlocal_SMSNotifications_Block_Adminhtml_Smsnotifications_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
   public function __construct()
{
parent::__construct();
$collection = Mage::getModel('smsnotifications/data')->getCollection();
$this->setCollection($collection);
}

protected function _prepareLayout()
{
parent::_prepareLayout();

$pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
$pager->setCollection($this->getCollection());
$this->setChild('pager', $pager);
$this->getCollection()->load();

return $this;
}

public function getPagerHtml()
{
return $this->getChildHtml('pager');
}

public function getCollection() 
{ 
$limit = 10;
$curr_page = 1;

if(Mage::app()->getRequest()->getParam('p'))
{
$curr_page = Mage::app()->getRequest()->getParam('p');
}

//Calculate Offset 
$offset = ($curr_page - 1) * $limit;

$collection = Mage::getModel('smsnotifications/data')->getCollection()
->addFieldToFilter('status',1);

$collection->getSelect()->limit($limit,$offset);

return $collection;
} 


}

