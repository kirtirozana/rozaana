<?php

class MT_Giftcard_GiftcardController
    extends Mage_Core_Controller_Front_Action
{
    public function pdfAction()
    {
        if(!Mage::helper('customer')->isLoggedIn()){
            Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('customer/account'));
        }

        $params = $this->getRequest()->getParams();
        if (isset($params['id']) && is_numeric($params['id'])) {
            $orderId = $params['id'];

            $orders = Mage::getResourceModel('sales/order_collection')
                ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
                ->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
                ->addFieldToFilter('entity_id', $orderId)
                ->setPageSize(1);

            if ($order = $orders->getFirstItem()) {
                $content = Mage::getSingleton('giftcard/giftcard_action')
                    ->exportOrderGiftCard($order->getId(), 'pdf');

                $fileName = Mage::helper('giftcard')->__('gift_card') . '_' . $order->getIncrementId() . '.pdf';
                return $this->_prepareDownloadResponse(
                    $fileName,
                    $content
                );
            }
        }

        return $this->_forward('noRoute');
    }
}
