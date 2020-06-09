<?php
/**
 * 
 * @author luuthanhthuy
 *	    	//  <div style="display:none" class="magenestajaxcartmeta" data-productid="<?php echo $_product->getId() ?>" data-productype="<?php echo $_product->getTypeId() ?>" data-canconfigure="<?php echo $can_con ?>"></div>

 */
class HN_Ajaxcart_Block_Product_Price extends Mage_Catalog_Block_Product_Price {
	
	    protected function _toHtml() {
	    	$_product = $this->getProduct();
	    	$show_quick_view = false;
	    	$quickview = "";
	    	
	    	$action = Mage::app ()->getFrontController ()->getAction ();
	    	
	    	$fullActionName = $action->getFullActionName () ;
	    	
	    	if ($fullActionName == 'catalog_category_view') {
	    		$show_quick_view = true;
	    		$quickview_link = Mage::getBaseUrl() .'ajaxcart/index/quickview/product/' . $_product->getId();
	    		$format =  ' <a  class="mn-quickview" data-productid="%s" data-productype="%s"  onclick="mn_quickview(this)" data-qvl="%s"><i class="fa fa-search"></i> </a>';
	    		$quickview  =sprintf($format,  $_product->getId()  , $_product->getTypeId() ,$quickview_link);
	    	} 
	    
	    	 
	    	
	    	
	    	$format =  ' <div style="display:none" class="magenestajaxcartmeta" data-productid="%s" data-productype="%s" data-canconfigure="%s"></div>';
	    	
	    	$extra =sprintf($format , $_product->getId()  , $_product->getTypeId() , 'yes');
	    	if ($show_quick_view ) $extra .= $quickview;
	    	return parent::_toHtml(). $extra;
	    }
	 
}