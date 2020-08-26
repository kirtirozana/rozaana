window.onload = function() {
    if(document.getElementsByClassName('adminhtml-rewards-earning-rule-edit').length && document.getElementById('behavior_trigger')) {
        enableReferralOptions();
    }

    if(document.getElementsByClassName('adminhtml-rewards-earning-rule-edit').length && document.getElementById('earning_rule_tabs_product_section_content')) {
        enablePercentLabel(document.getElementById('earning_style'));
    }


}


/**
 * Function to control on-the-fly using of Referral customer properties in Earning points Behaviour rule
 */
function enableReferralOptions() {
    var status = true;
    if(document.getElementById('behavior_trigger').value == 'referred_customer_order' || document.getElementById('behavior_trigger').value == 'referred_customer_signup') {
        status = false;
    }

    var changers = document.getElementsByClassName('element-value-changer');
    for(var i = 0; i < changers.length; i++) {
        if(changers[i].name.indexOf('child') != -1) {
            var options = changers[i].options;
            for(var j = 0; j < options.length; j++) {
                if(options[j].text.indexOf('Referred:') != -1) {
                    options[j].disabled = status;
                }
            }
        }
    }
}

/**
 * Function to change on-the-fly label of field in Earning rule of Product type
 */

function enablePercentLabel(selectElement)
{
    var tableBody = selectElement.parentNode.parentNode.parentNode;
    var row = tableBody.getElementsByTagName('tr')[1];
    var cell = row.getElementsByTagName('td')[0];
    var label = cell.getElementsByTagName('label')[0];
    if(selectElement.value == 'earning_style_percent_price')
    {
        label.innerHTML = 'Price Percent (X) <span class="required">*</span>';
    } else {
        label.innerHTML = 'Number of Points (X) <span class="required">*</span>';
    }
}
