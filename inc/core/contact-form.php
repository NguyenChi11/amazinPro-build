<?php
function buildpro_cf7_is_active()
{
    return class_exists('WPCF7_ContactForm');
}

function buildpro_cf7_demo_form_title()
{
    return 'BuildPro Contact Form';
}

function buildpro_cf7_demo_form_id_option()
{
    return 'buildpro_cf7_demo_form_id';
}

function buildpro_cf7_wait_option()
{
    return 'buildpro_cf7_wait_activation';
}

function buildpro_cf7_get_saved_form_id()
{
    return (int) get_option(buildpro_cf7_demo_form_id_option(), 0);
}

function buildpro_cf7_save_form_id($id)
{
    update_option(buildpro_cf7_demo_form_id_option(), (int) $id);
}

function buildpro_cf7_demo_form_content()
{
    $options = array(
        'Residential Development',
        'Commercial Development',
        'Industrial Development',
        'Other'
    );
    $select = implode('" "', $options);
    $form = '';
    $form .= '<div class="contact-form__grid">';
    $form .= '<p class="contact-form__field"><label class="contact-form__label">Full name</label>[text* your-name class:contact-form__input placeholder "John Doe"]</p>';
    $form .= '<p class="contact-form__field"><label class="contact-form__label">Email Address</label>[email* your-email class:contact-form__input placeholder "support@amazinpro.com"]</p>';
    $form .= '<p class="contact-form__field"><label class="contact-form__label">Phone Number</label>[tel your-phone class:contact-form__input placeholder "(+84)349582808"]</p>';
    $form .= '<p class="contact-form__field"><label class="contact-form__label">Project Type</label>[select* project-type class:contact-form__input include_blank "' . $select . '"]</p>';
    $form .= '<p class="contact-form__field"><label class="contact-form__label">Project Type</label>[textarea your-message class:contact-form__input placeholder "Tell us about your project requirements . . ."]</p>';
    $form .= '<p class="contact-form__actions">[submit class:contact-form__submit "Submit Request"]</p>';
    $form .= '</div>';
    return $form;
}

function buildpro_cf7_find_form_id()
{
    $saved = buildpro_cf7_get_saved_form_id();
    if ($saved > 0) {
        $p = get_post($saved);
        if ($p && $p->post_type === 'wpcf7_contact_form') {
            return (int) $saved;
        }
    }
    $existing = get_page_by_title(buildpro_cf7_demo_form_title(), OBJECT, 'wpcf7_contact_form');
    if ($existing) {
        buildpro_cf7_save_form_id($existing->ID);
        return (int) $existing->ID;
    }
    return 0;
}

function buildpro_cf7_update_form_if_needed($form_id = 0)
{
    $fid = $form_id > 0 ? (int) $form_id : buildpro_cf7_find_form_id();
    if ($fid <= 0) {
        return 0;
    }
    $content = (string) get_post_meta($fid, '_form', true);
    $needs_update = (
        strpos($content, 'contact-form__input') === false
        || strpos($content, 'contact-form__submit') === false
        || strpos($content, 'contact-form__grid') === false
    );
    if ($needs_update) {
        update_post_meta($fid, '_form', buildpro_cf7_demo_form_content());
    }
    return $fid;
}

function buildpro_cf7_create_form()
{
    if (!buildpro_cf7_is_active()) {
        return 0;
    }
    $existing_id = buildpro_cf7_find_form_id();
    if ($existing_id > 0) {
        return (int) $existing_id;
    }
    $post_id = wp_insert_post(array(
        'post_type' => 'wpcf7_contact_form',
        'post_status' => 'publish',
        'post_title' => buildpro_cf7_demo_form_title(),
        'post_name' => 'buildpro-contact-form',
    ));
    if ($post_id && !is_wp_error($post_id)) {
        add_post_meta($post_id, '_form', buildpro_cf7_demo_form_content(), true);
        buildpro_cf7_save_form_id($post_id);
        return (int) $post_id;
    }
    return 0;
}

function buildpro_cf7_maybe_create_form()
{
    if (!buildpro_cf7_is_active()) {
        update_option(buildpro_cf7_wait_option(), 1);
        return;
    }
    $created_id = buildpro_cf7_create_form();
    if ($created_id > 0) {
        delete_option(buildpro_cf7_wait_option());
        buildpro_cf7_update_form_if_needed($created_id);
    } else {
        buildpro_cf7_update_form_if_needed();
    }
}

function buildpro_cf7_on_theme_activate()
{
    buildpro_cf7_maybe_create_form();
}
add_action('after_switch_theme', 'buildpro_cf7_on_theme_activate');

function buildpro_cf7_on_plugins_loaded()
{
    $wait = (int) get_option(buildpro_cf7_wait_option(), 0);
    if ($wait === 1 && buildpro_cf7_is_active()) {
        buildpro_cf7_maybe_create_form();
    }
    if (buildpro_cf7_is_active()) {
        buildpro_cf7_update_form_if_needed();
    }
}
add_action('plugins_loaded', 'buildpro_cf7_on_plugins_loaded', 99);

function buildpro_cf7_on_plugin_activate($plugin, $network_wide)
{
    if ($plugin === 'contact-form-7/wp-contact-form-7.php') {
        buildpro_cf7_maybe_create_form();
    }
}
add_action('activated_plugin', 'buildpro_cf7_on_plugin_activate', 10, 2);
