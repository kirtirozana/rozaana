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
 * @package     Apptha_Advancedreports
 * @version     0.2.1
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 * 
 */
?>
<?php
class Apptha_Advancedreports_Adminhtml_AdvancedreportsController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('report/advancedreports')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

        return $this;
    }

    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    // For all reports tabs      
    public function editAction() {

        // Reset advanced reports session dates
        $from = date('Y-m-d', strtotime('-30 day'));
        $to = date('Y-m-d', strtotime("-1 today midnight"));

        if (Mage::getSingleton('core/session')->getAdvancedReportDateOption()) {
            Mage::getSingleton('core/session')->unsAdvancedReportDateOption();
            Mage::getSingleton('core/session')->unsAdvancedReportDateFrom();
            Mage::getSingleton('core/session')->unsAdvancedReportDateTo();
            Mage::getSingleton('core/session')->unsAdvancedReportStore();
        }
        Mage::getSingleton('core/session')->setAdvancedReportDateOption('Custom');
        Mage::getSingleton('core/session')->setAdvancedReportDateFrom($from);
        Mage::getSingleton('core/session')->setAdvancedReportDateTo($to);

        if (Mage::app()->getRequest()->getParam('advanced_storeid')) {
            $store_id = Mage::app()->getRequest()->getParam('advanced_storeid');
            Mage::getSingleton('core/session')->setAdvancedReportStore($store_id);
        } else {
            Mage::getSingleton('core/session')->setAdvancedReportStore(0);
        }


        $this->loadLayout();

        // Setting advanced reports menu active
        $this->_setActiveMenu('report/advancedreports');

        // Setting page title 
        $this->getLayout()->getBlock('head')->setTitle('Advanced Reports');

        $this->_addContent($this->getLayout()->createBlock('advancedreports/adminhtml_advancedreports_edit'))
                ->_addLeft($this->getLayout()->createBlock('advancedreports/adminhtml_advancedreports_edit_tabs'));

        $this->renderLayout();
    }

    // For advanced report page         
    public function newAction() {
        $this->_forward('edit');
    }

    public function advancedReportsAction() {

        // Calculating for csv / xml file to download report data.
        $from = $this->getRequest()->getParam('from');
        $to = $this->getRequest()->getParam('to');
        $report = $this->getRequest()->getParam('report');
        $contentType = 'application/octet-stream';
        switch ($report) {
            case "transactions_csv":
                $transaction_option_value = $this->getRequest()->getParam('transaction_option_value');

                // For transactions csv         
                $db_from = Mage::getModel('core/date')->gmtDate(null, strtotime($from));
                $db_to = Mage::getModel('core/date')->gmtDate(null, strtotime($to));

                $sales_status = array('complete', 'processing');


                if (Mage::getSingleton('core/session')->getAdvancedReportStore() == 0) {
                    $orderTotals = Mage::getResourceModel('sales/order_grid_collection')
                            ->addAttributeToFilter('main_table.status', array('in' => $sales_status))
                            ->addAttributeToFilter('main_table.created_at', array('from' => $db_from, 'to' => $db_to))
                            ->setOrder('main_table.created_at', 'desc');
                } else {
                    $store_id = Mage::getSingleton('core/session')->getAdvancedReportStore();
                    $orderTotals = Mage::getResourceModel('sales/order_grid_collection')
                            ->addAttributeToFilter('main_table.status', array('in' => $sales_status))
                            ->addAttributeToFilter('main_table.created_at', array('from' => $db_from, 'to' => $db_to))
                            ->addAttributeToFilter('main_table.store_id', $store_id)
                            ->setOrder('main_table.created_at', 'desc');
                }


                $salesFlatOrder = (string) Mage::getConfig()->getTablePrefix() . 'sales_flat_order';
                $orderTotals->getSelect()->join(array('sales_flat_order' => $salesFlatOrder), "(sales_flat_order.entity_id=main_table.entity_id )", array('sales_flat_order.base_tax_amount', 'sales_flat_order.base_shipping_amount', 'sales_flat_order.total_qty_ordered'));
                $fileName = $this->__('transactions') . '.csv';
                $content = '"' . $this->__('Transaction') . '","' . $this->__('Revenue') . '","' . $this->__('Tax') . '","' . $this->__('Shipping') . '","' . $this->__('Quantity') . '"' . "\n";
                $exportsno_csv = 1;
                $transactions_revenue_value = 0;
                foreach ($orderTotals as $value) {
                    if ($transaction_option_value == 0) {
                        $transactions_revenue_value = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value->getBaseGrandTotal(), 2);
                    } else {
                        $transactions_revenue_value = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value->getBaseGrandTotal() - ( $value->getBaseShippingAmount() + $value->getBaseTaxAmount()), 2);
                    }
                    $content.='"' . $value->getIncrementId() . '"';
                    $content.=',"' . $transactions_revenue_value . '"';
                    $content.=',"' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value->getBaseTaxAmount(), 2) . '"';
                    $content.=',"' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value->getBaseShippingAmount(), 2) . '"';
                    $content.=',"' . round($value->getTotalQtyOrdered()) . '"' . "\n";
                    $exportsno_csv = $exportsno_csv + 1;
                }
                break;
            case "product_csv":

                // For product csv
                $product_option_value = $this->getRequest()->getParam('product_option_value');
                $db_from = Mage::getModel('core/date')->gmtDate(null, strtotime($from));
                $db_to = Mage::getModel('core/date')->gmtDate(null, strtotime($to));
                $orderTotals = Mage::getModel('sales/order_item')->getCollection();
                $orderTotals->getSelect()->joinInner(array('cp' => Mage::getSingleton('core/resource')->getTableName('sales/order_grid')), 'cp.entity_id = main_table.order_id AND cp.status IN ("complete","processing")', array('cp.created_at as sfog_created_at'));
                $orderTotals->addAttributeToFilter('cp.created_at', array('from' => $db_from, 'to' => $db_to));

                if (Mage::getSingleton('core/session')->getAdvancedReportStore() != 0) {
                    $store_id = Mage::getSingleton('core/session')->getAdvancedReportStore();
                    $orderTotals->addAttributeToFilter('cp.store_id', $store_id);
                }

                $obj = Mage::getModel('catalog/product');
                $product_report = array();
                $qty = array();
                $qty_month = array();
                $qty_week = array();
                foreach ($orderTotals as $value) {
                    $_product = $obj->load($value->getProductId());
                    $_product_name = $_product->getName();
                    if (empty($product_report[$_product_name]['name'])) {
                        $product_report[$_product_name]['name'] = $_product->getName();
                        $product_report[$_product_name]['qty'] = 0;
                        $product_report[$_product_name]['price'] = 0;
                        $product_report[$_product_name]['price_net'] = 0;
                        $product_report[$_product_name]['uni_qty'] = 0;
                    }
                    if (array_key_exists($_product_name, $product_report)) {
                        $product_report[$_product_name]['qty'] = $product_report[$_product_name]['qty'] + round($value->getQtyOrdered());
                        $product_report[$_product_name]['price'] = $product_report[$_product_name]['price'] + ($value->getBaseOriginalPrice() * $value->getQtyOrdered()) - $value->getBaseDiscountAmount() + $value->getBaseTaxAmount();
                        $product_report[$_product_name]['price_net'] = $product_report[$_product_name]['price_net'] + ($value->getBaseOriginalPrice() * $value->getQtyOrdered()) - $value->getBaseDiscountAmount();
                        $product_report[$_product_name]['uni_qty'] = $product_report[$_product_name]['uni_qty'] + 1;
                    } else {
                        $product_report[$_product_name]['qty'] = round($value->getQtyOrdered());
                        $product_report[$_product_name]['price'] = ($value->getBaseOriginalPrice() * $value->getQtyOrdered()) - $value->getBaseDiscountAmount() + $value->getBaseTaxAmount();
                        $product_report[$_product_name]['price_net'] = ($value->getBaseOriginalPrice() * $value->getQtyOrdered()) - $value->getBaseDiscountAmount();
                        $product_report[$_product_name]['uni_qty'] = 1;
                    }
                }
                $fileName = $this->__('product') . '.csv';
                $content = '"' . $this->__('Product') . '","' . $this->__('Quantity') . '","' . $this->__('Unique Purchases') . '","' . $this->__('Product Revenue') . '","' . $this->__('Average Price') . '","' . $this->__('Average QTY') . '"' . "\n";
                $exportsno_csv = 1;
                foreach ($product_report as $value) {
                    $content.='"' . $value['name'] . '"';
                    $content.=',"' . $value['qty'] . '"';
                    $content.=',"' . $value['uni_qty'] . '"';
                    if ($product_option_value == 0) {
                        $content.=',"' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value['price'], 2) . '"';
                        $content.=',"' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value['price'] / $value['qty'], 2) . '"';
                    } else {
                        $content.=',"' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value['price_net'], 2) . '"';
                        $content.=',"' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value['price_net'] / $value['qty'], 2) . '"';
                    }
                    $content.=',"' . round($value['qty'] / $value['uni_qty'], 2) . '"' . "\n";
                    $exportsno_csv = $exportsno_csv + 1;
                }
                break;
            case "sales_csv":

                // For sales csv
                $revenue_option_value = $this->getRequest()->getParam('revenue_option_value');
                $db_from = Mage::getModel('core/date')->gmtDate(null, strtotime($from));
                $db_to = Mage::getModel('core/date')->gmtDate(null, strtotime($to));
                $sales_status = array('complete', 'processing');

                if (Mage::getSingleton('core/session')->getAdvancedReportStore() == 0) {
                    $orderTotals = Mage::getResourceModel('sales/order_grid_collection')
                            ->addAttributeToFilter('main_table.status', array('in' => $sales_status))
                            ->addAttributeToFilter('main_table.created_at', array('from' => $db_from, 'to' => $db_to))
                            ->setOrder('main_table.created_at', 'desc');
                } else {
                    $store_id = Mage::getSingleton('core/session')->getAdvancedReportStore();
                    $orderTotals = Mage::getResourceModel('sales/order_grid_collection')
                            ->addAttributeToFilter('main_table.status', array('in' => $sales_status))
                            ->addAttributeToFilter('main_table.created_at', array('from' => $db_from, 'to' => $db_to))
                            ->addAttributeToFilter('main_table.store_id', $store_id)
                            ->setOrder('main_table.created_at', 'desc');
                }

                $salesFlatOrder = (string) Mage::getConfig()->getTablePrefix() . 'sales_flat_order';
                $orderTotals->getSelect()->join(array('sales_flat_order' => $salesFlatOrder), "(sales_flat_order.entity_id=main_table.entity_id )", array('sales_flat_order.base_tax_amount', 'sales_flat_order.base_shipping_amount'));
                $revenue = array();
                $revenue_net = array();
                foreach ($orderTotals as $order) {

                    // Day wise gross
                    $date_r = Mage::getModel('core/date')->date(null, strtotime($order->getCreatedAt()));
                    $date_r = date('l, F j, Y', strtotime($date_r));
                    if (array_key_exists($date_r, $revenue)) {
                        $revenue[$date_r] = $revenue[$date_r] + $order->getBaseGrandTotal();
                    } else {
                        $revenue[$date_r] = $order->getBaseGrandTotal();
                    }

                    // Day wise net
                    $date_r = Mage::getModel('core/date')->date(null, strtotime($order->getCreatedAt()));
                    $date_r = date('l, F j, Y', strtotime($date_r));

                    if (array_key_exists($date_r, $revenue_net)) {
                        $revenue_net[$date_r] = $revenue_net[$date_r] + $order->getBaseGrandTotal() - ( $order->getBaseShippingAmount() + $order->getBaseTaxAmount());
                    } else {
                        $revenue_net[$date_r] = $order->getBaseGrandTotal() - ( $order->getBaseShippingAmount() + $order->getBaseTaxAmount());
                    }
                }
                $total_revenue_value = 0;
                $revenue_table_value = array();

                if ($revenue_option_value == 0) {
                    $revenue_table_value = $revenue;
                    $total_revenue_value = array_sum($revenue);
                } else {
                    $revenue_table_value = $revenue_net;
                    $total_revenue_value = array_sum($revenue_net);
                }
                $fileName = $this->__('sales') . '.csv';
                $content = '"' . $this->__('Date') . '","' . $this->__('Revenue') . '","' . $this->__('Percent %') . '"' . "\n";
                $exportsno_csv = 1;
                foreach ($revenue_table_value as $key => $value) {
                    $content.='"' . Date('M j, Y', strtotime($key)) . '"';
                    $content.=',"' . Mage::helper('core')->currency(round($value, 2), true, false) . '"';
                    $content.=',"' . round(($value / $total_revenue_value * 100), 2) . ' %"' . "\n";

                    $exportsno_csv = $exportsno_csv + 1;
                }
                break;
            case "timetopurchase_csv":

                //  timetopurchase csv	
                $db_from = Mage::getModel('core/date')->gmtDate(null, strtotime($from));
                $db_to = Mage::getModel('core/date')->gmtDate(null, strtotime($to));
                $sales_status = array('complete', 'processing');

                if (Mage::getSingleton('core/session')->getAdvancedReportStore() == 0) {
                    $carts = Mage::getResourceModel('sales/order_grid_collection')
                            ->addAttributeToFilter('main_table.status', array('in' => $sales_status))
                            ->addAttributeToFilter('main_table.created_at', array('from' => $db_from, 'to' => $db_to));
                } else {
                    $store_id = Mage::getSingleton('core/session')->getAdvancedReportStore();
                    $carts = Mage::getResourceModel('sales/order_grid_collection')
                            ->addAttributeToFilter('main_table.status', array('in' => $sales_status))
                            ->addAttributeToFilter('main_table.store_id', $store_id)
                            ->addAttributeToFilter('main_table.created_at', array('from' => $db_from, 'to' => $db_to));
                }

                $salesFlatQuote = (string) Mage::getConfig()->getTablePrefix() . 'sales_flat_quote';
                $carts->getSelect()->join(array('sales_flat_quote' => $salesFlatQuote), "(sales_flat_quote.reserved_order_id=main_table.increment_id)", array('sales_flat_quote.created_at as sfq_created_at', 'sales_flat_quote.updated_at as sfq_updated_at'));
                $timetopurchase = array();
                for ($inc = 0; $inc <= 10; $inc++) {
                    $timetopurchase[$inc] = 0;
                }
                $timetopurchase['11-25'] = 0;
                $timetopurchase['25+'] = 0;
                $i = 0;
                $j = 0;
                foreach ($carts as $value) {
                    $date_u = Mage::getModel('core/date')->date(null, strtotime($value->getSfqUpdatedAt()));
                    $date_c = Mage::getModel('core/date')->date(null, strtotime($value->getSfqCreatedAt()));
                    $datediff = abs(strtotime($date_u) - strtotime($date_c));
                    $nodays = intval(floor($datediff / 86400));
                    if ($nodays > 10 && $nodays <= 25) {
                        $timetopurchase['11-25'] = $i + 1;
                        $i = $i + 1;
                    } elseif ($nodays > 25) {
                        $timetopurchase['25+'] = $j + 1;
                        $j = $j + 1;
                    } else {
                        $timetopurchase[$nodays] = $timetopurchase[$nodays] + 1;
                    }
                }
                $total_timetopurchase = array_sum($timetopurchase);
                ksort($timetopurchase);
                $fileName = $this->__('time_to_purchase') . '.csv';
                $content = '"' . $this->__('Days to Transaction') . '","' . $this->__('Transactions') . '","' . $this->__('Percentage of total %') . '"' . "\n";
                foreach ($timetopurchase as $key => $value) {

                    // Resolving issue for Division by zero
                    if ($total_timetopurchase != 0) {
                        $content.='"' . $key . '"';
                        $content.=',"' . $value . '"';
                        $content.=',"' . round(($value / $total_timetopurchase * 100), 2) . ' %"' . "\n";
                    }
                }
                break;

            case "topsellingproducts_csv":

                //  topsellingproducts csv	
                // Assigning from and to date for top selling products report
                $topsellingproducts_from = date('Y-m-01', strtotime("-6 months"));
                $topsellingproducts_to = date('Y-m-t', strtotime("-1 months"));

                // Converting local time to db time
                $db_from = Mage::getModel('core/date')->gmtDate(null, strtotime($topsellingproducts_from));
                $db_to = Mage::getModel('core/date')->gmtDate(null, strtotime($topsellingproducts_to));

                $from_all_days = strtotime($topsellingproducts_from);
                $to_all_days = strtotime($topsellingproducts_to);

                // Initiliazing months array
                $months = array();

                // Calculating all months between given dates
                $tmp = date('mY', $to_all_days);
                $month_key = date('M, Y', $from_all_days);
                $months[$month_key] = 0;
                while ($from_all_days < $to_all_days) {

                    if ($from_all_days < 31) {
                        $from_all_days = strtotime(date('Y-m-d', $from_all_days) . ' +1 month');
                    } else {
                        $from_all_days = strtotime(date('Y-m-d', $from_all_days) . ' +15days');
                    }

                    if (date('mY', $from_all_days) != $tmp && ($from_all_days < strtotime($topsellingproducts_to))) {
                        $month_key = date('M, Y', $from_all_days);
                        $months[$month_key] = 0;
                    }
                }
                $month_key = date('M, Y', strtotime($topsellingproducts_to));
                $months[$month_key] = 0;


                $month_increment = 1;
                foreach ($months as $key => $month) {
                    $top_selling_product_month[$month_increment] = $key;
                    $month_increment = $month_increment + 1;
                }


                // Top selling products collection 
                $orderTotals = Mage::getModel('sales/order_item')->getCollection();
                $orderTotals->getSelect()->joinInner(array('cp' => Mage::getSingleton('core/resource')->getTableName('sales/order_grid')), 'cp.entity_id = main_table.order_id AND cp.status IN ("complete","processing")', array('cp.created_at as sfog_created_at'));
                $orderTotals->addAttributeToFilter('cp.created_at', array('from' => $db_from, 'to' => $db_to));

                if (Mage::getSingleton('core/session')->getAdvancedReportStore() != 0) {
                    $store_id = Mage::getSingleton('core/session')->getAdvancedReportStore();
                    $orderTotals->addAttributeToFilter('cp.store_id', $store_id);
                }

                // Product collection for getting product name
                $obj = Mage::getModel('catalog/product');
                $topsellingproducts_report = array();


                // Calculating product data
                foreach ($orderTotals as $value) {

                    $_product = $obj->load($value->getProductId());
                    $_product_name = $_product->getName();

                    // Assigning product name
                    if (empty($topsellingproducts_report[$_product_name]['name'])) {
                        $topsellingproducts_report[$_product_name]['name'] = $_product->getName();
                        $topsellingproducts_report[$_product_name]['id'] = $value->getProductId();
                        $topsellingproducts_report[$_product_name]['total_revenue'] = 0;
                        foreach ($months as $key => $month) {
                            $topsellingproducts_report[$_product_name][$key] = $month;
                        }
                    }

                    if (array_key_exists($_product_name, $topsellingproducts_report)) {

                        // Last sixth month data
                        $date_sixth_month = Mage::getModel('core/date')->date(null, strtotime($value->getSfogCreatedAt()));
                        $date_sixth_month = date('M, Y', strtotime($date_sixth_month));
                        if (array_key_exists($date_sixth_month, $topsellingproducts_report[$_product_name])) {
                            $topsellingproducts_report[$_product_name][$date_sixth_month] = $topsellingproducts_report[$_product_name][$date_sixth_month] + ($value->getBaseOriginalPrice() * $value->getQtyOrdered()) - $value->getBaseDiscountAmount();
                        } else {
                            $topsellingproducts_report[$_product_name][$date_sixth_month] = ($value->getBaseOriginalPrice() * $value->getQtyOrdered()) - $value->getBaseDiscountAmount();
                        }
                        $topsellingproducts_report[$_product_name]['total_revenue'] = $topsellingproducts_report[$_product_name]['total_revenue'] + ($value->getBaseOriginalPrice() * $value->getQtyOrdered()) - $value->getBaseDiscountAmount();
                    }
                }

                $sorted_topsellingproducts_report = Mage::helper('advancedreports')->sort_array_by_key($topsellingproducts_report, 'total_revenue');

                $sno = 1;

                if (!empty($top_selling_product_month[1])) {
                    $first_data = $top_selling_product_month[1];
                }
                if (!empty($top_selling_product_month[2])) {
                    $second_data = $top_selling_product_month[2];
                }
                if (!empty($top_selling_product_month[3])) {
                    $third_data = $top_selling_product_month[3];
                }
                if (!empty($top_selling_product_month[4])) {
                    $forth_data = $top_selling_product_month[4];
                }
                if (!empty($top_selling_product_month[5])) {
                    $fifth_data = $top_selling_product_month[5];
                }
                if (!empty($top_selling_product_month[6])) {
                    $sixth_data = $top_selling_product_month[6];
                }

                $fileName = $this->__('topsellingproducts') . '.csv';
                $content = '"' . $this->__('Product') . '","' . $first_data . '","' . $second_data . '","' . $third_data . '","' . $forth_data . '","' . $fifth_data . '","' . $sixth_data . '","' . $this->__('Total Revenue') . '"' . "\n";
                foreach ($sorted_topsellingproducts_report as $key => $value) {
                    $content.='"' . $value['name'] . '"';
                    $content.=',"' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value[$first_data], 2) . '"';
                    $content.=',"' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value[$second_data], 2) . '"';
                    $content.=',"' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value[$third_data], 2) . '"';
                    $content.=',"' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value[$forth_data], 2) . '"';
                    $content.=',"' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value[$fifth_data], 2) . '"';
                    $content.=',"' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value[$sixth_data], 2) . '"';
                    $content.=',"' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value['total_revenue'], 2) . '"';
                    $content.="\n";

                    $sno = $sno + 1;
                    if ($sno > 25) {
                        break;
                    }
                }
                break;


            case "gainer_csv":

                //  gainer csv	
                $gainer_start_date = strtotime($from);
                $gainer_end_date = strtotime($to);

                $gainer_dates_diff = abs($gainer_end_date - $gainer_start_date);

                $gainer_no_days = $gainer_dates_diff / 86400;
                $gainer_no_days = 0 - $gainer_no_days;

                $gainer_compare_start_date = date('Y-m-d', strtotime($from . ' ' . $gainer_no_days . ' day'));
                $gainer_compare_end_date = date('Y-m-d', strtotime($from));
                $compare_from_display = date('Y-m-d', strtotime($from . ' ' . $gainer_no_days . ' day'));
                $compare_to_display = date('Y-m-d', strtotime($from . ' - 1 day'));

                $from_display = date('Y-m-d', strtotime($from));
                $to_display = date('Y-m-d', strtotime($to . ' - 1 day'));

                // Converting local time to db time
                $db_from = Mage::getModel('core/date')->gmtDate(null, strtotime($gainer_compare_start_date));
                $db_to = Mage::getModel('core/date')->gmtDate(null, strtotime($to));

                // Gainer report collection 
                $orderTotals = Mage::getModel('sales/order_item')->getCollection();
                $orderTotals->getSelect()->joinInner(array('cp' => Mage::getSingleton('core/resource')->getTableName('sales/order_grid')), 'cp.entity_id = main_table.order_id AND cp.status IN ("complete","processing")', array('cp.created_at as sfog_created_at'));
                $orderTotals->addAttributeToFilter('cp.created_at', array('from' => $db_from, 'to' => $db_to));

                if (Mage::getSingleton('core/session')->getAdvancedReportStore() != 0) {
                    $store_id = Mage::getSingleton('core/session')->getAdvancedReportStore();
                    $orderTotals->addAttributeToFilter('cp.store_id', $store_id);
                }


                // Product collection for getting product name
                $obj = Mage::getModel('catalog/product');
                $gainer_report = array();

                // Calculating product data
                foreach ($orderTotals as $value) {

                    $_product = $obj->load($value->getProductId());
                    $_product_name = $_product->getName();

                    // Assigning product name
                    if (empty($gainer_report[$_product_name]['name'])) {
                        $gainer_report[$_product_name]['name'] = $_product->getName();
                        $gainer_report[$_product_name]['id'] = $value->getProductId();
                        $gainer_report[$_product_name]['revenue_diff'] = 0;
                        $gainer_report[$_product_name]['revenue_first'] = 0;
                        $gainer_report[$_product_name]['revenue_second'] = 0;
                    }

                    if (array_key_exists($_product_name, $gainer_report)) {
                        $purchased_date = Mage::getModel('core/date')->date(null, strtotime($value->getSfogCreatedAt()));
                        $purchased_date_value = strtotime($purchased_date);

                        if ($purchased_date_value >= $gainer_start_date && $purchased_date_value <= $gainer_end_date) {
                            $gainer_report[$_product_name]['revenue_second'] = $gainer_report[$_product_name]['revenue_second'] + ($value->getBaseOriginalPrice() * $value->getQtyOrdered()) - $value->getBaseDiscountAmount();
                        } else {
                            $gainer_report[$_product_name]['revenue_first'] = $gainer_report[$_product_name]['revenue_first'] + ($value->getBaseOriginalPrice() * $value->getQtyOrdered()) - $value->getBaseDiscountAmount();
                        }
                    }
                }

                $gainer_report_data = array();

                foreach ($gainer_report as $value) {
                    if ($value['revenue_second'] > $value['revenue_first']) {
                        $gainer_product = $value['name'];
                        $gainer_report_data[$gainer_product]['name'] = $value['name'];
                        $gainer_report_data[$gainer_product]['id'] = $value['id'];
                        $gainer_report_data[$gainer_product]['revenue_first'] = $value['revenue_first'];
                        $gainer_report_data[$gainer_product]['revenue_second'] = $value['revenue_second'];
                        $gainer_report_data[$gainer_product]['revenue_diff'] = $value['revenue_second'] - $value['revenue_first'];
                        $revenue_diff = $value['revenue_second'] - $value['revenue_first'];
                        if ($value['revenue_first'] != 0) {
                            $gainer_report_data[$gainer_product]['percent'] = $revenue_diff / $value['revenue_first'] * 100;
                        } else {
                            $gainer_report_data[$gainer_product]['percent'] = 100;
                        }
                    }
                }

                $sorted_gainer_report = Mage::helper('advancedreports')->sort_array_by_key($gainer_report_data, 'percent');

                $fileName = $this->__('gainerloser') . '.csv';
                $content = '"' . $this->__('Product') . '","' . Date('M j, Y', strtotime($compare_from_display)) . ' - ' . Date('M j, Y', strtotime($compare_to_display)) . '","' . Date('M j, Y', strtotime($from_display)) . ' - ' . Date('M j, Y', strtotime($to_display)) . '"' . "\n";

                $content.= "\n";
                $sno = 1;
                foreach ($sorted_gainer_report as $value) {
                    $content.='"' . $value['name'] . '"';
                    $content.=',"' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value['revenue_first'], 2) . '"';
                    $content.=',"' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value['revenue_second'], 2) . ' (+' . round($value['percent'], 2) . ' %)' . '"';
                    $content.= "\n";
                    $sno = $sno + 1;

                    if ($sno > 10) {
                        break;
                    }
                }

                $content.= "\n";
                $loser_report_data = array();
                $revenue_diff = 0;
                foreach ($gainer_report as $value) {
                    if ($value['revenue_first'] > $value['revenue_second']) {
                        $loser_product = $value['name'];
                        $loser_report_data[$loser_product]['name'] = $value['name'];
                        $loser_report_data[$loser_product]['id'] = $value['id'];
                        $loser_report_data[$loser_product]['revenue_first'] = $value['revenue_first'];
                        $loser_report_data[$loser_product]['revenue_second'] = $value['revenue_second'];
                        $loser_report_data[$loser_product]['revenue_diff'] = $value['revenue_first'] - $value['revenue_second'];
                        $revenue_diff = $value['revenue_first'] - $value['revenue_second'];
                        if ($value['revenue_second'] != 0) {
                            $loser_report_data[$loser_product]['percent'] = $revenue_diff / $value['revenue_first'] * 100;
                        } else {
                            $loser_report_data[$loser_product]['percent'] = 100;
                        }
                    }
                }
                $sorted_loser_report = Mage::helper('advancedreports')->sort_array_by_key($loser_report_data, 'percent');

                $sno = 1;
                foreach ($sorted_loser_report as $value) {
                    $content.='"' . $value['name'] . '"';
                    $content.=',"' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value['revenue_first'], 2) . '"';
                    $content.=',"' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value['revenue_second'], 2) . ' (-' . round($value['percent'], 2) . ' %)' . '"';
                    $content.= "\n";
                    $sno = $sno + 1;

                    if ($sno > 10) {
                        break;
                    }
                }

                break;

            case "followupproducts_csv":

                $followup_start_date = $from;
                $followup_end_date = $from;

                if (!empty($_advanced_get_option)) {
                    if ($_advanced_get_option == 'Today') {
                        $followup_start_date = date('Y-m-d', strtotime($followup_end_date . ' - 1 day'));
                    } elseif ($_advanced_get_option == 'LastWeek') {
                        $followup_start_date = date('Y-m-d', strtotime($followup_end_date . ' - 7 day'));
                    } else {
                        $followup_start_date = date('Y-m-d', strtotime($followup_end_date . ' - 1 month'));
                    }
                } else {
                    $followup_start_date = date('Y-m-d', strtotime($followup_end_date . ' - 1 month'));
                }

                // For displaying dates
                $followup_from_display = $followup_start_date;
                $followup_to_display = date('Y-m-d', strtotime($followup_end_date . ' - 1 day'));

                $from_display = $from;
                $to_display = date('Y-m-d', strtotime($to . ' - 1 day'));

                // Converting local time to db time
                $db_from = Mage::getModel('core/date')->gmtDate(null, strtotime($followup_start_date));
                $db_to = Mage::getModel('core/date')->gmtDate(null, strtotime($to));

                // Getting all followup products
                $followup_products = Mage::helper('advancedreports')->getFollowupproductsCollection();

                if (!empty($followup_products)) {
                    $collections = Mage::getModel('sales/order_item')->getCollection()
                            ->addAttributeToFilter('main_table.product_id', $followup_products);
                    $collections->getSelect()->joinInner(array('cp' => Mage::getSingleton('core/resource')->getTableName('sales/order_grid')), 'cp.entity_id = main_table.order_id AND cp.status IN ("complete","processing")', array('cp.created_at as sfog_created_at'));
                    $collections->addAttributeToFilter('cp.created_at', array('from' => $db_from, 'to' => $db_to));

                    if (Mage::getSingleton('core/session')->getAdvancedReportStore() != 0) {
                        $store_id = Mage::getSingleton('core/session')->getAdvancedReportStore();
                        $collections->addAttributeToFilter('cp.store_id', $store_id);
                    }
                }


                // Product collection for getting product name
                $obj = Mage::getModel('catalog/product');
                $followup_report = array();

                // Calculating product data
                foreach ($collections as $value) {
                    $_product = $obj->load($value->getProductId());
                    $_product_name = $_product->getName();

                    // Assigning product name
                    if (empty($followup_report[$_product_name]['name'])) {
                        $followup_report[$_product_name]['name'] = $_product->getName();
                        $followup_report[$_product_name]['id'] = $value->getProductId();
                        $followup_report[$_product_name]['revenue_diff'] = 0;
                        $followup_report[$_product_name]['revenue_first'] = 0;
                        $followup_report[$_product_name]['revenue_second'] = 0;
                    }

                    if (array_key_exists($_product_name, $followup_report)) {
                        $purchased_date = Mage::getModel('core/date')->date(null, strtotime($value->getSfogCreatedAt()));
                        $purchased_date_value = strtotime($purchased_date);

                        if ($purchased_date_value >= $from && $purchased_date_value <= $to) {
                            $followup_report[$_product_name]['revenue_second'] = $followup_report[$_product_name]['revenue_second'] + ($value->getBaseOriginalPrice() * $value->getQtyOrdered()) - $value->getBaseDiscountAmount();
                        } else {
                            $followup_report[$_product_name]['revenue_first'] = $followup_report[$_product_name]['revenue_first'] + ($value->getBaseOriginalPrice() * $value->getQtyOrdered()) - $value->getBaseDiscountAmount();
                        }
                    }
                }


                // Assinging defualt values for unsold products

                foreach ($followup_products as $fproduct) {
                    $_product = $obj->load($fproduct);
                    $_product_name = $_product->getName();
                    if (!array_key_exists($_product_name, $followup_report)) {
                        $followup_report[$_product_name]['name'] = $_product->getName();
                        $followup_report[$_product_name]['id'] = $fproduct;
                        $followup_report[$_product_name]['revenue_diff'] = 0;
                        $followup_report[$_product_name]['revenue_first'] = 0;
                        $followup_report[$_product_name]['revenue_second'] = 0;
                    }
                }


                $followup_report_data = array();
                $revenue_diff = 0;
                foreach ($followup_report as $value) {
                    // Calculating gain percentage
                    if ($value['revenue_second'] > $value['revenue_first']) {
                        $followup_product = $value['name'];
                        $followup_report_data[$followup_product]['name'] = $value['name'];
                        $followup_report_data[$followup_product]['id'] = $value['id'];
                        $followup_report_data[$followup_product]['revenue_first'] = $value['revenue_first'];
                        $followup_report_data[$followup_product]['revenue_second'] = $value['revenue_second'];
                        $followup_report_data[$followup_product]['revenue_diff'] = $value['revenue_second'] - $value['revenue_first'];
                        $revenue_diff = $value['revenue_second'] - $value['revenue_first'];
                        $followup_report_data[$followup_product]['comparison'] = 'gainer';
                        
                        // For division by zero issue
                        if($value['revenue_first'] != 0)
                        {    
                        $followup_report_data[$followup_product]['percent'] = $revenue_diff / $value['revenue_first'] * 100;
                        }
                        elseif ($value['revenue_first'] == 0 && $revenue_diff != 0 ) {
                        $followup_report_data[$followup_product]['percent'] = 100;
                        }
                        else {
                        $followup_report_data[$followup_product]['percent']=0;    
                        }
                    } else {
                        // Calculating lose percentage   
                        $followup_product = $value['name'];
                        $followup_report_data[$followup_product]['name'] = $value['name'];
                        $followup_report_data[$followup_product]['id'] = $value['id'];
                        $followup_report_data[$followup_product]['revenue_first'] = $value['revenue_first'];
                        $followup_report_data[$followup_product]['revenue_second'] = $value['revenue_second'];
                        $followup_report_data[$followup_product]['revenue_diff'] = $value['revenue_first'] - $value['revenue_second'];
                        $revenue_diff = $value['revenue_first'] - $value['revenue_second'];
                        $followup_report_data[$followup_product]['comparison'] = 'loser';
                       if($value['revenue_first'] != 0)
                       {    
                        $followup_report_data[$followup_product]['percent'] = $revenue_diff / $value['revenue_first'] * 100;
                       }
                       elseif($value['revenue_second'] == 0 && $revenue_diff != 0) {
                       $followup_report_data[$followup_product]['percent'] = 100;
                       }
                       else {
                       $followup_report_data[$followup_product]['percent']=0;    
                       }
                    }
                }


                $fileName = $this->__('followupproducts') . '.csv';

                $content = '"' . $this->__('Product') . '","' . Date('M j, Y', strtotime($followup_from_display)) . ' - ' . Date('M j, Y', strtotime($followup_to_display)) . '","' . Date('M j, Y', strtotime($from_display)) . ' - ' . Date('M j, Y', strtotime($to_display)) . '"' . "\n";

                $sno = 1;
                foreach ($followup_report_data as $value) {

                    if ($value['comparison'] == 'gainer') {
                     if($value['percent'] != 0){   $percent = ' (+' . round($value['percent'], 2) . ' %)'; } else{ $percent = $value['percent'].'%'; }
                    } else {                       
                     if($value['percent'] != 0){ $percent = ' (-' . round($value['percent'], 2) . ' %)'; }else{ $percent = $value['percent'].'%';}
                    }

                    $content.='"' . $value['name'] . '"';
                    $content.=',"' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value['revenue_first'], 2) . '"';
                    $content.=',"' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value['revenue_second'], 2) .' '. $percent . '"';
                    $content.= "\n";
                    $sno = $sno + 1;
                }

                break;


            case "transactions_xml":

                // For transactions xml
                $transaction_option_value = $this->getRequest()->getParam('transaction_option_value');
                $db_from = Mage::getModel('core/date')->gmtDate(null, strtotime($from));
                $db_to = Mage::getModel('core/date')->gmtDate(null, strtotime($to));
                $sales_status = array('complete', 'processing');


                if (Mage::getSingleton('core/session')->getAdvancedReportStore() == 0) {
                    $orderTotals = Mage::getResourceModel('sales/order_grid_collection')
                            ->addAttributeToFilter('main_table.status', array('in' => $sales_status))
                            ->addAttributeToFilter('main_table.created_at', array('from' => $db_from, 'to' => $db_to))
                            ->setOrder('main_table.created_at', 'desc');
                } else {
                    $store_id = Mage::getSingleton('core/session')->getAdvancedReportStore();
                    $orderTotals = Mage::getResourceModel('sales/order_grid_collection')
                            ->addAttributeToFilter('main_table.status', array('in' => $sales_status))
                            ->addAttributeToFilter('main_table.created_at', array('from' => $db_from, 'to' => $db_to))
                            ->addAttributeToFilter('main_table.store_id', $store_id)
                            ->setOrder('main_table.created_at', 'desc');
                }

                $salesFlatOrder = (string) Mage::getConfig()->getTablePrefix() . 'sales_flat_order';
                $orderTotals->getSelect()->join(array('sales_flat_order' => $salesFlatOrder), "(sales_flat_order.entity_id=main_table.entity_id )", array('sales_flat_order.base_tax_amount', 'sales_flat_order.base_shipping_amount', 'sales_flat_order.total_qty_ordered'));
                $fileName = $this->__('transactions') . '.xml';
                $content = '<?xml version="1.0"?>';
                $content.='<transactions><header><Transaction>Transaction</Transaction><Revenue>Revenue</Revenue><Tax>Tax</Tax><Shipping>Shipping</Shipping><Quantity>Quantity</Quantity></header>';
                $exportsno_csv = 1;
                $transactions_revenue_value = 0;
                foreach ($orderTotals as $value) {
                    if ($transaction_option_value == 0) {
                        $transactions_revenue_value = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value->getBaseGrandTotal(), 2);
                    } else {
                        $transactions_revenue_value = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value->getBaseGrandTotal() - ( $value->getBaseShippingAmount() + $value->getBaseTaxAmount()), 2);
                    }
                    $content.='<Data><Transaction>' . $value->getIncrementId() . '</Transaction>';
                    $content.='<Revenue>' . $transactions_revenue_value . '</Revenue>';
                    $content.='<Tax>' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value->getBaseTaxAmount(), 2) . '</Tax>';
                    $content.='<Shipping>' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value->getBaseShippingAmount(), 2) . '</Shipping>';
                    $content.='<Quantity>' . round($value->getTotalQtyOrdered()) . '</Quantity></Data>';
                    $exportsno_csv = $exportsno_csv + 1;
                }
                $content.='</transactions>';
                break;
            case "product_xml":

                // For product xml
                $product_option_value = $this->getRequest()->getParam('product_option_value');
                $db_from = Mage::getModel('core/date')->gmtDate(null, strtotime($from));
                $db_to = Mage::getModel('core/date')->gmtDate(null, strtotime($to));
                $orderTotals = Mage::getModel('sales/order_item')->getCollection();
                $orderTotals->getSelect()->joinInner(array('cp' => Mage::getSingleton('core/resource')->getTableName('sales/order_grid')), 'cp.entity_id = main_table.order_id AND cp.status IN ("complete","processing")', array('cp.created_at as sfog_created_at'));
                $orderTotals->addAttributeToFilter('cp.created_at', array('from' => $db_from, 'to' => $db_to));

                if (Mage::getSingleton('core/session')->getAdvancedReportStore() != 0) {
                    $store_id = Mage::getSingleton('core/session')->getAdvancedReportStore();
                    $orderTotals->addAttributeToFilter('cp.store_id', $store_id);
                }

                $obj = Mage::getModel('catalog/product');
                $product_report = array();
                $qty = array();
                $qty_month = array();
                $qty_week = array();

                foreach ($orderTotals as $value) {
                    $_product = $obj->load($value->getProductId());
                    $_product_name = $_product->getName();
                    if (empty($product_report[$_product_name]['name'])) {
                        $product_report[$_product_name]['name'] = $_product->getName();
                        $product_report[$_product_name]['qty'] = 0;
                        $product_report[$_product_name]['price'] = 0;
                        $product_report[$_product_name]['price_net'] = 0;
                        $product_report[$_product_name]['uni_qty'] = 0;
                    }
                    if (array_key_exists($_product_name, $product_report)) {
                        $product_report[$_product_name]['qty'] = $product_report[$_product_name]['qty'] + round($value->getQtyOrdered());
                        $product_report[$_product_name]['price'] = $product_report[$_product_name]['price'] + ($value->getBaseOriginalPrice() * $value->getQtyOrdered()) - $value->getBaseDiscountAmount();
                        $product_report[$_product_name]['price_net'] = $product_report[$_product_name]['price_net'] + ($value->getBaseOriginalPrice() * $value->getQtyOrdered()) - ($value->getBaseDiscountAmount() + $value->getBaseTaxAmount());
                        $product_report[$_product_name]['uni_qty'] = $product_report[$_product_name]['uni_qty'] + 1;
                    } else {
                        $product_report[$_product_name]['qty'] = round($value->getQtyOrdered());
                        $product_report[$_product_name]['price'] = ($value->getBaseOriginalPrice() * $value->getQtyOrdered()) - $value->getBaseDiscountAmount();
                        $product_report[$_product_name]['price_net'] = ($value->getBaseOriginalPrice() * $value->getQtyOrdered()) - ( $value->getBaseDiscountAmount() + $value->getBaseTaxAmount());
                        $product_report[$_product_name]['uni_qty'] = 1;
                    }
                }
                $fileName = $this->__('product') . '.xml';
                $content = '<?xml version="1.0"?>';
                $content.='<product><header><Product>Product</Product><Quantity>Quantity</Quantity><UniquePurchases>Unique Purchases</UniquePurchases><ProductRevenue>Product Revenue</ProductRevenue><AveragePrice>Average Price</AveragePrice><AverageQTY>Average QTY</AverageQTY></header>';
                $exportsno_csv = 1;
                foreach ($product_report as $value) {
                    $content.='<Data><Product>' . $value['name'] . '</Product>';
                    $content.='<Quantity>' . $value['qty'] . '</Quantity>';
                    $content.='<UniquePurchases>' . $value['uni_qty'] . '</UniquePurchases>';
                    if ($product_option_value == 0) {
                        $content.='<ProductRevenue>' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value['price'], 2) . '</ProductRevenue>';
                        $content.='<AveragePrice>' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value['price'] / $value['qty'], 2) . '</AveragePrice>';
                    } else {
                        $content.='<ProductRevenue>' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value['price_net'], 2) . '</ProductRevenue>';
                        $content.='<AveragePrice>' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value['price_net'] / $value['qty'], 2) . '</AveragePrice>';
                    }
                    $content.='<ProductRevenue>' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value['price'], 2) . '</ProductRevenue>';
                    $content.='<AveragePrice>' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value['price'] / $value['qty'], 2) . '</AveragePrice>';
                    $content.='<AverageQTY>' . round($value['qty'] / $value['uni_qty'], 2) . '</AverageQTY></Data>';
                    $exportsno_csv = $exportsno_csv + 1;
                }
                $content.='</product>';
                break;
            case "sales_xml":

                // For sales xml
                $revenue_option_value = $this->getRequest()->getParam('revenue_option_value');
                $db_from = Mage::getModel('core/date')->gmtDate(null, strtotime($from));
                $db_to = Mage::getModel('core/date')->gmtDate(null, strtotime($to));
                $sales_status = array('complete', 'processing');

                if (Mage::getSingleton('core/session')->getAdvancedReportStore() == 0) {
                    $orderTotals = Mage::getResourceModel('sales/order_grid_collection')
                            ->addAttributeToFilter('main_table.status', array('in' => $sales_status))
                            ->addAttributeToFilter('main_table.created_at', array('from' => $db_from, 'to' => $db_to))
                            ->setOrder('main_table.created_at', 'desc');
                } else {
                    $store_id = Mage::getSingleton('core/session')->getAdvancedReportStore();
                    $orderTotals = Mage::getResourceModel('sales/order_grid_collection')
                            ->addAttributeToFilter('main_table.status', array('in' => $sales_status))
                            ->addAttributeToFilter('main_table.created_at', array('from' => $db_from, 'to' => $db_to))
                            ->addAttributeToFilter('main_table.store_id', $store_id)
                            ->setOrder('main_table.created_at', 'desc');
                }

                $salesFlatOrder = (string) Mage::getConfig()->getTablePrefix() . 'sales_flat_order';
                $orderTotals->getSelect()->join(array('sales_flat_order' => $salesFlatOrder), "(sales_flat_order.entity_id=main_table.entity_id )", array('sales_flat_order.base_tax_amount', 'sales_flat_order.base_shipping_amount'));
                $revenue = array();
                $revenue_net = array();
                foreach ($orderTotals as $order) {

                    // Day wise gross
                    $date_r = Mage::getModel('core/date')->date(null, strtotime($order->getCreatedAt()));
                    $date_r = date('l, F j, Y', strtotime($date_r));
                    if (array_key_exists($date_r, $revenue)) {
                        $revenue[$date_r] = $revenue[$date_r] + $order->getBaseGrandTotal();
                    } else {
                        $revenue[$date_r] = $order->getBaseGrandTotal();
                    }

                    // Day wise net
                    $date_r = Mage::getModel('core/date')->date(null, strtotime($order->getCreatedAt()));
                    $date_r = date('l, F j, Y', strtotime($date_r));

                    if (array_key_exists($date_r, $revenue_net)) {
                        $revenue_net[$date_r] = $revenue_net[$date_r] + $order->getBaseGrandTotal() - ( $order->getBaseShippingAmount() + $order->getBaseTaxAmount() );
                    } else {
                        $revenue_net[$date_r] = $order->getBaseGrandTotal() - ( $order->getBaseShippingAmount() + $order->getBaseTaxAmount());
                    }
                }
                $total_revenue_value = 0;
                $revenue_table_value = array();
                if ($revenue_option_value == 0) {
                    $revenue_table_value = $revenue;
                    $total_revenue_value = array_sum($revenue);
                } else {
                    $revenue_table_value = $revenue_net;
                    $total_revenue_value = array_sum($revenue_net);
                }
                $fileName = $this->__('sales') . '.xml';
                $content = '<?xml version="1.0"?>';
                $content.='<sales><header><Date>Date</Date><Revenue>Revenue</Revenue><Percent>Percent %</Percent></header>';
                $exportsno_csv = 1;
                foreach ($revenue_table_value as $key => $value) {
                    $content.='<Data><Date>' . Date('M j, Y', strtotime($key)) . '</Date>';
                    $content.='<Revenue>' . Mage::helper('core')->currency(round($value, 2), true, false) . '</Revenue>';
                    $content.='<Percent>' . round(($value / $total_revenue_value * 100), 2) . ' %</Percent></Data>';
                    $exportsno_csv = $exportsno_csv + 1;
                }
                $content.='</sales>';
                break;
            case "timetopurchase_xml":

                //  For timetopurchase xml
                $db_from = Mage::getModel('core/date')->gmtDate(null, strtotime($from));
                $db_to = Mage::getModel('core/date')->gmtDate(null, strtotime($to));
                $sales_status = array('complete', 'processing');

                if (Mage::getSingleton('core/session')->getAdvancedReportStore() == 0) {
                    $carts = Mage::getResourceModel('sales/order_grid_collection')
                            ->addAttributeToFilter('main_table.status', array('in' => $sales_status))
                            ->addAttributeToFilter('main_table.created_at', array('from' => $db_from, 'to' => $db_to));
                } else {
                    $store_id = Mage::getSingleton('core/session')->getAdvancedReportStore();
                    $carts = Mage::getResourceModel('sales/order_grid_collection')
                            ->addAttributeToFilter('main_table.status', array('in' => $sales_status))
                            ->addAttributeToFilter('main_table.store_id', $store_id)
                            ->addAttributeToFilter('main_table.created_at', array('from' => $db_from, 'to' => $db_to));
                }

                $salesFlatQuote = (string) Mage::getConfig()->getTablePrefix() . 'sales_flat_quote';
                $carts->getSelect()->join(array('sales_flat_quote' => $salesFlatQuote), "(sales_flat_quote.reserved_order_id=main_table.increment_id)", array('sales_flat_quote.created_at as sfq_created_at', 'sales_flat_quote.updated_at as sfq_updated_at'));
                $timetopurchase = array();
                for ($inc = 0; $inc <= 10; $inc++) {
                    $timetopurchase[$inc] = 0;
                }
                $timetopurchase['11-25'] = 0;
                $timetopurchase['25+'] = 0;
                $i = 0;
                $j = 0;
                foreach ($carts as $value) {
                    $date_u = Mage::getModel('core/date')->date(null, strtotime($value->getSfqUpdatedAt()));
                    $date_c = Mage::getModel('core/date')->date(null, strtotime($value->getSfqCreatedAt()));
                    $datediff = abs(strtotime($date_u) - strtotime($date_c));
                    $nodays = intval(floor($datediff / 86400));
                    if ($nodays > 10 && $nodays <= 25) {
                        $timetopurchase['11-25'] = $i + 1;
                        $i = $i + 1;
                    } elseif ($nodays > 25) {
                        $timetopurchase['25+'] = $j + 1;
                        $j = $j + 1;
                    } else {
                        $timetopurchase[$nodays] = $timetopurchase[$nodays] + 1;
                    }
                }
                $total_timetopurchase = array_sum($timetopurchase);
                ksort($timetopurchase);
                $fileName = $this->__('time_to_purchase') . '.xml';
                $content = '<?xml version="1.0"?>';
                $content.='<timetopurchase><header><DaystoTransaction>Days to Transaction</DaystoTransaction><Transactions>Transactions</Transactions><Percentageoftotal>Percentage of total %</Percentageoftotal></header>';
                foreach ($timetopurchase as $key => $value) {

                    // Resolving issue for Division by zero
                    if ($total_timetopurchase != 0) {
                        $content.='<Data>';
                        $content.='<DaystoTransaction>' . $key . '</DaystoTransaction>';
                        $content.='<Transactions>' . $value . '</Transactions>';
                        $content.='<Percentageoftotal>' . round(($value / $total_timetopurchase * 100), 2) . '%</Percentageoftotal></Data>';
                    }
                }
                $content.='</timetopurchase>';
                break;

            case "topsellingproducts_xml":

                //  topsellingproducts csv	
                // Assigning from and to date for top selling products report
                $topsellingproducts_from = date('Y-m-01', strtotime("-6 months"));
                $topsellingproducts_to = date('Y-m-t', strtotime("-1 months"));

                // Converting local time to db time
                $db_from = Mage::getModel('core/date')->gmtDate(null, strtotime($topsellingproducts_from));
                $db_to = Mage::getModel('core/date')->gmtDate(null, strtotime($topsellingproducts_to));

                $from_all_days = strtotime($topsellingproducts_from);
                $to_all_days = strtotime($topsellingproducts_to);

                // Initiliazing months array
                $months = array();

                // Calculating all months between given dates
                $tmp = date('mY', $to_all_days);
                $month_key = date('MY', $from_all_days);
                $months[$month_key] = 0;
                while ($from_all_days < $to_all_days) {

                    if ($from_all_days < 31) {
                        $from_all_days = strtotime(date('Y-m-d', $from_all_days) . ' +1 month');
                    } else {
                        $from_all_days = strtotime(date('Y-m-d', $from_all_days) . ' +15days');
                    }

                    if (date('mY', $from_all_days) != $tmp && ($from_all_days < strtotime($topsellingproducts_to))) {
                        $month_key = date('MY', $from_all_days);
                        $months[$month_key] = 0;
                    }
                }
                $month_key = date('MY', strtotime($topsellingproducts_to));
                $months[$month_key] = 0;


                $month_increment = 1;
                foreach ($months as $key => $month) {
                    $top_selling_product_month[$month_increment] = $key;
                    $month_increment = $month_increment + 1;
                }


                // Top selling products collection 
                $orderTotals = Mage::getModel('sales/order_item')->getCollection();
                $orderTotals->getSelect()->joinInner(array('cp' => Mage::getSingleton('core/resource')->getTableName('sales/order_grid')), 'cp.entity_id = main_table.order_id AND cp.status IN ("complete","processing")', array('cp.created_at as sfog_created_at'));
                $orderTotals->addAttributeToFilter('cp.created_at', array('from' => $db_from, 'to' => $db_to));

                if (Mage::getSingleton('core/session')->getAdvancedReportStore() != 0) {
                    $store_id = Mage::getSingleton('core/session')->getAdvancedReportStore();
                    $orderTotals->addAttributeToFilter('cp.store_id', $store_id);
                }

                // Product collection for getting product name
                $obj = Mage::getModel('catalog/product');
                $topsellingproducts_report = array();


                // Calculating product data
                foreach ($orderTotals as $value) {

                    $_product = $obj->load($value->getProductId());
                    $_product_name = $_product->getName();

                    // Assigning product name
                    if (empty($topsellingproducts_report[$_product_name]['name'])) {
                        $topsellingproducts_report[$_product_name]['name'] = $_product->getName();
                        $topsellingproducts_report[$_product_name]['id'] = $value->getProductId();
                        $topsellingproducts_report[$_product_name]['total_revenue'] = 0;
                        foreach ($months as $key => $month) {
                            $topsellingproducts_report[$_product_name][$key] = $month;
                        }
                    }

                    if (array_key_exists($_product_name, $topsellingproducts_report)) {

                        // Last sixth month data
                        $date_sixth_month = Mage::getModel('core/date')->date(null, strtotime($value->getSfogCreatedAt()));
                        $date_sixth_month = date('MY', strtotime($date_sixth_month));
                        if (array_key_exists($date_sixth_month, $topsellingproducts_report[$_product_name])) {
                            $topsellingproducts_report[$_product_name][$date_sixth_month] = $topsellingproducts_report[$_product_name][$date_sixth_month] + ($value->getBaseOriginalPrice() * $value->getQtyOrdered()) - $value->getBaseDiscountAmount();
                        } else {
                            $topsellingproducts_report[$_product_name][$date_sixth_month] = ($value->getBaseOriginalPrice() * $value->getQtyOrdered()) - $value->getBaseDiscountAmount();
                        }
                        $topsellingproducts_report[$_product_name]['total_revenue'] = $topsellingproducts_report[$_product_name]['total_revenue'] + ($value->getBaseOriginalPrice() * $value->getQtyOrdered()) - $value->getBaseDiscountAmount();
                    }
                }

                $sorted_topsellingproducts_report = Mage::helper('advancedreports')->sort_array_by_key($topsellingproducts_report, 'total_revenue');

                $sno = 1;

                if (!empty($top_selling_product_month[1])) {
                    $first_data = $top_selling_product_month[1];
                }
                if (!empty($top_selling_product_month[2])) {
                    $second_data = $top_selling_product_month[2];
                }
                if (!empty($top_selling_product_month[3])) {
                    $third_data = $top_selling_product_month[3];
                }
                if (!empty($top_selling_product_month[4])) {
                    $forth_data = $top_selling_product_month[4];
                }
                if (!empty($top_selling_product_month[5])) {
                    $fifth_data = $top_selling_product_month[5];
                }
                if (!empty($top_selling_product_month[6])) {
                    $sixth_data = $top_selling_product_month[6];
                }

                $fileName = $this->__('topsellingproducts') . '.xml';
                $content = '<topsellingproducts><header><Product>Product</Product>' . '<' . $first_data . '>' . $first_data . '</' . $first_data . '><' . $second_data . '>' . $second_data . '</' . $second_data . '><' . $third_data . '>' . $third_data . '</' . $third_data . '><' . $forth_data . '>' . $forth_data . '</' . $forth_data . '><' . $fifth_data . '>' . $fifth_data . '</' . $fifth_data . '><' . $sixth_data . '>' . $sixth_data . '</' . $sixth_data . '><TotalRevenue>TotalRevenue</TotalRevenue></header>';


                foreach ($sorted_topsellingproducts_report as $key => $value) {
                    $content.='<data>';
                    $content.='<Product>' . $value['name'] . '</Product>';
                    $content.='<' . $first_data . '>' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value[$first_data], 2) . '</' . $first_data . '>';
                    $content.='<' . $second_data . '>' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value[$second_data], 2) . '</' . $second_data . '>';
                    $content.='<' . $third_data . '>' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value[$third_data], 2) . '</' . $third_data . '>';
                    $content.='<' . $forth_data . '>' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value[$forth_data], 2) . '</' . $forth_data . '>';
                    $content.='<' . $fifth_data . '>' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value[$fifth_data], 2) . '</' . $fifth_data . '>';
                    $content.='<' . $sixth_data . '>' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value[$sixth_data], 2) . '</' . $sixth_data . '>';
                    $content.='<TotalRevenue>' . $value['total_revenue'] . '</TotalRevenue>';
                    $content.='</data>';

                    $sno = $sno + 1;
                    if ($sno > 25) {
                        break;
                    }
                }
                $content.='</topsellingproducts>';

                break;


            case "gainer_xml":

                //  For gainer xml

                $gainer_start_date = strtotime($from);
                $gainer_end_date = strtotime($to);

                $gainer_dates_diff = abs($gainer_end_date - $gainer_start_date);

                $gainer_no_days = $gainer_dates_diff / 86400;
                $gainer_no_days = 0 - $gainer_no_days;

                $gainer_compare_start_date = date('Y-m-d', strtotime($from . ' ' . $gainer_no_days . ' day'));
                $gainer_compare_end_date = date('Y-m-d', strtotime($from));

                // Converting local time to db time
                $db_from = Mage::getModel('core/date')->gmtDate(null, strtotime($gainer_compare_start_date));
                $db_to = Mage::getModel('core/date')->gmtDate(null, strtotime($to));

                // Gainer report collection 
                $orderTotals = Mage::getModel('sales/order_item')->getCollection();
                $orderTotals->getSelect()->joinInner(array('cp' => Mage::getSingleton('core/resource')->getTableName('sales/order_grid')), 'cp.entity_id = main_table.order_id AND cp.status IN ("complete","processing")', array('cp.created_at as sfog_created_at'));
                $orderTotals->addAttributeToFilter('cp.created_at', array('from' => $db_from, 'to' => $db_to));

                if (Mage::getSingleton('core/session')->getAdvancedReportStore() != 0) {
                    $store_id = Mage::getSingleton('core/session')->getAdvancedReportStore();
                    $orderTotals->addAttributeToFilter('cp.store_id', $store_id);
                }

                // Product collection for getting product name
                $obj = Mage::getModel('catalog/product');
                $gainer_report = array();

                // Calculating product data
                foreach ($orderTotals as $value) {

                    $_product = $obj->load($value->getProductId());
                    $_product_name = $_product->getName();

                    // Assigning product name
                    if (empty($gainer_report[$_product_name]['name'])) {
                        $gainer_report[$_product_name]['name'] = $_product->getName();
                        $gainer_report[$_product_name]['id'] = $value->getProductId();
                        $gainer_report[$_product_name]['revenue_diff'] = 0;
                        $gainer_report[$_product_name]['revenue_first'] = 0;
                        $gainer_report[$_product_name]['revenue_second'] = 0;
                    }

                    if (array_key_exists($_product_name, $gainer_report)) {
                        $purchased_date = Mage::getModel('core/date')->date(null, strtotime($value->getSfogCreatedAt()));
                        $purchased_date_value = strtotime($purchased_date);

                        if ($purchased_date_value >= $gainer_start_date && $purchased_date_value <= $gainer_end_date) {
                            $gainer_report[$_product_name]['revenue_second'] = $gainer_report[$_product_name]['revenue_second'] + ($value->getBaseOriginalPrice() * $value->getQtyOrdered()) - $value->getBaseDiscountAmount();
                        } else {
                            $gainer_report[$_product_name]['revenue_first'] = $gainer_report[$_product_name]['revenue_first'] + ($value->getBaseOriginalPrice() * $value->getQtyOrdered()) - $value->getBaseDiscountAmount();
                        }
                    }
                }

                $gainer_report_data = array();

                foreach ($gainer_report as $value) {
                    if ($value['revenue_second'] > $value['revenue_first']) {
                        $gainer_product = $value['name'];
                        $gainer_report_data[$gainer_product]['name'] = $value['name'];
                        $gainer_report_data[$gainer_product]['id'] = $value['id'];
                        $gainer_report_data[$gainer_product]['revenue_first'] = $value['revenue_first'];
                        $gainer_report_data[$gainer_product]['revenue_second'] = $value['revenue_second'];
                        $gainer_report_data[$gainer_product]['revenue_diff'] = $value['revenue_second'] - $value['revenue_first'];
                        $revenue_diff = $value['revenue_second'] - $value['revenue_first'];
                        if ($value['revenue_first'] != 0) {
                            $gainer_report_data[$gainer_product]['percent'] = $revenue_diff / $value['revenue_first'] * 100;
                        } else {
                            $gainer_report_data[$gainer_product]['percent'] = 100;
                        }
                    }
                }

                $sorted_gainer_report = Mage::helper('advancedreports')->sort_array_by_key($gainer_report_data, 'percent');

                $fileName = $this->__('gainerloser') . '.xml';
                $content = '<?xml version="1.0"?>';
                $content.='<GainerLoser><header><Product>Product</Product><Revenue>Revenue</Revenue><Revenue>Revenue</Revenue></header>';
                $sno = 1;
                foreach ($sorted_gainer_report as $value) {
                    $content.='<gainer>';
                    $content.='<Product>' . $value['name'] . '</Product>';
                    $content.='<Revenue>' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value['revenue_first'], 2) . '</Revenue>';
                    $content.='<Revenue>' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value['revenue_second'], 2) . ' (+' . round($value['percent'], 2) . ' %)' . '</Revenue>';
                    $content.='</gainer>';

                    $sno = $sno + 1;
                    if ($sno > 10) {
                        break;
                    }
                }

                $loser_report_data = array();
                $revenue_diff = 0;
                foreach ($gainer_report as $value) {
                    if ($value['revenue_first'] > $value['revenue_second']) {
                        $loser_product = $value['name'];
                        $loser_report_data[$loser_product]['name'] = $value['name'];
                        $loser_report_data[$loser_product]['id'] = $value['id'];
                        $loser_report_data[$loser_product]['revenue_first'] = $value['revenue_first'];
                        $loser_report_data[$loser_product]['revenue_second'] = $value['revenue_second'];
                        $loser_report_data[$loser_product]['revenue_diff'] = $value['revenue_first'] - $value['revenue_second'];
                        $revenue_diff = $value['revenue_first'] - $value['revenue_second'];
                        if ($value['revenue_second'] != 0) {
                            $loser_report_data[$loser_product]['percent'] = $revenue_diff / $value['revenue_first'] * 100;
                        } else {
                            $loser_report_data[$loser_product]['percent'] = 100;
                        }
                    }
                }
                $sorted_loser_report = Mage::helper('advancedreports')->sort_array_by_key($loser_report_data, 'percent');


                $sno = 1;
                foreach ($sorted_loser_report as $value) {
                    $content.='<loser>';
                    $content.='<Product>' . $value['name'] . '</Product>';
                    $content.='<Revenue>' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value['revenue_first'], 2) . '</Revenue>';
                    $content.='<Revenue>' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value['revenue_second'], 2) . ' (-' . round($value['percent'], 2) . ' %)' . '</Revenue>';
                    $content.='</loser>';

                    $sno = $sno + 1;
                    if ($sno > 10) {
                        break;
                    }
                }

                $content.='</GainerLoser>';
                break;

            case "followupproducts_xml":

                $followup_start_date = $from;
                $followup_end_date = $from;

                if (!empty($_advanced_get_option)) {
                    if ($_advanced_get_option == 'Today') {
                        $followup_start_date = date('Y-m-d', strtotime($followup_end_date . ' - 1 day'));
                    } elseif ($_advanced_get_option == 'LastWeek') {
                        $followup_start_date = date('Y-m-d', strtotime($followup_end_date . ' - 7 day'));
                    } else {
                        $followup_start_date = date('Y-m-d', strtotime($followup_end_date . ' - 1 month'));
                    }
                } else {
                    $followup_start_date = date('Y-m-d', strtotime($followup_end_date . ' - 1 month'));
                }

                // For displaying dates
                $followup_from_display = $followup_start_date;
                $followup_to_display = date('Y-m-d', strtotime($followup_end_date . ' - 1 day'));

                // Converting local time to db time
                $db_from = Mage::getModel('core/date')->gmtDate(null, strtotime($followup_start_date));
                $db_to = Mage::getModel('core/date')->gmtDate(null, strtotime($to));

                // Getting all followup products
                $followup_products = Mage::helper('advancedreports')->getFollowupproductsCollection();

                if (!empty($followup_products)) {
                    $collections = Mage::getModel('sales/order_item')->getCollection()
                            ->addAttributeToFilter('main_table.product_id', $followup_products);
                    $collections->getSelect()->joinInner(array('cp' => Mage::getSingleton('core/resource')->getTableName('sales/order_grid')), 'cp.entity_id = main_table.order_id AND cp.status IN ("complete","processing")', array('cp.created_at as sfog_created_at'));
                    $collections->addAttributeToFilter('cp.created_at', array('from' => $db_from, 'to' => $db_to));

                    if (Mage::getSingleton('core/session')->getAdvancedReportStore() != 0) {
                        $store_id = Mage::getSingleton('core/session')->getAdvancedReportStore();
                        $collections->addAttributeToFilter('cp.store_id', $store_id);
                    }
                }


                // Product collection for getting product name
                $obj = Mage::getModel('catalog/product');
                $followup_report = array();

                // Calculating product data
                foreach ($collections as $value) {
                    $_product = $obj->load($value->getProductId());
                    $_product_name = $_product->getName();

                    // Assigning product name
                    if (empty($followup_report[$_product_name]['name'])) {
                        $followup_report[$_product_name]['name'] = $_product->getName();
                        $followup_report[$_product_name]['id'] = $value->getProductId();
                        $followup_report[$_product_name]['revenue_diff'] = 0;
                        $followup_report[$_product_name]['revenue_first'] = 0;
                        $followup_report[$_product_name]['revenue_second'] = 0;
                    }

                    if (array_key_exists($_product_name, $followup_report)) {
                        $purchased_date = Mage::getModel('core/date')->date(null, strtotime($value->getSfogCreatedAt()));
                        $purchased_date_value = strtotime($purchased_date);

                        if ($purchased_date_value >= $from && $purchased_date_value <= $to) {
                            $followup_report[$_product_name]['revenue_second'] = $followup_report[$_product_name]['revenue_second'] + ($value->getBaseOriginalPrice() * $value->getQtyOrdered()) - $value->getBaseDiscountAmount();
                        } else {
                            $followup_report[$_product_name]['revenue_first'] = $followup_report[$_product_name]['revenue_first'] + ($value->getBaseOriginalPrice() * $value->getQtyOrdered()) - $value->getBaseDiscountAmount();
                        }
                    }
                }
                
                
                    // Assinging defualt values for unsold products

                foreach ($followup_products as $fproduct) {
                    $_product = $obj->load($fproduct);
                    $_product_name = $_product->getName();
                    if (!array_key_exists($_product_name, $followup_report)) {
                        $followup_report[$_product_name]['name'] = $_product->getName();
                        $followup_report[$_product_name]['id'] = $fproduct;
                        $followup_report[$_product_name]['revenue_diff'] = 0;
                        $followup_report[$_product_name]['revenue_first'] = 0;
                        $followup_report[$_product_name]['revenue_second'] = 0;
                    }
                }


                $followup_report_data = array();
                $revenue_diff = 0;
                foreach ($followup_report as $value) {
                    // Calculating gain percentage
                    if ($value['revenue_second'] > $value['revenue_first']) {
                        $followup_product = $value['name'];
                        $followup_report_data[$followup_product]['name'] = $value['name'];
                        $followup_report_data[$followup_product]['id'] = $value['id'];
                        $followup_report_data[$followup_product]['revenue_first'] = $value['revenue_first'];
                        $followup_report_data[$followup_product]['revenue_second'] = $value['revenue_second'];
                        $followup_report_data[$followup_product]['revenue_diff'] = $value['revenue_second'] - $value['revenue_first'];
                        $revenue_diff = $value['revenue_second'] - $value['revenue_first'];
                        $followup_report_data[$followup_product]['comparison'] = 'gainer';
                        // For division by zero issue
                        if($value['revenue_first'] != 0)
                        {    
                        $followup_report_data[$followup_product]['percent'] = $revenue_diff / $value['revenue_first'] * 100;
                        }
                        elseif ($value['revenue_first'] == 0 && $revenue_diff != 0 ) {
                        $followup_report_data[$followup_product]['percent'] = 100;
                        }
                        else {
                        $followup_report_data[$followup_product]['percent']=0;    
                        }
                    } else {
                        // Calculating lose percentage   
                        $followup_product = $value['name'];
                        $followup_report_data[$followup_product]['name'] = $value['name'];
                        $followup_report_data[$followup_product]['id'] = $value['id'];
                        $followup_report_data[$followup_product]['revenue_first'] = $value['revenue_first'];
                        $followup_report_data[$followup_product]['revenue_second'] = $value['revenue_second'];
                        $followup_report_data[$followup_product]['revenue_diff'] = $value['revenue_first'] - $value['revenue_second'];
                        $revenue_diff = $value['revenue_first'] - $value['revenue_second'];
                        $followup_report_data[$followup_product]['comparison'] = 'loser';
                                                
                       if($value['revenue_first'] != 0)
                       {    
                        $followup_report_data[$followup_product]['percent'] = $revenue_diff / $value['revenue_first'] * 100;
                       }
                       elseif($value['revenue_second'] == 0 && $revenue_diff != 0) {
                       $followup_report_data[$followup_product]['percent'] = 100;
                       }
                       else {
                       $followup_report_data[$followup_product]['percent']=0;    
                       }
                    }
                }


                $fileName = $this->__('followupproducts') . '.xml';
                $content = '<?xml version="1.0"?>';
                $content.='<followupproducts><header><Product>Product</Product><Revenue>Revenue</Revenue><Revenue>Revenue</Revenue></header>';
                $sno = 1;
                foreach ($followup_report_data as $value) {

                       if ($value['comparison'] == 'gainer') {
                     if($value['percent'] != 0){   $percent = ' (+' . round($value['percent'], 2) . ' %)'; } else{ $value['percent'].'%'; }
                    } else {                       
                     if($value['percent'] != 0){ $percent = ' (-' . round($value['percent'], 2) . ' %)'; }else{ $percent = $value['percent'].'%';}
                    }

                    $content.='<Data>';
                    $content.='<Product>' . $value['name'] . '</Product>';
                    $content.='<Revenue>' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value['revenue_first'], 2) . '</Revenue>';
                    $content.='<Revenue>' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol() . round($value['revenue_second'], 2) . ' '.$percent . '</Revenue>';
                    $content.='</Data>';
                    $sno = $sno + 1;
                }
                $content.='</followupproducts>';
                break;

            default:
        }

        // For download report csv / xml file    
        $contentType = 'application/octet-stream';
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

    // Calling sales tab block        
    public function salesAction() {
        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('advancedreports/adminhtml_advancedreports_edit_tab_sales')
                        ->toHtml()
        );
    }

    // Calling product tab block                
    public function productAction() {
        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('advancedreports/adminhtml_advancedreports_edit_tab_product')
                        ->toHtml()
        );
    }

    // Calling transactions tab block                
    public function transactionsAction() {
        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('advancedreports/adminhtml_advancedreports_edit_tab_transactions')
                        ->toHtml()
        );
    }

    // Calling timetopurchase tab block                
    public function timetopurchaseAction() {
        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('advancedreports/adminhtml_advancedreports_edit_tab_timetopurchase')
                        ->toHtml()
        );
    }

    // Calling timetopurchase tab block                
    public function salesdatewiseAction() {
        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('advancedreports/adminhtml_advancedreports_edit_tab_salesdatewise')
                        ->toHtml()
        );
    }

    // Calling top selling products tab block                
    public function topsellingproductsAction() {
        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('advancedreports/adminhtml_advancedreports_edit_tab_topsellingproducts')
                        ->toHtml()
        );
    }

    // Calling gainer tab block                
    public function gainerAction() {
        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('advancedreports/adminhtml_advancedreports_edit_tab_gainer')
                        ->toHtml()
        );
    }

    // Calling follow up products tab block                
    public function followupproductsAction() {
        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('advancedreports/adminhtml_advancedreports_edit_tab_followupproducts')
                        ->toHtml()
        );
    }

// Adding follow up products

    public function updatefollowupproductsAction() {

        $product_id = $this->getRequest()->getParam('followup_product_id');       
      
        if (Mage::getSingleton('core/session')->getAdvancedReportStore() != 0) {
        $store_id = Mage::getSingleton('core/session')->getAdvancedReportStore();
        }
        else {
        $store_id = 0;    
        }

        $collections = Mage::getModel('advancedreports/advancedreports')->getCollection()
                ->addFieldToFilter('store_id', $store_id)
                ->addFieldToFilter('product_id', $product_id);


        if (!empty($product_id)) {
            if (count($collections) >= 1) {

                // Assign table prefix if it's exist
                $table_name = Mage::getSingleton('core/resource')->getTableName('advancedreports');
                $connection = Mage::getSingleton('core/resource')
                        ->getConnection('core_write');
                $connection->beginTransaction();
                $fields = array();
                $fields['status_id'] = 1;
                $where[] = $connection->quoteInto('store_id = ?', $store_id);
                $where[] = $connection->quoteInto('product_id =?', $product_id);
                $connection->update($table_name, $fields, $where);
                $connection->commit();
            } else {

                $collections = Mage::getModel('advancedreports/advancedreports');
                $collections->setStoreId($store_id);
                $collections->setProductId($product_id);
                $collections->setStatusId(1);
                $collections->save();
            }
        }
    }

// Removing follow up product

    public function removefollowupproductsAction() {

        $product_id = $this->getRequest()->getParam('followup_product_id');
              
        if (Mage::getSingleton('core/session')->getAdvancedReportStore() != 0) {
        $store_id = Mage::getSingleton('core/session')->getAdvancedReportStore();
        }
        else {
        $store_id = 0;    
        }        

        $collections = Mage::getModel('advancedreports/advancedreports')->getCollection()
                ->addFieldToFilter('store_id', $store_id)
                ->addFieldToFilter('product_id', $product_id)
                ->addFieldToFilter('status_id', 1);

        if (count($collections) >= 1 && !empty($product_id)) {

            // Assign table prefix if it's exist
            $table_name = Mage::getSingleton('core/resource')->getTableName('advancedreports');
            $connection = Mage::getSingleton('core/resource')
                    ->getConnection('core_write');
            $connection->beginTransaction();
            $fields = array();
            $fields['status_id'] = 0;
            $where[] = $connection->quoteInto('store_id = ?', $store_id);
            $where[] = $connection->quoteInto('product_id = ?', $product_id);
            $connection->update($table_name, $fields, $where);
            $connection->commit();
        }
    }

}