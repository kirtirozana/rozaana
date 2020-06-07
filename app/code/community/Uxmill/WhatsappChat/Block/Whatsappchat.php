<?php
/**
 * Uxmill
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the umxill.co license that is
 * available through the world-wide-web.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Uxmill
 * @package     Uxmill_WhatsappChat
 * @copyright   Copyright (c) Uxmill (http://www.uxmill.co)
 * @license     http://www.uxmill.co
 */

class Uxmill_WhatsappChat_Block_Whatsappchat extends Mage_Core_Block_Template
{
    /**
     * @var
     */
    protected $chat;

    /**
     * Uxmill_WhatsappChat_Block_Whatsappcontact constructor.
     *
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        parent::__construct($args);
        $this->chat = Mage::helper('whatsappchat')->getConfigValue();
    }

    /**
     * @return string
     * @todo need to inject device check library insted of custom
     */
    public function checkDevices()
    {

        if (Mage::helper("mobiledetect")->getDetact()->isMobile() ) {
            return "mobile";
        } else {
            return "web";
        }
    }

    /**
     * @return string
     * @todo need to inject device check library insted of custom
     */
    public function getUrlKey()
    {
        
        if (Mage::helper("mobiledetect")->getDetact()->isMobile() ) {
            return "whatsapp://send?l=en&phone=" . $this->getChat();
        } else {
            return "https://web.whatsapp.com/send?l=en&phone=" . $this->getChat();
        }
    }

    /**
     * @return string
     */
    public function getChat()
    {
        try {
            $data = $this->chat['mobile_number'];
            $data .= "&text=" . $this->chat['default_message'];
            return $data;
        } catch (Exception $e) {
            Mage::log($e->getMessage());
            return "";
        }
    }

    /**
     * @return mixed
     */
    public function getWhatsappChat()
    {
        return Mage::helper('whatsappchat')->getConfigValue();
    }

}
