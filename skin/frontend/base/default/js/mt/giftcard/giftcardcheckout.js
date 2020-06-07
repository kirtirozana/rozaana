var GiftCardCheckout = {

    config: {},

    isIWDCheckout: false,

    init: function(config)
    {
        GiftCardCheckout.config = config;
        GiftCardCheckout.updateEvents();
    },

    initPayment: function()
    {
        GiftCardCheckout.setAsPaymentMethod(GiftCardCheckout.config.zeroTotal);
        GiftCardCheckout.initEvents();
        GiftCardCheckout.updateEvents();
        GiftCardCheckout.endLoading();
    },

    initEvents: function()
    {
        jQuery('#p_method_giftcard').unbind('click').click(function(){
            if (jQuery(this).is(':checked'))
                jQuery('.gc-form-element').show();
            else {
                jQuery('.gc-form-element').hide();
                GiftCardCheckout.clearGiftCardCode();
                jQuery('input[name="apply_gift_card[]"]').removeAttr('checked');
            }
        });
    },

    addGiftCardCodeSubmit :function()
    {
        GiftCardCheckout.startLoading();
        var giftCardCode = jQuery('#gift_card_code').val();

        jQuery('#gc-applied-codes-error').hide();
        jQuery('#gc-please-wait').show();

        var formVisible = 0;
        if (jQuery('#p_method_giftcard').is(':checked'))
            formVisible = 1;

        jQuery.post(GiftCardCheckout.config.requestUrl+'addGiftCardCodeAjax', {
            is_form_visible: formVisible,
            giftcard_code: giftCardCode
        }, function(response){
            var data = jQuery.parseJSON(response);
            if (data.content != '') {
                jQuery('#checkout-payment-method-gift-card').html(data.content);
                GiftCardCheckout.setAsPaymentMethod(data.zeroTotal);
            }
            GiftCardCheckout.updateEvents();
            GiftCardCheckout.endLoading();

            if (GiftCardCheckout.isIWDCheckout){
                IWD.OPC.Checkout.pullReview();
            }
        });
    },

    addGiftCardCode :function(giftCardCode)
    {
        GiftCardCheckout.startLoading();
        jQuery.post(GiftCardCheckout.config.requestUrl+'addGiftCardCodeAjax', {
            giftcard_code: giftCardCode
        }, function(response){
            var data = jQuery.parseJSON(response);
            GiftCardCheckout.setAsPaymentMethod(data.zeroTotal);
            GiftCardCheckout.endLoading();

            if (GiftCardCheckout.isIWDCheckout){
                IWD.OPC.Checkout.pullReview();
            }
        });
    },

    removeGiftCardCode :function(giftCardCode)
    {
        GiftCardCheckout.startLoading();
        jQuery.post(GiftCardCheckout.config.requestUrl+'removeGiftCardCodeAjax', {
            giftcard_code: giftCardCode
        }, function(response){
            var data = jQuery.parseJSON(response);
            GiftCardCheckout.setAsPaymentMethod(data.zeroTotal);
            GiftCardCheckout.endLoading();

            if (GiftCardCheckout.isIWDCheckout){
                IWD.OPC.Checkout.pullReview();
            }
        });
    },

    clearGiftCardCode :function()
    {
        GiftCardCheckout.startLoading();
        jQuery.post(GiftCardCheckout.config.requestUrl+'clearGiftCardCodeAjax', {}, function(response){
            var data = jQuery.parseJSON(response);
            GiftCardCheckout.setAsPaymentMethod(data.zeroTotal);
            GiftCardCheckout.endLoading();

            if (GiftCardCheckout.isIWDCheckout){
                IWD.OPC.Checkout.pullReview();
            }
        });
    },

    updateEvents :function()
    {
        jQuery('input[name="apply_gift_card[]"]').unbind('click').click(function(){
            if (jQuery(this).is(':checked')) {
                GiftCardCheckout.addGiftCardCode(jQuery(this).val());
            } else {
                GiftCardCheckout.removeGiftCardCode(jQuery(this).val());
            }
        });
    },

    startLoading :function()
    {
        jQuery('#gc-please-wait').show();
        jQuery('.gc-cart-action button').attr('disabled', 'disabled');
        jQuery('input[name="apply_gift_card[]"]').attr('disabled', 'disabled');
        jQuery('#p_method_giftcard').attr('disabled', 'disabled');
        jQuery('#gift_card_code').attr('disabled', 'disabled');
    },

    endLoading :function()
    {
        jQuery('#gc-please-wait').hide();
        jQuery('.gc-cart-action button').removeAttr('disabled');
        jQuery('input[name="apply_gift_card[]"]').removeAttr('disabled');
        jQuery('#p_method_giftcard').removeAttr('disabled');
        jQuery('#gift_card_code').removeAttr('disabled');
    },

    hidePaymentMethod: function()
    {
        jQuery('#p_method_giftcard').parent().remove();
    },

    setAsPaymentMethod: function(condition)
    {
        if (condition == true) {
            setTimeout(function(){
                jQuery('#p_method_giftcard_hidden').trigger('click');
            }, 500);

        } else {
            jQuery('#p_method_giftcard_hidden').removeAttr('checked');
        }
    },

    iwdCheckoutKeyupFilter: function(event)
    {
        if (event.target.id == 'gift_card_code') {
            return false;
        }
        return true;
    },

    iwdCheckoutEvents: function()
    {
        if (!GiftCardCheckout.isIWDCheckout) {
            GiftCardCheckout.isIWDCheckout = true;
        }

    }
};

jQuery.noConflict();