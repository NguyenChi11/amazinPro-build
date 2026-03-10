<?php
$pid = get_queried_object_id();
$use_mod = is_customize_preview();
$enabled = $use_mod ? get_theme_mod('buildpro_about_policy_enabled', 1) : get_post_meta($pid, 'buildpro_about_policy_enabled', true);
$enabled = $enabled === '' ? 1 : (int) $enabled;
$title_left = $use_mod ? get_theme_mod('buildpro_about_policy_title_left', '') : get_post_meta($pid, 'buildpro_about_policy_title_left', true);
$business_registration = $use_mod ? get_theme_mod('buildpro_about_policy_business_registration', '') : get_post_meta($pid, 'buildpro_about_policy_business_registration', true);
$general_contractor = $use_mod ? get_theme_mod('buildpro_about_policy_general_contractor', '') : get_post_meta($pid, 'buildpro_about_policy_general_contractor', true);
$duns_number = $use_mod ? get_theme_mod('buildpro_about_policy_duns_number', '') : get_post_meta($pid, 'buildpro_about_policy_duns_number', true);
$title_right = $use_mod ? get_theme_mod('buildpro_about_policy_title_right', '') : get_post_meta($pid, 'buildpro_about_policy_title_right', true);
$warranty_desc = $use_mod ? get_theme_mod('buildpro_about_policy_warranty_desc', '') : get_post_meta($pid, 'buildpro_about_policy_warranty_desc', true);
$items = $use_mod ? get_theme_mod('buildpro_about_policy_items', array()) : get_post_meta($pid, 'buildpro_about_policy_items', true);
$items = is_array($items) ? $items : array();
$certs = $use_mod ? get_theme_mod('buildpro_about_policy_certifications', array()) : get_post_meta($pid, 'buildpro_about_policy_certifications', true);
$certs = is_array($certs) ? $certs : array();
// legacy single certification
$legacy_img_id = (int) get_post_meta($pid, 'buildpro_about_policy_certification_image_id', true);
$legacy_img_url = $legacy_img_id ? wp_get_attachment_image_url($legacy_img_id, 'thumbnail') : '';
$legacy_url = get_post_meta($pid, 'buildpro_about_policy_certification_url', true);
$legacy_title = get_post_meta($pid, 'buildpro_about_policy_certification_title', true);
$legacy_desc = get_post_meta($pid, 'buildpro_about_policy_certification_desc', true);
if ($legacy_img_id || $legacy_img_url || $legacy_title || $legacy_desc || $legacy_url) {
    $certs = array_merge(array(array(
        'image_id' => $legacy_img_id,
        'image_url' => $legacy_img_url,
        'url' => (string)$legacy_url,
        'title' => (string)$legacy_title,
        'desc' => (string)$legacy_desc,
    )), $certs);
}
// sanitize display certs: resolve image url
$display_certs = array();
foreach ($certs as $c) {
    $img_id = isset($c['image_id']) ? (int)$c['image_id'] : 0;
    $img_url = $img_id ? wp_get_attachment_image_url($img_id, 'thumbnail') : (isset($c['image_url']) ? (string)$c['image_url'] : '');
    $ct = isset($c['title']) ? (string)$c['title'] : '';
    $cd = isset($c['desc']) ? (string)$c['desc'] : '';
    $cu = isset($c['url']) ? (string)$c['url'] : '';
    if ($img_url || $ct !== '' || $cd !== '' || $cu !== '') {
        $display_certs[] = array('image_url' => $img_url, 'title' => $ct, 'desc' => $cd, 'url' => $cu);
    }
}
$display_items = array();
foreach ($items as $it) {
    $icon_id = isset($it['icon_id']) ? (int)$it['icon_id'] : 0;
    $icon_url = $icon_id ? wp_get_attachment_image_url($icon_id, 'thumbnail') : (isset($it['icon_url']) ? (string)$it['icon_url'] : '');
    $t = isset($it['title']) ? (string)$it['title'] : '';
    $d = isset($it['desc']) ? (string)$it['desc'] : '';
    if ($icon_url || $t !== '' || $d !== '') {
        $display_items[] = array('icon_url' => $icon_url, 'title' => $t, 'desc' => $d);
    }
}
if ($enabled) :
?>
    <section class="about-policy">
        <?php if (is_customize_preview()) : ?>
            <div class="policy__hover-outline" aria-hidden="true"></div>
        <?php endif; ?>
        <div class="about-policy__inner">
            <div class="about-policy__grid">
                <div class="about-policy__left">
                    <?php if ($title_left !== '') : ?>
                        <h2 class="about-policy__title"><?php echo esc_html($title_left); ?></h2>
                    <?php endif; ?>
                    <?php if ($business_registration !== '' || $general_contractor !== '' || $duns_number !== '') : ?>
                        <div class="about-policy__details">
                            <?php if ($business_registration !== '') : ?>
                                <div class="about-policy__detail">
                                    <span class="about-policy__detail-label">Bussiness Registration</span>
                                    <span class="about-policy__detail-value"><?php echo esc_html((string)$business_registration); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($general_contractor !== '') : ?>
                                <div class="about-policy__detail">
                                    <span class="about-policy__detail-label">General Contractor Lic</span>
                                    <span class="about-policy__detail-value"><?php echo esc_html((string)$general_contractor); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($duns_number !== '') : ?>
                                <div class="about-policy__detail">
                                    <span class="about-policy__detail-label">DUNS Number</span>
                                    <span class="about-policy__detail-value"><?php echo esc_html((string)$duns_number); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($display_certs)) : ?>
                        <?php foreach ($display_certs as $c) : ?>
                            <div class="about-policy__cert-card">
                                <div class="about-policy__cert-icon">
                                    <?php if (!empty($c['image_url'])) : ?>
                                        <img src="<?php echo esc_url($c['image_url']); ?>" alt="">
                                    <?php else : ?>
                                        <div class="about-policy__cert-placeholder"></div>
                                    <?php endif; ?>
                                </div>
                                <div class="about-policy__cert-content">
                                    <?php if (!empty($c['title'])) : ?>
                                        <div class="about-policy__cert-title"><?php echo esc_html($c['title']); ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($c['desc'])) : ?>
                                        <div class="about-policy__cert-desc"><?php echo esc_html($c['desc']); ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($c['url'])) : ?>
                                        <a class="about-policy__cert-link" href="<?php echo esc_url($c['url']); ?>" target="_blank" rel="noopener">
                                            <span>View Certification</span>
                                            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="about-policy__right">
                    <?php if ($title_right !== '') : ?>
                        <h2 class="about-policy__title"><?php echo esc_html($title_right); ?></h2>
                    <?php endif; ?>
                    <?php if ($warranty_desc !== '') : ?>
                        <p class="about-policy__text"><?php echo esc_html($warranty_desc); ?></p>
                    <?php endif; ?>
                    <div class="about-policy__warranty-list">
                        <?php if (!empty($display_items)) : foreach ($display_items as $it) : ?>
                                <div class="about-policy__warranty-item">
                                    <div class="about-policy__warranty-icon">
                                        <?php if (!empty($it['icon_url'])) : ?>
                                            <img src="<?php echo esc_url($it['icon_url']); ?>" alt="">
                                        <?php else : ?>
                                            <div class="about-policy__warranty-placeholder"></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="about-policy__warranty-content">
                                        <?php if (!empty($it['title'])) : ?>
                                            <div class="about-policy__warranty-title"><?php echo esc_html($it['title']); ?></div>
                                        <?php endif; ?>
                                        <?php if (!empty($it['desc'])) : ?>
                                            <div class="about-policy__warranty-desc"><?php echo esc_html($it['desc']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                        <?php endforeach;
                        endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>