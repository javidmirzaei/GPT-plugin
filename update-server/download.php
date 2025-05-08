<?php
// این فایل برای ارائه فایل ZIP جدید استفاده می‌شود
$plugin = isset($_GET['plugin']) ? $_GET['plugin'] : '';

// بررسی درخواست پلاگین
if ($plugin === 'wp-gpt-intermediate/wp-gpt-intermediate.php') {
    // مسیر فایل ZIP نسخه جدید
    $file_path = __DIR__ . '/files/wp-gpt-intermediate-1.0.2.zip';
    
    // بررسی وجود فایل
    if (file_exists($file_path)) {
        // تنظیم هدرهای مناسب برای دانلود
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="wp-gpt-intermediate-1.0.2.zip"');
        header('Content-Length: ' . filesize($file_path));
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // خواندن و ارسال فایل
        readfile($file_path);
        exit;
    } else {
        // فایل موجود نیست
        http_response_code(404);
        echo 'فایل بروزرسانی یافت نشد.';
    }
} else {
    // پلاگین نامعتبر
    http_response_code(400);
    echo 'درخواست نامعتبر است.';
}
?> 