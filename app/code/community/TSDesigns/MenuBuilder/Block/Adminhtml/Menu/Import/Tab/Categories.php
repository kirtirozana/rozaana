<?php
/**
 * TSDesigns_MenuBuilder_Block_Adminhtml_Menu_Import_Tab_Categories
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Licence that is bundled with 
 * this package in the file LICENSE.txt. It is also available through 
 * the world-wide-web at this URL: http://www.tsdesigns.de/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@tsdesigns.de so we can send you a copy immediately.
 *
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * versions in the future. If you wish to customize Magento or this extension for your
 * needs please refer to http://www.magentocommerce.com or http://www.tsdesigns.de
 * for more information.
 * 
 *
 * @category TSDesigns
 * @package TSDesigns_MenuBuilder
 * @author Tobias Schifftner, TSDesigns
 * @license http://www.tsdesigns.de/license
 * @copyright This software is protected by copyright, (c) 2011 TSDesigns.
 * @version 1.6.0 - 2011-10-21 10:31:26
 *
 */
class TSDesigns_MenuBuilder_Block_Adminhtml_Menu_Import_Tab_Categories
 extends Mage_Adminhtml_Block_Catalog_Category_Tree
{
    protected $_categoryIds;
    protected $_selectedNodes = null;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('tsdesigns/menubuilder/menu/import/categories.phtml');
    }

    /**
     * Retrieve currently edited product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Checks when this block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return false;
    }

    protected function getCategoryIds()
    {
        return array();
    }

    public function getIdsString()
    {
        return implode(',', $this->getCategoryIds());
    }

    public function getRootNode()
    {
//        $root = parent::getRoot();
        $root = $this->getRoot();
        if ($root && in_array($root->getId(), $this->getCategoryIds())) {
            $root->setChecked(true);
        }
        return $root;
    }

    public function getRoot($parentNodeCategory=null, $recursionLevel=3)
    {
        if (!is_null($parentNodeCategory) && $parentNodeCategory->getId()) {
            return $this->getNode($parentNodeCategory, $recursionLevel);
        }
        $root = Mage::registry('root');
        if (is_null($root)) {
            $storeId = (int) $this->getRequest()->getParam('store');

            if ($storeId) {
                $store = Mage::app()->getStore($storeId);
                $rootId = $store->getRootCategoryId();
            }
            else {
                $rootId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
            }

            $ids = $this->getSelectedCategoriesPathIds($rootId);
            $tree = Mage::getResourceSingleton('catalog/category_tree')
                ->loadByIds($ids, false, false);

            if ($this->getCategory()) {
                $tree->loadEnsuredNodes($this->getCategory(), $tree->getNodeById($rootId));
            }

            $tree->addCollectionData($this->getCategoryCollection());

            $root = $tree->getNodeById($rootId);

            if ($root && $rootId != Mage_Catalog_Model_Category::TREE_ROOT_ID) {
                $root->setIsVisible(true);
                if ($this->isReadonly()) {
                    $root->setDisabled(true);
                }
            }
            elseif($root && $root->getId() == Mage_Catalog_Model_Category::TREE_ROOT_ID) {
                $root->setName(Mage::helper('catalog')->__('Root'));
            }

            Mage::register('root', $root);
        }

        return $root;
    }

    protected function _getNodeJson($node, $level=1)
    {
        $item = parent::_getNodeJson($node, $level);

        $isParent = $this->_isParentSelectedCategory($node);

        if ($isParent) {
            $item['expanded'] = true;
        }

//        if ($node->getLevel() > 1 && !$isParent && isset($item['children'])) {
//            $item['children'] = array();
//        }


        if (in_array($node->getId(), $this->getCategoryIds())) {
            $item['checked'] = true;
        }

        if ($this->isReadonly()) {
            $item['disabled'] = true;
        }
        return $item;
    }

    protected function _isParentSelectedCategory($node)
    {
        foreach ($this->_getSelectedNodes() as $selected) {
            if ($selected) {
                $pathIds = explode('/', $selected->getPathId());
                if (in_array($node->getId(), $pathIds)) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function _getSelectedNodes()
    {
        if ($this->_selectedNodes === null) {
            $this->_selectedNodes = array();
            $root = $this->getRoot();
            foreach ($this->getCategoryIds() as $categoryId) {
                if ($root) {
                    $this->_selectedNodes[] = $root->getTree()->getNodeById($categoryId);
                }
            }
        }

        return $this->_selectedNodes;
    }

    public function getCategoryChildrenJson($categoryId)
    {
        $category = Mage::getModel('catalog/category')->load($categoryId);
        $node = $this->getRoot($category, 1)->getTree()->getNodeById($categoryId);

        if (!$node || !$node->hasChildren()) {
            return '[]';
        }

        $children = array();
        foreach ($node->getChildren() as $child) {
            $children[] = $this->_getNodeJson($child);
        }

        return Mage::helper('core')->jsonEncode($children);
    }

    public function getLoadTreeUrl($expanded=null)
    {
        return $this->getUrl('*/*/categoriesJson', array('_current'=>true));
    }

    /**
     * Return distinct path ids of selected categories
     *
     * @param int $rootId Root category Id for context
     * @return array
     */
    public function getSelectedCategoriesPathIds($rootId = false)
    {
        $ids = array();
        $collection = Mage::getModel('catalog/category')->getCollection()
            ->addFieldToFilter('entity_id', array('in'=>$this->getCategoryIds()));
        foreach ($collection as $item) {
            if ($rootId && !in_array($rootId, $item->getPathIds())) {
                continue;
            }
            foreach ($item->getPathIds() as $id) {
                if (!in_array($id, $ids)) {
                    $ids[] = $id;
                }
            }
        }
        return $ids;
    }
}