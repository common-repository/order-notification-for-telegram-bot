<?php

namespace OrderNotificationForTelegramBot\Common;


use OrderNotificationForTelegramBot\Base\Singleton;

class Shortcodes extends Singleton
{


    function init()
    {

        define('ONFTB_PLUGIN_SHORTCODES', apply_filters('onftb_shortcodes', [
            'site_name' => 'bloginfo',
            'order_id' => 'id',
            'order_items' => 'items',
            'order_items_epo' => 'items_epo',
            'order_date' => 'date_created',
            'order_date_per' => 'created_date_per',
            'order_status' => 'status',
            'order_total' => 'total',
            'order_note' => 'customer_note',

            'billing_first_name' => 'billing_first_name',
            'billing_last_name' => 'billing_last_name',
            'billing_address_part_1' => 'billing_address_1',
            'billing_address_part_2' => 'billing_address_2',
            'billing_address_city' => 'billing_city',
            'billing_address_state' => 'billing_state',
            'billing_address_postcode' => 'billing_postcode',
            'billing_email' => 'billing_email',
            'billing_phone' => 'billing_phone',
            'billing_payment_method' => 'payment_method_title',
            'billing_shipping_method' => 'shipping_method',
            'billing_address_google_map' => 'shipping_address_map_url',

            'customer_id' => 'customer_id',
            'customer_ip' => 'customer_ip_address',
            'customer_order_count' => 'customer_order_count',
            'customer_user_agent' => 'customer_user_agent',
            'view_order' => 'view_order_url',
            'edit_order' => 'edit_order_url',
            'payment_url' => 'check_for_payment_url',


        ]));
    }
}