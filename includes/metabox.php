<?php
add_action('add_meta_boxes', function() {
    add_meta_box(
        'wp_gpt_metabox',
        'تولید محتوا با GPT',
        'wp_gpt_metabox_callback',
        'post',
        'side',
        'high'
    );
});

function wp_gpt_metabox_callback($post) {
    ?>
    <div>
        <label for="wp-gpt-prompt">درخواست خود را وارد کنید:</label>
        <textarea class="text-area" id="wp-gpt-prompt" rows="4" style="width: 100%;"></textarea>
        <button id="wp-gpt-generate" class="btn">تولید محتوا</button>
        <div id="wp-gpt-output" style="margin-top: 10px;"></div>
    </div>
    <?php
}
