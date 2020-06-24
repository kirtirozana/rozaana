<?php
class Meetanshi_MobileLogin_MobileloginController extends Mage_Core_Controller_Front_Action
{
    public function SendotpAction() 
    {
        $params = $this->getRequest()->getParams();
        return Mage::helper('mobilelogin')->Otpsave($params);
    }
    public function VerifyotpAction() 
    {
        $params = $this->getRequest()->getParams();
        return Mage::helper('mobilelogin')->Verifyotp($params);
    }
    public function ForgotPasswordAction()
    {
        $params = $this->getRequest()->getParams();
        $mobilenumber=$params['mobilenumber'];
        $newPassword=$params['password'];
        $return['url']="";
        try {
            if(!$newPassword=="") {
                $collection = Mage::getModel('customer/customer')->getCollection();
                $collection->addAttributeToFilter('mobile_number', array('eq' => $mobilenumber));
                $custData = $collection->getData();

                $customer = Mage::getModel('customer/customer')->load($custData[0]['entity_id']);
                $customer->setPassword($newPassword);
                $customer->save();
                Mage::getSingleton("core/session")->addSuccess("Password Change Successfully");
                $return['url'] = Mage::getBaseUrl() . 'customer/account/login';
                print_r(json_encode($return));
            }
        }
        catch (Exception $e)
        {
            print_r(json_encode($return));
            Mage::log("Password Change Error :".$e->getMessage(), null, 'Mobilelogin.log');
        }


    }
}
