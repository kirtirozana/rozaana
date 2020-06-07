<?php
/**
 * Apptha
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category    Apptha
 * @package     Apptha_Customer-Follow-Up
 * @version     1.1
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 * */
class Apptha_Customerfollowup_Adminhtml_CustomerfollowupController extends Mage_Adminhtml_Controller_action {
    /**
     * XML configuration paths
     */
    const EMAIL_TO_RECIPIENT_TEMPLATE_XML_PATH = 'customerfollowup/general/email_template';
    const XML_PATH_EMAIL_IDENTITY = 'customerfollowup/general/sender_email_id';
    const XML_PATH_EMAIL_RECIPIENT = 'contacts/email/recipient_email';

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('customerfollowup/items')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

        return $this;
    }

    /**
     * fucntion to view customer details
     */
    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    /**
     * fucntion to delete customer followup details
     */
    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('customerfollowup/customerfollowup');

                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Follow Up customer was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * fucntion to delete customer followup details
     */
    public function massDeleteAction() {
        $customerfollowupIds = $this->getRequest()->getParam('customerfollowup');
        if (!is_array($customerfollowupIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($customerfollowupIds as $customerfollowupId) {
                    $customerfollowup = Mage::getModel('customerfollowup/customerfollowup')->load($customerfollowupId);
                    $customerfollowup->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($customerfollowupIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * fucntion to export records as csv file
     */
    public function exportCsvAction() {
        $fileName = 'customerfollowup.csv';
        $content = $this->getLayout()->createBlock('customerfollowup/adminhtml_customerfollowup_grid')
                        ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    /**
     * fucntion to export records as xml file
     */
    public function exportXmlAction() {
        $fileName = 'customerfollowup.xml';
        $content = $this->getLayout()->createBlock('customerfollowup/adminhtml_customerfollowup_grid')
                        ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    /**
     * fucntion to send upload response
     */
    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream') {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }

    /**
     * fucntion to send mail to customer
     */
    public function sendmailAction() {
        $id = $this->getRequest()->getParam('id'); //get customerfollowup id
        $model = Mage::getModel('customerfollowup/customerfollowup')->load($id);
        $this->sendMailtocustomers($model); //function call for send mail to customers
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Mail sent successfully'));
        $this->_redirect('*/*/');
    }

    /**
     * fucntion to send mails for group of customers
     */
    public function massMailAction() {
        $customerfollowupIds = $this->getRequest()->getParam('customerfollowup'); //get collection of id
        if (!is_array($customerfollowupIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($customerfollowupIds as $customerfollowupId) {
                    $customerfollowup = Mage::getModel('customerfollowup/customerfollowup')->load($customerfollowupId);
                    $this->sendMailtocustomers($customerfollowup); //functionc call for send mail to customers
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Mail sent successfully'
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * fucntion to send mails to customers
     */
    public function sendMailtocustomers($model) {
        $orderId = $model->getOrderId(); //get order id
        $cartId = $model->getCartId(); //get cart id
        $currencySumbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->
                          getCurrentCurrencyCode())->getSymbol();//get currency symbol
        if ($orderId != 0) {
            /* getting cart page details for order */
            $orderItemcollection = Mage::getModel('sales/order')->load($orderId);
            $productCollection = $orderItemcollection->getAllItems(); //get order item collection            
            $i = 1;
            $grandTotal = 0;
            foreach ($productCollection as $product) {
                $productId = $product->getProductId();
                $catalogModel = Mage::getModel('catalog/product');
                $_product = $catalogModel->load($productId); //get product details for product id
                $rowTotal = $product->getPrice() * $product->getQtyOrdered();
                
                /* content of the cart table */
                $productDetails .= '<tr style="background:#F8F7F5;">
                                     <td valign="top" style="border-width: 0; text-align:left; border: 1px solid #CCC; border-right:0; border-top:0; color: #0A263C; font:normal 13px arial; padding:5px 0 5px 10px; border-bottom: 0;">' . $i . '</td>
                                     <td valign="top" style="border-width: 0; text-align:left; border: 1px solid #CCC; border-right:0; border-top:0; color: #0A263C; font:bold 13px arial; padding:5px 0 5px 10px; border-bottom: 0;"><a href="' . $_product->getProductUrl() . '">' . $product->getName() . '</a></td>
                                     <td valign="top" style="border-width: 0; text-align:left; border: 1px solid #CCC; border-right:0; border-top:0; color: #0A263C; font:normal 13px arial; padding:5px 0 5px 10px; border-bottom: 0;"><a href="' . $_product->getProductUrl() . '"><img src="' . Mage::helper('catalog/image')->init($_product, 'image') . '" width="75" height="75"/></a></td>
                                     <td valign="top" style="border-width: 0; text-align:left; border: 1px solid #CCC; border-right:0; border-top:0; color: #0A263C; font:normal 13px arial; padding:5px 0 5px 10px; border-bottom: 0;">' . $product->getSku() . '</td>
                                     <td valign="top" style="border-width: 0; text-align:left; border: 1px solid #CCC; border-right:0; border-top:0; color: #0A263C; font:normal 13px arial; padding:5px 0 5px 10px; border-bottom: 0;">' . $currencySumbol.number_format($product->getPrice(), 2, '.', '') . '</td>
                                     <td valign="top" style="border-width: 0; text-align:left; border: 1px solid #CCC; border-right:0; border-top:0; color: #0A263C; font:normal 13px arial; padding:5px 0 5px 10px; border-bottom: 0;">' . number_format($product->getQtyOrdered()) . '</td>
                                     <td valign="top" style="border-width: 0; text-align:left; border: 1px solid #CCC;  border-top:0; color: #0A263C; font:normal 13px arial; padding:5px 0 5px 10px; border-bottom: 0;">' . $currencySumbol.number_format($rowTotal, 2, '.', '') . '</td>
                                     </tr>';
                $i++;
                $grandTotal = $grandTotal + $rowTotal;
            }
            $grandTotal = $currencySumbol.number_format($grandTotal, 2, '.', '');
        } elseif ($cartId != 0) {
            /* getting cart page details for order */
           
            //get product collection for a particular cart id
            $quoteObj = Mage::getModel('sales/quote')->getCollection()->addFieldToFilter('entity_id', $cartId);
            foreach ($quoteObj as $items) {
                $productCollection = $items->getAllItems(); //get product collection
                /* customer followup cart page content */
                $i = 1;
                $grandTotal = 0;
                foreach ($productCollection as $product) {
                    $productId = $product->getProductId();
                    $catalogModel = Mage::getModel('catalog/product');
                    $_product = $catalogModel->load($productId); //get product details
                    $rowTotal = $product->getPrice() * $product->getQty();
                    /* content of the cart table */
                    $productDetails .= '<tr style="background:#F8F7F5;">
                                     <td valign="top" style="border-width: 0; text-align:left; border: 1px solid #CCC; border-right:0; border-top:0; color: #0A263C; font:normal 13px arial; padding:5px 0 5px 10px; border-bottom: 0;">' . $i . '</td>
                                     <td valign="top" style="border-width: 0; text-align:left; border: 1px solid #CCC; border-right:0; border-top:0; color: #0A263C; font:bold 13px arial; padding:5px 0 5px 10px; border-bottom: 0;"><a href="' . $_product->getProductUrl() . '">' . $product->getName() . '</a></td>
                                     <td valign="top" style="border-width: 0; text-align:left; border: 1px solid #CCC; border-right:0; border-top:0; color: #0A263C; font:normal 13px arial; padding:5px 0 5px 10px; border-bottom: 0;"><a href="' . $_product->getProductUrl() . '"><img src="' . Mage::helper('catalog/image')->init($_product, 'image') . '" width="75" height="75"/></a></td>
                                     <td valign="top" style="border-width: 0; text-align:left; border: 1px solid #CCC; border-right:0; border-top:0; color: #0A263C; font:normal 13px arial; padding:5px 0 5px 10px; border-bottom: 0;">' . $product->getSku() . '</td>
                                     <td valign="top" style="border-width: 0; text-align:left; border: 1px solid #CCC; border-right:0; border-top:0; color: #0A263C; font:normal 13px arial; padding:5px 0 5px 10px; border-bottom: 0;">' . $currencySumbol.number_format($product->getPrice(), 2, '.', '') . '</td>
                                     <td valign="top" style="border-width: 0; text-align:left; border: 1px solid #CCC; border-right:0; border-top:0; color: #0A263C; font:normal 13px arial; padding:5px 0 5px 10px; border-bottom: 0;">' . number_format($product->getQty()) . '</td>
                                     <td valign="top" style="border-width: 0; text-align:left; border: 1px solid #CCC;  border-top:0; color: #0A263C; font:normal 13px arial; padding:5px 0 5px 10px; border-bottom: 0;">' . $currencySumbol.number_format($rowTotal, 2, '.', '') . '</td>
                                     </tr>';
                    $i++;
                    $grandTotal = $grandTotal + $rowTotal;
                }
                $grandTotal = $currencySumbol.number_format($grandTotal, 2, '.', '');
            }            
        }
        $template = self::EMAIL_TO_RECIPIENT_TEMPLATE_XML_PATH; //get template path
        $email = $model->getCustomerEmail(); //get customer email
        $date = $model->getCreatedTime(); //get created time
        $date = date('F j, Y', strtotime($date));
        //To create new object
        $postObject = new Varien_Object();
        //set data in the mail template
        $postObject->setData('product_details', $productDetails);
        $postObject->setData('subtotal', $grandTotal);
        $postObject->setData('name', $name);
        $postObject->setData('date', $date);
        //function call for send mail
        $this->_sendEmailTransaction($email, $name, $template, $postObject->getData());
    }

    /**
     * fucntion to send followup mail to customers
     */
    protected function _sendEmailTransaction($emailto, $name, $template, $data) {
        $templateId = Mage::getStoreConfig($template); //get template id
        /* set sender given by admin */
        $sender = Mage::getStoreConfig('customerfollowup/general/sender_email_id');
        $senderEmail = Mage::getStoreConfig("trans_email/ident_$sender/email"); //get sender email
        $senderName = Mage::getStoreConfig("trans_email/ident_$sender/name"); //get sender name

        $sender = array('name' => $senderName, 'email' => $senderEmail);
        try {
            /* @var $mailTemplate Mage_Core_Model_Email_Template */
            $mailTemplate = Mage::getModel('core/email_template');
            /* Send Transactional Email */
            $mailTemplate->sendTransactional(
                    $templateId,
                    $sender,
                    $emailto,
                    $name,
                    $data
            );
        } catch (Exception $e) {
            $this->_getSession()->addError(Mage::helper('customerfollowup')->__("Email can not send !"));
        }
    }

}