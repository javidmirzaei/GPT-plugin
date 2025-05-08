jQuery(document).ready(function($) {
    $('#wp-gpt-generate').on('click', function() {
        const prompt = $('#wp-gpt-prompt').val();
        const $output = $('#wp-gpt-output');
        $output.html('در حال تولید...');

        $.ajax({
            url: wpGptAjax.ajax_url,
            method: 'POST',
            data: {
                action: 'wp_gpt_generate',
                nonce: wpGptAjax.nonce,
                prompt: prompt
            },
            success: function(response) {
                if (response.success) {
                    $output.html(response.data.content);
                } else {
                    $output.html('خطا: ' + response.data.message);
                }
            },
            error: function() {
                $output.html('خطا در ارتباط با سرور.');
            }
        });
    });
});
