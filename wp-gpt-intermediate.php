<?php
/*
Plugin Name: تولید محتوا با هوش مصنوعی
Description: این افزونه به شما امکان می‌دهد با استفاده از هوش مصنوعی GPT محتوای حرفه‌ای و خلاقانه تولید کنید. قابلیت تولید انواع متن از جمله مقالات، توضیحات محصول، متون تبلیغاتی و بسیاری موارد دیگر را دارد.
Version: 1.1.0
Author: آراد برندینگ
Author URI: https://aradbranding.com
Update URI: https://github.com/javidmirzaei/GPT-plugin/
Text Domain: content-generator
Domain Path: /languages
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}



// Load required files
require_once plugin_dir_path(__FILE__) . 'includes/api.php';
require_once plugin_dir_path(__FILE__) . 'includes/metabox.php';
require_once plugin_dir_path(__FILE__) . 'includes/updater.php';

// Initialize update system
if (class_exists('WP_GPT_Updater')) {
    $updater = new WP_GPT_Updater(__FILE__);
}

// Register scripts and styles
add_action('admin_enqueue_scripts', function($hook) {
    // Scripts for post editor pages
    if (in_array($hook, ['post.php', 'post-new.php'])) {
        wp_enqueue_script('wp-gpt-js', plugin_dir_url(__FILE__) . 'assets/js/admin.js', ['jquery'], '1.0', true);
        wp_enqueue_style('wp-gpt-css', plugin_dir_url(__FILE__) . 'assets/css/admin.css', [], '1.0');
        wp_localize_script('wp-gpt-js', 'wpGptAjax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wp_gpt_nonce')
        ]);
    }
    
    // Scripts for settings page
    if ($hook === 'toplevel_page_wp-gpt-settings') {
        wp_enqueue_style('dashicons');
        wp_enqueue_script('wp-gpt-settings-js', plugin_dir_url(__FILE__) . 'assets/js/settings.js', ['jquery'], '1.0', true);
        wp_enqueue_style('wp-gpt-settings-css', plugin_dir_url(__FILE__) . 'assets/css/settings.css', [], '1.0');
    }
});

// Add settings page as a main menu item
add_action('admin_menu', function() {
    add_menu_page(
        'تنظیمات تولید محتوا',
        'تنظیمات تولید محتوا',
        'manage_options',
        'wp-gpt-settings',
        'wp_gpt_settings_page',
        'dashicons-admin-customizer', // آیکون مرتبط با هوش مصنوعی
        25 // موقعیت منو در سایدبار (بالاتر)
    );
});

add_action('admin_init', function() {
    register_setting('wp_gpt_settings', 'wp_gpt_username');
    register_setting('wp_gpt_settings', 'wp_gpt_api_key');
    register_setting('wp_gpt_settings', 'wp_gpt_charge_amount');
    register_setting('wp_gpt_settings', 'wp_gpt_intermediate_url');
    
    add_settings_section('wp_gpt_main', 'تنظیمات اصلی', function() {
        echo '<p>تنظیمات مورد نیاز برای عملکرد افزونه تولید محتوا با GPT را پیکربندی کنید.</p>';
    }, 'wp-gpt-settings');
    
    add_settings_field('wp_gpt_username', 'نام کاربری', function() {
        $username = get_option('wp_gpt_username');
        echo '<input type="text" name="wp_gpt_username" value="' . esc_attr($username) . '" class="regular-text" />';
        echo '<p class="description">نام کاربری ثبت شده شما برای سرویس تولید محتوا با GPT.</p>';
    }, 'wp-gpt-settings', 'wp_gpt_main');
    
    add_settings_field('wp_gpt_api_key', 'کلید API هوش مصنوعی', function() {
        $api_key = get_option('wp_gpt_api_key');
        echo '<input type="text" name="wp_gpt_api_key" value="' . esc_attr($api_key) . '" class="regular-text" />';
        echo '<p class="description">کلید API اپن‌ای‌آی شما برای تولید محتوا الزامی است. <a href="https://platform.openai.com/account/api-keys" target="_blank">کلید API خود را از اینجا دریافت کنید</a>.</p>';
    }, 'wp-gpt-settings', 'wp_gpt_main');
    
    add_settings_field('wp_gpt_charge_amount', 'مبلغ شارژ', function() {
        $charge_amount = get_option('wp_gpt_charge_amount');
        echo '<input type="number" name="wp_gpt_charge_amount" value="' . esc_attr($charge_amount) . '" class="regular-text" />';
        echo '<p class="description">مبلغ شارژ برای تولید محتوا را تنظیم کنید (در صورت کاربرد).</p>';
    }, 'wp-gpt-settings', 'wp_gpt_main');

    add_settings_field('wp_gpt_intermediate_url', 'آدرس سایت واسط', function() {
        $url = get_option('wp_gpt_intermediate_url');
        echo '<input type="url" name="wp_gpt_intermediate_url" value="' . esc_attr($url) . '" class="regular-text" />';
        echo '<p class="description">آدرس سایت واسط برای پردازش درخواست‌ها (در صورت استفاده از پراکسی).</p>';
    }, 'wp-gpt-settings', 'wp_gpt_main');
});

function wp_gpt_settings_page() {
    ?>
    <div class="wrap">
        <h1>تنظیمات تولید محتوا</h1>
        
        <div class="wp-gpt-settings-section">
            <p>
                به صفحه تنظیمات افزونه تولید محتوا با هوش مصنوعی خوش آمدید. با استفاده از این افزونه می‌توانید محتوای حرفه‌ای و با کیفیت بالا برای وب‌سایت خود تولید کنید.
            </p>
            <p>
                <strong>قابلیت‌های اصلی:</strong>
                <ul style="padding-right: 20px; margin-top: 10px;">
                    <li>تولید خودکار مقالات و محتوای وبلاگ</li>
                    <li>ساخت توضیحات محصول جذاب و متقاعدکننده</li>
                    <li>ایجاد متون تبلیغاتی و بازاریابی حرفه‌ای</li>
                    <li>نوشتن توضیحات سئو و متاتگ‌های بهینه</li>
                    <li>کمک در تولید محتوای شبکه‌های اجتماعی</li>
                </ul>
            </p>
            <p>
                برای استفاده از این افزونه، به یک کلید API معتبر OpenAI نیاز دارید. 
                <a href="https://platform.openai.com/account/api-keys" target="_blank">کلید API خود را از اینجا دریافت کنید</a>.
            </p>
        </div>
        
        <form method="post" action="options.php">
            <?php
            settings_fields('wp_gpt_settings');
            do_settings_sections('wp-gpt-settings');
            submit_button('ذخیره تنظیمات');
            ?>
        </form>
        
        <div class="wp-gpt-settings-section" style="margin-top: 30px;">
            <h2>راهنما و پشتیبانی</h2>
            <p>
                برای استفاده بهینه از این افزونه، پیشنهاد می‌کنیم موارد زیر را رعایت کنید:
            </p>
            <ul style="padding-right: 20px; margin-top: 10px;">
                <li>کلید API معتبر با اعتبار کافی تهیه کنید</li>
                <li>از این افزونه برای تولید محتوای با کیفیت و اصیل استفاده کنید</li>
                <li>محتوای تولید شده را برای اطمینان از دقت و کیفیت، همیشه بررسی و ویرایش کنید</li>
                <li>برای حفظ هویت برند خود، سبک و لحن محتوا را شخصی‌سازی کنید</li>
            </ul>
            <p>
                برای راهنمایی بیشتر یا گزارش مشکلات، با ما از طریق <a href="mailto:support@aaradbranding.com">support@aaradbranding.com</a> تماس بگیرید.
            </p>
        </div>
        
        <div class="wp-gpt-settings-section" style="margin-top: 30px; background-color: #f8f8f8;">
            <h2>نحوه استفاده</h2>
            <p>
                پس از تنظیم افزونه، می‌توانید در بخش ویرایشگر محتوا، از قابلیت‌های هوش مصنوعی برای تولید محتوا استفاده کنید.
                کافیست موضوع مورد نظر خود را وارد کرده و بر روی دکمه «تولید محتوا» کلیک کنید.
            </p>
            <p>
                برای دریافت نتایج بهتر، توصیه می‌کنیم درخواست‌های دقیق و مشخصی به هوش مصنوعی ارسال کنید.
                هرچه درخواست شما دقیق‌تر باشد، محتوای بهتری دریافت خواهید کرد.
            </p>
        </div>
    </div>
    <?php
}
