<?php 
class Textlocal_SMSNotifications_Block_Button extends Mage_Adminhtml_Block_System_Config_Form_Field
{

   public function __construct()
    {
              $this->_controller = 'adminhtml_button';
        $this->_blockGroup = 'button';
        $this->_headerText = Mage::helper('smsnotifications/data')->__('Order Export');
        $this->_addButtonLabel = Mage::helper('smsnotifications/data')->__('Export All Orders');
        parent::__construct();
      }

}
?>