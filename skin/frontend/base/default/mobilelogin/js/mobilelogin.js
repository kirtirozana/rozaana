function phonenumber(inputtxt) {
    return true;
}

jQuery(document).ready(function () {
    jQuery(function () {
        jQuery('.mobilelogin-otpsend').on('keydown', '#mobilenumber', function (e) {
            -1 !== jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) || (/65|67|86|88/.test(e.keyCode) && (e.ctrlKey === true || e.metaKey === true)) && (!0 === e.ctrlKey || !0 === e.metaKey) || 35 <= e.keyCode && 40 >= e.keyCode || (e.shiftKey || 48 > e.keyCode || 57 < e.keyCode) && (96 > e.keyCode || 105 < e.keyCode) && e.preventDefault()
        });
    });
    jQuery('.mobilelogin-close').click(function () {
        jQuery('.mobilemodal').hide();
    });
    jQuery('#forgotEmail').on('click',function () {

        jQuery('.mobilemodal').hide();
        jQuery('.mobile-forgotpasswd').show();
    });

    var otptype = jQuery('#otptype').val();
    var billingpage=false;
    jQuery('#update_mobile').on('click',function () {
        jQuery('.mobilenumber-update').show();
    });
    jQuery(".sendotp").on('click', function () {
        var mobilenumber = jQuery('#mobilenumber').val();

        var id = jQuery(this).attr('id');
        var baseUrl = getBaseUrl();
        var isresend = 0;
        var oldmobile=0;
        if (id === "resendotp") {
            isresend = 1;
        }
        if(mobilenumber==""){
            jQuery('.mobilelogin-error').text('Field cannot be empty');
            jQuery('.mobilelogin-error').css("color", "red");
            jQuery('.mobilelogin-error').show().delay(5000).fadeOut();
        }
        else {

            if(otptype=="update")
            {
                oldmobile=jQuery('#old_mobilenumber').val();
            }
            if (phonenumber(mobilenumber)) {
                jQuery.ajax({
                    url: baseUrl + 'mobilelogin/mobilelogin/sendotp',
                    type: 'POST',
                    data: {
                        mobilenumber: mobilenumber,
                        logintype: otptype,
                        resendotp: isresend,
                        oldmobile:oldmobile
                    },
                    success: function (response) {
                        console.log(response)
                        var obj = jQuery.parseJSON(response);
                        if (obj.succeess === "true") {
                            jQuery('.mobilepopup-content').hide();
                            jQuery('#sendotp').hide();
                            jQuery('.footerlinks').hide();
                            jQuery('.mobile-otpverify').show();
                            jQuery('#mobilelogin-popup-message').text("OTP Sent Succeessfully");
                            jQuery('#mobilelogin-popup-message').css("color", "green");
                            jQuery('#mobilelogin-popup-message').show().delay(5000).fadeOut();
                        }
                        else {
                            jQuery('#mobilelogin-popup-message').text(obj.errormsg + "");
                            jQuery('#mobilelogin-popup-message').css("color", "red");
                            jQuery('#mobilelogin-popup-message').show().delay(5000).fadeOut();

                        }
                    }
                });
            }
            else {
                jQuery('.mobilelogin-error').show().delay(5000).fadeOut();
            }
        }

    });
    jQuery("#btnverifyotp").on('click', function () {

        var baseUrl = getBaseUrl();
        var mobilenumber = jQuery('#mobilenumber').val();
        var otpcode = jQuery('#inputotpverify').val();
        var isCheckout = jQuery('#ischeckout').val();
        var oldmobile;
        if(otptype=="update")
        {
            oldmobile=jQuery('#old_mobilenumber').val();
        }
        jQuery.ajax({
            url: baseUrl + 'mobilelogin/mobilelogin/verifyotp',
            type: 'POST',
            data: {
                mobilenumber: mobilenumber,
                verifytype: otptype,
                otpcode: otpcode,
                oldmobile:oldmobile
            },
            success: function (response) {
                var obj = jQuery.parseJSON(response);
                if (obj.succeess !== "") {
                    if(otptype==="register") {
                        jQuery('.mobilemodal').hide();
                        jQuery('#mobile_number').val(mobilenumber);
                        if(isCheckout==='ischeckout')
                        {
                            billingpage=true;
                            checkout.setMethod();
                            jQuery('#billing\\:telephone').val(mobilenumber);
                            jQuery('#billing\\:telephone').prop("readonly", true);
                        }
                        else {
                            jQuery('.mobilelogin-regisration').show();
                        }
                    }
                    if(otptype==="forgot"){
                        jQuery('.mobile-otpverify').hide();
                        jQuery('.mobile-password').show();
                        jQuery('.mobilepopup-title').text('Reset Password');
                    }
                    if(otptype==="login"){
                        jQuery('.mobilemodal').hide();
                        window.location.href=getBaseUrl()+'customer/account/';
                    }
                    if(otptype==="update"){
                        jQuery('.mobilenumber-update').hide();
                        window.location.reload();
                    }
                }
                else {
                    jQuery('#mobilelogin-popup-message').text(obj.errormsg);
                    jQuery('#mobilelogin-popup-message').css("color", "red");
                    jQuery('#mobilelogin-popup-message').show().delay(5000).fadeOut();
                }
            }
        });
    });
    jQuery('#changepassword').on('click', function () {
        var baseUrl = getBaseUrl();
        var mobilenumber = jQuery('#mobilenumber').val();
        var passwd = jQuery('#mobpassword').val();
        var repasswd = jQuery('#mobrepassword').val();
        var baseUrl = getBaseUrl();
        if (passwd !== repasswd) {
            jQuery('#mobilelogin-popup-message').text('Password Mismatch');
            jQuery('#mobilelogin-popup-message').css("color", "red");
            jQuery('#mobilelogin-popup-message').show().delay(5000).fadeOut();
        }
        else {
            jQuery.ajax({
                url: baseUrl + "mobilelogin/mobilelogin/forgotpassword",
                type: 'POST',
                async: false,
                data: {
                    mobilenumber: mobilenumber,
                    password: passwd
                },
                success: function (response) {
                    var obj = jQuery.parseJSON(response);
                    if (obj.url !== "") {
                        window.location.href = obj.url;
                    }
                    else {
                        jQuery('#mobilelogin-popup-message').text('Password not change');
                        jQuery('#mobilelogin-popup-message').css("color", "red");
                        jQuery('#mobilelogin-popup-message').show().delay(5000).fadeOut();
                    }
                }
            });

        }
    });
    jQuery('#btncheckout').on('click',function () {
        var radioValue = jQuery("input[name='checkout_method']:checked").val();
        if(radioValue==="register") {
            if(billingpage==true){
                checkout.setMethod();
            }
            else {
                jQuery('.mobilemodal.checkout-register').show();
            }
        }
        else
        {
            checkout.setMethod();
        }
    });
    jQuery(document).ajaxStart(function () {
        jQuery(".mobileloader .wait").css("display", "block");
    });

    jQuery(document).ajaxComplete(function () {
        jQuery(".mobileloader .wait").css("display", "none");
    });
});