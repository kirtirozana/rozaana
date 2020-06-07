<?php

/*
 *
 * @author makis palasis, made for afoipalasi.gr
 */

class Palasis_Mobilelistmode_Block_Catalog_Product_List_Toolbar
  extends Mage_Catalog_Block_Product_List_Toolbar {

      protected function _construct() {
        Mage_Core_Block_Template::_construct();
        $this->_orderField = Mage::getStoreConfig(
                        Mage_Catalog_Model_Config::XML_PATH_LIST_DEFAULT_SORT_BY
        );

        $this->_availableOrder = $this->_getConfig()->getAttributeUsedForSortByArray();

        $isUsingMobile = Zend_Http_UserAgent_Mobile::match(
                Mage::helper('core/http')->getHttpUserAgent(),
                $_SERVER
        );
        if ($isUsingMobile && Mage::getStoreConfig('catalog/frontend/list_mode_mobile')) {
          $listSwitch = Mage::getStoreConfig('catalog/frontend/list_mode_mobile');
        } else {
          $listSwitch = Mage::getStoreConfig('catalog/frontend/list_mode');
        }
        switch ($listSwitch) {
          case 'grid':
            $this->_availableMode = array('grid' => $this->__('Grid'));
            break;

          case 'list':
            $this->_availableMode = array('list' => $this->__('List'));
            break;

          case 'grid-list':
            $this->_availableMode = array('grid' => $this->__('Grid'), 'list' => $this->__('List'));
            break;

          case 'list-grid':
            $this->_availableMode = array('list' => $this->__('List'), 'grid' => $this->__('Grid'));
            break;
        }
        $this->setTemplate('catalog/product/list/toolbar.phtml');
      }

}
