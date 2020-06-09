<?php
class HN_Ajaxcart_Block_Toprate extends Mage_Catalog_Block_Product_New {
	protected function _getProductCollection() {
		/* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
		$collection = Mage::getResourceModel ( 'catalog/product_collection' );
		$collection->setVisibility ( Mage::getSingleton ( 'catalog/product_visibility' )->getVisibleInCatalogIds () );
		$collection = $this->_addProductAttributesAndPrices ( $collection )->addIdFilter ( $this->getProductIds () )->setCurPage ( 1 );
		
		return $collection;
	}
	public function getProductIds() {
		$output = array();
		$rating = $this->getTopRatedProduct();
		if ($rating) {
			foreach ($rating as $item) {
				
				$product = $item['product'];
				$output[]=$product->getId();
			}
		}
		
		return $output;
	}
	public function getTopRatedProduct() {
		$limit = 6;
		
		// get all visible products
		$_products = Mage::getModel ( 'catalog/product' )->getCollection ();
		$_products->setVisibility ( Mage::getSingleton ( 'catalog/product_visibility' )->getVisibleInCatalogIds () );
		$_products->addAttributeToSelect ( '*' )->addStoreFilter ();
		
		$_rating = array ();
		
		foreach ( $_products as $_product ) {
			
			$storeId = Mage::app ()->getStore ()->getId ();
			
			// get ratings for individual product
			$_productRating = Mage::getModel ( 'review/review_summary' )->setStoreId ( $storeId )->load ( $_product->getId () );
			
			$_rating [] = array (
					'rating' => $_productRating ['rating_summary'],
					'product' => $_product 
			);
		}
		
		// sort in descending order of rating
		arsort ( $_rating );
		
		// returning limited number of products and ratings
		return array_slice ( $_rating, 0, $limit );
	}
}