<?php
/*
Plugin Name: CONTENT GENERATOR
Description: Generates content using CHAT-GPT.
Version: 1.0.5
Author: Aaradbranding
Update URI: https://github.com/javidmirzaei/GPT-plugin/
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

// Add settings page
add_action('admin_menu', function() {
    add_options_page(
        'Settings',
        'GPT-CONTENT-GEN Settings',
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
    
    add_settings_section('wp_gpt_main', 'Main Settings', null, 'wp-gpt-settings');
    
    add_settings_field('wp_gpt_username', 'Username', function() {
        $username = get_option('wp_gpt_username');
        echo '<input type="text" name="wp_gpt_username" value="' . esc_attr($username) . '" class="regular-text" />';
    }, 'wp-gpt-settings', 'wp_gpt_main');
    
    add_settings_field('wp_gpt_api_key', 'OpenAI API Key', function() {
        $api_key = get_option('wp_gpt_api_key');
        echo '<input type="text" name="wp_gpt_api_key" value="' . esc_attr($api_key) . '" class="regular-text" />';
    }, 'wp-gpt-settings', 'wp_gpt_main');
    
    add_settings_field('wp_gpt_charge_amount', 'Charge Amount', function() {
        $charge_amount = get_option('wp_gpt_charge_amount');
        echo '<input type="number" name="wp_gpt_charge_amount" value="' . esc_attr($charge_amount) . '" class="regular-text" />';
    }, 'wp-gpt-settings', 'wp_gpt_main');

    add_settings_field('wp_gpt_intermediate_url', 'Intermediate Site URL', function() {
        $url = get_option('wp_gpt_intermediate_url');
        echo '<input type="url" name="wp_gpt_intermediate_url" value="' . esc_attr($url) . '" class="regular-text" />';
    }, 'wp-gpt-settings', 'wp_gpt_main');
    
    // Add update settings section
    add_settings_section('wp_gpt_updates', 'Update Settings', function() {
        echo '<p>Configure the automatic plugin update settings.</p>';
    }, 'wp-gpt-settings');
    
    add_settings_field('wp_gpt_update_server', 'Update Server URL', function() {
        $update_server = get_option('wp_gpt_update_server');
        echo '<input type="url" name="wp_gpt_update_server" value="' . esc_attr($update_server) . '" class="regular-text" />';
        echo '<p class="description">Enter the plugin update server URL. If left empty, GitHub will be used.</p>';
    }, 'wp-gpt-settings', 'wp_gpt_updates');
});

function wp_gpt_settings_page() {
    ?>
    <div class="wrap">
        <h1>Settings</h1>
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
