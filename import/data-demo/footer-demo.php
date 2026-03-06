<?php
function buildpro_import_footer_demo()
{
    $has_any = false;
    $mods = array(
        'footer_banner_image_id',
        'footer_information_logo_id',
        'footer_information_title',
        'footer_information_sub_title',
        'footer_information_description',
        'footer_list_pages',
        'footer_contact_location',
        'footer_contact_phone',
        'footer_contact_email',
        'footer_contact_time',
        'footer_contact_links',
        'footer_create_build_text',
        'footer_policy_text',
        'footer_policy_link',
        'footer_servicer_text',
        'footer_servicer_link'
    );
    foreach ($mods as $m) {
        $val = get_theme_mod($m, null);
        if ($val !== null && $val !== '' && !(is_array($val) && empty($val))) {
            $has_any = true;
            break;
        }
    }
    if ($has_any) {
        return;
    }
    if (!function_exists('buildpro_import_parse_js')) {
        return;
    }
    $data = buildpro_import_parse_js('/assets/data/footer-data.js', 'footerData');
    if (!is_array($data)) {
        return;
    }
    if (isset($data['banner']) && $data['banner'] && function_exists('buildpro_import_image_id')) {
        $bid = buildpro_import_image_id($data['banner']);
        if ($bid) {
            set_theme_mod('footer_banner_image_id', (int)$bid);
        }
    }
    if (isset($data['information']) && is_array($data['information'])) {
        $info = $data['information'];
        if (isset($info['logo']) && $info['logo'] && function_exists('buildpro_import_image_id')) {
            $lid = buildpro_import_image_id($info['logo']);
            if ($lid) {
                set_theme_mod('footer_information_logo_id', (int)$lid);
            }
        }
        if (isset($info['title']) && is_string($info['title'])) {
            set_theme_mod('footer_information_title', (string)$info['title']);
        }
        if (isset($info['subTitle']) && is_string($info['subTitle'])) {
            set_theme_mod('footer_information_sub_title', (string)$info['subTitle']);
        }
        if (isset($info['description']) && is_string($info['description'])) {
            set_theme_mod('footer_information_description', (string)$info['description']);
        }
    }
    if (isset($data['pages']) && is_array($data['pages'])) {
        $pages = array();
        foreach ($data['pages'] as $p) {
            $pages[] = array(
                'title' => isset($p['title']) ? (string)$p['title'] : '',
                'url' => isset($p['url']) ? esc_url_raw($p['url']) : '',
                'target' => isset($p['target']) ? (string)$p['target'] : '',
            );
        }
        set_theme_mod('footer_list_pages', $pages);
    }
    if (isset($data['contact']) && is_array($data['contact'])) {
        $ct = $data['contact'];
        if (isset($ct['location'])) {
            set_theme_mod('footer_contact_location', (string)$ct['location']);
        }
        if (isset($ct['phone'])) {
            set_theme_mod('footer_contact_phone', (string)$ct['phone']);
        }
        if (isset($ct['email'])) {
            set_theme_mod('footer_contact_email', (string)$ct['email']);
        }
        if (isset($ct['time'])) {
            set_theme_mod('footer_contact_time', (string)$ct['time']);
        }
    }
    if (isset($data['contactLinks']) && is_array($data['contactLinks'])) {
        $links = array();
        foreach ($data['contactLinks'] as $cl) {
            $icon_id = 0;
            if (isset($cl['icon']) && $cl['icon'] && function_exists('buildpro_import_image_id')) {
                $icon_id = (int) buildpro_import_image_id($cl['icon']);
            }
            $links[] = array(
                'icon_id' => $icon_id,
                'title' => isset($cl['title']) ? (string)$cl['title'] : '',
                'url' => isset($cl['url']) ? esc_url_raw($cl['url']) : '',
                'target' => isset($cl['target']) ? (string)$cl['target'] : '',
            );
        }
        set_theme_mod('footer_contact_links', $links);
    }
    if (isset($data['createBuildText'])) {
        set_theme_mod('footer_create_build_text', (string)$data['createBuildText']);
    }
    if (isset($data['policy']) && is_array($data['policy'])) {
        $pt = isset($data['policy']['text']) ? (string)$data['policy']['text'] : '';
        $pu = isset($data['policy']['url']) ? esc_url_raw($data['policy']['url']) : '';
        set_theme_mod('footer_policy_text', $pt);
        set_theme_mod('footer_policy_link', array('url' => $pu, 'title' => $pt, 'target' => ''));
    }
    if (isset($data['service']) && is_array($data['service'])) {
        $st = isset($data['service']['text']) ? (string)$data['service']['text'] : '';
        $su = isset($data['service']['url']) ? esc_url_raw($data['service']['url']) : '';
        set_theme_mod('footer_servicer_text', $st);
        set_theme_mod('footer_servicer_link', array('url' => $su, 'title' => $st, 'target' => ''));
    }
}
