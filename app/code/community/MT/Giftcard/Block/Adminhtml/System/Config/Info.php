<?php

class MT_Giftcard_Block_Adminhtml_System_Config_Info
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $config = $this->getConfig();
        $version = Mage::helper('giftcard')->getExtensionVersion();
        $html = '<h4>'.$config['name'].'. Version: <span style="color: #eb5e00">'.$version.'</span></h4>';
        $html .= '<div style="height: 40px"><a target="_blank"  style="margin-right: 10px; color: #494848; padding: 5px 10px; border: 1px solid #494848; text-decoration: none" href="'.$config['home'].'"/><b>Extension Page</b></a>'
            .'<a target="_blank" style="color: #494848; padding: 5px 10px; border: 1px solid #494848; text-decoration: none" href="'.$config['manual'].'"/><b>User Guide</b></a> </div>';

        $html .= '<p>You are using <span style="color: #eb5e00"><b>FREE</b></span> extension version.'
            .' Check it out, how many great features have <a  target="_blank" href="'.$config['paid_link'].'"/><span style="color: #eb5e00"><b>PRO</b></span></a> version!'
            .' <a style="margin: 0 0 0 10px; padding: 5px 10px; border: 1px solid #eb5e00; text-decoration: none" target="_blank" href="'.$config['compare'].'"><b>Compare</b></a></p>';
        $html .='<table border="0" style="padding-top: 5px"><tr>';
        $html .='<td><img src="'.$config['paid_img'].'" /></td>';
        $html .='<td style="padding: 0 0 0 20px"><div style="font-size: 16px; padding: 0 0 10px;"><b>'.$config['paid_name'].'</b></div>';
        $html .='<div style="height: 40px;"><a style="padding: 5px 10px; border: 1px solid #eb5e00; text-decoration: none" target="_blank" href="'.$config['paid_link'].'"><b>View Details</b></a>'
            .'<a style="margin: 0 0 0 10px; padding: 5px 10px; border: 1px solid #eb5e00; text-decoration: none" target="_blank" href="'.$config['paid_link'].'"><b>Get it Now</b></a></div>';
        $html .='<div><b>PRO version features</b></div>';
        $html .='<ul style="list-style: circle; padding-left: 16px;">';

        foreach ($config['features'] as $feature) {
            $html .='<li>'.$feature.'</li>';
        }


        $html .='</ul>';
        $html .='</td></tr></table>';
        return $this->_decorateRowHtml($element, $html);
    }


    public function getConfig()
    {
        return array(
            'name' => 'Gift Card (FREE)',
            'manual' => 'http://www.magetrend.com/gift-card-free/user-guide/?utm_source=m1gcf',
            'home' => 'http://www.magetrend.com/gift-card-free/?utm_source=m1gcf',
            'compare' => 'http://www.magetrend.com/gift-card-free/?utm_source=m1gcf#compare',
            'paid_name' => 'Gift Card PRO',
            'paid_link' => 'http://www.magetrend.com/gift-card/?utm_source=m1gcf',
            'paid_img' => 'http://www.magetrend.com/media/images/info/magento-gift-card.jpg',
            'features' => array(
                '2 x Gift Card Designs',
                'Plastic/Paper Gift Card Type',
                'Gift Card Code Generator',
                'Send by Post',
                'Send to Friend',
                'Gift Card Import Tool',
                'FREE Support & Money Back Guarantee',
            )
        );
    }


}