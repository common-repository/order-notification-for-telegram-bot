<?php

namespace OrderNotificationForTelegramBot\Common\Helper;

use OrderNotificationForTelegramBot\Common\PersianDate;
use WC_Countries;
use WC_Customer;
use WC_Order_Item_Product;

class WooCommerce
{

    public $pattern;
    public $order;
    public $order_id;
    private $_epo_prefix = 'order_items_epo_';

    function __construct($order_id)
    {
        $this->pattern = array();
        $this->order = wc_get_order($order_id);
        $this->order_id = $order_id;

        foreach (ONFTB_PLUGIN_SHORTCODES as $method) {
            $method = Methods::generateMethodName($method);
            $camelCaseMethod = Methods::convertToCamelCase($method);

            if (method_exists($this, $camelCaseMethod)) {
                add_filter('onftb_filter_method_' . $method, [$this, "$camelCaseMethod"]);
            }
        }
    }

    function getCheckForPaymentUrl($arg): string
    {
        if ($this->order->get_status() === 'pending_payment' || $this->order->get_status() === 'pending') {
            return $this->order->get_checkout_payment_url();
        }
        return "وضعیت سفارش درحال پرداخت نیست!";
    }

    function getBillingState($arg): string
    {
        $wc = new WC_Countries();

        return ($wc->get_states($wc->get_base_country())[$arg]) ?? $arg;
    }

    function getStatus($arg): string
    {
        return (wc_get_order_status_name($arg)) ?? $arg;
    }

    function getItemsEpo($key): string
    {
        $epos = "";
        $items = $this->order->get_items();

        foreach ($items as $item) {

            if (!$item instanceof WC_Order_Item_Product) {
                return "";
            }

            $_epo = $item->get_meta($key);

            if (!empty($_epo)) {
                $epos .= $_epo . PHP_EOL;
            }
        }

        return $epos;
    }

    function getTotal($arg): string
    {
        return (wc_price($arg)) ?? $arg;
    }

    function getItems($arg): string
    {

        if (!is_array($this->order->get_items())) {
            return "";
        }

        if (count($this->order->get_items()) < 1) {
            return "";
        }

        $product = chr(10);

        foreach ($this->order->get_items() as $item) {
            if (!$item instanceof WC_Order_Item_Product) {
                return "";
            }
            $product .= $item->get_name() . ' × ' . $item->get_quantity() . ' عدد' . ' با قیمت ' . wc_price($item->get_total()) . chr(10);
        }

        return $product;
    }

    function getCustomerOrderCount()
    {
        $count = "";
        try {
            $customer = new WC_Customer($this->order->get_customer_id());
            $count = $customer->get_order_count();
        } catch (\Exception $e) {
        }

        return $count ?? "";
    }

    function getCreatedDatePer()
    {
        return (PersianDate::jdate('d F Y, g:i a', strtotime($this->order->get_date_created()))) ?? "";
    }

    public function getBillingDetails($str)
    {
        $this->decodeShortcode($str);

        return str_replace(array_keys($this->pattern), array_values($this->pattern), $str);
    }

    private function applyFilter($filter_postfix, $data)
    {
        return apply_filters('onftb_filter_method_' . $filter_postfix, $data);
    }

    private function decodeShortcode($str)
    {
        $shortcode_pattern = '/\{.+?}/m';
        preg_match_all($shortcode_pattern, $str, $matches);
        array_walk_recursive($matches, function ($item) {
            $shortcode = preg_replace('/[{}]/', '', $item);


            if (str_starts_with($shortcode, $this->_epo_prefix)) {
                $_epo_key = substr($shortcode, strlen($this->_epo_prefix));
                $_epo_method = Methods::generateMethodName('items_epo');
                $this->pattern[$item] = $this->applyFilter($_epo_method, $_epo_key);
                return;
            }

            if (!isset(ONFTB_PLUGIN_SHORTCODES[$shortcode])) {
                return;
            }

            $method = Methods::generateMethodName(ONFTB_PLUGIN_SHORTCODES[$shortcode]);
            $data = $item;

            switch ($method):
                case is_callable($method):
                    $data = $method();
                    break;

                case method_exists($this, $method):
                    $data = $this->$method();
                    break;

                case method_exists($this->order, $method):
                    $data = $this->order->$method();
                    break;
            endswitch;

            $this->pattern[$item] = $this->applyFilter($method, $data);

        });
    }

}