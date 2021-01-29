<?php

class Meetanshi_Restrictzip_Model_Attribute_Data_Postcode extends Mage_Customer_Model_Attribute_Data_Postcode
{
    public function validateValue($value)
    {
        $countryId = $this->getExtractedData('country_id');
        $optionalZip = Mage::helper('directory')->getCountriesWithOptionalZip();

        if (Mage::helper('restrictzip')->isEnable()) {
            if (!Mage::helper('restrictzip')->isZipCodeAllowed($value)) {
                $errors[] = Mage::helper('restrictzip')->__(Mage::helper('restrictzip')->getErrorMsg());
                return $errors;
            }
        }

        if (!in_array($countryId, $optionalZip)) {
            return parent::validateValue($value);
        }

        return true;
    }
}
