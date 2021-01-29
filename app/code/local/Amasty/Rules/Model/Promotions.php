<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */
class Amasty_Rules_Model_Promotions
{

    /**
     * @var array
     */
    protected $_discount = array();

    /**
     * @var null
     */
    public $itemsWithDiscount = null;

    /**
     * @var array
     */
    protected $_ruleDiscount = array();

    /**
     * @var bool
     */
    public $isSpecialPromotions = false;

    /**
     * @param \Varien_Event_Observer $observer
     *
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function process($observer)
    {
        /** @var \Mage_SalesRule_Model_Rule $rule */
        $rule = $observer->getEvent()->getRule();
        /** @var \Mage_Sales_Model_Quote_Item $item */
        $item = $observer->getEvent()->getItem();

        if (!$item->getId()) {
            return false;
        }

        Mage::helper('amrules')->addPassedItem($item->getId());

        /** @var \Mage_Sales_Model_Quote_Address $address */
        $address = $observer->getEvent()->getAddress();
        /** @var \Mage_Sales_Model_Quote $quote */
        $quote = $observer->getEvent()->getQuote();
        $itemId = $item->getId();
        /** @var \Varien_Object $result */
        $result = $observer->getEvent()->getResult();

        $amountToDisplay = 0;
        $types = Mage::helper('amrules')->getDiscountTypes(true);
        $isset = isset($types[$rule->getSimpleAction()]);
        $this->isSpecialPromotions = false;

        if ($isset) {
            $ruleDiscount = $this->discountForSpecialPromotion($rule, $quote, $address, $item, $amountToDisplay);
        } elseif (in_array(
                $rule->getSimpleAction(),
                array('ampromo_items', 'ampromo_cart', 'ampromo_spent', 'ampromo_product'))
            && $item->getIsPromo()) {
            $ampromoDiscountValue = $rule->getAmpromoDiscountValue();
            if (strpos($ampromoDiscountValue, '%') !== false) {
                $amountToDisplay = $item->getPrice() * (int)$ampromoDiscountValue / 100;
            } elseif ($ampromoDiscountValue < 0) {
                $amountToDisplay = abs($ampromoDiscountValue);
            } else {
                $amountToDisplay = $item->getPrice() - $ampromoDiscountValue;
            }
        } else { //it's default rule
            $amountToDisplay = $observer->getEvent()->getResult()->getDiscountAmount();
        }

        if ($this->skip($rule, $item, $address) && $amountToDisplay > 0.0001) {
            $this->unsetDiscount($result, $item);

            return false;
        }

        if ($this->isSpecialPromotions) {
            $this->setDiscount(
                $result, $item, $ruleDiscount[$itemId]['discount'],
                $ruleDiscount[$itemId]['base_discount'], $ruleDiscount[$itemId]['percent']
            );
        }

        $currentAmount = $item->getRowTotal() - $item->getDiscountAmount() - $amountToDisplay;

        if ($currentAmount < 0) {
            $amountToDisplay += $currentAmount;
        }

        if ($amountToDisplay >= 0.0001) {
            $this->_addFullDescription($address, $rule, $item, $amountToDisplay);
        }

        return true;
    }

    /**
     * @param \Mage_SalesRule_Model_Rule $rule
     * @param \Mage_Sales_Model_Quote $quote
     * @param \Mage_Sales_Model_Quote_Address $address
     * @param \Mage_Sales_Model_Quote_Item $item
     * @param float $amountToDisplay
     *
     * @return array
     */
    protected function discountForSpecialPromotion($rule, $quote, $address, $item, &$amountToDisplay)
    {
        $itemId = $item->getId();

        if (!isset($this->_discount[$rule->getId()])) {
            $action = $rule->getSimpleAction();
            if (in_array($action, Mage::helper('amrules')->getNoneMaxDiscountRules())) {
                $rule->setMaxDiscount(0);
            }
            $className = str_replace('_', '', $rule->getSimpleAction());
            $ruleProcessor = Mage::getSingleton(
                'amrules_discount/' . $className
            );

            $ruleProcessor->setPriceSelector($rule->getPriceSelector());

            $discount = $ruleProcessor->calculateDiscount(
                $rule, $address, $quote
            );

            if (in_array($action, Mage::helper('amrules')->getFixedRules())) {
                foreach ($discount as &$itemDiscount) {
                    if ($itemDiscount['discount'] < 0) {
                        $itemDiscount['discount'] = 0;
                    }
                    if ($itemDiscount['base_discount'] < 0) {
                        $itemDiscount['base_discount'] = 0;
                    }
                }
            }

            $discount = $ruleProcessor->prepareDiscount($discount, $address, $rule);
            $this->_discount[$rule->getId()] = $discount;
        }

        $ruleDiscount = $this->_discount[$rule->getId()];

        if (!empty($ruleDiscount[$itemId])) {

            $this->isSpecialPromotions = true;
            isset($ruleDiscount[$item->getId()]['percent'])
                ? $ruleDiscount[$item->getId()]['percent']
                : $ruleDiscount[$item->getId()]['percent'] = 0;
            $ruleDiscount[$itemId] = $this->_limitMaxDiscount($ruleDiscount, $rule, $itemId, $quote);
            $amountToDisplay = $ruleDiscount[$itemId]['discount'];
        }

        return $ruleDiscount;
    }

    /**
     * check all items in quote for promo items
     *
     * @param $rule
     * @param $observer
     *
     * @return bool
     *
     */
    protected function _checkForPack($rule, $observer)
    {
        if ($rule->getPromoSku()) {
            $items = $observer->getEvent()->getQuote()->getAllItems();
            $arrayWithPromoSkus = explode(',', $rule->getPromoSku());
            $arrayWithPromoSkus = $this->_sortAndUniqueArray($arrayWithPromoSkus);
            $arrayWithQuoteSkus = array();
            foreach ($items as $item) {
                $arrayWithQuoteSkus[] = $item->getSku();
            }
            $arrayWithQuoteSkus = $this->_sortAndUniqueArray($arrayWithQuoteSkus);
            $intersection = array_intersect($arrayWithPromoSkus, $arrayWithQuoteSkus);
            if ($arrayWithPromoSkus && $intersection !== $arrayWithPromoSkus) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $array
     *
     * @return array
     */
    protected function _sortAndUniqueArray($array)
    {
        sort($array);
        array_unique($array);

        return $array;
    }

    /**
     * @param \Varien_Object $result
     * @param \Mage_Sales_Model_Quote_Item $item
     * @param float $discount
     * @param float $baseDiscount
     * @param float $percent
     */
    protected function setDiscount($result, $item, $discount, $baseDiscount, $percent)
    {
        $result->setDiscountAmount($discount);
        $result->setBaseDiscountAmount($baseDiscount);
        if ($percent > 0) {
            $item->setDiscountPercent($percent);
        }
        $item->setIsSpecialPromotion(true);
    }

    /**
     * @param \Varien_Object $result
     * @param \Mage_Sales_Model_Quote_Item $item
     */
    protected function unsetDiscount($result, $item)
    {
        $result->setDiscountAmount(0);
        $result->setBaseDiscountAmount(0);
        $item->setDiscountPercent(0);
        $item->setIsSpecialPromotion(false);
    }

    /**
     * determines if we should skip the items with special price or other (in futeure) conditions
     *
     * @param \Mage_SalesRule_Model_Rule $rule
     * @param \Mage_Sales_Model_Quote_Item $item
     * @param \Mage_Sales_Model_Quote_Address $address
     *
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function skip($rule, $item, $address)
    {
        if ($rule->getSimpleAction() == 'cart_fixed') {
            return false;
        }

        $storeId = $item->getQuote()->getStoreId();
        $websiteId = Mage::getModel('core/store')->load($storeId)->getWebsiteId();
        $groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();

        $origProduct = $item->getProduct();
        $tierPrices = $origProduct->getTierPrice();

        if (Mage::getStoreConfig('amrules/skip/skip_tier_price')) {
            foreach ($tierPrices as $tierPrice) {
                if (($tierPrice['cust_group'] == $groupId
                        || Mage_Customer_Model_Group::CUST_GROUP_ALL == $tierPrice['cust_group'])
                    && $item->getQty() >= $tierPrice['price_qty']
                    && $websiteId == $tierPrice['website_id']
                ) {
                    return true;
                }
            }
        }

        if ($item->getProductType() == 'bundle') {
            return false;
        }

        if (is_null($this->itemsWithDiscount)) {
            $productIds = array();
            $this->itemsWithDiscount = array();

            foreach (Mage::getSingleton('amrules_discount/abstract')->getAllItems($address) as $addressItem) {
                $productIds[] = $addressItem->getProductId();
            }

            if (!$productIds) {
                return false;
            }

            if ($websiteId === '0') {
                foreach ($productIds as $productId) {
                    $product = Mage::getModel('catalog/product')->load($productId);
                    if ($product->getPrice() > $product->getFinalPrice()) {
                        $this->itemsWithDiscount[] = $product->getId();
                    }
                }
            } else {
                $productsCollection = Mage::getModel('catalog/product')
                    ->getCollection()
                    ->addPriceData()
                    ->addAttributeToFilter('entity_id', array('in' => $productIds))
                    ->addAttributeToFilter(
                        'price', array('gt' => new Zend_Db_Expr('final_price'))
                    )
                    ->addAttributeToFilter(
                        'special_from_date',
                        array(
                            'or' => array(
                                0 => array('date' => true, 'to' => now()),
                                1 => array('is' => new Zend_Db_Expr('null'))
                            )
                        )
                    )
                    ->addAttributeToFilter(
                        'special_to_date',
                        array(
                            'or' => array(
                                0 => array('date' => true, 'from' => now()),
                                1 => array('is' => new Zend_Db_Expr('null'))
                            )
                        ),
                        'left'
                    );

                foreach ($productsCollection as $product) {
                    $this->itemsWithDiscount[] = $product->getId();
                }
            }
        }

        if (Mage::getStoreConfig('amrules/skip/skip_special_price_configurable')) {
            if ($item->getProductType() == "configurable") {
                foreach ($item->getChildren() as $child) {
                    if (in_array($child->getProductId(), $this->itemsWithDiscount)) {
                        return true;
                    }
                }
            }
        }
        switch ($rule->getData('amskip_rule')) {
            case 0:
                if (Mage::getStoreConfig('amrules/skip/skip_special_price')) {
                    if (in_array($item->getProductId(), $this->itemsWithDiscount)) {
                        return true;
                    }
                }
                break;
            case 1:
                if (in_array($item->getProductId(), $this->itemsWithDiscount)) {
                    return true;
                }
                break;
            case 3:
                $price = $item->getDiscountCalculationPrice();
                ($price !== null)
                    ? $price = $item->getBaseDiscountCalculationPrice()
                    : $price = $item->getBaseCalculationPrice();
                $price -= $item->getBaseDiscountAmount();

                if ($price && $item->getProduct()->getPrice() > $price) {
                    return true;
                }
                break;
        }

        return false;
    }

    /**
     * Adds a detailed description of the discount
     *
     * @param \Mage_Sales_Model_Quote_Address $address
     * @param \Mage_SalesRule_Model_Rule $rule
     * @param \Mage_Sales_Model_Quote_Item $item
     * @param float $discount
     *
     * @return $this
     */
    protected function _addFullDescription($address, $rule, $item, $discount)
    {
        // we need this to fix double prices with one step checkouts
        $ind = $rule->getId() . '-' . $item->getId();

        if (isset($this->descrPerItem[$ind])) {
            return $this;
        }
        $this->descrPerItem[$ind] = true;

        $descr = $address->getFullDescr();
        $debugInfo = $address->getDebugInfo();

        if (!is_array($descr)) {
            $descr = array();
        }

        if (!is_array($debugInfo)) {
            $debugInfo = array();
        }

        if (empty($descr[$rule->getId()])) {

            $ruleLabel = $rule->getStoreLabel($address->getQuote()->getStore());
            if (!$ruleLabel) {
                if (Mage::helper('ambase')->isModuleActive('Amasty_Coupon')) {
                    if (!$ruleLabel) {
                        $ruleLabel = $rule->getCouponCode(); // possible wrong code, known issue
                    }
                } else { // most frequent case
                    // take into account "generate and import amasty extension"
                    //	UseAutoGeneration
                    if ($rule->getUseAutoGeneration() || $rule->getCouponCode()) {
                        $ruleLabel = $rule->getCouponCode();
                    }
                }
            }

            if (!$ruleLabel) {
                $ruleLabel = $rule->getName();
            }

            $descr[$rule->getId()] =
                array('label' => '<strong>' . htmlspecialchars($ruleLabel) . '</strong>', 'amount' => 0);
            $debugInfo[$rule->getId()] = $descr[$rule->getId()];
        }
        // skip the rule as it adds discount to each item
        // version before 1.4.1 has no class constants for actions
        $skipTypes = array('cart_fixed', Amasty_Rules_Helper_Data::TYPE_AMOUNT);

        if (!in_array($rule->getSimpleAction(), $skipTypes)
            && Mage::getStoreConfig('amrules/breakdown_settings/breakdown_products')
        ) {
            $sep = ($descr[$rule->getId()]['amount'] > 0) ? ', <br/> ' : ': ';
            $descr[$rule->getId()]['label'] = $descr[$rule->getId()]['label']
                . $sep . htmlspecialchars($item->getName());
        }

        if (Mage::helper('amrules/debug')->isDebugDisplayAllowed()) {
            if (!in_array($rule->getSimpleAction(), $skipTypes)) {
                $sep = ($debugInfo[$rule->getId()]['amount'] > 0) ? ', <br/> ' : ': ';
                $debugInfo[$rule->getId()]['label'] = $debugInfo[$rule->getId()]['label']
                    . $sep . htmlspecialchars($item->getName()) . '---' . $discount;
            }
            $debugInfo[$rule->getId()]['amount'] += $discount;
        }

        $descr[$rule->getId()]['amount'] += $discount;
        $address->setFullDescr($descr);
        $address->setDebugInfo($debugInfo);
    }

    /**
     * @param array $ruleDiscount
     * @param \Mage_SalesRule_Model_Rule $rule
     * @param int $itemId
     * @param \Mage_Sales_Model_Quote $quote
     *
     * @return array
     */
    protected function _limitMaxDiscount($ruleDiscount, $rule, $itemId, $quote)
    {
        $maxDiscount = $rule->getMaxDiscount();
        if ($maxDiscount == 0) {
            return $ruleDiscount[$itemId];
        }
        $ruleId = $rule->getId();

        if (isset($this->_ruleDiscount[$ruleId])) {
            $this->_ruleDiscount[$ruleId] += $ruleDiscount[$itemId]['base_discount'];
        } else {
            $this->_ruleDiscount[$ruleId] = $ruleDiscount[$itemId]['base_discount'];
        }

        if ($this->_ruleDiscount[$ruleId] > $maxDiscount) {
            $canAdd = $maxDiscount - ($this->_ruleDiscount[$ruleId] - $ruleDiscount[$itemId]['base_discount']);
            if ($canAdd > 0) {
                $ruleDiscount[$itemId]['base_discount'] = $canAdd;
                $ruleDiscount[$itemId]['discount'] = max(0, $quote->getStore()->convertPrice($ruleDiscount[$itemId]['base_discount']));
            } else {
                $ruleDiscount[$itemId]['base_discount'] = 0;
                $ruleDiscount[$itemId]['discount'] = max(0, $quote->getStore()->convertPrice($ruleDiscount[$itemId]['base_discount']));
            }
        }

        return $ruleDiscount[$itemId];
    }
}
