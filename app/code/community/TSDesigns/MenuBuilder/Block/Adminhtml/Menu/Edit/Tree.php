<?php
/**
 * TSDesigns_MenuBuilder_Block_Adminhtml_Menu_Edit_Tree
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
class TSDesigns_MenuBuilder_Block_Adminhtml_Menu_Edit_Tree 
	extends TSDesigns_MenuBuilder_Block_Adminhtml_Menu_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('menubuilder/menu/edit/tree.phtml');
        $this->setUseAjax(true);
    }

    protected function _prepareLayout()
    { 
        $addUrl = $this->getUrl("*/*/add", array(
            '_current'=>true,
            'id'=>null,
            '_query' => false
        ));

        if ($this->canAddNode()) {
            $this->setChild('add_node_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'     => Mage::helper('menubuilder')->__('Node'),
                        'onclick'   => "addNew('".$addUrl."', false)",
                        'class'     => 'add'
                    ))
            );
        }

        if ($this->canAddRootNode()) {
            $this->setChild('add_root_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'     => Mage::helper('menubuilder')->__('Menu'),
                        'onclick'   => "addNew('".$addUrl."', true)",
                        'class'     => 'add',
                        'id'        => 'add_root_node_button'
                    ))
            );
        }
        
        $this->setChild('store_switcher',
            $this->getLayout()->createBlock('adminhtml/store_switcher')
                ->setSwitchUrl($this->getUrl('*/*/*', array('_current'=>true, '_query'=>false, 'store'=>null)))
                ->setTemplate('store/switcher/enhanced.phtml')
        );
        
        return parent::_prepareLayout();
    }

    protected function _getDefaultStoreId()
    {
        return TSDesigns_MenuBuilder_Model_Abstract::DEFAULT_STORE_ID;
    }

    public function getMenuCollection()
    {        
        $collection = $this->getData('node_collection');
        
        if (is_null($collection)) {
        	$storeId = $this->getRequest()->getParam('store', $this->_getDefaultStoreId());
        	
        	$collection = Mage::getModel('menubuilder/menu')->getCollection();
        	$collection->addAttributeToSelect('name')
        		->addAttributeToSelect('is_active')
        		->addAttributeToSelect('link_to')
        		->setStoreId($storeId);
            $this->setData('node_collection', $collection);
        }
        
        return $collection;
    }

    public function getAddRootNodeButtonHtml()
    {
        return $this->getChildHtml('add_root_button');
    }
    
    public function getAddNodeButtonHtml()
    {
        return $this->getChildHtml('add_node_button');
    }

    public function getExpandButtonHtml()
    {
        return $this->getChildHtml('expand_button');
    }

    public function getCollapseButtonHtml()
    {
        return $this->getChildHtml('collapse_button');
    }

    public function getStoreSwitcherHtml()
    {
        return $this->getChildHtml('store_switcher');
    }

    public function getLoadTreeUrl($expanded=null)
    {
        $params = array('_current'=>true, 'id'=>null,'store'=>null);
        if (
            (is_null($expanded) && Mage::getSingleton('admin/session')->getIsTreeWasExpanded())
            || $expanded == true) {
            $params['expand_all'] = true;
        }
        return $this->getUrl('*/*/nodesJson', $params);
    }

    public function getNodesUrl()
    {
        return $this->getUrl('*/menu/jsonTree');
    }

    public function getSwitchTreeUrl()
    {
        return $this->getUrl("*/menu/tree", array('_current'=>true, 'store'=>null, '_query'=>false, 'id'=>null, 'parent'=>null));
    }

    public function getIsWasExpanded()
    {
        return Mage::getSingleton('admin/session')->getIsTreeWasExpanded();
    }

    public function getMoveUrl()
    {
        return $this->getUrl('*/menu/move', array('store'=>$this->getRequest()->getParam('store')));
    }

    public function getTree($parentNodeCategory=null)
    {
       	$rootArray = array($this->_getNodeJson($this->getRoot($parentNodeCategory)));   
       	//Mage::helper('debug')->log($rootArray);    	
        $tree = isset($rootArray[0]['children']) ? $rootArray[0]['children'] : array();
        //Mage::helper('debug')->log($tree);
        return $tree;
        //return $rootArray;
    }

    public function getTreeJson($parentNodeCategory=null)
    {
        return Zend_Json::encode($this->getTree($parentNodeCategory));
    }

    /**
     * Get JSON of array of categories, that are breadcrumbs for specified category path
     *
     * @param string $path
     * @param string $javascriptVarName
     * @return string
     */
    public function getBreadcrumbsJavascript($path, $javascriptVarName)
    {
        if (empty($path)) {
            return '';
        }
        $categories = Mage::getResourceSingleton('menubuilder/menu_tree')
        	->setStoreId($this->getStore()->getId())->loadBreadcrumbsArray($path);
        if (empty($categories)) {
            return '';
        }
        foreach ($categories as $key => $category) {
            $categories[$key] = $this->_getNodeJson($category);
        }
		if(version_compare(Mage::getVersion(), '1.6.0.0', '>=')) {
	        return
	            '<script type="text/javascript">'
	            . ' parent.updateTree(' . Zend_Json::encode($categories) . ');'
	            . '</script>';
		}
        return
            '<script type="text/javascript">'
            . $javascriptVarName . ' = ' . Zend_Json::encode($categories) . ';'
            . '</script>';
    }
    
    public function getIcon($node)
    {
    	if($node->getData('menu_code')) {
    		$icon = 'root';
    	} else {
    		$icon = $node->getData('link_to') ? $node->getData('link_to') : 'nowhere';
    	}

    	return str_replace('_', '-', 'tree-node-' . $icon); 
    }

    /**
     * Get JSON of a tree node or an associative array
     *
     * @param Varien_Data_Tree_Node|array $node
     * @param int $level
     * @return string
     */
    protected function _getNodeJson($node, $level = 0)
    {
        // create a node from data array
        if (is_array($node)) {
            $node = new Varien_Data_Tree_Node($node, 'entity_id', new Varien_Data_Tree);
        }
		//Mage::helper('debug')->log($node->getData());
        $item = array();
        $item['text'] = $this->buildNodeName($node);

        //$rootForStores = Mage::getModel('core/store')->getCollection()->loadByCategoryIds(array($node->getEntityId()));
        $rootForStores = in_array($node->getEntityId(), $this->getRootIds());

        $item['id']  = $node->getId();
        $item['store']  = (int) $this->getStore()->getId();
        $item['path'] = $node->getData('path');

		
        //$item['cls']  = $node->getData('isRoot') ? 'root-folder ' : 'folder ';
        #$item['cls']  = 'file ';
        #$item['cls'] .= $node->getIsActive() ? 'active-category' : 'no-active-category';
        $item['iconCls'] = $this->getIcon($node);
     
        //die($item['cls']);
        $item['cls'] = 'folder ' . ($node->getData('is_active') ? 'active-category' : 'no-active-category');
        //$item['allowDrop'] = ($level<3) ? true : false;
        $allowMove = $this->_isCategoryMoveable($node);
        $item['allowDrop'] = $allowMove;
        // disallow drag if it's first level and category is root of a store
        $item['allowDrag'] = $allowMove && (($node->getLevel()==1) ? false : true);

        if ((int)$node->getChildrenCount()>0) {
            $item['children'] = array();
        }

        $isParent = $this->_isParentSelectedCategory($node);

        if ($node->hasChildren()) {
            $item['children'] = array();
            if (!($this->getUseAjax() && $node->getLevel() > 1 && !$isParent)) {
                foreach ($node->getChildren() as $child) {
                    $item['children'][] = $this->_getNodeJson($child, $level+1);
                }
            }
        }

        if ($isParent || $node->getLevel() < 2) {
            $item['expanded'] = true;
        }

        return $item;
    }
    
    /**
     * Get category name
     *
     * @param Varien_Object $node
     * @return string
     */
    public function buildNodeName($node)
    {
        return $this->htmlEscape($node->getName());
    }

    protected function _isCategoryMoveable($node)
    {
        $options = new Varien_Object(array(
            'is_moveable' => true,
            'category' => $node
        ));

        Mage::dispatchEvent('menubuilder_adminhtml_menu_tree_is_moveable',
            array('options'=>$options)
        );

        return $options->getIsMoveable();
    }

    protected function _isParentSelectedCategory($node)
    {
        if ($node && $this->getMenu()) {
            $pathIds = $this->getMenu()->getPathIds();
            if (in_array($node->getId(), $pathIds)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if page loaded by outside link to category edit
     *
     * @return boolean
     */
    public function isClearEdit()
    {
        return (bool) $this->getRequest()->getParam('clear');
    }

    /**
     * Check availability of adding sub category
     *
     * @return boolean
     */
    public function canAddNode()
    {
        $options = new Varien_Object(array('is_allow'=>true));
        Mage::dispatchEvent(
            'menubuilder_adminhtml_menu_tree_can_add_node',
            array(
                'category' => $this->getMenu(),
                'options'   => $options,
                'store'    => $this->getStore()->getId()
            )
        );

        return $options->getIsAllow();
    }
    
    /**
     * Check availability of adding root category
     *
     * @return boolean
     */
    public function canAddRootNode()
    {
        $options = new Varien_Object(array('is_allow'=>true));
        Mage::dispatchEvent(
            'menubuilder_adminhtml_menu_tree_can_add_root_node',
            array(
                'category' => $this->getMenu(),
                'options'   => $options,
                'store'    => $this->getStore()->getId()
            )
        );

        return $options->getIsAllow();
    }

}
