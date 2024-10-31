<?php

namespace OrderNotificationForTelegramBot\admin;

use WC_Settings_Page;

class OptionPanel extends WC_Settings_Page {

	public function __construct() {
		parent::__construct();
		$this->id    = 'onftb';
		$this->label = __( 'اطلاع رسانی ربات تلگرام' );
		add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );

	}

	public function get_settings( $section = null ) {
		$settings =
			array(
				'section_title'                      => array(
					'title' => __( 'راهنما' ),
					'type'  => 'title',
					'desc'  => $this->renderHelpDescription(),
					'id'    => 'wc_settings_tab_onftb_title_1'
				),
				'section_title_tg'                   => array(
					'title' => __( 'تلگرام' ),
					'type'  => 'title',
					'desc'  => "",
					'id'    => 'wc_settings_tab_onftb_title_tg',
				),
				'token'                              => array(
					'title'    => __( 'توکن ربات تلگرام' ),
					'type'     => 'text',
					'id'       => 'onftb_setting_token',
					'desc'     => __( 'توکن ربات که شبیه همچین چیزیه 123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11 را وارد کنید' ),
					'desc_tip' => false
				),
				'chatid'                             => array(
					'title'    => __( 'آیدی چت یا گروه' ),
					'type'     => 'text',
					'id'       => 'onftb_setting_chatid',
					'desc_tip' => false,
					'desc'     => __( 'آیدی چت پی وی یا گروه که شبیه همچین چیزیه 431654987 را وارد کنید، برای اطلاعات بیشتر به ربات تلگرامی @UserAccInfoBot مراجعه نمایید' )
				),
				'tg_google_script'                   => array(
					'title'    => __( 'استفاده از گوگل اسکریپت بعنوان پراکسی' ),
					'type'     => 'url',
					'id'       => 'onftb_setting_tg_google_script',
					'desc_tip' => false,
					'desc'     => __( 'یه چیزی شبیه این https://script.google.com/***83f2e0f33d4dce3f331e013c***/exec هستش. برای اطلاعات بیشتر به کانال افزونه مراجعه نمایید.' )
				),
				'section_end_tg'                     => array(
					'type' => 'sectionend',
					'id'   => 'wc_settings_tab_onftb_end_section_tg'
				),
				'section_title_proxy'                => array(
					'title' => __( 'استفاده از پراکسی افزونه' ),
					'type'  => 'title',
					'desc'  => "",
					'id'    => 'wc_settings_tab_onftb_title_proxy',
					'css'   => 'color:red;'
				),
				'is_use_default_endpoint'            => array(
					'title'    => __( 'از پراکسی افزونه استفاده نشود' ),
					'type'     => 'checkbox',
					'id'       => 'is_use_default_endpoint',
					'desc_tip' => false,
					'desc'     => __( "اگر هاستتون خارجی هستش حتما تیک بزنید!" )
				),
				'section_end_proxy'                  => array(
					'type' => 'sectionend',
					'id'   => 'wc_settings_tab_onftb_end_section_tg'
				),
				'section_title_settings'             => array(
					'title' => __( 'تنظیمات افزونه' ),
					'type'  => 'title',
					'desc'  => "",
					'id'    => 'wc_settings_tab_onftb_title_settings',
					'css'   => 'color:red;'
				),
				'sending_after_order_status_changed' => array(
					'title'    => __( 'ارسال نوتیفیکشن با تغییر وضعیت' ),
					'type'     => 'checkbox',
					'id'       => 'onftb_send_after_order_status_changed',
					'desc_tip' => false,
					'desc'     => __( "براساس وضعیت های انتخابی هنگام ثبت سفارش نوتیفیکیشن ارسال می شود. در غیر اینصورت برای همه وضعیت ها نوتیفیکیشن ارسال می شود." )
				),
				'order_statuses'                     => array(
					'title'    => __( 'انتخاب وضعیت های سفارش' ),
					'type'     => 'multiselect',
					'id'       => 'onftb_order_statuses',
					'options'  => wc_get_order_statuses(),
					'class'    => 'wc-enhanced-select',
					'desc_tip' => false,
					'css'      => 'width:45%;',
					'desc'     => __( 'وضعیت هایی که برایشان نوتیفیکیشن ارسال می شود' )
				),
				'message_template'                   => array(
					'title'             => __( 'نمونه پیام ارسالی' ),
					'type'              => 'textarea',
					'id'                => 'onftb_setting_template',
					'class'             => 'code',
					'css'               => 'max-width:550px;width:65%;',
					'default'           =>
						'نام وبسایت: {site_name}' . chr( 10 ) .
						'شماره سفارش: {order_id}' . chr( 10 ) .
						'زمان ثبت سفارش: {order_date}' . chr( 10 ) .
						'زمان ثبت سفارش (شمسی): {order_date_per}' . chr( 10 ) .
						'وضعیت سفارش: {order_status}' . chr( 10 ) .
						'آیتم های سفارش: {order_items}' . chr( 10 ) .
						'مجموع مبلغ سفارش: {order_total}' . chr( 10 ) .
						'نام: {billing_first_name}' . chr( 10 ) .
						'نام خانوادگی: {billing_last_name}' . chr( 10 ) .
						'بخش اول آدرس: {billing_address_part_1}' . chr( 10 ) .
						'بخش دوم آدرس: {billing_address_part_2}' . chr( 10 ) .
						'شهر: {billing_address_city}' . chr( 10 ) .
						'استان: {billing_address_state}' . chr( 10 ) .
						'کدپستی: {billing_address_postcode}' . chr( 10 ) .
						'ایمیل: {billing_email}' . chr( 10 ) .
						'شماره تلفن: {billing_phone}' . chr( 10 ) .
						'روش پرداخت: {billing_payment_method}' . chr( 10 ) .
						'روش ارسال: {billing_shipping_method}' . chr( 10 ) .
						'آیپی پرداخت کننده: {customer_ip}' . chr( 10 ) .
						'آیدی کاربری مشتری: {customer_id}' . chr( 10 ) .
						'یادداشت مشتری: {order_note}' . chr( 10 ) .
						'تعداد سفارش های مشتری: {customer_order_count}' . chr( 10 ) .
						'agent پرداخت کننده: {customer_user_agent}' . chr( 10 ),
					'لینک ویرایش سفارش:  {edit_order}' . chr( 10 ),
					'لینک مشاهده سفارش:  {view_order}' . chr( 10 ),
					'custom_attributes' => [ 'rows' => 35 ],
				),
				'section_end'                        => array(
					'type' => 'sectionend',
					'id'   => 'wc_settings_tab_onftb_end_section_2'
				),
			);

		return apply_filters( 'wc_settings_tab_onftb_settings', $settings, $section );

	}

	public function renderHelpDescription() {
		$token_help  = wp_kses( __( "تنها با ارسال پیام به ربات <a href='https://t.me/botfather' target='_blank'>@BotFather</a> و ارسال متن <code>/start</code>, سپس <code>/newbot</code> ومشخصات ربات که شامل نام و آیدی می باشد، توکن ربات شما برای تان ارسال می گردد.", 'onftb' ), array(
			'a'    => [
				'href'   => 'https://t.me/BotFather',
				'target' => '_blank'
			],
			'code' => []
		) );
		$chatid_help = wp_kses( __( "برای دریافت آیدی تان نیز از ربات <a href='https://t.me/UserAccInfoBot' target='_blank'>@UserAccInfoBot</a> استفاده کنید. راهنمایی های بیشتر و نحوه بدست آوردن آیدی چت یا گروه در این ربات وجود دارد.", "onftb" ), [
			'a'    => [
				'href'   => 'https://t.me/UserAccInfoBot',
				'target' => '_blank'
			],
			'code' => []
		] );
		$more_info   = wp_kses( __( "کانال تلگرامی افزونه را دنبال کنید:  <a href='https://t.me/ONFTB' target='_blank'>@ONFTB</a>", "onftb" ), [
			'a'    => [
				'href'   => 'https://tlgrm.in/ONFTB',
				'target' => '_blank'
			],
			'code' => []
		] );

		return $token_help . chr( 10 ) . $chatid_help . chr( 10 ) . $more_info;
	}

	public function renderAllowTagsDescription() {
		?>
        <style>
            .row {
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
            }

            textarea {
                width: 100%;
                font: 12px/normal 'Monaco', 'Menlo', 'Ubuntu Mono', 'Consolas', 'source-code-pro', monospace;
            }
            td div b{
                line-height: 2em;
            }
        </style>
        <div class="row text-right">
            <table class="form-table">
                <tbody>
                <tr>
                    <th><?php echo __( 'ارسال پیام تستی' ) ?></th>
                    <td>
                        <button id="onftb_send_test_message" type="button"
                                class="button-primary"><?= __( 'ارسال پیام' ) ?></button>
                    </td>
                </tr>
                <tr>
                    <th><?php echo __( 'تگ های مجاز' ) ?></th>
                    <td>
                        <div class="text-right" style="text-align: right;">
                            <pre>&lt;b&gt;&lt;strong&gt;&lt;i&gt;&lt;u&gt;&lt;em&gt;&lt;ins&gt;&lt;s&gt;&lt;strike&gt;&lt;del&gt;&lt;a&gt;&lt;code&gt;&lt;pre&gt;&lt;tg-spoiler&gt;&lt;blockquote&gt;&lt;tg-emoji&gt;</pre>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php echo __( 'شورت کد های قابل استفاده' ) ?></th>
                    <td>
                        <div>
                            <b>نام وبسایت: </b><code>{site_name}</code>
                            <b>شماره سفارش: </b><code>{order_id}</code>
                            <b>آیتم های سفارش: </b><code>{order_items}</code>
                            <b>زمان ثبت سفارش: </b><code>{order_date}</code>
                            <b>زمان ثبت سفارش (شمسی): </b><code>{order_date_per}</code>
                            <br>
                            <b>وضعیت سفارش: </b><code>{order_status}</code>
                            <b>مجموع مبلغ سفارش: </b><code>{order_total}</code>
                            <b>یادداشت مشتری: </b><code>{order_note}</code>
                            <b>نام: </b><code>{billing_first_name}</code>
                            <b>نام خانوادگی: </b><code>{billing_last_name}</code>
                            <br>
                            <b>بخش اول آدرس: </b><code>{billing_address_part_1}</code>
                            <b>بخش دوم آدرس: </b><code>{billing_address_part_2}</code>
                            <b>شهر: </b><code>{billing_address_city}</code>
                            <b>استان: </b><code>{billing_address_state}</code>
                            <b>کدپستی: </b><code>{billing_address_postcode}</code>
                            <br>
                            <b>ایمیل: </b><code>{billing_email}</code>
                            <b>شماره تلفن: </b><code>{billing_phone}</code>
                            <b>روش پرداخت: </b><code>{billing_payment_method}</code>
                            <b>روش ارسال: </b><code>{billing_shipping_method}</code>
                            <b>آیدی کاربری مشتری: </b><code>{customer_id}</code>
                            <br>
                            <b>آیپی پرداخت کننده: </b><code>{customer_ip}</code>
                            <b>تعداد سفارش های مشتری: </b><code>{customer_order_count}</code>
                            <b>agent پرداخت کننده: </b><code>{customer_user_agent}</code>
                            <b>لینک ویرایش سفارش: </b><code>{edit_order}</code>
                            <b>لینک مشاهده سفارش: </b><code>{view_order}</code>
                            <br>
                            <b>لینک گوگل مپ آدرس سفارش: </b><code>{billing_address_google_map}</code>
                            <b>لینک پرداخت سفارش درصورت وجود: </b><code>{payment_url}</code>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
		<?php
	}

	public function output() {
		echo '<div id="nktgnfw-header">
			<a href="https://t.me/ONFTB" target="_blank">
			<img src="' . ONFTB_PLUGIN_TG_BANNER . '" alt="Order Notification For Telegram Bot"></a>
			<br>
		    </div>';

        echo '<div id="woonotify-header">
			<a href="https://t.me/onftb/74" target="_blank">
			<img src="' . ONFTB_PLUGIN_WOONOTIFY_TG_BANNER . '" alt="WooNotify Ads"></a>
			<br>
		    </div>';
		$settings = $this->get_settings();
		\WC_Admin_Settings::output_fields( $settings );
		$this->renderAllowTagsDescription();
	}

	public function save() {
		$settings = $this->get_settings();
		\WC_Admin_Settings::save_fields( $settings );
	}
}