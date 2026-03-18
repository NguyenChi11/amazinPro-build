<?php
if (!defined('ABSPATH')) {
    exit;
}
$data = isset($data) && is_array($data) ? $data : array();
$title = isset($data['title']) ? $data['title'] : '';
$desc = isset($data['desc']) ? $data['desc'] : '';
?>
<div class="buildpro-post-block">
    <h4><?php echo esc_html__('Post Section', 'buildpro'); ?></h4>
    <p class="buildpro-post-field">
        <label><?php echo esc_html__('Title', 'buildpro'); ?></label>
        <input type="text" class="regular-text" data-field="title" value="<?php echo esc_attr($title); ?>"
            placeholder="<?php echo esc_attr__('LATEST POSTS', 'buildpro'); ?>">
    </p>
    <p class="buildpro-post-field">
        <label><?php echo esc_html__('Description', 'buildpro'); ?></label>
        <textarea rows="4" class="large-text" data-field="desc"
            placeholder="<?php echo esc_attr__('Description', 'buildpro'); ?>"><?php echo esc_textarea($desc); ?></textarea>
    </p>
    <input type="hidden" id="buildpro-post-data" <?php $this->link(); ?>
        value="<?php echo esc_attr(wp_json_encode($data)); ?>">
</div>