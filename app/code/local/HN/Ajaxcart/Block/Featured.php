<?php
class HN_Ajaxcart_Block_Featured extends  Mage_Catalog_Block_Product_New {
	
	protected $_productIds = array();
	
	protected function _getProductCollection()
	{
		/* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
		
        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addIdFilter($this->getProductIds())->setCurPage(1);
        
        return   $collection;
	}
	
	public function setProductIds($ids) {
		if ($ids) {
			$this->_productIds = explode(',', $ids);
			return $this;
		}
	}
	
	public function getProductIds() {
		return $this->_productIds;
	}
}