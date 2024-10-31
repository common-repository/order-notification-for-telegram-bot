<?php

namespace OrderNotificationForTelegramBot;


if (!defined('ABSPATH')) {
    exit;
}

require_once 'vendor/autoload.php';

/**
 * @package OrderNotificationForTelegramBot
 * @version 1.8.1
 */

/*
Plugin Name: اطلاع رسانی سفارشات از طریق ربات تلگرام
Plugin URI: https://devarea.ir/OrderNotificationForTelegramBot
Description: اطلاع رسانی سفارشات از طریق ربات تلگرام
Author: دِو ایریا | Dev Area
Version: 1.8.1
Author URI: https://devarea.ir
Text Domain: OrderNotificationForTelegramBot
License URI: https://opensource.org/licenses/MIT
Code Name: OrderNotificationForTelegramBot
WC requires at least: 9.0.0
WC tested up to: 9.1.4
Requires at least: 6.0
Requires PHP: 7.4
*/

define('ONFTB_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('ONFTB_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ONFTB_PLUGIN_FILE', __FILE__);
define('ONFTB_PLUGIN_BASE_NAME', plugin_basename(__FILE__));
define('ONFTB_PLUGIN_TG_BANNER', plugins_url("assets/img/onftb-tg-banner.jpg", __FILE__));
define('ONFTB_PLUGIN_WOONOTIFY_TG_BANNER', plugins_url("assets/img/woonotify-tg-banner.gif", __FILE__));
define('ONFTB_PLUGIN_ACCESS_TAGS', '<b><strong><i><u><em><ins><s><strike><del><a><code><pre><tg-spoiler><blockquote><tg-emoji>');
define('ONFTB_PLUGIN_DEFAULT_PROXY_ENDPOINT', base64_decode("aHR0cHM6Ly9wcm94eS5tb2hhbW1hZG1hbGVraXJhZC5pci8/cmVzdF9yb3V0ZT0vdGVsZWdyYW1Qcm94eS92Mi8="));

App::getInstance();