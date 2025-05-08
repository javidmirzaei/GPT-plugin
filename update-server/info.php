<?php
// خروجی این فایل باید یک JSON باشد که اطلاعات نسخه جدید را برمی‌گرداند
header('Content-Type: application/json');

$plugin = isset($_GET['plugin']) ? $_GET['plugin'] : '';

if ($plugin === 'wp-gpt-intermediate/wp-gpt-intermediate.php') {
    $info = [
        'name' => 'CONTENT GENERATOR',
        'version' => '1.0.2',
        'author' => 'Majid',
        'requires' => '5.0',
        'tested' => '6.4',
        'downloaded' => 1000,
        'last_updated' => date('Y-m-d'),
        'description' => 'Generates content using CHAT-GPT.',
        'changelog' => '<h4>نسخه 1.0.2</h4>
        <ul>
            <li>بهبود عملکرد و حل مشکلات</li>
            <li>اضافه شدن قابلیت جدید</li>
            <li>رفع باگ‌های گزارش شده</li>
        </ul>',
        'download_url' => 'https://example.com/plugins/content-generator/wp-gpt-intermediate-1.0.2.zip'
    ];
    
    echo json_encode($info);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Plugin not found']);
}
?> 