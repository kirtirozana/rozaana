
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
 * @package     Apptha_Advancedreports
 * @version     0.2.1
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 * 
 */

// For individual product report

function product_individual(product_id)
{
    var js_product_ajax_url = jq("#product_ajax_url").val();
    var product_option = 'gross';
    var active = 'day';
    if (jq('#product_report_month_select').hasClass("active")) {
        active = 'month';
    }
    else if (jq('#product_report_week_select').hasClass("active"))
    {
        active = 'week';
    }
    else
    {
        active = 'day';
    }
    if (jq('#li_product_tax_exc').hasClass("active")) {
        product_option = 'net';
    }
    else
    {
        product_option = 'gross';
    }
    var ajax_data = '';
    var date_range_option = jq("#product-date-range-select").val();
    if (date_range_option != 'Custom')
    {
        ajax_data = 'page=product&date_range_option=' + date_range_option + '&product_option=' + product_option + '&active=' + active + '&report_product_id=' + product_id;
    }
    else
    {
        var custom_date_from = jq("#product-revenue-custom-date-from").val();
        var custom_date_to = jq("#product-revenue-custom-date-to").val();
        ajax_data = 'page=product&date_range_option=' + date_range_option + '&from=' + custom_date_from + '&to=' + custom_date_to + '&product_option=' + product_option + '&active=' + active + '&report_product_id=' + product_id;
    }

    jq("#advanced_report_main_ajax_load").show();
    jq("#product-report-date-picker").hide();

    jq.ajax({
        type: "GET",
        url: js_product_ajax_url,
        data: ajax_data,
        success: function(data) {
            jq("#advanced_report_main_ajax_load").hide();
            jq('#advancedreports_tabs_product_section_content').html(data);
        }
    });
}

// Reset product datepicker

function productcancel()
{
    document.getElementById('product-date-range-select').value = document.getElementById('product_reset_dropdown').value;
    document.getElementById("product-report-err-msg").style.display = "none";
    document.getElementById('product-custom-date-from').value = '';
    document.getElementById('product-custom-date-to').value = '';
    var value = document.getElementById('product-date-range-select').value;
    if (value == 'Custom')
    {
        document.getElementById('product-custom-date-from').disabled = false;
        document.getElementById('product-custom-date-to').disabled = false;
    }
    else
    {
        document.getElementById('product-custom-date-from').disabled = true;
        document.getElementById('product-custom-date-to').disabled = true;
    }
}


// Product tax/shipping exclude ajax

function product_exc_id_click()
{
    if (jq('#li_product_tax_exc').hasClass("inactive"))
    {
        jq("#advanced_report_main_ajax_load").show();

        var js_advanced_indiviudal_productid = jq("#advanced-individual-product-id").val();

        var js_product_ajax_url = jq("#product_ajax_url").val();

        var active = 'day';

        if (jq('#product_report_month_select').hasClass("active")) {
            active = 'month';
        }
        else if (jq('#product_report_week_select').hasClass("active"))
        {
            active = 'week';
        }
        else
        {
            active = 'day';
        }

        var ajax_data = '';
        var date_range_option = jq("#product-date-range-select").val();

        if (date_range_option != 'Custom')
        {
            ajax_data = 'page=product&date_range_option=' + date_range_option + '&product_option=net&active=' + active + '&report_product_id=' + js_advanced_indiviudal_productid;
        }
        else
        {
            var custom_date_from = jq("#product-revenue-custom-date-from").val();
            var custom_date_to = jq("#product-revenue-custom-date-to").val();
            ajax_data = 'page=product&date_range_option=' + date_range_option + '&from=' + custom_date_from + '&to=' + custom_date_to + '&product_option=net&active=' + active + '&report_product_id=' + js_advanced_indiviudal_productid;
        }

        jq.ajax({
            type: "GET",
            url: js_product_ajax_url,
            data: ajax_data,
            success: function(data) {
                jq("#advanced_report_main_ajax_load").hide();
                jq('#advancedreports_tabs_product_section_content').html(data);
            }
        });
    }
}





// Product tax/shipping include ajax

function product_inc_id_click()
{

    if (jq('#li_product_tax_inc').hasClass("inactive"))
    {
        jq("#advanced_report_main_ajax_load").show();
        var js_advanced_indiviudal_productid = jq("#advanced-individual-product-id").val();
        var js_product_ajax_url = jq("#product_ajax_url").val();
        var active = 'day';

        if (jq('#product_report_month_select').hasClass("active")) {
            active = 'month';
        }
        else if (jq('#product_report_week_select').hasClass("active"))
        {
            active = 'week';
        }
        else
        {
            active = 'day';
        }

        var ajax_data = '';
        var date_range_option = jq("#product-date-range-select").val();

        if (date_range_option != 'Custom')
        {
            ajax_data = 'page=product&date_range_option=' + date_range_option + '&product_option=gross&active=' + active + '&report_product_id=' + js_advanced_indiviudal_productid;
        }
        else
        {
            var custom_date_from = jq("#product-revenue-custom-date-from").val();
            var custom_date_to = jq("#product-revenue-custom-date-to").val();
            ajax_data = 'page=product&date_range_option=' + date_range_option + '&from=' + custom_date_from + '&to=' + custom_date_to + '&product_option=gross&active=' + active + '&report_product_id=' + js_advanced_indiviudal_productid;
        }

        jq.ajax({
            type: "GET",
            url: js_product_ajax_url,
            data: ajax_data,
            success: function(data) {
                jq("#advanced_report_main_ajax_load").hide();
                jq('#advancedreports_tabs_product_section_content').html(data);
            }
        });
    }
}


// Product report apply function

function product_report_apply_click()
{

    var js_product_ajax_url = jq("#product_ajax_url").val();

    var js_advanced_indiviudal_productid = jq("#advanced-individual-product-id").val();

    var product_option = 'gross';
    var active = 'day';

    if (jq('#product_report_month_select').hasClass("active")) {
        active = 'month';
    }
    else if (jq('#product_report_week_select').hasClass("active"))
    {
        active = 'week';
    }
    else
    {
        active = 'day';
    }

    if (jq('#li_product_tax_exc').hasClass("active")) {
        product_option = 'net';
    }
    else
    {
        product_option = 'gross';
    }


    var ajax_data = '';
    var date_range_option = jq("#product-date-range-select").val();

    if (date_range_option != 'Custom')
    {
        ajax_data = 'page=product&date_range_option=' + date_range_option + '&product_option=' + product_option + '&active=' + active + '&report_product_id=' + js_advanced_indiviudal_productid + '&advanced_session=1';
    }
    else
    {
        if (jq("#product-custom-date-from").val() == '' || jq("#product-custom-date-to").val() == '')
        {
            jq("#product-report-err-msg").show();
            return;
        }
        else
        {
            var custom_date_from = jq("#product-custom-date-from").val();
            var custom_date_to = jq("#product-custom-date-to").val();
        }
        ajax_data = 'page=product&date_range_option=' + date_range_option + '&from=' + custom_date_from + '&to=' + custom_date_to + '&product_option=' + product_option + '&active=' + active + '&report_product_id=' + js_advanced_indiviudal_productid + '&advanced_session=1';
    }

    jq("#advanced_report_main_ajax_load").show();
    jq("#product-report-date-picker").hide();

    jq.ajax({
        type: "GET",
        url: js_product_ajax_url,
        data: ajax_data,
        success: function(data) {
            jq("#advanced_report_main_ajax_load").hide();
            jq('#advancedreports_tabs_product_section_content').html(data);
        }
    });

}



// Reset salesd datepicker
function salescancel() {

    document.getElementById('date-range-select').value = document.getElementById('sales_reset_dropdown').value;
    document.getElementById("report-err-msg").style.display = "none";
    document.getElementById('custom-date-from').value = '';
    document.getElementById('custom-date-to').value = '';

    var value = document.getElementById('date-range-select').value;
    if (value == 'Custom')
    {
        document.getElementById('custom-date-from').disabled = false;
        document.getElementById('custom-date-to').disabled = false;
    }
    else
    {
        document.getElementById('custom-date-from').disabled = true;
        document.getElementById('custom-date-to').disabled = true;
    }

}



// Sales tax/shipping exclude ajax

function sales_exc_id_click() {

    if (jq('#li_sales_tax_exc').hasClass("inactive"))
    {
        jq("#advanced_report_main_ajax_load").show();
        var js_sales_ajax_url = jq("#sales_ajax_url").val();

        var active = 'day';

        if (jq('#report_month_select').hasClass("active")) {
            active = 'month';
        }
        else if (jq('#report_week_select').hasClass("active"))
        {
            active = 'week';
        }
        else
        {
            active = 'day';
        }

        var ajax_data = '';
        var date_range_option = jq("#date-range-select").val();

        if (date_range_option != 'Custom')
        {
            ajax_data = 'page=sales&date_range_option=' + date_range_option + '&revenue_option=net&active=' + active;
        }
        else
        {
            var custom_date_from = jq("#revenue-custom-date-from").val();
            var custom_date_to = jq("#revenue-custom-date-to").val();

            ajax_data = 'page=sales&date_range_option=' + date_range_option + '&from=' + custom_date_from + '&to=' + custom_date_to + '&revenue_option=net&active=' + active;
        }

        jq.ajax({
            type: "GET",
            url: js_sales_ajax_url,
            data: ajax_data,
            success: function(data) {
                jq("#advanced_report_main_ajax_load").hide();
                jq('#advancedreports_tabs_sales_section_content').html(data);
            }
        });
    }
}



// Sales tax/shipping include ajax

function sales_inc_id_click() {

    if (jq('#li_sales_tax_inc').hasClass("inactive"))
    {
        jq("#advanced_report_main_ajax_load").show();
        var js_sales_ajax_url = jq("#sales_ajax_url").val();
        var active = 'day';

        if (jq('#report_month_select').hasClass("active")) {
            active = 'month';
        }
        else if (jq('#report_week_select').hasClass("active"))
        {
            active = 'week';
        }
        else
        {
            active = 'day';
        }

        var ajax_data = '';
        var date_range_option = jq("#date-range-select").val();

        if (date_range_option != 'Custom')
        {
            ajax_data = 'page=sales&date_range_option=' + date_range_option + '&revenue_option=gross&active=' + active;
        }
        else
        {
            var custom_date_from = jq("#revenue-custom-date-from").val();
            var custom_date_to = jq("#revenue-custom-date-to").val();

            ajax_data = 'page=sales&date_range_option=' + date_range_option + '&from=' + custom_date_from + '&to=' + custom_date_to + '&revenue_option=gross&active=' + active;
        }

        jq.ajax({
            type: "GET",
            url: js_sales_ajax_url,
            data: ajax_data,
            success: function(data) {
                jq("#advanced_report_main_ajax_load").hide();
                jq('#advancedreports_tabs_sales_section_content').html(data);
            }
        });
    }
}



// Sales report apply function

function sales_report_apply_click() {


    var js_sales_ajax_url = jq("#sales_ajax_url").val();
    var revenue_option = 'gross';
    var active = 'day';

    if (jq('#report_month_select').hasClass("active")) {
        active = 'month';
    }
    else if (jq('#report_week_select').hasClass("active"))
    {
        active = 'week';
    }
    else
    {
        active = 'day';
    }

    if (jq('#li_sales_tax_exc').hasClass("active")) {
        revenue_option = 'net';
    }
    else
    {
        revenue_option = 'gross';
    }


    var ajax_data = '';
    var date_range_option = jq("#date-range-select").val();

    if (date_range_option != 'Custom')
    {
        ajax_data = 'page=sales&date_range_option=' + date_range_option + '&revenue_option=' + revenue_option + '&active=' + active + '&advanced_session=1';
    }
    else
    {

        if (jq("#custom-date-from").val() == '' || jq("#custom-date-to").val() == '')
        {
            jq("#report-err-msg").show();
            return;
        }
        else
        {
            var custom_date_from = jq("#custom-date-from").val();
            var custom_date_to = jq("#custom-date-to").val();
        }
        ajax_data = 'page=sales&date_range_option=' + date_range_option + '&from=' + custom_date_from + '&to=' + custom_date_to + '&revenue_option=' + revenue_option + '&active=' + active + '&advanced_session=1';


    }

    jq("#advanced_report_main_ajax_load").show();
    jq("#report-date-picker").hide();

    jq.ajax({
        type: "GET",
        url: js_sales_ajax_url,
        data: ajax_data,
        success: function(data) {
            jq("#advanced_report_main_ajax_load").hide();
            jq('#advancedreports_tabs_sales_section_content').html(data);
        }
    });
}

// Reset transaction dates
function transactionscancel() {

    document.getElementById('transactions-date-range-select').value = document.getElementById('transactions_reset_dropdown').value;
    document.getElementById("transactions-report-err-msg").style.display = "none";

    document.getElementById('transactions-custom-date-from').value = '';
    document.getElementById('transactions-custom-date-to').value = '';

    var value = document.getElementById('transactions-date-range-select').value;
    if (value == 'Custom')
    {
        document.getElementById('transactions-custom-date-from').disabled = false;
        document.getElementById('transactions-custom-date-to').disabled = false;
    }
    else
    {
        document.getElementById('transactions-custom-date-from').disabled = true;
        document.getElementById('transactions-custom-date-to').disabled = true;
    }

}


// Transactions tax/shipping exclude ajax


function transactions_exc_id_click() {

    if (jq('#li_transactions_tax_exc').hasClass("inactive"))
    {
        jq("#advanced_report_main_ajax_load").show();
        var js_transactions_ajax_url = jq("#transactions_ajax_url").val();

        var active = 'day';

        if (jq('#transactions_report_month_select').hasClass("active")) {
            active = 'month';
        }
        else if (jq('#transactions_report_week_select').hasClass("active"))
        {
            active = 'week';
        }
        else
        {
            active = 'day';
        }

        var ajax_data = '';
        var date_range_option = jq("#transactions-date-range-select").val();

        if (date_range_option != 'Custom')
        {
            ajax_data = 'page=transactions&date_range_option=' + date_range_option + '&transaction_option=net&active=' + active;
        }
        else
        {
            var custom_date_from = jq("#transaction-revenue-custom-date-from").val();
            var custom_date_to = jq("#transaction-revenue-custom-date-to").val();
            ajax_data = 'page=transactions&date_range_option=' + date_range_option + '&from=' + custom_date_from + '&to=' + custom_date_to + '&transaction_option=net&active=' + active;
        }

        jq.ajax({
            type: "GET",
            url: js_transactions_ajax_url,
            data: ajax_data,
            success: function(data) {
                jq("#advanced_report_main_ajax_load").hide();
                jq('#advancedreports_tabs_transactions_section_content').html(data);
            }
        });
    }

}

// Transactions tax/shipping include ajax

function transactions_inc_id_click() {

    if (jq('#li_transactions_tax_inc').hasClass("inactive"))
    {
        jq("#advanced_report_main_ajax_load").show();
        var js_transactions_ajax_url = jq("#transactions_ajax_url").val();
        var active = 'day';

        if (jq('#transactions_report_month_select').hasClass("active")) {
            active = 'month';
        }
        else if (jq('#transactions_report_week_select').hasClass("active"))
        {
            active = 'week';
        }
        else
        {
            active = 'day';
        }

        var ajax_data = '';
        var date_range_option = jq("#transactions-date-range-select").val();

        if (date_range_option != 'Custom')
        {
            ajax_data = 'page=transactins&date_range_option=' + date_range_option + '&transaction_option=gross&active=' + active;
        }
        else
        {
            var custom_date_from = jq("#transaction-revenue-custom-date-from").val();
            var custom_date_to = jq("#transaction-revenue-custom-date-to").val();

            ajax_data = 'page=transactions&date_range_option=' + date_range_option + '&from=' + custom_date_from + '&to=' + custom_date_to + '&transaction_option=gross&active=' + active;
        }
        jq.ajax({
            type: "GET",
            url: js_transactions_ajax_url,
            data: ajax_data,
            success: function(data) {
                jq("#advanced_report_main_ajax_load").hide();
                jq('#advancedreports_tabs_transactions_section_content').html(data);
            }
        });
    }
}

// Transactions report apply function

function transactions_report_apply_click() {

    var js_transactions_ajax_url = jq("#transactions_ajax_url").val();

    var transaction_option = 'gross';
    var active = 'day';

    if (jq('#transactions_report_month_select').hasClass("active")) {
        active = 'month';
    }
    else if (jq('#transactions_report_week_select').hasClass("active"))
    {
        active = 'week';
    }
    else
    {
        active = 'day';
    }

    if (jq('#li_transactions_tax_exc').hasClass("active")) {
        transaction_option = 'net';
    }
    else
    {
        transaction_option = 'gross';
    }


    var ajax_data = '';
    var date_range_option = jq("#transactions-date-range-select").val();
    if (date_range_option != 'Custom')
    {
        ajax_data = 'page=transactions&date_range_option=' + date_range_option + '&transaction_option=' + transaction_option + '&active=' + active + '&advanced_session=1';
    }
    else
    {

        if (jq("#transactions-custom-date-from").val() == '' || jq("#transactions-custom-date-to").val() == '')
        {
            jq("#transactions-report-err-msg").show();
            return;
        }
        else
        {
            var custom_date_from = jq("#transactions-custom-date-from").val();
            var custom_date_to = jq("#transactions-custom-date-to").val();
        }
        ajax_data = 'page=transactions&date_range_option=' + date_range_option + '&from=' + custom_date_from + '&to=' + custom_date_to + '&transaction_option=' + transaction_option + '&active=' + active + '&advanced_session=1';
    }

    jq("#advanced_report_main_ajax_load").show();
    jq("#transactions-report-date-picker").hide();


    jq.ajax({
        type: "GET",
        url: js_transactions_ajax_url,
        data: ajax_data,
        success: function(data) {
            jq("#advanced_report_main_ajax_load").hide();
            jq('#advancedreports_tabs_transactions_section_content').html(data);
        }
    });
}



// Time to purchase report apply function

function timetopurchase_report_apply_click() {


    var js_timetopurchase_ajax_url = jq("#timetopurchase_ajax_url").val();

    var ajax_data = '';
    var date_range_option = jq("#timetopurchase-date-range-select").val();

    if (date_range_option != 'Custom')
    {
        ajax_data = 'page=timetopurchase&date_range_option=' + date_range_option + '&advanced_session=1';
    }
    else
    {

        if (jq("#timetopurchase-custom-date-from").val() == '' || jq("#timetopurchase-custom-date-to").val() == '')
        {
            jq("#timetopurchase-report-err-msg").show();
            return;
        }
        else
        {
            var custom_date_from = jq("#timetopurchase-custom-date-from").val();
            var custom_date_to = jq("#timetopurchase-custom-date-to").val();
        }
        ajax_data = 'page=timetopurchase&date_range_option=' + date_range_option + '&from=' + custom_date_from + '&to=' + custom_date_to + '&advanced_session=1';
    }

    jq("#advanced_report_main_ajax_load").show();
    jq("#timetopurchase-report-date-picker").hide();

    jq.ajax({
        type: "GET",
        url: js_timetopurchase_ajax_url,
        data: ajax_data,
        success: function(data) {
            jq("#advanced_report_main_ajax_load").hide();
            jq('#advancedreports_tabs_timetopurchase_section_content').html(data);
        }
    });
}

// Reset timetopuchase dates

function timetopurchasecancel() {
    document.getElementById('timetopurchase-date-range-select').value = document.getElementById('timetopurchase_reset_dropdown').value;
    document.getElementById("timetopurchase-report-err-msg").style.display = "none";

    document.getElementById('timetopurchase-custom-date-from').value = '';
    document.getElementById('timetopurchase-custom-date-to').value = '';

    var value = document.getElementById('timetopurchase-date-range-select').value;
    if (value == 'Custom')
    {
        document.getElementById('timetopurchase-custom-date-from').disabled = false;
        document.getElementById('timetopurchase-custom-date-to').disabled = false;
    }
    else
    {
        document.getElementById('timetopurchase-custom-date-from').disabled = true;
        document.getElementById('timetopurchase-custom-date-to').disabled = true;
    }

}



function salesdatewise(individual_date)
{

    var js_sales_datewise_ajax_url = jq("#sales_datewise_ajax_url").val();


    var revenue_option = 'gross';
    var active = 'day';

    if (jq('#report_month_select').hasClass("active")) {
        active = 'month';
    }
    else if (jq('#report_week_select').hasClass("active"))
    {
        active = 'week';
    }
    else
    {
        active = 'day';
    }

    if (jq('#li_sales_tax_exc').hasClass("active")) {
        revenue_option = 'net';
    }
    else
    {
        revenue_option = 'gross';
    }
    var ajax_data = '';
    var date_range_option = jq("#date-range-select").val();

    var custom_date_from = jq("#revenue-custom-date-from").val();
    var custom_date_to = jq("#revenue-custom-date-to").val();
    ajax_data = 'page=saleswise&date_range_option=' + date_range_option + '&from=' + custom_date_from + '&to=' + custom_date_to + '&revenue_option=' + revenue_option + '&active=' + active + '&individual_date=' + individual_date;

    jq("#advanced_report_main_ajax_load").show();
    jq("#report-date-picker").hide();

    jq.ajax({
        type: "GET",
        url: js_sales_datewise_ajax_url,
        data: ajax_data,
        success: function(data) {
            jq("#advanced_report_main_ajax_load").hide();
            jq('#advancedreports_tabs_sales_section_content').html(data);
        }
    });

}

// For back to sales report

function backtosalesreport()
{
    var js_sales_datewise_ajax_url = jq("#sales_ajax_url").val();
    var revenue_option = jq("#sales-wise-revenue-option").val();
    var active = jq("#sales-wise-active").val();
    var ajax_data = '';
    var date_range_option = jq("#sales-wise-date-range-option").val();
    var custom_date_from = jq("#sales-wise-custom-date-from").val();
    var custom_date_to = jq("#sales-wise-custom-date-to").val();

    ajax_data = 'page=sales&date_range_option=' + date_range_option + '&from=' + custom_date_from + '&to=' + custom_date_to + '&revenue_option=' + revenue_option + '&active=' + active;

    jq("#advanced_report_main_ajax_load").show();

    jq.ajax({
        type: "GET",
        url: js_sales_datewise_ajax_url,
        data: ajax_data,
        success: function(data) {
            jq("#advanced_report_main_ajax_load").hide();
            jq('#advancedreports_tabs_sales_section_content').html(data);
        }
    });
}

// Gainer report apply function

function gainer_report_apply_click() {


    var js_gainer_ajax_url = jq("#gainer_ajax_url").val();

    var ajax_data = '';
    var date_range_option = jq("#gainer-date-range-select").val();

    if (date_range_option != 'Custom')
    {
        ajax_data = 'page=gainer&date_range_option=' + date_range_option + '&advanced_session=1';
    }
    else
    {

        if (jq("#gainer-custom-date-from").val() == '' || jq("#gainer-custom-date-to").val() == '')
        {
            jq("#gainer-report-err-msg").show();
            return;
        }
        else
        {
            var custom_date_from = jq("#gainer-custom-date-from").val();
            var custom_date_to = jq("#gainer-custom-date-to").val();
        }
        ajax_data = 'page=gainer&date_range_option=' + date_range_option + '&from=' + custom_date_from + '&to=' + custom_date_to + '&advanced_session=1';
    }

    jq("#advanced_report_main_ajax_load").show();
    jq("#gainer-report-date-picker").hide();

    jq.ajax({
        type: "GET",
        url: js_gainer_ajax_url,
        data: ajax_data,
        success: function(data) {
            jq("#advanced_report_main_ajax_load").hide();
            jq('#advancedreports_tabs_gainer_section_content').html(data);
        }
    });
}

// Reset Gainer dates

function gainercancel() {
    document.getElementById('gainer-date-range-select').value = document.getElementById('gainer_reset_dropdown').value;
    document.getElementById("gainer-report-err-msg").style.display = "none";

    document.getElementById('gainer-custom-date-from').value = '';
    document.getElementById('gainer-custom-date-to').value = '';

    var value = document.getElementById('gainer-date-range-select').value;
    if (value == 'Custom')
    {
        document.getElementById('gainer-custom-date-from').disabled = false;
        document.getElementById('gainer-custom-date-to').disabled = false;
    }
    else
    {
        document.getElementById('gainer-custom-date-from').disabled = true;
        document.getElementById('gainer-custom-date-to').disabled = true;
    }

}


// Changing advanced reports by session dates

function advanced_report_session_date(report, url)
{
    var ajax_data = '';
    var js_product_ajax_url = url;
    var page = report;

    if (js_product_ajax_url == 'undefined' || js_product_ajax_url == '')
    {
        js_product_ajax_url = jq("#sales_ajax_url").val();
        page = 'sales';
    }

    ajax_data = 'page=' + page + '&advanced_report_date_session=1';

    if (page == 'sales')
    {
        jq("#advanced_report_main_ajax_load").show();
    }
    if (page == 'product')
    {
        jq("#advanced_report_main_ajax_load").show();
    }
    if (page == 'transactions')
    {
        jq("#advanced_report_main_ajax_load").show();
    }
    if (page == 'timetopurchase')
    {
        jq("#advanced_report_main_ajax_load").show();
    }
    if (page == 'topsellingproducts')
    {
        jq("#advanced_report_main_ajax_load").show();
    }
    if (page == 'gainer')
    {
        jq("#advanced_report_main_ajax_load").show();
    }

    if (page == 'followupproducts')
    {
        jq("#advanced_report_main_ajax_load").show();
    }

    jq.ajax({
        type: "GET",
        url: js_product_ajax_url,
        data: ajax_data,
        success: function(data) {

            if (page == 'sales')
            {
                jq("#advanced_report_main_ajax_load").hide();
                jq('#advancedreports_tabs_sales_section_content').html(data);
            }
            if (page == 'product')
            {
                jq("#advanced_report_main_ajax_load").hide();
                jq('#advancedreports_tabs_product_section_content').html(data);
            }
            if (page == 'transactions')
            {
                jq("#advanced_report_main_ajax_load").hide();
                jq('#advancedreports_tabs_transactions_section_content').html(data);
            }
            if (page == 'timetopurchase')
            {
                jq("#advanced_report_main_ajax_load").hide();
                jq('#advancedreports_tabs_timetopurchase_section_content').html(data);
            }
            if (page == 'topsellingproducts')
            {
                jq("#advanced_report_main_ajax_load").hide();
                jq('#advancedreports_tabs_topsellingproducts_section_content').html(data);
            }
            if (page == 'gainer')
            {
                jq("#advanced_report_main_ajax_load").hide();
                jq('#advancedreports_tabs_gainer_section_content').html(data);
            }

            if (page == 'followupproducts')
            {
                jq("#advanced_report_main_ajax_load").hide();
                jq('#advancedreports_tabs_followupproducts_section_content').html(data);
            }
        }

    });

}

function advancedreports_switcher_change(store_id) {


    if (store_id == '')
    {
        store_id = 0;
    }

    page = 'sales';

    if (jq('#advancedreports_tabs_sales_section').hasClass('active'))
    {
        page = 'sales';
    }
    if (jq('#advancedreports_tabs_product_section').hasClass('active'))
    {
        page = 'product';
    }
    if (jq('#advancedreports_tabs_transactions_section').hasClass('active'))
    {
        page = 'transactions';
    }
    if (jq('#advancedreports_tabs_timetopurchase_section').hasClass('active'))
    {
        page = 'timetopurchase';
    }
    if (jq('#advancedreports_tabs_topsellingproducts_section').hasClass('active'))
    {
        page = 'topsellingproducts';
    }
    if (jq('#advancedreports_tabs_gainer_section').hasClass('active'))
    {
        page = 'gainer';
    }

    if (jq('#advancedreports_tabs_followupproducts_section').hasClass('active'))
    {
        page = 'followupproducts';
    }


    self.location = jq('#advanced_switch_url').val() + 'page/' + page + '/advanced_storeid/' + store_id + '/';

}

// For follow up product report option

function followupProductsOption(value)
{
    if (value == "Today")
    {
        jq("#followup_report_day_select").addClass('active');
        jq("#followup_report_week_select").removeClass('active');
        jq("#followup_report_month_select").removeClass('active');
    }
    else if (value == "LastWeek")
    {
        jq("#followup_report_week_select").addClass('active');
        jq("#followup_report_day_select").removeClass('active');
        jq("#followup_report_month_select").removeClass('active');
    } else
    {
        jq("#followup_report_month_select").addClass('active');
        jq("#followup_report_day_select").removeClass('active');
        jq("#followup_report_week_select").removeClass('active');
    }
    var url = jq("#followupproducts_ajax_url").val() + '?date_range_option=' + value;
    var report = "followupproducts";
    advanced_report_session_date(report, url);
}


jq(document).ready(function() {

    jq("#advancedreports_tabs_sales_section").click(function() {
        var url = jq("#sales_ajax_url").val();
        var report = "sales";
        advanced_report_session_date(report, url);
    });

    jq("#advancedreports_tabs_product_section").click(function() {
        var url = jq("#product_ajax_url").val();
        var report = "product";
        advanced_report_session_date(report, url);
    });

    jq("#advancedreports_tabs_transactions_section").click(function() {
        var url = jq("#transactions_ajax_url").val();
        var report = "transactions";
        advanced_report_session_date(report, url);
    });

    jq("#advancedreports_tabs_timetopurchase_section").click(function() {
        var url = jq("#timetopurchase_ajax_url").val();
        var report = "timetopurchase";
        advanced_report_session_date(report, url);
    });


    jq("#advancedreports_tabs_topsellingproducts_section").click(function() {
        var url = jq("#topsellingproducts_ajax_url").val();
        var report = "topsellingproducts";
        advanced_report_session_date(report, url);
    });

    jq("#advancedreports_tabs_gainer_section").click(function() {
        var url = jq("#gainer_ajax_url").val();
        var report = "gainer";
        advanced_report_session_date(report, url);
    });


    jq("#advancedreports_tabs_followupproducts_section").click(function() {
        var url = jq("#followupproducts_ajax_url").val();
        var report = "followupproducts";
        advanced_report_session_date(report, url);
    });


    jq(".advancedreports_followupproducts").live("click", function() {

        var followup_update = jq('#advancedreports_updatefollowupproducts').val();
        var followup_remove = jq('#advancedreports_removefollowupproducts').val();
        var product_id = jq(this).val();


        if (jq(this).is(':checked')) {
            jq.ajax({
                type: "GET",
                url: followup_update + product_id,
                data: "",
                success: function(data) {
                }
            });

        } else {
            jq.ajax({
                type: "GET",
                url: followup_remove + product_id,
                data: "",
                success: function(data) {
                }
            });
        }
    });


// For tool tip

jq(".advance_followup").live('mouseenter',function(){
    jq(this).find('.checkbox_tooltip').stop().show();
    if( jq(this).find('.checkbox_tooltip input').is(':checked'))
    {    
    jq(this).find('.checkbox_tooltip em').html(Translator.translate('Do you want to unfollow up this product'));
    }
    else
    {
    jq(this).find('.checkbox_tooltip em').html(Translator.translate('Do you want to follow up this product')); 
    }    
});

jq(".advance_followup").live('mouseleave',function(e){
       
    if (!jq(this).find(".checkbox_tooltip").is(':hidden'))
    {    
    jq(this).find('.checkbox_tooltip').stop().fadeOut();       
    }
});
});
