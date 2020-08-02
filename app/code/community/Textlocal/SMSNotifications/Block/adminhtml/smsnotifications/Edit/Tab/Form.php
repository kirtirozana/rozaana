<?php
$dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
$fieldset->addField('start_date', 'date', array(
'label' => Mage::helper('banner')->__('Start Date'),
'title' => Mage::helper('banner')->__('Start Date'),
'name' => 'start_date',
'image' => $this->getSkinUrl('images/grid-cal.gif'),
'format' => $dateFormatIso,
'value' => 'start_date',
//'required' => true,
));
$fieldset->addField('end_date', 'date', array(
'label' => Mage::helper('banner')->__('End Date'),
'title' => Mage::helper('banner')->__('End Date'),
'name' => 'end_date',
'image' => $this->getSkinUrl('images/grid-cal.gif'),
'format' => $dateFormatIso,
'value' => 'end_date',
//'required' => true,
));
?>