<?php
require_once "Mage/Customer/controllers/AccountController.php";

class Meetanshi_MobileLogin_Customer_AccountController extends Mage_Customer_AccountController
{
    public function postDispatch()
    {
        parent::postDispatch();
        Mage::dispatchEvent('controller_action_postdispatch_adminhtml', array('controller_action' => $this));
    }

    public function loginPostAction()
    {
        $session = $this->_getSession();

        $collection = Mage::getModel('customer/customer')->getCollection();
        $username = $this->getRequest()->getPost();
        $usermobile = $username['login']['username'];
        $websiteId = Mage::app()->getWebsite()->getId();


        if (!filter_var($usermobile, FILTER_VALIDATE_EMAIL) === false):
            $collection->addAttributeToFilter('email', array('eq' => $username['login']['username']));
        else:
            $collection->addAttributeToFilter('mobile_number', array('eq' => $username['login']['username']));
        endif;

        $custData = $collection->getData();
        $email = trim($custData[0]['email']);
        $customerId = (int)trim($custData[0]['entity_id']);
        try {
            $authenticateUser = Mage::getModel('customer/customer')->setWebsiteId($websiteId)->authenticate($email, $username['login']['password']);
        } catch (Exception $e) {
            $session->addError('Invalid Login Detail');
            $this->_redirect('customer/account');
        }

        try {
            if ($authenticateUser && $customerId) {
                $customer = Mage::getModel('customer/customer')->load($customerId);
                $session->setCustomerAsLoggedIn($customer);
                $message = $this->__('You are now logged in as %s', $customer->getName());
                $session->addSuccess($message);
            } else {
                Mage::throwException($this->__('The login attempt was unsuccessful. Some parameter is missing Or wrong data '));
            }
        } catch (Exception $e) {
            $session->addError($e->getMessage());
        }
        
        $this->_loginPostRedirect();
    }
}

