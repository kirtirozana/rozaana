<?php
class Meetanshi_MobileLogin_Model_Mysql4_Mobilelogin_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('mobilelogin/mobilelogin');
    }    
}
