<?php
function buildpro_import_header_demo()
{
    $logo_id = (int) get_theme_mod('header_logo', 0);
    $title = (string) get_theme_mod('buildpro_header_title', '');
    $desc = (string) get_theme_mod('buildpro_header_description', '');
    $quote_text = (string) get_theme_mod('buildpro_header_quote_text', '');
    $quote_url = (string) get_theme_mod('buildpro_header_quote_url', '');
    if ($title === '') {
        $title = (string) get_theme_mod('header_text', '');
    }
    if ($desc === '') {
        $desc = (string) get_theme_mod('header_description', '');
    }
    if (($logo_id || $title !== '' || $desc !== '') && ($quote_text !== '' || $quote_url !== '')) {
        return;
    }
    if (function_exists('buildpro_import_parse_js')) {
        $data = buildpro_import_parse_js('/assets/data/header-data.js', 'headerData');
        if (is_array($data)) {
            if (isset($data['logo']) && $data['logo']) {
                if (function_exists('buildpro_import_image_id')) {
                    $img_id = buildpro_import_image_id($data['logo']);
                    if ($img_id) {
                        set_theme_mod('header_logo', (int)$img_id);
                    }
                }
            }
            if (isset($data['title'])) {
                $t = (string)$data['title'];
                if ($t !== '' && $title === '') {
                    set_theme_mod('buildpro_header_title', $t);
                    remove_theme_mod('header_text');
                }
            }
            if (isset($data['description'])) {
                $d = (string)$data['description'];
                if ($d !== '' && $desc === '') {
                    set_theme_mod('buildpro_header_description', $d);
                    remove_theme_mod('header_description');
                }
            }
            if (isset($data['quoteText'])) {
                $qt = (string)$data['quoteText'];
                if ($qt !== '' && $quote_text === '') {
                    set_theme_mod('buildpro_header_quote_text', $qt);
                }
            }
            if (isset($data['quoteUrl'])) {
                $qu = esc_url_raw((string)$data['quoteUrl']);
                if ($qu !== '' && $quote_url === '') {
                    set_theme_mod('buildpro_header_quote_url', $qu);
                }
            }
        }
    }
}
