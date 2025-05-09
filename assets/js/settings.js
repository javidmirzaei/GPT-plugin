jQuery(document).ready(function($) {
    // Add classes to form elements for styling
    $('.wrap h1').text('تنظیمات تولید محتوا');
    $('.wrap').addClass('wp-gpt-settings-page');
    
    // Add classes to settings sections
    $('form h2').each(function() {
        var $heading = $(this);
        var $section = $heading.next('table');
        
        $heading.wrap('<div class="wp-gpt-settings-section"></div>');
        var $container = $heading.parent();
        
        $section.find('tr').each(function() {
            var $row = $(this);
            var $label = $row.find('th');
            var $field = $row.find('td');
            
            // Create a new div with the right classes
            var $fieldContainer = $('<div class="wp-gpt-settings-field"></div>');
            
            // Move the label and field into the new container
            $fieldContainer.append($label.find('label').clone());
            $fieldContainer.append($field.children());
            
            // Special handling for API key field
            if ($label.text().indexOf('کلید API') !== -1) {
                $fieldContainer.addClass('wp-gpt-api-key-field');
                var $input = $fieldContainer.find('input');
                $input.attr('type', 'password');
                
                // Add toggle button for showing/hiding the API key
                var $toggle = $('<button type="button" class="wp-gpt-api-key-toggle" aria-label="نمایش/پنهان‌سازی کلید API">' +
                               '<span class="dashicons dashicons-visibility"></span></button>');
                
                $fieldContainer.append($toggle);
                
                // Add API key validation status indicator
                var $status = $('<span class="wp-gpt-api-status">تایید نشده</span>');
                $fieldContainer.append($status);
                
                // Toggle API key visibility
                $toggle.on('click', function(e) {
                    e.preventDefault();
                    
                    var type = $input.attr('type') === 'password' ? 'text' : 'password';
                    $input.attr('type', type);
                    
                    // Toggle icon
                    var $icon = $(this).find('.dashicons');
                    if (type === 'text') {
                        $icon.removeClass('dashicons-visibility').addClass('dashicons-hidden');
                    } else {
                        $icon.removeClass('dashicons-hidden').addClass('dashicons-visibility');
                    }
                });
                
                // Basic client-side API key format validation
                $input.on('blur', function() {
                    var apiKey = $(this).val().trim();
                    
                    if (apiKey) {
                        if (apiKey.startsWith('sk-') && apiKey.length > 20) {
                            $status.text('فرمت معتبر').removeClass('invalid checking').addClass('valid');
                        } else {
                            $status.text('فرمت نامعتبر').removeClass('valid checking').addClass('invalid');
                        }
                    } else {
                        $status.text('تایید نشده').removeClass('valid invalid checking');
                    }
                });
            }
            
            // Replace the table row with our new container
            $container.append($fieldContainer);
        });
        
        // Remove the original table
        $section.remove();
    });
    
    // Add tooltips to fields
    var tooltips = {
        'نام کاربری': 'نام کاربری شما برای دسترسی به سیستم تولید محتوا. این نام توسط آراد برندینگ به شما ارائه شده است.',
        'کلید API اپن‌ای‌آی': 'کلید API شما برای اتصال به سرویس OpenAI و تولید محتوا با هوش مصنوعی. این کلید را می‌توانید از حساب کاربری OpenAI خود دریافت کنید.',
        'مبلغ شارژ': 'در صورتی که از این افزونه برای ارائه خدمات به کاربران استفاده می‌کنید، می‌توانید مبلغ هر درخواست تولید محتوا را در اینجا تنظیم کنید.',
        'آدرس سایت واسط': 'اگر از یک سرور واسط برای ارتباط با API استفاده می‌کنید (به دلیل محدودیت‌های دسترسی)، آدرس آن را در اینجا وارد کنید.'
    };
    
    $('.wp-gpt-settings-field label').each(function() {
        var labelText = $(this).text();
        var tooltipText = tooltips[labelText];
        
        if (tooltipText) {
            $(this).append(
                $('<span class="wp-gpt-tooltip"><span class="dashicons dashicons-editor-help"></span>' +
                  '<span class="tooltip-content">' + tooltipText + '</span></span>')
            );
        }
    });
    
    // استایل دهی به دکمه ذخیره تنظیمات
    var $saveButton = $('.wp-gpt-settings-page .button-primary');
    $saveButton.text('ذخیره تنظیمات');
    
    // اضافه کردن آیکون به دکمه
    $saveButton.prepend('<span class="dashicons dashicons-saved" style="margin-left: 8px; vertical-align: text-top;"></span>');
    
    // افکت های هاور پیشرفته برای دکمه
    $saveButton.hover(
        function() {
            $(this).css({
                'background': '#c1636e',
                'transform': 'translateY(-2px)',
                'box-shadow': '0 4px 8px rgba(165, 82, 95, 0.3)'
            });
        },
        function() {
            $(this).css({
                'background': '#a5525f',
                'transform': 'translateY(0)',
                'box-shadow': '0 2px 0 #943b47'
            });
        }
    );
    
    // Add animations and feedback on save
    $('form').on('submit', function() {
        $saveButton.addClass('updating')
            .css({
                'position': 'relative',
                'padding-right': '40px',
                'padding-left': '20px'
            })
            .prepend(
                $('<span class="spinner is-active"></span>').css({
                    'position': 'absolute',
                    'right': '10px',
                    'left': 'auto',
                    'top': '50%',
                    'margin-top': '-10px'
                })
            );
            
        // اضافه کردن افکت موج دار زیبا هنگام کلیک
        $("<span class='ripple'></span>").appendTo($saveButton);
        var $ripple = $saveButton.find('.ripple');
        $ripple.css({
            'position': 'absolute',
            'border-radius': '50%',
            'background': 'rgba(255, 255, 255, 0.4)',
            'transform': 'scale(0)',
            'width': '100px',
            'height': '100px',
            'right': '0',
            'top': '0',
            'animation': 'ripple 0.8s linear'
        });
        
        setTimeout(function() {
            $ripple.remove();
        }, 800);
    });
    
    // اضافه کردن انیمیشن موج دار به CSS
    $('<style>@keyframes ripple {to {transform: scale(2.5); opacity: 0;}}</style>').appendTo('head');
    
    // Add plugin version info
    $('.wp-gpt-settings-page').append(
        $('<div class="wp-gpt-version-info">تولید محتوا با هوش مصنوعی | نسخه 1.1.0 | &copy; آراد برندینگ</div>')
    );
    
    // اضافه کردن یک نوار پیشرفت تزئینی 
    var $progressBar = $('<div class="wp-gpt-progress-bar"><div class="wp-gpt-progress-fill"></div></div>');
    $('.wp-gpt-settings-page').prepend($progressBar);
    
    $progressBar.css({
        'height': '4px',
        'width': '100%',
        'background': '#f0f0f0',
        'border-radius': '2px',
        'margin-bottom': '20px',
        'overflow': 'hidden'
    });
    
    $('.wp-gpt-progress-fill').css({
        'height': '100%',
        'width': '0',
        'background': 'linear-gradient(to right, #a5525f, #d4af37)',
        'transition': 'width 1.5s cubic-bezier(0.1, 0.7, 0.6, 1)'
    });
    
    // اجرای انیمیشن پیشرفت پس از بارگذاری صفحه
    setTimeout(function() {
        $('.wp-gpt-progress-fill').css('width', '100%');
    }, 500);
}); 