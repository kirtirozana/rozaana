<?php

class Meetanshi_Restrictzip_Model_Mysql4_Restrictzip extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('restrictzip/restrictzip', 'zip_code_id');
    }
}
