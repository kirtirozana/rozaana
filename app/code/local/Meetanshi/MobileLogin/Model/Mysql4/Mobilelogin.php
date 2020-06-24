<?php
class Meetanshi_MobileLogin_Model_Mysql4_Mobilelogin extends  Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('mobilelogin/mobilelogin', 'mobilelogin_id');
    }    
}
