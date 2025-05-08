<?php
/*
Plugin Name: CONTENT GENERATOR
Description: Generates content using CHAT-GPT.
Version: 1.0.1
Author: Majid
Update URI: https://example.com/plugins/content-generator/
*/

// جلوگیری از دسترسی مستقیم
if (!defined('ABSPATH')) {
    exit;
}

// لود فایل‌های مورد نیاز
require_once plugin_dir_path(__FILE__) . 'includes/api.php';
require_once plugin_dir_path(__FILE__) . 'includes/metabox.php';
require_once plugin_dir_path(__FILE__) . 'includes/updater.php';

// راه‌اندازی سیستم بروزرسانی
if (class_exists('WP_GPT_Updater')) {
    $updater = new WP_GPT_Updater(__FILE__);
}

// ثبت اسکریپت‌ها و استایل‌ها
add_action('admin_enqueue_scripts', function($hook) {
    if (!in_array($hook, ['post.php', 'post-new.php'])) {
        return;
    }
    wp_enqueue_script('wp-gpt-js', plugin_dir_url(__FILE__) . 'assets/js/admin.js', ['jquery'], '1.0', true);
    wp_enqueue_style('wp-gpt-css', plugin_dir_url(__FILE__) . 'assets/css/admin.css', [], '1.0');
    wp_localize_script('wp-gpt-js', 'wpGptAjax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('wp_gpt_nonce')
    ]);
});

// اضافه کردن صفحه تنظیمات
add_action('admin_menu', function() {
    add_options_page(
        'تنظیمات',
        'تنظیمات اصلی GPT-CONTENT-GEN',
        'manage_options',
        'wp-gpt-settings',
        'wp_gpt_settings_page'
    );
});

add_action('admin_init', function() {
    register_setting('wp_gpt_settings', 'wp_gpt_username');
    register_setting('wp_gpt_settings', 'wp_gpt_api_key');
    register_setting('wp_gpt_settings', 'wp_gpt_charge_amount');
    register_setting('wp_gpt_settings', 'wp_gpt_intermediate_url');
    register_setting('wp_gpt_settings', 'wp_gpt_update_server');
    
    add_settings_section('wp_gpt_main', 'تنظیمات اصلی', null, 'wp-gpt-settings');
    
    add_settings_field('wp_gpt_username', 'نام کاربری', function() {
        $username = get_option('wp_gpt_username');
        echo '<input type="text" name="wp_gpt_username" value="' . esc_attr($username) . '" class="regular-text" />';
    }, 'wp-gpt-settings', 'wp_gpt_main');
    
    add_settings_field('wp_gpt_api_key', 'کلید API OpenAI', function() {
        $api_key = get_option('wp_gpt_api_key');
        echo '<input type="text" name="wp_gpt_api_key" value="' . esc_attr($api_key) . '" class="regular-text" />';
    }, 'wp-gpt-settings', 'wp_gpt_main');
    
    add_settings_field('wp_gpt_charge_amount', 'مقدار شارژ', function() {
        $charge_amount = get_option('wp_gpt_charge_amount');
        echo '<input type="number" name="wp_gpt_charge_amount" value="' . esc_attr($charge_amount) . '" class="regular-text" />';
    }, 'wp-gpt-settings', 'wp_gpt_main');

    add_settings_field('wp_gpt_intermediate_url', 'URL سایت واسط', function() {
        $url = get_option('wp_gpt_intermediate_url');
        echo '<input type="url" name="wp_gpt_intermediate_url" value="' . esc_attr($url) . '" class="regular-text" />';
    }, 'wp-gpt-settings', 'wp_gpt_main');
    
    // اضافه کردن بخش تنظیمات بروزرسانی
    add_settings_section('wp_gpt_updates', 'تنظیمات بروزرسانی', function() {
        echo '<p>تنظیمات مربوط به بروزرسانی‌های خودکار پلاگین را تعیین کنید.</p>';
    }, 'wp-gpt-settings');
    
    add_settings_field('wp_gpt_update_server', 'آدرس سرور بروزرسانی', function() {
        $update_server = get_option('wp_gpt_update_server');
        echo '<input type="url" name="wp_gpt_update_server" value="' . esc_attr($update_server) . '" class="regular-text" />';
        echo '<p class="description">آدرس سرور بروزرسانی پلاگین را وارد کنید. اگر خالی باشد، از گیت‌هاب استفاده می‌شود.</p>';
    }, 'wp-gpt-settings', 'wp_gpt_updates');
});

function wp_gpt_settings_page() {
    ?>
    <div class="wrap">
        <h1>تنظیمات </h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('wp_gpt_settings');
            do_settings_sections('wp-gpt-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}
