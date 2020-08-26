<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/extension_rewards
 * @version   1.1.42
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



class Mirasvit_Rewards_Helper_Mail
{
    /**
     * @var array
     */
    public $emails = array();

    /**
     * @return Mirasvit_Rewards_Model_Config
     */
    protected function getConfig()
    {
        return Mage::getSingleton('rewards/config');
    }

    /**
     * @return string
     */
    protected function getSender()
    {
        return $this->getConfig()->getNotificationSenderEmail(Mage::helper('rewards')->getCurrentStore());
    }

    /**
     * @param string $templateName
     * @param string $senderName
     * @param string $senderEmail
     * @param string $recipientEmail
     * @param string $recipientName
     * @param array $variables
     * @param int $storeId
     * @return bool
     */
    protected function send(
        $templateName,
        $senderName,
        $senderEmail,
        $recipientEmail,
        $recipientName,
        $variables,
        $storeId
    ) {
        if (!$senderEmail) {
            return false;
        }
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        $template = Mage::getModel('core/email_template');
        $template->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
                 ->sendTransactional(
                     $templateName,
                     array(
                     'name' => $senderName,
                     'email' => $senderEmail,
                     ),
                     $recipientEmail,
                     $recipientName,
                     $variables
                 );
                 $text = $template->getProcessedTemplate($variables, true);
                 $this->emails[] = array(
                     'text' => $text,
                     'recipient_email' => $recipientEmail,
                     'recipient_name' => $recipientName
                 );
                 $translate->setTranslateInline(true);

                 return true;
    }

    /**
     * @param Mirasvit_Rewards_Model_Transaction $transaction
     * @param string|bool $emailMessage
     * @return bool
     */
    public function sendNotificationBalanceUpdateEmail($transaction, $emailMessage = false)
    {
        if ($emailMessage) {
            $emailMessage = $this->parseVariables($emailMessage, $transaction);
        }

        $customer = $transaction->getCustomer();
        $templateName = $this->getConfig()->getNotificationBalanceUpdateEmailTemplate($customer->getStore()->getId());
        if ($templateName == 'none') {
            return false;
        }
        $recipientEmail = $customer->getEmail();
        $recipientName = $customer->getName();
        $storeId = $customer->getStore()->getId();
        Mage::helper('rewards')->setCurrentStore($customer->getStore());
        $variables = array(
            'customer' => $customer,
            'store' => $customer->getStore(),
            'transaction' => $transaction,
            'transaction_days_left' => $transaction->getDaysLeft(),
            'transaction_amount' => Mage::helper('rewards')->formatPoints($transaction->getAmount()),
            'transaction_comment' => $transaction->getComment(),
            'balance_total' => Mage::helper('rewards')
                                ->formatPoints(Mage::helper('rewards/balance')->getBalancePoints($customer)),
            'message' => $emailMessage,
            'no_message' => $emailMessage == false || $emailMessage == '',
        );
        $senderName = Mage::getStoreConfig("trans_email/ident_{$this->getSender()}/name");
        $senderEmail = Mage::getStoreConfig("trans_email/ident_{$this->getSender()}/email");
        $this->send($templateName, $senderName, $senderEmail, $recipientEmail, $recipientName, $variables, $storeId);
    }

    /**
     * @param Mirasvit_Rewards_Model_Transaction $transaction
     * @return bool
     */
    public function sendNotificationPointsExpireEmail($transaction)
    {
        $customer = $transaction->getCustomer();
        $templateName = $this->getConfig()->getNotificationPointsExpireEmailTemplate();
        if ($templateName == 'none') {
            return false;
        }
        $recipientEmail = $customer->getEmail();
        $recipientName = $customer->getName();
        $storeId = $customer->getStore()->getId();
        $transactionAmount = $transaction->getAmount() - $transaction->getAmountUsed();
        $variables = array(
            'customer' => $customer,
            'store' => $customer->getStore(),
            'transaction' => $transaction,
            'transaction_days_left' => $transaction->getDaysLeft(),
            'transaction_amount' => Mage::helper('rewards')->formatPoints($transactionAmount),
        );
        $senderName = Mage::getStoreConfig("trans_email/ident_{$this->getSender()}/name");
        $senderEmail = Mage::getStoreConfig("trans_email/ident_{$this->getSender()}/email");
        $this->send($templateName, $senderName, $senderEmail, $recipientEmail, $recipientName, $variables, $storeId);
    }

    /**
     * @param Mirasvit_Rewards_Model_Referral $referral
     * @param string $message
     * @return bool
     */
    public function sendReferralInvitationEmail($referral, $message)
    {
        $templateName = $this->getConfig()->getReferralInvitationEmailTemplate();
        if ($templateName == 'none') {
            return false;
        }
        $recipientEmail = $referral->getEmail();
        $recipientName = $referral->getName();
        $storeId = $referral->getStoreId();
        $customer = $referral->getCustomer();
        $variables = array(
            'customer' => $customer,
            'name' => $referral->getName(),
            'message' => $message,
            'invitation_url' => $referral->getInvitationUrl(),
        );
        $senderName = Mage::getStoreConfig("trans_email/ident_{$this->getSender()}/name");
        $senderEmail = Mage::getStoreConfig("trans_email/ident_{$this->getSender()}/email");
        $this->send($templateName, $senderName, $senderEmail, $recipientEmail, $recipientName, $variables, $storeId);
    }

    /**
     * Can parse template and return ready text.
     *
     * @param string $variable  - text with variables like {{var customer.name}}
     * @param array  $variables - array of variables
     * @param array  $storeId
     *
     * @return string - ready text
     */
    public function processVariable($variable, $variables, $storeId)
    {
        // save current design settings
        $currentDesignConfig = clone $this->_getDesignConfig();

        $this->_setDesignConfig(array('area' => 'frontend', 'store' => $storeId));
        $this->_applyDesignConfig();
        $template = Mage::getModel('core/email_template');
        $template->setTemplateText($variable);
        $html = $template->getProcessedTemplate($variables);
        // restore previous design settings
        $this->_setDesignConfig($currentDesignConfig->getData());
        $this->_applyDesignConfig();

        return $html;
    }

    /**
     * @param int $store
     * @return string
     */
    protected function _getLogoUrl($store)
    {
        $store = Mage::app()->getStore($store);
        $fileName = $store->getConfig('design/email/logo');
        if ($fileName) {
            $uploadDir = Mage_Adminhtml_Model_System_Config_Backend_Email_Logo::UPLOAD_DIR;
            $fullFileName = Mage::getBaseDir('media').DS.$uploadDir.DS.$fileName;
            if (file_exists($fullFileName)) {
                return Mage::getBaseUrl('media').$uploadDir.'/'.$fileName;
            }
        }

        return Mage::getDesign()->getSkinUrl('images/logo_email.gif');
    }

    /**
     * @param int $store
     * @return null|string
     */
    protected function _getLogoAlt($store)
    {
        $store = Mage::app()->getStore($store);
        $alt = $store->getConfig('design/email/logo_alt');
        if ($alt) {
            return $alt;
        }

        return $store->getFrontendName();
    }

    /**
     * @var array
     */
    protected $_designConfig;

    /**
     * @param array $config
     * @return $this
     */
    protected function _setDesignConfig(array $config)
    {
        $this->_getDesignConfig()->setData($config);

        return $this;
    }

    /**
     * @return array|Varien_Object
     */
    protected function _getDesignConfig()
    {
        if ($this->_designConfig === null) {
            $store = is_object(Mage::getDesign()->getStore())
                ? Mage::getDesign()->getStore()->getId()
                : Mage::getDesign()->getStore();

            $this->_designConfig = new Varien_Object(array(
                'area' => Mage::getDesign()->getArea(),
                'store' => $store,
                'theme' => Mage::getDesign()->getTheme('template'),
                'package_name' => Mage::getDesign()->getPackageName(),
            ));
        }

        return $this->_designConfig;
    }

    /**
     * @return $this
     * @throws Mage_Core_Exception
     */
    protected function _applyDesignConfig()
    {
        $designConfig = $this->_getDesignConfig();
        $design = Mage::getDesign();
        $designConfig->setOldArea($design->getArea())
            ->setOldStore($design->getStore());

        if ($designConfig->hasData('area')) {
            Mage::getDesign()->setArea($designConfig->getArea());
        }
        if ($designConfig->hasData('store')) {
            $store = $designConfig->getStore();
            Mage::app()->setCurrentStore($store);

            $locale = new Zend_Locale(Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $store));
            Mage::app()->getLocale()->setLocale($locale);
            Mage::app()->getLocale()->setLocaleCode($locale->toString());
            if ($designConfig->hasData('area')) {
                Mage::getSingleton('core/translate')->setLocale($locale)
                    ->init($designConfig->getArea(), true);
            }
            $design->setStore($store);
            $design->setTheme('');
            $design->setPackageName('');
        }
        if ($designConfig->hasData('theme')) {
            Mage::getDesign()->setTheme($designConfig->getTheme());
        }
        if ($designConfig->hasData('package_name')) {
            Mage::getDesign()->setPackageName($designConfig->getPackageName());
        }

        return $this;
    }


    /**
     * @param string $text
     * @param Mirasvit_Rewards_Model_Transaction $transaction
     * @return string
     */
    public function parseVariables($text, $transaction)
    {
        $customer = $transaction->getCustomer();
        $variables = array(
            'customer' => $customer,
            'store' => $customer->getStore(),
            'transaction' => $transaction,
            'transaction_days_left' => $transaction->getDaysLeft(),
            'transaction_amount' => Mage::helper('rewards')->formatPoints($transaction->getAmount()),
            'balance_total' => Mage::helper('rewards')
                                ->formatPoints(Mage::helper('rewards/balance')->getBalancePoints($customer)),
        );
        $text = $this->processVariable($text, $variables, $customer->getStore()->getId());

        return $text;
    }
}
