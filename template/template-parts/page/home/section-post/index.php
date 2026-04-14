<?php
$enabled = get_post_meta(get_the_ID(), 'buildpro_post_enabled', true);
$enabled = $enabled === '' ? 1 : (int) $enabled;
$title = get_post_meta(get_the_ID(), 'title_post', true);
$desc = get_post_meta(get_the_ID(), 'description_post', true);
$view_all_text = get_post_meta(get_the_ID(), 'buildpro_post_view_all_text', true);
if (is_customize_preview()) {
    $enabled_mod = get_theme_mod('buildpro_post_enabled', 1);
    $enabled = (int) $enabled_mod;
    $bundle = get_theme_mod('buildpro_post_data', array());
    if (is_string($bundle)) {
        $decoded = json_decode($bundle, true);
        if (is_array($decoded)) {
            $bundle = $decoded;
        }
    }
    if (is_array($bundle) && !empty($bundle)) {
        if (isset($bundle['title'])) {
            $title = $bundle['title'];
        }
        if (isset($bundle['desc'])) {
            $desc = $bundle['desc'];
        }
        if (isset($bundle['view_all_text'])) {
            $view_all_text = $bundle['view_all_text'];
        }
    }
    $mod_title = get_theme_mod('title_post', '');
    if ($mod_title !== '') {
        $title = $mod_title;
    }
    $mod_desc = get_theme_mod('description_post', '');
    if ($mod_desc !== '') {
        $desc = $mod_desc;
    }
    $mod_view_all = get_theme_mod('buildpro_post_view_all_text', '');
    if ($mod_view_all !== '') {
        $view_all_text = $mod_view_all;
    }
}
if (!is_string($view_all_text) || $view_all_text === '') {
    $view_all_text = __('View All Posts', 'buildpro');
}
if ($enabled !== 1) {
    return;
}

$posts = [];
$query = new WP_Query(array(
    'post_type' => 'post',
    'posts_per_page' => 3,
    'orderby' => 'date',
    'order' => 'DESC',
    'ignore_sticky_posts' => true,
    'post_status' => 'publish',
    'no_found_rows' => true,
));
if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        $id = get_the_ID();
        $posts[] = array(
            'id' => $id,
            'title' => get_the_title($id),
            'image' => get_the_post_thumbnail_url($id, 'large'),
            'date' => get_the_date('', $id),
            'link' => get_permalink($id),
        );
    }
    wp_reset_postdata();
}
if (empty($posts)) {
    return;
}
?>
<section class="section-post" data-aos="fade-up">
    <?php if (is_customize_preview()): ?>
        <div class="section-post__hover-outline"></div>
    <?php endif; ?>
    <div class="section-post__header">
        <?php if ($title !== ''): ?>
            <h2 class="section-post__title" id="section-post-title"><?php echo esc_html($title); ?></h2>
        <?php endif; ?>
        <?php if ($desc !== ''): ?>
            <p class="section-post__description" id="section-post-desc"><?php echo esc_html($desc); ?></p>
        <?php endif; ?>
    </div>
    <div class="section-post__list">
        <?php foreach ($posts as $p): ?>
            <a class="section-post__item" href="<?php echo esc_url($p['link']); ?>">
                <div class="section-post__item-image">
                    <?php if (!empty($p['image'])): ?>
                        <img src="<?php echo esc_url($p['image']); ?>" alt="<?php echo esc_attr($p['title']); ?>">
                    <?php endif; ?>
                </div>
                <div class="section-post__item-content">
                    <div class="section-post__item-top">
                        <?php echo buildpro_svg_icon('calendar-days', 'regular', 'section-post__item-icon'); ?>
                        <p class="section-post__item-date"><?php echo esc_html($p['date']); ?></p>
                    </div>
                    <h3 class="section-post__item-title"><?php echo esc_html($p['title']); ?></h3>
                    <p class="section-post__item-desc">
                        <?php echo esc_html(get_post_meta($p['id'], 'buildpro_post_description', true)); ?>
                    </p>
                </div>
                <div class="section-post__item-bottom">
                    <p class="section-post__item-readmore"><?php esc_html_e('Read more', 'buildpro'); ?>
                        <img src="<?php echo esc_url(get_theme_file_uri('/assets/images/icon/Arrow_Right_blue.png')); ?>"
                            alt="<?php echo esc_attr__('Right arrow', 'buildpro'); ?>"
                            class="section-services__item-link-icon">
                    </p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
    <?php
    $blog_page_url = '';
    $blog_pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'blogs-page.php', 'number' => 1));
    if (!empty($blog_pages)) {
        $blog_page_url = get_permalink($blog_pages[0]->ID);
    }
    ?>
    <div class="section-portfolio__page-link">
        <a class="section-portfolio__page-link-text" href="<?php echo esc_url($blog_page_url); ?>">
            <?php echo esc_html($view_all_text); ?>
        </a>
        <svg class="section-banner__item-button-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"
            aria-hidden="true" focusable="false">
            <path
                d="M566.6 342.6C579.1 330.1 579.1 309.8 566.6 297.3L406.6 137.3C394.1 124.8 373.8 124.8 361.3 137.3C348.8 149.8 348.8 170.1 361.3 182.6L466.7 288L96 288C78.3 288 64 302.3 64 320C64 337.7 78.3 352 96 352L466.7 352L361.3 457.4C348.8 469.9 348.8 490.2 361.3 502.7C373.8 515.2 394.1 515.2 406.6 502.7L566.6 342.7z" />
        </svg>
    </div>
</section>