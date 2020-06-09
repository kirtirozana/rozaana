<?php
require_once 'Mage/Checkout/controllers/CartController.php';

//if (!class_exists('Mage_Checkout_CartController')) require_once Mage::getModuleDir('controllers', 'Mage_Checkout').DS.'CartController.php';;
class HN_Ajaxcart_KartController extends Mage_Checkout_CartController {
	public function addAction()
	{
		$output = array(); //result , message
		
// 		if (!$this->_validateFormKey()) {
			
// 			$output['result'] = 'error';
// 			$output['message'] = Mage::helper('ajaxcart')->__('The form key is invalid') ;
			
// 			echo json_encode($output);
			
// 			return;
// 		}
		$cart   = $this->_getCart();
		$params = $this->getRequest()->getParams();
		try {
			if (isset($params['qty'])) {
				$filter = new Zend_Filter_LocalizedToNormalized(
						array('locale' => Mage::app()->getLocale()->getLocaleCode())
				);
				$params['qty'] = $filter->filter($params['qty']);
			}
	
			$product = $this->_initProduct();
			$related = $this->getRequest()->getParam('related_product');
	
			/**
			 * Check product availability
			*/
			if (!$product) {
				
				$output['result'] = 'error';
			    $output['message'] = Mage::helper('ajaxcart')->__('The product does not exist') ;
			    
			    echo json_encode($output);
			    	
				return;
			}
	
			$cart->addProduct($product, $params);
			if (!empty($related)) {
				$cart->addProductsByIds(explode(',', $related));
			}
	
			$cart->save();
	
			$this->_getSession()->setCartWasUpdated(true);
	
			/**
			 * @todo remove wishlist observer processAddToCart
			*/
			Mage::dispatchEvent('checkout_cart_add_product_complete',
					array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
			);
	
			if (!$this->_getSession()->getNoCartRedirect(true)) {
				if (!$cart->getQuote()->getHasError()) {
					$message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
					$this->_getSession()->addSuccess($message);
				}
				
				$output['result'] = 'success';
			    $output['message'] = $message ;
			   
			    $output['count'] = Mage::getSingleton('checkout/cart')->getSummaryQty() ;

			    $layout = $this->loadLayout();

			    $cart_content =   Mage::getSingleton('core/layout')->getBlock('root')->getChild('header')->getChild('minicart_head')->getChild('minicart_content')->toHtml();
			    
			    $output['cart'] = $cart_content ;
			    
			    echo json_encode($output);
			    	
				return;
			}
		} catch (Mage_Core_Exception $e) {
			if ($this->_getSession()->getUseNotice(true)) {
				$this->_getSession()->addNotice(Mage::helper('core')->escapeHtml($e->getMessage()));
			} else {
				$messages = array_unique(explode("\n", $e->getMessage()));
				foreach ($messages as $message) {
					$this->_getSession()->addError(Mage::helper('core')->escapeHtml($message));
				}
			}
	
			
			
			$output['result'] = 'error';
			$output['message'] = Mage::helper('core')->escapeHtml($e->getMessage()) ;
			
		} catch (Exception $e) {
			$this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
			Mage::logException($e);
			$output['result'] = 'error';
			$output['message'] = Mage::helper('core')->escapeHtml($e->getMessage()) ;
			
		}
		
		echo json_encode($output);
		
		return;
	}
	
}