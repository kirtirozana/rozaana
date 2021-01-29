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
 * @package     Apptha_Deliveryschedule
 * @version     0.1.0
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2015 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 * */
/**
 * SelectDdate() - used to highlight the selected slot 
 */
function selectDdate(date, dtime, cost,k){
        document.getElementById('shipping_delivery_date').value = date;
        document.getElementById('shipping_delivery_time').value = dtime;
        document.getElementById('shipping_delivery_cost').value = cost;
        document.getElementById('roz_shipping_delivery_date').innerHTML = formatDate(date);
        document.getElementById('roz_shipping_delivery_time').innerHTML = formatTime(dtime,k);
        jQuery('#slideshow-holder ul li a').removeClass('ddate_day_active');
    }
/**
 * selectScheduleType() - for change the time slot based on schedule types
 */
function selectScheduleType(current){
    jQuery('#current_slot_slide').val(current);
    jQuery('.delivery_schedule_time').attr('style','display:none;');
    jQuery('#slidediv_'+current).attr('style','display:block;');
}



function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) 
        month = '0' + month;
    if (day.length < 2) 
        day = '0' + day;

    return [day, month,year].join('-');
}



function formatTime(time,k) 
{   var nodelistValue = document.querySelectorAll(".delivery_time > p");
    return nodelistValue[k].innerHTML;
}
