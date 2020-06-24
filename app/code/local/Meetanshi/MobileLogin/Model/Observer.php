<?php

class Meetanshi_Mobilelogin_Model_Observer
{
    public function order_place_after($observer)
    {
        try {
            $billingaddress = $observer->getOrder()->getBillingAddress();
            $customerEmail = $billingaddress->getEmail();
            $customer = Mage::getModel("customer/customer");
            $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
            $customer->loadByEmail($customerEmail);
            $mobile = $customer->getMobileNumber();
            if ($mobile == "") {
                $mobileloginModel = Mage::getModel('mobilelogin/mobilelogin');
                $mobiledata=$mobileloginModel->load($billingaddress->getTelephone(), 'mobilenumber');
                if (count($mobiledata->getData())) {
                    if (!$mobiledata->getRegisterVerify()) {
                        {
                            Mage::throwException('Please Verify Mobilenumber');
                        }
                    } else {
                        $customer->setMobileNumber($billingaddress->getTelephone());
                        $customer->save();
                    }
                }
            }
        }
        catch (Exception $e)
        {
            Mage::log("Sales Observer".$e->getMessage(), null, "Mobilelogin.log");
        }
    }
}
