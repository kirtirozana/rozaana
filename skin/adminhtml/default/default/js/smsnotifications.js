
    jQuery(document).ready(function($){
      jQuery(".click-me").click(function(){
      //Translator.add('I have been translated.','<?php echo Mage::helper('smsnotifications/data')->__('I have been translated.')?>');
      jQuery("#popup-mpdal").addClass('open-popop');
    });

       jQuery(".close-popop").click(function(){
        
      jQuery("#popup-mpdal").removeClass('open-popop');
    });

});
    
