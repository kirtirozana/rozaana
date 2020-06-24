<?php
class Meetanshi_MobileLogin_Model_MobileLogin extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('mobilelogin/mobilelogin');
    }
}
