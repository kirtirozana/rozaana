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

class Uxmill_WhatsappChat_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     *
     */
    const CHAT_ENABLE = 'whatsappchat/general/enabled';
    /**
     *
     */
    const ENABLE_IN = 'whatsappchat/general/enable_in';
    /**
     *
     */
    const DEFAULT_MESSAGE = 'whatsappchat/general/default_message';
    /**
     *
     */
    const BUTTON_TYPE = 'whatsappchat/settings/button_type';
    /**
     *
     */
    const PERSON_NAME = 'whatsappchat/settings/person_name';
    /**
     *
     */
    const MOBILE_NUMBER = 'whatsappchat/settings/mobile_number';
    /**
     *
     */
    const CHAT_BOX_TEXT = 'whatsappchat/settings/chatboxtext';
    /**
     *
     */
    const MESSAGE = 'whatsappchat/settings/message';
    /**
     *
     */
    const BACKGROUND_COLOR = 'whatsappchat/settings/background_color';
    /**
     *
     */
    const ICON_COLOR = 'whatsappchat/settings/icon_color';
    /**
     *
     */
    const TOP = 'whatsappchat/settings/top';
    /**
     *
     */
    const RIGHT = 'whatsappchat/settings/right';
    /**
     *
     */
    const BOTTOM = 'whatsappchat/settings/bottom';
    /**
     *
     */
    const LEFT = 'whatsappchat/settings/left';
    /**
     *
     */
    const ANIMATION = 'whatsappchat/settings/animation';
    /**
     *
     */
    const BUTTON_TEXT = 'whatsappchat/settings/text';
    /**
     *
     */
    const FROM_DATE = 'whatsappchat/settings/fromdate';
    /**
     *
     */
    const TO_DATE = 'whatsappchat/settings/todate';
    /**
     *
     */
    const CLOSE = 'whatsappchat/settings/close';


    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)Mage::getStoreConfig(self::CHAT_ENABLE);

    }

    /**
     * @return array
     */
    public function getConfigValue()
    {
        $data[] = "";
        $data['chat_enable'] = Mage::getStoreConfig(self::CHAT_ENABLE);
        $data['enable_in'] = Mage::getStoreConfig(self::ENABLE_IN);
        $data['default_message'] = Mage::getStoreConfig(self::DEFAULT_MESSAGE);
        $data['button_type'] = Mage::getStoreConfig(self::BUTTON_TYPE);
        $data['person_name'] = Mage::getStoreConfig(self::PERSON_NAME);
        $data['mobile_number'] = Mage::getStoreConfig(self::MOBILE_NUMBER);
        $data['chat_box_text'] = Mage::getStoreConfig(self::CHAT_BOX_TEXT);
        $data['message'] = Mage::getStoreConfig(self::MESSAGE);
        $data['text'] = Mage::getStoreConfig(self::BUTTON_TEXT);
        $data['background_color'] = '#' . Mage::getStoreConfig(self::BACKGROUND_COLOR);
        $data['icon_color'] = '#' . Mage::getStoreConfig(self::ICON_COLOR);
        $data['from'] = Mage::getStoreConfig(self::FROM_DATE);
        $data['to'] = Mage::getStoreConfig(self::TO_DATE);
        $data['top'] = Mage::getStoreConfig(self::TOP);
        $data['right'] = Mage::getStoreConfig(self::RIGHT);
        $data['bottom'] = Mage::getStoreConfig(self::BOTTOM);
        $data['left'] = Mage::getStoreConfig(self::LEFT);
        $data['animation'] = Mage::getStoreConfig(self::ANIMATION);
        $data['close'] = Mage::getStoreConfig(self::CLOSE);
        if ($data['top'] <= '0' || $data['top'] == '') :
            $data['top'] = '';
        endif;
        if ($data['right'] <= '0' || $data['right'] == '') :
            $data['right'] = '';
        endif;
        if ($data['bottom'] <= '0') :
            $data['bottom'] = '25';
        endif;
        if (($data['left'] <= '0') && ($data['right'] <= '0')) :
            $data['left'] = '25';
        endif;
        $data['close_top'] = '';
        $data['close_right'] = '';
        $data['close_bottom'] = '';
        $data['close_left'] = '';
        $data['close_right_mobile'] = '';
        if ($data['button_type'] == "chatbox") :
            if ($data['chat_box_text'] == '') :
                $data['chat_box_text'] = 'Chat with us on WhatsApp';
            endif;
            $data['chat_top'] = '';
            $data['chat_right'] = '';
            $data['chat_left'] = '';
            $data['chat_bottom'] = '';
            if ($data['top'] != '' && $data['top'] > '0') :
                $data['chat_top'] = (int)($data['top']) + 60;
            else :
                $data['chat_top'] = '';
                $data['chat_bottom'] = (int)($data['bottom']) + 60;
            endif;
            if ($data['right'] != '' && $data['right'] > '0') :
                $data['chat_right'] = (int)($data['right']);
                $data['left'] = '';
            else :
                $data['chat_right'] = '';
                $data['chat_left'] = (int)($data['left']);
            endif;
        elseif ($data['button_type'] == "icon"):
            if ($data['text'] == '') :
                $data['text'] = 'WhatsApp Contact';
            endif;
            $data['title_top'] = '18';
            $data['title_left'] = '';
            $data['title_right'] = '';
            if ($data['close']) :
                if ($data['top'] != '' && $data['top'] > '0') :
                    $data['close_top'] = (int)($data['top']) - 3;
                    $data['close_bottom'] = '';
                else :
                    $data['close_top'] = '';
                    $data['close_bottom'] = (int)($data['bottom']) + 50;
                endif;
                if ($data['right'] != '' && $data['right'] > '0') :
                    $data['close_right'] = (int)($data['right']) - 5;
                    $data['close_right_mobile'] = (int)($data['right']) - 10;
                    $data['title_right'] = '55';
                    $data['close_left'] = '';
                else :
                    $data['close_left'] = (int)($data['left']) + 55;
                    $data['title_left'] = '55';
                endif;
            endif;
        endif;
        return $data;
    }
}
