<?php
$description_ft = get_theme_mod('footer_information_description', '');
$list_pages = get_theme_mod('footer_list_pages', array());
$list_pages = is_array($list_pages) ? $list_pages : array();
$footer_pages = array();
foreach ($list_pages as $row) {
    $url = isset($row['url']) ? $row['url'] : '';
    $title = isset($row['title']) ? $row['title'] : '';
    $target = isset($row['target']) ? $row['target'] : '';
    $footer_pages[] = array(
        'url' => $url,
        'title' => $title,
        'target' => $target,
    );
}
$contact_location = get_theme_mod('footer_contact_location', '');
$contact_phone = get_theme_mod('footer_contact_phone', '');
$contact_email = get_theme_mod('footer_contact_email', '');
$contact_time = get_theme_mod('footer_contact_time', '');
$contact_link_rows = get_theme_mod('footer_contact_links', array());
$contact_link_rows = is_array($contact_link_rows) ? $contact_link_rows : array();
$contact_links = array();
foreach ($contact_link_rows as $row) {
    $icon_id = isset($row['icon_id']) ? (int) $row['icon_id'] : 0;
    $icon_url = $icon_id ? wp_get_attachment_image_url($icon_id, 'full') : '';
    $link_url = isset($row['url']) ? $row['url'] : '';
    $link_title = isset($row['title']) ? $row['title'] : '';
    $link_target = isset($row['target']) ? $row['target'] : '';
    $contact_links[] = array(
        'icon_url' => $icon_url,
        'url' => $link_url,
        'title' => $link_title,
        'target' => $link_target,
    );
}
$banner_image_id = get_theme_mod('footer_banner_image_id', 0);
$background_url = $banner_image_id ? wp_get_attachment_image_url($banner_image_id, 'full') : '';
$bg_style = $background_url ? ' style="background-image:url(' . esc_url($background_url) . ')"' : '';
$create_build_text = get_theme_mod('footer_create_build_text', '');
$policy_ft = get_theme_mod('footer_policy_text', '');
$link_policy = get_theme_mod('footer_policy_link', array('url' => '', 'title' => '', 'target' => ''));
$policy_url = is_array($link_policy) ? ($link_policy['url'] ?? '') : '';
$policy_target = is_array($link_policy) ? ($link_policy['target'] ?? '') : '';
$service_ft = get_theme_mod('footer_servicer_text', '');
$link_server = get_theme_mod('footer_servicer_link', array('url' => '', 'title' => '', 'target' => ''));
$service_url = is_array($link_server) ? ($link_server['url'] ?? '') : '';
$service_target = is_array($link_server) ? ($link_server['target'] ?? '') : '';
$has_data = ($background_url || $description_ft || !empty($footer_pages) || $contact_location || $contact_phone || $contact_email || $contact_time || !empty($contact_links) || $create_build_text || $policy_ft || $policy_url || $service_ft || $service_url);
if (!is_customize_preview() && !$has_data) {
    return;
}
?>

<script>
    (function() {
        try {
            window.footerI18n = window.footerI18n || {};
            window.footerI18n.connectWithUs = window.footerI18n.connectWithUs || <?php echo wp_json_encode(esc_html__('Connect with us', 'buildpro')); ?>;
            window.footerI18n.menu = window.footerI18n.menu || <?php echo wp_json_encode(esc_html__('Menu', 'buildpro')); ?>;
            window.footerI18n.contact = window.footerI18n.contact || <?php echo wp_json_encode(esc_html__('Contact', 'buildpro')); ?>;
            window.footerI18n.policy = window.footerI18n.policy || <?php echo wp_json_encode(esc_html__('Policy', 'buildpro')); ?>;
            window.footerI18n.service = window.footerI18n.service || <?php echo wp_json_encode(esc_html__('Service', 'buildpro')); ?>;
            window.footerI18n.footerLogoAlt = window.footerI18n.footerLogoAlt || <?php echo wp_json_encode(esc_attr__('Footer Logo', 'buildpro')); ?>;
            window.footerI18n.iconAlt = window.footerI18n.iconAlt || <?php echo wp_json_encode(esc_attr__('icon', 'buildpro')); ?>;
        } catch (e) {}
    })();
</script>

<footer id="colophon" class="site-footer" <?php echo $bg_style; ?>>
    <div class="footer__inner">
        <?php if (is_customize_preview()): ?>
            <div class="footer__hover-outline"></div>
            <script>
                (function() {
                    var btn = document.querySelector('.footer__customize-button');
                    if (btn && window.parent && window.parent.wp && window.parent.wp.customize) {
                        btn.addEventListener('click', function() {
                            window.parent.wp.customize.section('buildpro_footer_section').focus();
                        });
                    }
                })();
            </script>
        <?php endif; ?>
        <div class="footer__header">
            <div class="footer__brand">
                <div class="footer__brand-logo">
                    <div class="footer__brand-logo-wrapper">
                        <?php
                        $header_logo_id = get_theme_mod('header_logo', 0);
                        $header_logo_url = $header_logo_id ? wp_get_attachment_image_url($header_logo_id, 'full') : '';
                        $header_title = get_theme_mod('buildpro_header_title', '');
                        $header_sub = get_theme_mod('buildpro_header_description', '');
                        $header_title = is_scalar($header_title) ? trim((string)$header_title) : '';
                        $header_sub = is_scalar($header_sub) ? trim((string)$header_sub) : '';
                        ?>
                        <?php if ($header_logo_url): ?>
                            <img class="footer__logo" src="<?php echo esc_url($header_logo_url); ?>" alt="<?php esc_attr_e('Footer Logo', 'buildpro'); ?>">
                        <?php endif; ?>
                        <?php if (!empty($header_title)): ?>
                            <h3 class="footer__title"><?php echo esc_html($header_title); ?></h3>
                        <?php endif; ?>
                        <?php if (!empty($header_sub)): ?>
                            <h4 class="footer__subtitle"><?php echo esc_html($header_sub); ?></h4>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (!empty($description_ft)): ?>
                    <p class="footer__description"><?php echo esc_html($description_ft); ?></p>
                <?php endif; ?>

                <h3 class="footer__connect-title"><?php esc_html_e('Connect with us', 'buildpro'); ?></h3>

                <?php if (!empty($contact_links)): ?>
                    <div class="footer__contact-links">
                        <?php foreach ($contact_links as $c): ?>
                            <?php
                            $target_attr = !empty($c['target']) ? ' target="' . esc_attr($c['target']) . '"' : '';
                            $rel_attr = (!empty($c['target']) && $c['target'] === '_blank') ? ' rel="noopener"' : '';
                            ?>
                            <a class="footer__contact-link" href="<?php echo esc_url($c['url']); ?>"
                                <?php echo $target_attr . $rel_attr; ?>>
                                <?php if (!empty($c['icon_url'])): ?>
                                    <img class="footer__contact-link-icon" src="<?php echo esc_url($c['icon_url']); ?>" alt="<?php esc_attr_e('icon', 'buildpro'); ?>">
                                <?php endif; ?>

                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="footer__pages_wrapper">
                <h3 class="footer__pages-title"><?php esc_html_e('Menu', 'buildpro'); ?></h3>
                <?php if (!empty($footer_pages)): ?>
                    <div class="footer__pages">
                        <?php foreach ($footer_pages as $p): ?>
                            <?php
                            $target_attr = !empty($p['target']) ? ' target="' . esc_attr($p['target']) . '"' : '';
                            $rel_attr = (!empty($p['target']) && $p['target'] === '_blank') ? ' rel="noopener"' : '';
                            ?>
                            <a class="footer__page-link" href="<?php echo esc_url($p['url']); ?>"
                                <?php echo $target_attr . $rel_attr; ?>>
                                <?php echo esc_html($p['title'] ?: $p['url']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="footer__contact">
                <h3 class="footer__contact-title"><?php esc_html_e('Contact', 'buildpro'); ?></h3>
                <div class="footer__contact-info">
                    <?php if (!empty($contact_location)): ?>
                        <p class="footer__contact-location">
                            <img class="footer__contact-icon"
                                src="<?php echo get_template_directory_uri(); ?>/assets/images/icon/icon_location_ft.png"
                                alt="<?php esc_attr_e('icon', 'buildpro'); ?>">
                            <?php echo esc_html($contact_location); ?>
                        </p>
                    <?php endif; ?>
                    <?php if (!empty($contact_phone)): ?>
                        <p class="footer__contact-phone">
                            <img class="footer__contact-icon"
                                src="<?php echo get_template_directory_uri(); ?>/assets/images/icon/icon_phone_ft.png"
                                alt="<?php esc_attr_e('icon', 'buildpro'); ?>">
                            <?php echo esc_html($contact_phone); ?>
                        </p>
                    <?php endif; ?>
                    <?php if (!empty($contact_email)): ?>
                        <p class="footer__contact-email">
                            <img class="footer__contact-icon"
                                src="<?php echo get_template_directory_uri(); ?>/assets/images/icon/icon_email_ft.png"
                                alt="<?php esc_attr_e('icon', 'buildpro'); ?>">
                            <?php echo esc_html($contact_email); ?>
                        </p>
                    <?php endif; ?>
                    <?php if (!empty($contact_time)): ?>
                        <p class="footer__contact-time">
                            <img class="footer__contact-icon"
                                src="<?php echo get_template_directory_uri(); ?>/assets/images/icon/icon_time_ft.png"
                                alt="<?php esc_attr_e('icon', 'buildpro'); ?>">
                            <?php echo esc_html($contact_time); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="footer__bottom">
            <?php if (!empty($create_build_text)): ?>
                <span class="footer__create"><?php echo esc_html($create_build_text); ?></span>
            <?php endif; ?>
            <?php if (!empty($policy_ft) || !empty($policy_url)): ?>
                <?php
                $target_attr = !empty($policy_target) ? ' target="' . esc_attr($policy_target) . '"' : '';
                $rel_attr = (!empty($policy_target) && $policy_target === '_blank') ? ' rel="noopener"' : '';
                ?>
                <a class="footer__policy" href="<?php echo esc_url($policy_url ?: '#'); ?>"
                    <?php echo $target_attr . $rel_attr; ?>>
                    <?php echo esc_html($policy_ft ?: __('Policy', 'buildpro')); ?>
                </a>
            <?php endif; ?>
            <?php if (!empty($service_ft) || !empty($service_url)): ?>
                <?php
                $target_attr = !empty($service_target) ? ' target="' . esc_attr($service_target) . '"' : '';
                $rel_attr = (!empty($service_target) && $service_target === '_blank') ? ' rel="noopener"' : '';
                ?>
                <a class="footer__service" href="<?php echo esc_url($service_url ?: '#'); ?>"
                    <?php echo $target_attr . $rel_attr; ?>>
                    <?php echo esc_html($service_ft ?: __('Service', 'buildpro')); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</footer><!-- #colophon -->