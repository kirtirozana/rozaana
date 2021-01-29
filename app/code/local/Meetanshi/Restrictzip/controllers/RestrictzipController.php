<?php

class Meetanshi_Restrictzip_RestrictzipController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        if (Mage::app()->getRequest()->getPost('zip_code') != '') {
            if (Mage::helper('restrictzip')->isZipCodeAllowed(
                Mage::app()->getRequest()->getPost('zip_code')
            )) {
                $extimateDeliveryTime = Mage::helper('restrictzip')->getEstimateDeliveryTimeFromZipCode(
                    Mage::app()->getRequest()->getPost('zip_code')
                );
                $suburbResultArray = array('status' => 'success',
                    'allowed_zip' => true,
                    'estimate_delivery_time' => $extimateDeliveryTime);
            } else {
                $suburbResultArray = array('status' => 'success', 'allowed_zip' => false);
            }
        } else
            $suburbResultArray = array('status' => 'error', 'allowed_zip' => null);

        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode($suburbResultArray));
    }

}
