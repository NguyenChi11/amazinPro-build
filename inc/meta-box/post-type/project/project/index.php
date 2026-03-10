<?php
function buildpro_save_project_meta($post_id)
{
    if (!isset($_POST['buildpro_project_meta_nonce']) || !wp_verify_nonce($_POST['buildpro_project_meta_nonce'], 'buildpro_project_meta_save')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (get_post_type($post_id) !== 'project') {
        return;
    }
    $banner_id = isset($_POST['project_banner_id']) ? absint($_POST['project_banner_id']) : 0;
    $location = isset($_POST['location_project']) ? sanitize_text_field($_POST['location_project']) : '';
    $about = isset($_POST['about_project']) ? wp_kses_post($_POST['about_project']) : '';
    $about_image_id = isset($_POST['about_image_project']) ? absint($_POST['about_image_project']) : 0;
    $price = isset($_POST['price_project']) ? sanitize_text_field($_POST['price_project']) : '';
    $information = isset($_POST['information_project']) ? wp_kses_post($_POST['information_project']) : '';
    $datetime = isset($_POST['date_time_project']) ? sanitize_text_field($_POST['date_time_project']) : '';
    $total_area = isset($_POST['total_area_project']) ? sanitize_text_field($_POST['total_area_project']) : '';
    $completion = isset($_POST['completion_project']) ? sanitize_text_field($_POST['completion_project']) : '';
    $arch_style = isset($_POST['architectural_style_project']) ? sanitize_text_field($_POST['architectural_style_project']) : '';
    $gallery_raw = isset($_POST['project_gallery_ids']) ? $_POST['project_gallery_ids'] : '';
    $gallery_ids = array();
    if (is_array($gallery_raw)) {
        foreach ($gallery_raw as $gid) {
            $gallery_ids[] = absint($gid);
        }
    } elseif (is_string($gallery_raw)) {
        $gallery_ids = array_filter(array_map('absint', explode(',', $gallery_raw)));
    }
    $standards_raw = isset($_POST['project_standards']) && is_array($_POST['project_standards']) ? $_POST['project_standards'] : array();
    $standards = array();
    foreach ($standards_raw as $row) {
        $img = isset($row['image_id']) ? absint($row['image_id']) : 0;
        $title = isset($row['title']) ? sanitize_text_field($row['title']) : '';
        $desc = isset($row['description']) ? sanitize_textarea_field($row['description']) : '';
        if ($img || $title !== '' || $desc !== '') {
            $standards[] = array('image_id' => $img, 'title' => $title, 'description' => $desc);
        }
    }
    update_post_meta($post_id, 'project_banner_id', $banner_id);
    update_post_meta($post_id, 'location_project', $location);
    update_post_meta($post_id, 'about_project', $about);
    update_post_meta($post_id, 'about_image_project', $about_image_id);
    update_post_meta($post_id, 'price_project', $price);
    update_post_meta($post_id, 'date_time_project', $datetime);
    update_post_meta($post_id, 'project_gallery_ids', $gallery_ids);
    update_post_meta($post_id, 'project_standards', $standards);
    update_post_meta($post_id, 'information_project', $information);
    update_post_meta($post_id, 'total_area_project', $total_area);
    update_post_meta($post_id, 'completion_project', $completion);
    update_post_meta($post_id, 'architectural_style_project', $arch_style);
}
add_action('save_post_project', 'buildpro_save_project_meta');
