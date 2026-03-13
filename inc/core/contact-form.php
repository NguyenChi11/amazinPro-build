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

function buildpro_cf7_is_flamingo_active()
{
    return class_exists('Flamingo_Contact') && class_exists('Flamingo_Inbound_Message');
}

function buildpro_cf7_get_posted_value($posted_data, $keys)
{
    foreach ((array) $keys as $key) {
        if (!empty($posted_data[$key])) {
            $value = $posted_data[$key];
            if (is_array($value)) {
                $value = implode(', ', array_filter(array_map('strval', $value)));
            }
            $value = is_string($value) ? trim($value) : '';
            if ($value !== '') {
                return $value;
            }
        }
    }
    return '';
}

function buildpro_cf7_subject_from_sender_email($email, $fallback = '')
{
    $email = is_string($email) ? trim($email) : '';
    if ($email === '' || strpos($email, '@') === false) {
        return $fallback;
    }

    $local_part = trim((string) strtok($email, '@'));
    if ($local_part === '') {
        return $email;
    }

    $pretty = str_replace(array('.', '_', '-'), ' ', $local_part);
    $pretty = preg_replace('/\s+/', ' ', (string) $pretty);
    $pretty = trim((string) $pretty);

    return $pretty !== '' ? ucwords($pretty) : $email;
}

function buildpro_cf7_set_flamingo_subject_from_sender($args)
{
    if (!is_array($args)) {
        return $args;
    }

    $from_email = isset($args['from_email']) ? (string) $args['from_email'] : '';
    $current_subject = isset($args['subject']) ? (string) $args['subject'] : '';
    $args['subject'] = buildpro_cf7_subject_from_sender_email($from_email, $current_subject);

    return $args;
}

add_filter('wpcf7_flamingo_inbound_message_parameters', 'buildpro_cf7_set_flamingo_subject_from_sender', 20, 1);

function buildpro_cf7_force_store_to_flamingo($contact_form, $result = array())
{
    if (!buildpro_cf7_is_flamingo_active() || !class_exists('WPCF7_Submission')) {
        return;
    }

    $submission = WPCF7_Submission::get_instance();
    if (!$submission) {
        return;
    }

    $posted_data = $submission->get_posted_data();
    if (empty($posted_data) || !is_array($posted_data)) {
        return;
    }

    if ($submission->get_meta('do_not_store')) {
        return;
    }

    $posted_data_hash = method_exists($submission, 'get_posted_data_hash') ? $submission->get_posted_data_hash() : '';
    if ($posted_data_hash !== '' && method_exists('Flamingo_Inbound_Message', 'find')) {
        $exists = Flamingo_Inbound_Message::find(array(
            'posts_per_page' => 1,
            'hash' => $posted_data_hash,
        ));
        if (!empty($exists)) {
            return;
        }
    }

    $email = buildpro_cf7_get_posted_value($posted_data, array('your-email', 'email'));
    $name = buildpro_cf7_get_posted_value($posted_data, array('your-name', 'fullname', 'name'));
    $subject = buildpro_cf7_get_posted_value($posted_data, array('your-subject', 'subject'));

    if ($subject === '') {
        $subject = sprintf('Form submission: %s', $contact_form->title());
    }

    $subject = buildpro_cf7_subject_from_sender_email($email, $subject);

    if ($email !== '' && is_email($email) && method_exists('Flamingo_Contact', 'add')) {
        Flamingo_Contact::add(array(
            'email' => $email,
            'name' => $name,
        ));
    }

    $timestamp = $submission->get_meta('timestamp');
    $meta = array(
        'url' => method_exists($submission, 'get_meta') ? (string) $submission->get_meta('url') : '',
        'remote_ip' => method_exists($submission, 'get_meta') ? (string) $submission->get_meta('remote_ip') : '',
        'user_agent' => method_exists($submission, 'get_meta') ? (string) $submission->get_meta('user_agent') : '',
    );

    $args = array(
        'channel' => 'contact-form-7',
        'status' => method_exists($submission, 'get_status') ? $submission->get_status() : 'mail_sent',
        'subject' => $subject,
        'from' => trim(sprintf('%s <%s>', $name, $email)),
        'from_name' => $name,
        'from_email' => $email,
        'fields' => $posted_data,
        'meta' => array_filter($meta),
        'akismet' => method_exists($submission, 'pull') ? (array) $submission->pull('akismet') : array(),
        'recaptcha' => method_exists($submission, 'pull') ? (array) $submission->pull('recaptcha') : array(),
        'consent' => method_exists($submission, 'collect_consent') ? (array) $submission->collect_consent() : array(),
        'timestamp' => $timestamp,
        'posted_data_hash' => $posted_data_hash,
    );

    $args = apply_filters('wpcf7_flamingo_inbound_message_parameters', $args);
    Flamingo_Inbound_Message::add($args);
}

add_action('wpcf7_submit', 'buildpro_cf7_force_store_to_flamingo', 20, 2);

function buildpro_cf7_skip_mail_for_demo_form($skip_mail, $contact_form)
{
    if (!buildpro_cf7_is_active() || !is_object($contact_form) || !method_exists($contact_form, 'id')) {
        return $skip_mail;
    }

    $demo_form_id = buildpro_cf7_find_form_id();
    if ($demo_form_id <= 0) {
        return $skip_mail;
    }

    if ((int) $contact_form->id() !== (int) $demo_form_id) {
        return $skip_mail;
    }

    $env = function_exists('wp_get_environment_type') ? wp_get_environment_type() : 'production';
    if (!in_array($env, array('local', 'development'), true)) {
        return $skip_mail;
    }

    // In local environments, mail() is commonly unavailable and causes mail_failed.
    // Skip actual sending for the BuildPro demo form so submit returns success.
    return true;
}

add_filter('wpcf7_skip_mail', 'buildpro_cf7_skip_mail_for_demo_form', 20, 2);
