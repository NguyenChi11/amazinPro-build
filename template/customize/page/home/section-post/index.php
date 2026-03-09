<?php
if (!defined('ABSPATH')) {
    exit;
}
$data = isset($data) && is_array($data) ? $data : array();
$title = isset($data['title']) ? $data['title'] : '';
$desc = isset($data['desc']) ? $data['desc'] : '';
?>
<div class="buildpro-post-block">
    <h4>Post Section</h4>
    <p class="buildpro-post-field">
        <label>Title</label>
        <input type="text" class="regular-text" data-field="title" value="<?php echo esc_attr($title); ?>"
            placeholder="LATEST POSTS">
    </p>
    <p class="buildpro-post-field">
        <label>Description</label>
        <textarea rows="4" class="large-text" data-field="desc"
            placeholder="description"><?php echo esc_textarea($desc); ?></textarea>
    </p>
    <input type="hidden" id="buildpro-post-data" <?php $this->link(); ?>
        value="<?php echo esc_attr(wp_json_encode($data)); ?>">
</div>