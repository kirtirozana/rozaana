<?php

class Meetanshi_Restrictzip_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isZipCodeAllowed($zipCode)
    {
        $collection = Mage::getModel('restrictzip/restrictzip')
            ->getCollection()
            ->addFieldToFilter('zip_code', $zipCode);

        if ($collection->getSize() != 0)
            return true;
        else
            return false;
    }

    public function getCityNameFromZipCode($zipCode)
    {
        $collection = Mage::getModel('restrictzip/restrictzip')
            ->getCollection()
            ->addFieldToFilter('zip_code', $zipCode);

        if ($collection->getSize() != 0) {
            foreach ($collection as $getcity) {
                return $getcity->getCity();
            }
        } else
            return false;
    }

    public function getEstimateDeliveryTimeFromZipCode($zipCode)
    {
        $collection = Mage::getModel('restrictzip/restrictzip')
            ->getCollection()
            ->addFieldToFilter('zip_code', $zipCode);

        if ($collection->getSize() != 0) {
            foreach ($collection as $getcity) {
                return $getcity->getEstimateDelTime();
            }
        } else
            return false;
    }

    public function isShowDelivery()
    {
        if (Mage::getStoreConfig('restrictzip/restrictzip/showdelivery')) {
            return 'block';
        } else {
            return 'none';
        }
    }

    public function isEnable()
    {
        return Mage::getStoreConfig('restrictzip/restrictzip/active');
    }

    public function getTitle()
    {
        return Mage::getStoreConfig('restrictzip/restrictzip/title');
    }

    public function getSuccessMsg()
    {
        return Mage::getStoreConfig('restrictzip/restrictzip/delsuc');
    }

    public function getErrorMsg()
    {
        return Mage::getStoreConfig('restrictzip/restrictzip/delerr');
    }
}
