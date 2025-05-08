<?php
function wp_gpt_generate_content($prompt) {
    $username = get_option('wp_gpt_username');
    $api_key = get_option('wp_gpt_api_key');
    $charge_amount = get_option('wp_gpt_charge_amount');
    $intermediate_url = get_option('wp_gpt_intermediate_url');

    if (!$username || !$api_key || !$charge_amount || !$intermediate_url) {
        return new WP_Error('missing_settings', 'برخی تنظیمات لازم وارد نشده‌اند.');
    }

    $response = wp_remote_post($intermediate_url, [
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode([
            'username' => $username,
            'api_key' => $api_key,
            'charge_amount' => $charge_amount,
            'prompt' => $prompt,
            'model' => 'gpt-3.5-turbo',
            'max_tokens' => 150,
            'temperature' => 0.7
        ]),
        'timeout' => 30
    ]);

    if (is_wp_error($response)) return $response;

    $body = json_decode(wp_remote_retrieve_body($response), true);
    return $body['content'] ?? new WP_Error('api_error', $body['error'] ?? 'خطا در تولید محتوا.');
}

add_action('wp_ajax_wp_gpt_generate', function() {
    check_ajax_referer('wp_gpt_nonce', 'nonce');
    $prompt = sanitize_text_field($_POST['prompt'] ?? '');
    if (!$prompt) wp_send_json_error(['message' => 'درخواست خالی است.']);

    $result = wp_gpt_generate_content($prompt);
    if (is_wp_error($result)) wp_send_json_error(['message' => $result->get_error_message()]);

    wp_send_json_success(['content' => $result]);
});
