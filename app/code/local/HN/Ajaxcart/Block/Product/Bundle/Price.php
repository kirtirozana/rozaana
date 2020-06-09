<?php
/**
 * 
 * @author luuthanhthuy
 *	    	//  <div style="display:none" class="magenestajaxcartmeta" data-productid="<?php echo $_product->getId() ?>" data-productype="<?php echo $_product->getTypeId() ?>" data-canconfigure="<?php echo $can_con ?>"></div>

 */
class HN_Ajaxcart_Block_Product_Bundle_Price extends Mage_Bundle_Block_Catalog_Product_Price {
	
	    protected function _toHtml() {
	    
	    	$_product = $this->getProduct();
	    	
	    	$format =  ' <div style="display:none" class="magenestajaxcartmeta" data-productid="%s" data-productype="%s" data-canconfigure="%s"></div>';
	    	
	    	$extra =sprintf($format , $_product->getId()  , $_product->getTypeId() , 'yes');
	    	return parent::_toHtml(). $extra;
	    }
	 
}