<?php
/**
 * TSDesigns_MenuBuilder_Model_Menu_Type_Internal
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
class TSDesigns_MenuBuilder_Model_Menu_Type_Internal extends TSDesigns_MenuBuilder_Model_Menu_Type_Abstract
{
	protected $_identifier = 'internal';
	protected $_model = null;
	
	public function getUrl($addBaseUrl = false)
	{
		return $addBaseUrl && $this->getData('link_to_internal') 
					? $this->_addBaseUrl($this->getData('link_to_internal')) 
					: $this->getData('link_to_internal');
	}
	
	public function isActive()
	{
		return $this->getData('link_to_internal');
	}
	
}