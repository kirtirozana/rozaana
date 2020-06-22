<?php

/**
 * Magespacex
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magespacex.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magespacex.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magespacex
 * @package     Magespacex_Storecredit
 * @module      Storecredit
 * @author      Magespacex Developer
 *
 * @copyright   Copyright (c) 2016 Magespacex (http://www.magespacex.com/)
 * @license     http://www.magespacex.com/license-agreement.html
 *
 */

/**
 * Customercredit Navigation Block
 * 
 * @category    Magespacex
 * @package     Magespacex_Customercredit
 * @author      Magespacex Developer
 */
class Magespacex_Customercredit_Block_Account_Navigation extends Mage_Customer_Block_Account_Navigation
{

    protected $_navigationTitle = '';

    /**
     * @param $title
     * @return $this
     */
    public function setNavigationTitle($title)
    {
        $this->_navigationTitle = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getNavigationTitle()
    {
        return $this->_navigationTitle;
    }

    /**
     * @param $name
     * @param $path
     * @param $label
     * @param bool $disabled
     * @param int $order
     * @param array $urlParams
     * @return $this
     */
    public function addLink($name, $path, $label, $disabled = false, $order = 0, $urlParams = array())
    {
        if (isset($this->_links[$order])) {
            $order++;
        }

        $link = new Varien_Object(array(
            'name' => $name,
            'path' => $path,
            'label' => $label,
            'disabled' => $disabled,
            'order' => $order,
            'url' => $this->getUrl($path, $urlParams),
        ));

        Mage::dispatchEvent('customercredit_account_navigation_add_link', array(
            'block' => $this,
            'link' => $link,
        ));

        $this->_links[$order] = $link;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLinks()
    {
        $links = new Varien_Object(array(
            'links' => $this->_links,
        ));

        Mage::dispatchEvent('customercredit_account_navigation_get_links', array(
            'block' => $this,
            'links_obj' => $links,
        ));

        $this->_links = $links->getLinks();

        ksort($this->_links);

        return $this->_links;
    }

    /**
     * @param $link
     * @return bool
     */
    public function isActive($link)
    {
        if (parent::isActive($link)) {
            return true;
        }
        return false;
    }

}
