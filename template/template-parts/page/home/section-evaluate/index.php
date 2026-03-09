<?php
$page_id = get_queried_object_id();
$enabled = get_post_meta($page_id, 'buildpro_evaluate_enabled', true);
$enabled = $enabled === '' ? 1 : (int) $enabled;
$evaluate_text = get_post_meta($page_id, 'buildpro_evaluate_text', true);
$evaluate_title = get_post_meta($page_id, 'buildpro_evaluate_title', true);
$evaluate_description = get_post_meta($page_id, 'buildpro_evaluate_desc', true);
if (is_customize_preview()) {
    $enabled_mod = get_theme_mod('buildpro_evaluate_enabled', 1);
    $enabled = (int) $enabled_mod;
    $bundle = get_theme_mod('buildpro_evaluate_data', array());
    if (is_string($bundle)) {
        $decoded = json_decode($bundle, true);
        if (is_array($decoded)) {
            $bundle = $decoded;
        }
    }
    if (is_array($bundle) && !empty($bundle)) {
        if (isset($bundle['text'])) {
            $evaluate_text = $bundle['text'];
        }
        if (isset($bundle['title'])) {
            $evaluate_title = $bundle['title'];
        }
        if (isset($bundle['desc'])) {
            $evaluate_description = $bundle['desc'];
        }
    }
    $mod_text = get_theme_mod('buildpro_evaluate_text', '');
    if ($mod_text !== '') {
        $evaluate_text = $mod_text;
    }
    $mod_title = get_theme_mod('buildpro_evaluate_title', '');
    if ($mod_title !== '') {
        $evaluate_title = $mod_title;
    }
    $mod_desc = get_theme_mod('buildpro_evaluate_desc', '');
    if ($mod_desc !== '') {
        $evaluate_description = $mod_desc;
    }
}
if ($enabled !== 1) {
    return;
}

$evaluate_items = [];
$rows = get_post_meta($page_id, 'buildpro_evaluate_items', true);
if (is_customize_preview()) {
    $bundle = isset($bundle) ? $bundle : get_theme_mod('buildpro_evaluate_data', array());
    if (is_string($bundle)) {
        $decoded2 = json_decode($bundle, true);
        if (is_array($decoded2)) {
            $bundle = $decoded2;
        }
    }
    if (is_array($bundle) && isset($bundle['items']) && is_array($bundle['items']) && !empty($bundle['items'])) {
        $rows = $bundle['items'];
    } else {
        $mods = get_theme_mod('buildpro_evaluate_items', array());
        if (is_array($mods) && !empty($mods)) {
            $rows = $mods;
        }
    }
}
$rows = is_array($rows) ? $rows : [];
foreach ($rows as $row) {
    $description = isset($row['description']) ? $row['description'] : '';
    $name = isset($row['name']) ? $row['name'] : '';
    $position = isset($row['position']) ? $row['position'] : '';
    $avatar_id = isset($row['avatar_id']) ? (int)$row['avatar_id'] : 0;
    $evaluate_items[] = [
        'description' => $description,
        'name' => $name,
        'position' => $position,
        'avatar_id' => $avatar_id,
    ];
}
if (empty($evaluate_items)) {
    return;
}
?>
<section class="section-evaluate">
    <?php if (is_customize_preview()): ?>
        <div class="section-evaluate__hover-outline"></div>
        <script>
            (function() {
                var btn = document.querySelector('.section-evaluate__customize-button');
                if (btn && window.parent && window.parent.wp && window.parent.wp.customize) {
                    btn.addEventListener('click', function() {
                        window.parent.wp.customize.section('buildpro_evaluate_section').focus();
                    });
                }
            })();
        </script>
    <?php endif; ?>
    <div class="section-evaluate-container">
        <div class="section-evaluate-left">
            <?php if ($evaluate_text !== ''): ?>
                <p class="section-evaluate__text"><?php echo esc_html($evaluate_text); ?></p>
            <?php endif; ?>
            <?php if ($evaluate_title !== ''): ?>
                <h2 class="section-evaluate__title"><?php echo esc_html($evaluate_title); ?></h2>
            <?php endif; ?>
            <?php if ($evaluate_description !== ''): ?>
                <p class="section-evaluate__description"><?php echo esc_html($evaluate_description); ?></p>
            <?php endif; ?>
        </div>
        <div class="section-evaluate-right">
            <div class="swiper section-evaluate__swiper swiper-container_evaluate">
                <div class="swiper-wrapper swiper-wrapper_evaluate ">
                    <?php foreach ($evaluate_items as $item): ?>
                        <div class="swiper-slide section-evaluate__swiper-slide">
                            <div class="section-evaluate__item">
                                <p class="section-evaluate__item-description"><?php echo esc_html($item['description']); ?>
                                </p>
                                <div class="section-evaluate__item-content">
                                    <div class="section-evaluate__item-avatar">
                                        <?php
                                        $avatar_url = $item['avatar_id'] ? wp_get_attachment_image_url($item['avatar_id'], 'thumbnail') : '';
                                        ?>
                                        <?php if ($avatar_url): ?>
                                            <img src="<?php echo esc_url($avatar_url); ?>"
                                                alt="<?php echo esc_attr($item['name']); ?>">
                                        <?php endif; ?>
                                    </div>
                                    <div class="section-evaluate__item-info">
                                        <h3 class="section-evaluate__item-name"><?php echo esc_html($item['name']); ?></h3>
                                        <p class="section-evaluate__item-position">
                                            <?php echo esc_html($item['position']); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </div>
    <?php if (empty($evaluate_items)): ?>
        <script src="<?php echo esc_url(get_theme_file_uri('/assets/data/evaluate-date.js')); ?>"></script>
    <?php endif; ?>
</section>
