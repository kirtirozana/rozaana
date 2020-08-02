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
 * @category    Textlocal
 * @package     Textlocal_smsnotifications
 * @copyright   Copyright (c) 2014 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Textlocal_smsnotifications_SmsnotificationsController extends Mage_Adminhtml_Controller_Action
{

    public function saveAction()
    {
        if($data['start_date'] != NULL )
        {
            $date = Mage::app()->getLocale()->date($data['start_date'], Zend_Date::DATE_SHORT);
            $model->setStartDate($date->toString('YYYY-MM-dd HH:mm:ss'));
        }
        if($data['end_date'] != NULL)
        {
            $date1 = Mage::app()->getLocale()->date($data['end_date'], Zend_Date::DATE_SHORT);
            $model->setEndDate($date1->toString('YYYY-MM-dd HH:mm:ss'));
        } 
    }
     public function indexAction()
    {

        $this->loadLayout();    
        $this->renderLayout();
    }




  

}
