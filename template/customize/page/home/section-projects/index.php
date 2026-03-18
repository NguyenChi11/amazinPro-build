<?php
// $data is provided by the including scope (BuildPro_Portfolio_Control::render_content)
// $this is WP_Customize_Control instance; we use $this->link() to bind the hidden input
if (!is_array($data)) {
    $data = array();
}
$title = isset($data['title']) ? sanitize_text_field($data['title']) : '';
$desc  = isset($data['description']) ? sanitize_textarea_field($data['description']) : '';
?>
<input type="hidden" id="buildpro-portfolio-data" <?php $this->link(); ?>
    value="<?php echo esc_attr(wp_json_encode(array('title' => $title, 'description' => $desc))); ?>">
<div id="buildpro-portfolio-wrapper">
    <div class="buildpro-portfolio-block">
        <h4><?php echo esc_html__('Portfolio Title', 'buildpro'); ?></h4>
        <p class="buildpro-portfolio-field">
            <label><?php echo esc_html__('Title', 'buildpro'); ?></label>
            <input type="text" class="regular-text" data-field="title" value="<?php echo esc_attr($title); ?>">
        </p>
        <h4><?php echo esc_html__('Description', 'buildpro'); ?></h4>
        <p class="buildpro-portfolio-field">
            <label><?php echo esc_html__('Description', 'buildpro'); ?></label>
            <textarea rows="4" class="large-text" data-field="description"><?php echo esc_textarea($desc); ?></textarea>
        </p>
    </div>
    <div class="buildpro-portfolio-actions"><button type="button" class="button button-primary"
            id="buildpro-portfolio-apply"><?php echo esc_html__('Apply', 'buildpro'); ?></button></div>
    <p class="description"><?php echo esc_html__('Changes are previewed instantly. Click Publish to save.', 'buildpro'); ?></p>
</div>