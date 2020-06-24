<?php

class Meetanshi_MobileLogin_CustomerController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
            Mage::getSingleton('core/session')
                ->addSuccess(Mage::helper('mobilelogin')->__('Please sign in or create a new account'));
        }
    }
    public function viewAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Mobilelogin'));
        $this->renderLayout();
    }
    public function updateAction()
    {
        try {

            $return['succeess'] = "";
            $param = $this->getRequest()->getParams();
            $mobilenumber = $param['mobilenumber'];

            $collection = Mage::getModel('customer/customer')->getCollection();
            $collection->addAttributeToFilter('mobile_number', array('eq' => $mobilenumber));

            if (count($collection->getData())) {
                $return['succeess']="true";
                print_r(json_encode($return));
            } else {
                print_r(json_encode($return));
            }
        }
        catch (Exception $e)
        {
            Mage::log("UPdate log :".$e->getMessage(),null,"Mobilelogin.log");
        }
    }
}
