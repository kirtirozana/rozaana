<?php
class  HN_Ajaxcart_Helper_Quickview  extends Mage_Catalog_Helper_Product_View{
	public function initProductLayout($product, $controller)
	{
		$design = Mage::getSingleton('catalog/design');
		$settings = $design->getDesignSettings($product);
	
		if ($settings->getCustomDesign()) {
			$design->applyCustomDesign($settings->getCustomDesign());
		}
	
		$update = $controller->getLayout()->getUpdate();
		$update->addHandle('default');
		$controller->addActionLayoutHandles();
		$update->addHandle('PRODUCT_TYPE_' . $product->getTypeId());
		$update->addHandle('PRODUCT_' . $product->getId());
		$controller->loadLayoutUpdates();
	
		// Apply custom layout update once layout is loaded
		$layoutUpdates = $settings->getLayoutUpdates();
		if ($layoutUpdates) {
			if (is_array($layoutUpdates)) {
				foreach($layoutUpdates as $layoutUpdate) {
					$update->addUpdate($layoutUpdate);
				}
			}
		}
	
		$controller->generateLayoutXml()->generateLayoutBlocks();
	
		// Apply custom layout (page) template once the blocks are generated
		if ($settings->getPageLayout()) {
			$controller->getLayout()->helper('page/layout')->applyTemplate($settings->getPageLayout());
		}
	
		//$controller->getLayout()->unsetBlock('header');
		$controller->getLayout()->unsetBlock('top.menu');
	
		$layout =  $controller->getLayout();
		 
		//$controller->getLayout()->unsetBlock('header');
		// $controller->getLayout()->unsetBlock('top.menu');
		$rootTempl = $controller->getLayout()->getBlock('root')->getTemplate();
	
		// $controller->getLayout()->getBlock('content')->setTemplate('page/empty.html');
	
		// $controller->getLayout()->unsetBlock('content');
	
		//         $controller->getLayout()->removeOutputBlock('header');
		//         $controller->getLayout()->removeOutputBlock('top.search');
		//         $controller->getLayout()->removeOutputBlock('footer');
	//hn_ajax_addcart_button
		$currentCategory = Mage::registry('current_category');
		//$root = $controller->getLayout()->getBlock('root')->setTemplate('hn_ajaxcart/empty.phtml');
		$root = $controller->getLayout()->getBlock('root')->setTemplate('hn_ajaxcart/empty.phtml');
	
		//$root->unsetChild('header');
		$root->unsetChild('footer');
		$template_view =    $root->getChild('content')->getChild('product.info')->getTemplate();
		$root->getChild('content')->getChild('product.info')->setTemplate('hn_ajaxcart/quickview.phtml');
// 		$content =  $root->getChild('content')->getChild('product.info')->unsetChild('product.info.sharing');
		$root->getChild('content')->unsetChild('hn_ajax_addcart_button');
		if ($root) {
			$controllerClass = $controller->getFullActionName();
			if ($controllerClass != 'catalog-product-view') {
				$root->addBodyClass('catalog-product-view');
			}
			$root->addBodyClass('product-' . $product->getUrlKey());
			if ($currentCategory instanceof Mage_Catalog_Model_Category) {
				$root->addBodyClass('categorypath-' . $currentCategory->getUrlPath())
				->addBodyClass('category-' . $currentCategory->getUrlKey());
			}
		}
	
		return $this;
	}
	
}
