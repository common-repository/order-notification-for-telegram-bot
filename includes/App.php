<?php

namespace OrderNotificationForTelegramBot;

use Automattic\WooCommerce\Utilities\FeaturesUtil;
use Exception;
use OrderNotificationForTelegramBot\Admin\OptionPanel;
use OrderNotificationForTelegramBot\Base\Singleton;
use OrderNotificationForTelegramBot\Common\Helper\TelegramBot;
use OrderNotificationForTelegramBot\Common\Helper\WooCommerce;
use OrderNotificationForTelegramBot\Common\Shortcodes;

class App extends Singleton
{

    protected TelegramBot $telegramInstance;

    function init(): void
    {
        add_action('onftb_woo_not_installed', [$this, 'noticeForInstallingWoocommerce']);
        add_action('onftb_woo_installed', [$this, 'run']);

        $this->checkForWooCommerce();
    }

    private function noticeForInstallingWoocommerce()
    {
        add_action('admin_notices', [$this, 'showWooCommerceError']);
    }

    private function wooNotInstalled()
    {
        do_action('onftb_woo_not_installed');
    }

    private function wooInstalled()
    {
        do_action('onftb_woo_installed');
    }

    private function defineMethods()
    {
        Shortcodes::getInstance();
    }

    private function checkForWooCommerce()
    {
        $activePlugins = apply_filters('active_plugins', get_option('active_plugins'));

        if (in_array('woocommerce/woocommerce.php', $activePlugins)) {
            $this->wooInstalled();
        } else {
            $this->wooNotInstalled();
        }
    }

    function run()
    {
        $this->defineMethods();

        add_action('before_woocommerce_init', function () {
            if (class_exists(FeaturesUtil::class)) {
                FeaturesUtil::declare_compatibility('custom_order_tables', ONFTB_PLUGIN_FILE);
            }
        });

        $this->initTelegramBotHelper();

        if (is_admin()) {
            add_filter('plugin_action_links_' . ONFTB_PLUGIN_BASE_NAME, [$this, 'addActionLinks'], 999);
            add_action('plugins_loaded', [$this, 'loadHooksInAdmin'], 26);
        }

        add_action('plugins_loaded', [$this, 'loadHooks'], 26);

    }

    function addActionLinks($actions): array
    {
        return array_merge($actions, ['<a href="' . admin_url('admin.php?page=wc-settings&tab=onftb') . '">پیکربندی</a>']);
    }

    function showWooCommerceError()
    {
        $class = 'notice notice-error';
        $message1 = __('افزونه <a href="https://wordpress.org/plugins/order-notification-for-telegram-bot">اطلاع رسانی سفارشات ووکامرس توسط ربات تلگرام</a> برای فعالیت های خود به افزونه ووکامرس نیازمند می باشد.');
        $message2 = __('لطفا از فعال بودن <a href="https://wordpress.org/plugins/woocommerce">ووکامرس</a> اطمینان حاصل فرمایید.');
        printf('<div class="%1$s"><p>%2$s</p><p>%3$s</p></div>', esc_attr($class), ($message1), ($message2));
    }

    function adminLoadJsScripts()
    {
        wp_enqueue_script('onftb', plugin_dir_url(__FILE__) . '../assets/js/admin.js', array('jquery'), false, true);
    }

    function sendTestMessage()
    {
        try {
            $this->telegramInstance->request(self::getTemplate());
            echo json_encode(['error' => 0, 'message' => __('پیام ارسال شد!')]);
            wp_die();
        } catch (Exception $ex) {
            echo json_encode(['error' => 1, 'message' => $ex->getMessage()]);
            wp_die();
        }
    }

    function loadHooks()
    {

        $orderStatusChanged = get_option('onftb_send_after_order_status_changed', false);

        if ($orderStatusChanged == 'yes') {
            add_action('woocommerce_order_status_changed', [$this, 'woocommerceOrderStatusChanged'], 20, 4);
        } else {
            add_action('woocommerce_new_order', [$this, 'woocommerceNewOrderArrived']);
        }
    }

    function loadHooksInAdmin()
    {

        add_action('wp_ajax_onftb_send_test_message', [$this, 'sendTestMessage']);

        add_filter('woocommerce_get_settings_pages', [$this, 'addWooCommerceSettingSection']);
        add_action('admin_enqueue_scripts', [$this, 'adminLoadJsScripts']);

    }

    public function woocommerceOrderStatusChanged($order_id, $status_transition_from, $status_transition_to, $that)
    {
        $order = wc_get_order($order_id);
        $statuses = get_option('onftb_order_statuses');
        if (in_array('wc-' . $order->get_status(), $statuses)) {
            $this->sendTgNotification($order->get_id());
        }

        do_action('onftb_order_status_changed', $order_id, $status_transition_from, $status_transition_to, $that);
    }

    public function woocommerceNewOrderArrived($order_id)
    {
        $order = wc_get_order($order_id);
        $wasSent = $order->get_meta('telegramWasSent');
        if (!$wasSent) {
            $order->update_meta_data('telegramWasSent', 1);
            $order->save();
            $this->sendTgNotification($order_id);
        }

        do_action('onftb_new_order_arrived', $order_id);
    }

    public function sendTgNotification($orderID)
    {
        $wc = new WooCommerce($orderID);
        $message = $wc->getBillingDetails(self::getTemplate());
        $this->telegramInstance->request($message);
    }

    private function initTelegramBotHelper()
    {
        $this->telegramInstance = TelegramBot::getInstance();
        $this->telegramInstance->setToken(get_option('onftb_setting_token'));
        $this->telegramInstance->setChatID(get_option('onftb_setting_chatid'));
        $this->telegramInstance->setParseMode('HTML');
        $this->telegramInstance->setAccessTags(ONFTB_PLUGIN_ACCESS_TAGS);
        $this->telegramInstance->setGoogleScript(get_option('onftb_setting_tg_google_script'));
        $this->telegramInstance->setDefaultEndpoint(ONFTB_PLUGIN_DEFAULT_PROXY_ENDPOINT);
        $this->telegramInstance->setUseTelegramEndPoint(get_option('is_use_default_endpoint') == 'yes');
    }
    private static function getTemplate()
    {
        return get_option('onftb_setting_template');
    }

    public function addWooCommerceSettingSection($settings)
    {
        $settings[] = new OptionPanel();

        return $settings;
    }
}