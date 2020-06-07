<?php

class MT_Giftcard_Adminhtml_Giftcard_ExportController extends Mage_Adminhtml_Controller_Action
{
    protected function _prepareDownloadResponseAction()
    {
        $content = $this->getRequest()->getParam('content');
        $fileName = $this->getRequest()->getParam('file_name');
        $session = Mage::getSingleton('admin/session');
        if ($session->isFirstPageAfterLogin()) {
            $this->_redirect($session->getUser()->getStartupPageUrl());
            return $this;
        }

        return $this->_prepareDownloadResponse(
            $fileName,
            $content
        );
    }

    public function orderAction()
    {
        $orderId = $this->getRequest()->getParam('id');
        $format = $this->getRequest()->getParam('format');
        $fileName   = 'gift_cards_'.str_replace(' ','_',Mage::getModel('core/date')->date('Y-m-d H:i:s')).'.'.$format;

        try {
            $content = Mage::getSingleton('giftcard/giftcard_action')
            ->exportOrderGiftCard($orderId, $format);
            if (!file_exists($content['value']))
                throw new Exception(Mage::helper('giftcard')->__('Can not to create file'));

            $this->_forward('_prepareDownloadResponse', 'giftcard_export', null, array(
                'file_name' => $fileName,
                'content' => $content
            ));

        } catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
    }

    protected function _isAllowed()
    {
        return true;
    }
}
