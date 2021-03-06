<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Phoenix
 * @package     Phoenix_Moneybookers
 * @copyright   Copyright (c) 2011 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
include_once('Mage/Catalog/Block/Product/View/Type/Configurable.php');
class HN_Ajaxcart_Block_Config extends  Mage_Catalog_Block_Product_View_Type_Configurable
{
	public function _prepareLayout() {

		return parent::_prepareLayout();
	}
	public function getJsonConfig()
	{
		Mage::log("haha", null,"config.txt", true);
		$attributes = array();
		$options    = array();
		$store      = $this->getCurrentStore();
		$taxHelper  = Mage::helper('tax');
		$currentProduct = $this->getProduct();

		$preconfiguredFlag = $currentProduct->hasPreconfiguredValues();
		if ($preconfiguredFlag) {
			$preconfiguredValues = $currentProduct->getPreconfiguredValues();
			$defaultValues       = array();
		}

		foreach ($this->getAllowProducts() as $product) {
			$productId  = $product->getId();

			foreach ($this->getAllowAttributes() as $attribute) {
				$productAttribute   = $attribute->getProductAttribute();
				$productAttributeId = $productAttribute->getId();
				$attributeValue     = $product->getData($productAttribute->getAttributeCode());
				Mage::log("attribute id ".$attributeValue, null, "config.txt", true);
				if (!isset($options[$productAttributeId])) {
					$options[$productAttributeId] = array();
				}

				if (!isset($options[$productAttributeId][$attributeValue])) {
					$options[$productAttributeId][$attributeValue] = array();
				}
				$options[$productAttributeId][$attributeValue][] = $productId;
			}
		}

		$this->_resPrices = array(
		$this->_preparePrice($currentProduct->getFinalPrice())
		);

		foreach ($this->getAllowAttributes() as $attribute) {
			$productAttribute = $attribute->getProductAttribute();
			$attributeId = $productAttribute->getId();
			$info = array(
               'id'        => $productAttribute->getId(),
               'code'      => $productAttribute->getAttributeCode(),
               'label'     => $attribute->getLabel(),
               'options'   => array()
			);

			$optionPrices = array();
			$prices = $attribute->getPrices();
			if (is_array($prices)) {
				foreach ($prices as $value) {
					if(!$this->_validateAttributeValue($attributeId, $value, $options)) {
						continue;
					}
					$currentProduct->setConfigurablePrice(
					$this->_preparePrice($value['pricing_value'], $value['is_percent'])
					);
					$currentProduct->setParentId(true);
					Mage::dispatchEvent(
                        'catalog_product_type_configurable_price',
					array('product' => $currentProduct)
					);
					$configurablePrice = $currentProduct->getConfigurablePrice();

					if (isset($options[$attributeId][$value['value_index']])) {
						$productsIndex = $options[$attributeId][$value['value_index']];
					} else {
						$productsIndex = array();
					}

					$info['options'][] = array(
                        'id'        => $value['value_index'],
                        'label'     => $value['label'],
                        'price'     => $configurablePrice,
                        'oldPrice'  => $this->_preparePrice($value['pricing_value'], $value['is_percent']),
                        'products'  => $productsIndex,
					);
					$optionPrices[] = $configurablePrice;
					//$this->_registerAdditionalJsPrice($value['pricing_value'], $value['is_percent']);
				}
			}
			/**
			 * Prepare formated values for options choose
			 */
			foreach ($optionPrices as $optionPrice) {
				foreach ($optionPrices as $additional) {
					$this->_preparePrice(abs($additional-$optionPrice));
				}
			}
			if($this->_validateAttributeInfo($info)) {
				$attributes[$attributeId] = $info;
			}

			// Add attribute default value (if set)
			if ($preconfiguredFlag) {
				$configValue = $preconfiguredValues->getData('super_attribute/' . $attributeId);
				if ($configValue) {
					$defaultValues[$attributeId] = $configValue;
				}
			}
		}

		$taxCalculation = Mage::getSingleton('tax/calculation');
		if (!$taxCalculation->getCustomer() && Mage::registry('current_customer')) {
			$taxCalculation->setCustomer(Mage::registry('current_customer'));
		}

		$_request = $taxCalculation->getRateRequest(false, false, false);
		$_request->setProductClassId($currentProduct->getTaxClassId());
		$defaultTax = $taxCalculation->getRate($_request);

		$_request = $taxCalculation->getRateRequest();
		$_request->setProductClassId($currentProduct->getTaxClassId());
		$currentTax = $taxCalculation->getRate($_request);

		$taxConfig = array(
            'includeTax'        => $taxHelper->priceIncludesTax(),
            'showIncludeTax'    => $taxHelper->displayPriceIncludingTax(),
            'showBothPrices'    => $taxHelper->displayBothPrices(),
            'defaultTax'        => $defaultTax,
            'currentTax'        => $currentTax,
            'inclTaxTitle'      => Mage::helper('catalog')->__('Incl. Tax')
		);

		$config = array(
            'attributes'        => $attributes,
            'template'          => str_replace('%s', '#{price}', $store->getCurrentCurrency()->getOutputFormat()),
		//            'prices'          => $this->_prices,
            'basePrice'         => $this->_registerJsPrice($this->_convertPrice($currentProduct->getFinalPrice())),
            'oldPrice'          => $this->_registerJsPrice($this->_convertPrice($currentProduct->getPrice())),
            'productId'         => $currentProduct->getId(),
            'chooseText'        => Mage::helper('catalog')->__('Choose an Option...'),
            'taxConfig'         => $taxConfig
		);

		if ($preconfiguredFlag && !empty($defaultValues)) {
			$config['defaultValues'] = $defaultValues;
		}

		$config = array_merge($config, $this->_getAdditionalConfig());
			
		return Mage::helper('core')->jsonEncode($config);
	}


	public function getProductId() {
		if (!$this->hasData('id')) {
			$this->setData('id', Mage::registry('id'));
		}
		return $this->getData('productid');

	}
	public function initProduct($productId) {
		$product = Mage::getModel('catalog/product')
		->setStoreId(Mage::app()->getStore()->getId())
		->load($productId);
		return $product;
	}


	public function getJsonHTML()
	{
		Mage::log("haha", null,"config.txt", true);
		$attributes = array();
		$options    = array();
		$store      = $this->getCurrentStore();
		$taxHelper  = Mage::helper('tax');
		$currentProduct = $this->getProduct();

		$preconfiguredFlag = $currentProduct->hasPreconfiguredValues();
		if ($preconfiguredFlag) {
			$preconfiguredValues = $currentProduct->getPreconfiguredValues();
			$defaultValues       = array();
		}

		foreach ($this->getAllowProducts() as $product) {
			$productId  = $product->getId();

			foreach ($this->getAllowAttributes() as $attribute) {
				$productAttribute   = $attribute->getProductAttribute();
				$productAttributeId = $productAttribute->getId();
				$attributeValue     = $product->getData($productAttribute->getAttributeCode());
				Mage::log("attribute id ".$attributeValue, null, "config.txt", true);
				if (!isset($options[$productAttributeId])) {
					$options[$productAttributeId] = array();
				}

				if (!isset($options[$productAttributeId][$attributeValue])) {
					$options[$productAttributeId][$attributeValue] = array();
				}
				$options[$productAttributeId][$attributeValue][] = $productId;
			}
		}

		$this->_resPrices = array(
		$this->_preparePrice($currentProduct->getFinalPrice())
		);

		foreach ($this->getAllowAttributes() as $attribute) {
			$productAttribute = $attribute->getProductAttribute();
			$attributeId = $productAttribute->getId();
			$info = array(
               'id'        => $productAttribute->getId(),
               'code'      => $productAttribute->getAttributeCode(),
               'label'     => $attribute->getLabel(),
               'options'   => array()
			);

			$optionPrices = array();
			$prices = $attribute->getPrices();
			if (is_array($prices)) {
				foreach ($prices as $value) {
					if(!$this->_validateAttributeValue($attributeId, $value, $options)) {
						continue;
					}
					$currentProduct->setConfigurablePrice(
					$this->_preparePrice($value['pricing_value'], $value['is_percent'])
					);
					$currentProduct->setParentId(true);
					Mage::dispatchEvent(
                        'catalog_product_type_configurable_price',
					array('product' => $currentProduct)
					);
					$configurablePrice = $currentProduct->getConfigurablePrice();

					if (isset($options[$attributeId][$value['value_index']])) {
						$productsIndex = $options[$attributeId][$value['value_index']];
					} else {
						$productsIndex = array();
					}

					$info['options'][] = array(
                        'id'        => $value['value_index'],
                        'label'     => $value['label'],
                        'price'     => $configurablePrice,
                        'oldPrice'  => $this->_preparePrice($value['pricing_value'], $value['is_percent']),
                        'products'  => $productsIndex,
					);
					$optionPrices[] = $configurablePrice;
					//$this->_registerAdditionalJsPrice($value['pricing_value'], $value['is_percent']);
				}
			}
			/**
			 * Prepare formated values for options choose
			 */
			foreach ($optionPrices as $optionPrice) {
				foreach ($optionPrices as $additional) {
					$this->_preparePrice(abs($additional-$optionPrice));
				}
			}
			if($this->_validateAttributeInfo($info)) {
				$attributes[$attributeId] = $info;
			}

			// Add attribute default value (if set)
			if ($preconfiguredFlag) {
				$configValue = $preconfiguredValues->getData('super_attribute/' . $attributeId);
				if ($configValue) {
					$defaultValues[$attributeId] = $configValue;
				}
			}
		}

		$taxCalculation = Mage::getSingleton('tax/calculation');
		if (!$taxCalculation->getCustomer() && Mage::registry('current_customer')) {
			$taxCalculation->setCustomer(Mage::registry('current_customer'));
		}

		$_request = $taxCalculation->getRateRequest(false, false, false);
		$_request->setProductClassId($currentProduct->getTaxClassId());
		$defaultTax = $taxCalculation->getRate($_request);

		$_request = $taxCalculation->getRateRequest();
		$_request->setProductClassId($currentProduct->getTaxClassId());
		$currentTax = $taxCalculation->getRate($_request);

		$taxConfig = array(
            'includeTax'        => $taxHelper->priceIncludesTax(),
            'showIncludeTax'    => $taxHelper->displayPriceIncludingTax(),
            'showBothPrices'    => $taxHelper->displayBothPrices(),
            'defaultTax'        => $defaultTax,
            'currentTax'        => $currentTax,
            'inclTaxTitle'      => Mage::helper('catalog')->__('Incl. Tax')
		);

		$config = array(
            'attributes'        => $attributes,
            'template'          => str_replace('%s', '#{price}', $store->getCurrentCurrency()->getOutputFormat()),
		//            'prices'          => $this->_prices,
            'basePrice'         => $this->_registerJsPrice($this->_convertPrice($currentProduct->getFinalPrice())),
            'oldPrice'          => $this->_registerJsPrice($this->_convertPrice($currentProduct->getPrice())),
            'productId'         => $currentProduct->getId(),
            'chooseText'        => Mage::helper('catalog')->__('Choose an Option...'),
            'taxConfig'         => $taxConfig
		);

		if ($preconfiguredFlag && !empty($defaultValues)) {
			$config['defaultValues'] = $defaultValues;
		}

		$config = array_merge($config, $this->_getAdditionalConfig());
		return $attributes;
//		$html = "<table><tr> ";
//		$firsttime = 0;
//		$used_key = 0;
//		$first_option = "";
//		foreach ($attributes as $key=> $att) {
//			if ($firsttime == 0) {
//				$used_key = $key;
//				foreach ($att['options'] as $option_key => $option_value) {
//					//echo $option_value['label'];
//					$first_option .="<option>" . $option_value['label'] . "</option>";
//
//				}
//				$firsttime++;
//				$html .= "<td>" .$att['label'] . "</td>" ;
//				$html .= "<td> <td> <select id='ajax_select_".$att['code']."'> <option id='ajax_option" .$att['code']."'>" .Mage::helper('catalog')->__('Choose an Option...') . "</option>";
//				$html .= $first_option;
//				$html .= "</select></td></tr>" ;
//			}
//			if ($firsttime != 0) {
//				if ($key!= $used_key) { 
//				$html .= "<td>" .$att['label'] . "</td>" ;
//				$html .= "<td> <td> <select id='ajax_select_".$att['code']."'> <option id='ajax_option" .$att['code']."'>" .Mage::helper('catalog')->__('Choose an Option...') . "</option></select></td></tr>" ;
//			}
//			}
//		}
//		return $html;
		//return Mage::helper('core')->jsonEncode($config);
	}
	
	public function getp() {
        $config = array();
        if (!$this->hasOptions()) {
            return Mage::helper('core')->jsonEncode($config);
        }

        $_request = Mage::getSingleton('tax/calculation')->getRateRequest(false, false, false);
        $_request->setProductClassId($this->getProduct()->getTaxClassId());
        $defaultTax = Mage::getSingleton('tax/calculation')->getRate($_request);

        $_request = Mage::getSingleton('tax/calculation')->getRateRequest();
        $_request->setProductClassId($this->getProduct()->getTaxClassId());
        $currentTax = Mage::getSingleton('tax/calculation')->getRate($_request);

        $_regularPrice = $this->getProduct()->getPrice();
        $_finalPrice = $this->getProduct()->getFinalPrice();
        $_priceInclTax = Mage::helper('tax')->getPrice($this->getProduct(), $_finalPrice, true);
        $_priceExclTax = Mage::helper('tax')->getPrice($this->getProduct(), $_finalPrice);

        $config = array(
            'productId'           => $this->getProduct()->getId(),
            'priceFormat'         => Mage::app()->getLocale()->getJsPriceFormat(),
            'includeTax'          => Mage::helper('tax')->priceIncludesTax() ? 'true' : 'false',
            'showIncludeTax'      => Mage::helper('tax')->displayPriceIncludingTax(),
            'showBothPrices'      => Mage::helper('tax')->displayBothPrices(),
            'productPrice'        => Mage::helper('core')->currency($_finalPrice, false, false),
            'productOldPrice'     => Mage::helper('core')->currency($_regularPrice, false, false),
            'priceInclTax'        => Mage::helper('core')->currency($_priceInclTax, false, false),
            'priceExclTax'        => Mage::helper('core')->currency($_priceExclTax, false, false),
            /**
             * @var skipCalculate
             * @deprecated after 1.5.1.0
             */
            'skipCalculate'       => ($_priceExclTax != $_priceInclTax ? 0 : 1),
            'defaultTax'          => $defaultTax,
            'currentTax'          => $currentTax,
            'idSuffix'            => '_clone',
            'oldPlusDisposition'  => 0,
            'plusDisposition'     => 0,
            'oldMinusDisposition' => 0,
            'minusDisposition'    => 0,
        );

        $responseObject = new Varien_Object();
        Mage::dispatchEvent('catalog_product_view_config', array('response_object'=>$responseObject));
        if (is_array($responseObject->getAdditionalOptions())) {
            foreach ($responseObject->getAdditionalOptions() as $option=>$value) {
                $config[$option] = $value;
            }
        }

        return Mage::helper('core')->jsonEncode($config);
    }
		

}