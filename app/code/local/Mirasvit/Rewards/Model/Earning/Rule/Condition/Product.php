<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/extension_rewards
 * @version   1.1.42
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



/**
 * @method Mirasvit_Helpdesk_Model_Rule_Condition_Product setAttributeOption(string $param)
 * @method string getAttribute()
 * @method $this setAttribute(string $param)
 */
class Mirasvit_Rewards_Model_Earning_Rule_Condition_Product extends Mage_Rule_Model_Condition_Abstract
{
    /**
     * @var array $_entityAttributeValues
     */
    protected $_entityAttributeValues = null;

    /**
     * @return Varien_Object
     */
    public function getAttributeObject()
    {
        try {
            /* @noinspection PhpUndefinedMethodInspection */
            $obj = Mage::getSingleton('eav/config')
                ->getAttribute('catalog_product', $this->getAttribute());
        } catch (Exception $e) {
            $obj = new Varien_Object();
            /* @noinspection PhpUndefinedMethodInspection */
            $obj->setEntity(Mage::getResourceSingleton('catalog/product'))
                ->setFrontendInput('text');
        }

        return $obj;
    }

    /**
     * @param array &$attributes
     * @return void
     */
    protected function _addSpecialAttributes(array &$attributes)
    {
        $attributes = array_merge($attributes, array(
            'attribute_set_id' => Mage::helper('rewards')->__('Attribute Set'),
            'sku' => Mage::helper('rewards')->__('SKU'),
            'category_ids' => Mage::helper('rewards')->__('Category'),
            'qty' => Mage::helper('rewards')->__('Quantity'),
            'type_id' => Mage::helper('rewards')->__('Product Type'),
            'price' => Mage::helper('rewards')->__('Base Price'),
            'final_price' => Mage::helper('rewards')->__('Final Price'),
            'special_price' => Mage::helper('rewards')->__('Special Price'),
            'image' => Mage::helper('rewards')->__('Base Image'),
            'thumbnail' => Mage::helper('rewards')->__('Thumbnail'),
            'small_image' => Mage::helper('rewards')->__('Small Image'),
            'image_size' => Mage::helper('rewards')->__('Base Image Size (bytes)'),
            'thumbnail_size' => Mage::helper('rewards')->__('Thumbnail Size (bytes)'),
            'small_image_size' => Mage::helper('rewards')->__('Small Image Size (bytes)'),
            'php' => Mage::helper('rewards')->__('PHP Condition'),
        ));
    }

    /**
     * @return Mirasvit_Rewards_Model_Earning_Rule_Condition_Product
     */
    public function loadAttributeOptions()
    {
        $productAttributes = Mage::getResourceSingleton('catalog/product')
            ->loadAllAttributes()
            ->getAttributesByCode();

        $attributes = array();
        /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
        foreach ($productAttributes as $attribute) {
            /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            if (!$attribute->isAllowedForRuleCondition()
                || !$attribute->getDataUsingMethod($this->_isUsedForRuleProperty)
            ) {
                continue;
            }
            $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }

        $this->_addSpecialAttributes($attributes);

        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * @return Mirasvit_Rewards_Model_Earning_Rule_Condition_Product
     */
    protected function _prepareValueOptions()
    {
        // Check that both keys exist. Maybe somehow only one was set not in this routine, but externally.
        $selectReady = $this->getData('value_select_options');
        $hashedReady = $this->getData('value_option');
        if ($selectReady && $hashedReady) {
            return $this;
        }

        // Get array of select options. It will be used as source for hashed options
        $selectOptions = null;
        if ($this->getAttribute() === 'attribute_set_id') {
            $entityTypeId = Mage::getSingleton('eav/config')
                ->getEntityType('catalog_product')->getId();
            $selectOptions = Mage::getResourceModel('eav/entity_attribute_set_collection')
                ->setEntityTypeFilter($entityTypeId)
                ->load()
                ->toOptionArray();
        } elseif ($this->getAttribute() === 'type_id') {
            $typeOptions = Mage::getSingleton('catalog/product_type')->getOptionArray();
            $selectOptions = array();
            foreach ($typeOptions as $option) {
                $selectOptions[] = array('label' => $option, 'value' => $option);
            }
        } elseif (is_object($this->getAttributeObject())) {
            $attributeObject = $this->getAttributeObject();
            if ($attributeObject->usesSource()) {
                /* @noinspection PhpUndefinedMethodInspection */
                if ($attributeObject->getFrontendInput() == 'multiselect') {
                    $addEmptyOption = false;
                } else {
                    $addEmptyOption = true;
                }
                $selectOptions = $attributeObject->getSource()->getAllOptions($addEmptyOption);
            }
        }

        // Set new values only if we really got them
        if ($selectOptions !== null) {
            // Overwrite only not already existing values
            if (!$selectReady) {
                $this->setData('value_select_options', $selectOptions);
            }
            if (!$hashedReady) {
                $hashedOptions = array();
                foreach ($selectOptions as $o) {
                    if (is_array($o['value'])) {
                        continue; // We cannot use array as index
                    }
                    $hashedOptions[$o['value']] = $o['label'];
                }
                $this->setData('value_option', $hashedOptions);
            }
        }

        return $this;
    }

    /**
     * Retrieve value by option.
     *
     * @param string $option
     * @return string
     */
    public function getValueOption($option = null)
    {
        $this->_prepareValueOptions();

        return $this->getData('value_option'.(($option !== null) ? '/'.$option : ''));
    }

    /**
     * Retrieve select option values.
     *
     * @return array
     */
    public function getValueSelectOptions()
    {
        $this->_prepareValueOptions();

        return $this->getData('value_select_options');
    }

    /**
     * Retrieve after element HTML.
     *
     * @return string
     */
    public function getValueAfterElementHtml()
    {
        $html = '';

        switch ($this->getAttribute()) {
            case 'sku': case 'category_ids':
                    $image = Mage::getDesign()->getSkinUrl('images/rule_chooser_trigger.gif');
                break;
        }

        if (!empty($image)) {
            $html = '<a href="javascript:void(0)" class="rule-chooser-trigger">
                <img src="'.$image.'" alt="" class="v-middle rule-chooser-trigger" title="'
                .Mage::helper('rule')->__('Open Chooser').'" /></a>';
        }

        return $html;
    }

    /**
     * @return Varien_Object
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        /* @noinspection PhpUndefinedMethodInspection */
        $element->setShowAsText(true);

        return $element;
    }

    /**
     * @param array $productCollection
     * @return Mirasvit_Rewards_Model_Earning_Rule_Condition_Product
     */
    public function collectValidatedAttributes($productCollection)
    {
        $attribute = $this->getAttribute();

        if (!in_array($attribute, array('category_ids', 'qty', 'php'))) {
            if ($attribute == 'image_size'
                || $attribute == 'small_image_size'
                || $attribute == 'thumbnail_size') {
                $attribute = str_replace('_size', '', $attribute);
            }

            $attributes = $this->getRule()->getCollectedAttributes();
            $attributes[$attribute] = true;
            $this->getRule()->setCollectedAttributes($attributes);
            $productCollection->addAttributeToSelect($attribute, 'left');
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getInputType()
    {
        if ($this->getAttribute() === 'attribute_set_id'
            || $this->getAttribute() === 'type_id') {
            return 'select';
        }
        if (!is_object($this->getAttributeObject())) {
            return 'string';
        }
        switch ($this->getAttributeObject()->getFrontendInput()) {
            case 'select':
                return 'select';

            case 'multiselect':
                return 'multiselect';

            case 'date':
                return 'date';

            case 'boolean':
                return 'boolean';

            default:
                return 'string';
        }
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {
        if ($this->getAttribute() === 'attribute_set_id'
            || $this->getAttribute() === 'type_id') {
            return 'select';
        }
        if (!is_object($this->getAttributeObject())) {
            return 'text';
        }
        switch ($this->getAttributeObject()->getFrontendInput()) {
            case 'select':
            case 'boolean':
                return 'select';

            case 'multiselect':
                return 'multiselect';

            case 'date':
                return 'date';

            default:
                return 'text';
        }
    }

    /**
     * @return Varien_Object
     */
    public function getValueElement()
    {
        $element = parent::getValueElement();
        if (is_object($this->getAttributeObject())) {
            switch ($this->getAttributeObject()->getFrontendInput()) {
                case 'date':
                    $element->setImage(Mage::getDesign()->getSkinUrl('images/grid-cal.gif'));
                    break;
            }
        }

        return $element;
    }

    /**
     * @return string
     */
    public function getValueElementChooserUrl()
    {
        $url = false;
        switch ($this->getAttribute()) {
            case 'sku':
            case 'category_ids':
                $url = 'adminhtml/promo_widget/chooser'
                    .'/attribute/'.$this->getAttribute();
                if ($this->getJsFormObject()) {
                    $url .= '/form/'.$this->getJsFormObject();
                }
                break;
        }

        return $url !== false ? Mage::helper('adminhtml')->getUrl($url) : '';
    }

    /**
     * @return bool
     */
    public function getExplicitApply()
    {
        switch ($this->getAttribute()) {
            case 'sku': case 'category_ids': case 'php':
                return true;
        }
        if (is_object($this->getAttributeObject())) {
            switch ($this->getAttributeObject()->getFrontendInput()) {
                case 'date':
                    return true;
            }
        }

        return false;
    }

    /**
     * @param array $arr
     * @return Mirasvit_Rewards_Model_Earning_Rule_Condition_Product
     */
    public function loadArray($arr)
    {
        $this->setAttribute(isset($arr['attribute']) ? $arr['attribute'] : false);
        $attribute = $this->getAttributeObject();

        if ($attribute && $attribute->getBackendType() == 'decimal') {
            if (isset($arr['value'])) {
                if (!empty($arr['operator'])
                    && in_array($arr['operator'], array('!()', '()'))
                    && false !== strpos($arr['value'], ',')) {
                    $tmp = array();
                    foreach (explode(',', $arr['value']) as $value) {
                        $tmp[] = Mage::app()->getLocale()->getNumber($value);
                    }
                    $arr['value'] = implode(',', $tmp);
                } else {
                    $arr['value'] = Mage::app()->getLocale()->getNumber($arr['value']);
                }
            } else {
                $arr['value'] = false;
            }
            $arr['is_value_parsed'] = isset($arr['is_value_parsed'])
                ? Mage::app()->getLocale()->getNumber($arr['is_value_parsed']) : false;
        }

        return parent::loadArray($arr);
    }

    /**
     * Validate product attrbute value for condition.
     *
     * @param Varien_Object $object
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        $attrCode = $this->getAttribute();

        switch ($attrCode) {
            case 'category_ids':
                if (!is_array($value = $this->getValueParsed())) {
                    $value = explode(', ', $this->getValueParsed());
                }
                $value = count(array_intersect($value, $object->getCategoryIds()));

                if ($this->getOperator() == '==') {
                    return $value > 0;
                } else if ($this->getOperator() == '!=') {
                    return $value == 0;
                } else {
                    return $this->validateAttribute($object->getCategoryIds());
                }
                break;

            case 'sku':
                return $this->validateAttribute($object->getSku());
                break;

            case 'qty':
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($object->getId());

                return $this->validateAttribute($stockItem->getQty());
                break;

            case 'price':
                return $this->validateAttribute($object->getPrice());
                break;

            case 'final_price':
                return $this->validateAttribute($object->getFinalPrice());
                break;

            case 'special_price':
                if (!$object->getSpecialPrice()) {
                    // Product does not have any special price, so it shall count as 0
                    return $this->validateAttribute(0);
                } else {
                    return $this->validateAttribute($object->getSpecialPrice());
                }
                break;

            case 'image_size':
            case 'small_image_size':
            case 'thumbnail_size':
                $imageCode = str_replace('_size', '', $attrCode);

                $imagePath = $object->getData($imageCode);
                $path = Mage::getBaseDir('media').DS.'catalog/product'.$imagePath;

                $size = 0;
                if (file_exists($path) && is_file($path)) {
                    $size = filesize($path);
                }

                return $this->validateAttribute($size);
                break;

            case 'php':
                $object = $object->load($object->getId());
                extract($object->getData());
                $expr = 'return '.$this->getValue().';';
                $value = eval($expr);

                if ($this->getOperator() == '==') {
                    return $value;
                } else {
                    return !$value;
                }

                break;

            default:
                if (!isset($this->_entityAttributeValues[$object->getId()])) {
                    $attr = $object->getResource()->getAttribute($attrCode);

                    if ($attr && $attr->getBackendType() == 'datetime' && !is_int($this->getValue())) {
                        $this->setValue(strtotime($this->getValue()));
                        $value = strtotime($object->getData($attrCode));

                        return $this->validateAttribute($value);
                    }

                    if ($attr && $attr->getFrontendInput() == 'multiselect') {
                        $value = $object->getData($attrCode);
                        $value = strlen($value) ? explode(',', $value) : array();

                        return $this->validateAttribute($value);
                    }

                    return parent::validate($object);
                } else {
                    $result = false; // any valid value will set it to TRUE
                    $oldAttrValue = $object->hasData($attrCode) ? $object->getData($attrCode) : null;
                    foreach ($this->_entityAttributeValues[$object->getId()] as $storeId => $value) {
                        $attr = $object->getResource()->getAttribute($attrCode);
                        if ($attr && $attr->getBackendType() == 'datetime') {
                            $value = strtotime($value);
                        } elseif ($attr && $attr->getFrontendInput() == 'multiselect') {
                            $value = strlen($value) ? explode(',', $value) : array();
                        }

                        $object->setData($attrCode, $value);
                        $result |= parent::validate($object);

                        if ($result) {
                            break;
                        }
                    }

                    if ($oldAttrValue === null) {
                        $object->unsetData($attrCode);
                    } else {
                        $object->setData($attrCode, $oldAttrValue);
                    }

                    return (bool) $result;
                }
                break;
        }
    }

    /**
     * @return string
     */
    public function getJsFormObject()
    {
        return 'rule_conditions_fieldset';
    }
}
