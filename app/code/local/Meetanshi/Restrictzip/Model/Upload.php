<?php

class Meetanshi_Restrictzip_Model_Upload extends Mage_Core_Model_Config_Data
{
    public function _afterSave()
    {
        Mage::getModel('restrictzip/import')->uploadAndImport();
    }
}
