<?php
$location_icon = 268;
$right_icon = 60;
$page_id = get_queried_object_id();
$enabled = get_post_meta($page_id, 'buildpro_portfolio_enabled', true);
$enabled = $enabled === '' ? 1 : (int)$enabled;
$portfolio_title = get_post_meta($page_id, 'projects_title', true);
$portfolio_desc = get_post_meta($page_id, 'projects_description', true);
if (is_customize_preview()) {
    $enabled_mod = get_theme_mod('buildpro_portfolio_enabled', 1);
    $enabled = (int)$enabled_mod;
    $data = get_theme_mod('buildpro_portfolio_data', array());
    if (is_array($data)) {
        if (isset($data['title']) && $data['title'] !== '') {
            $portfolio_title = $data['title'];
        } else {
            $mod_title = get_theme_mod('projects_title', '');
            if ($mod_title !== '') {
                $portfolio_title = $mod_title;
            }
        }
        if (isset($data['description']) && $data['description'] !== '') {
            $portfolio_desc = $data['description'];
        } else {
            $mod_desc = get_theme_mod('projects_description', '');
            if ($mod_desc !== '') {
                $portfolio_desc = $mod_desc;
            }
        }
    }
}
if ($enabled !== 1) {
    return;
}

$portfolio_items = [];
$query = new WP_Query(array(
    'post_type' => 'project',
    'posts_per_page' => 6,
    'orderby' => 'date',
    'order' => 'DESC',
    'post_status' => 'publish',
    'no_found_rows' => true,
));
if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        $image_id = get_post_thumbnail_id(get_the_ID());
        $name = get_the_title();
        $terms = get_the_terms(get_the_ID(), 'project-contruction');
        $text = '';
        if (is_array($terms) && ! empty($terms)) {
            $text = implode(', ', wp_list_pluck($terms, 'name'));
        }
        $location = get_post_meta(get_the_ID(), 'location_project', true);
        $link_url = get_permalink();
        $link_title = __('View Project', 'buildpro');
        $link_target = '';
        $portfolio_items[] = [
            'post_id' => get_the_ID(),
            'image_id' => $image_id,
            'text' => $text,
            'name' => $name,
            'location' => $location,
            'link_url' => $link_url,
            'link_title' => $link_title,
            'link_target' => $link_target,
        ];
    }
    wp_reset_postdata();
}
?>
<section class="section-portfolio">
    <?php if (is_customize_preview()): ?>
        <div class="section-portfolio__hover-outline"></div>

        <script>
            (function() {
                var btn = document.querySelector('.section-portfolio__customize-button');
                if (btn && window.parent && window.parent.wp && window.parent.wp.customize) {
                    btn.addEventListener('click', function() {
                        window.parent.wp.customize.section('buildpro_portfolio_section').focus();
                    });
                }
            })();
        </script>
    <?php endif; ?>
    <div class="section-portfolio__header">
        <?php if ($portfolio_title !== ''): ?>
            <h2 class="section-portfolio__title"><?php echo esc_html($portfolio_title); ?></h2>
        <?php endif; ?>
        <?php if ($portfolio_desc !== ''): ?>
            <p class="section-portfolio__description"><?php echo esc_html($portfolio_desc); ?></p>
        <?php endif; ?>
    </div>
    <div class="swiper section-portfolio__swiper">
        <div class="swiper-wrapper">
            <?php foreach ($portfolio_items as $item): ?>
                <div class="swiper-slide">
                    <a class="section-portfolio__item" href="<?php echo esc_url($item['link_url']); ?>"
                        <?php echo $item['link_target'] ? 'target="' . esc_attr($item['link_target']) . '"' : ''; ?>>
                        <div class="section-portfolio__item-image">
                            <?php
                            $img_url = $item['image_id'] ? wp_get_attachment_image_url($item['image_id'], 'full') : '';
                            ?>
                            <?php if ($img_url): ?>
                                <div class="section-portfolio__item-bg"
                                    style="background-image: url('<?php echo esc_url($img_url); ?>');"></div>
                            <?php else: ?>
                                <div class="section-portfolio__item-bg"></div>
                            <?php endif; ?>
                        </div>
                        <div class="section-portfolio__item-content">
                            <p class="section-portfolio__item-text"><?php echo $item['text']; ?></p>
                            <h3 class="section-portfolio__item-name"><?php echo $item['name']; ?></h3>
                            <div class="section-portfolio__item-location-wrapper">
                                <img src="<?php echo esc_url(get_theme_file_uri('/assets/images/icon/icon_location.png')); ?>"
                                    alt="<?php echo esc_attr__('Location', 'buildpro'); ?>" class="section-portfolio__item-location-icon">
                                <p class="section-portfolio__item-location"><?php echo esc_html($item['location']); ?></p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="swiper-button-prev section-portfolio__swiper-prev"></div>
        <div class="swiper-button-next section-portfolio__swiper-next"></div>
    </div>
    <?php
    $projects_page_url = '';
    $projects_pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'projects-page.php', 'number' => 1));
    if (!empty($projects_pages)) {
        $projects_page_url = get_permalink($projects_pages[0]->ID);
    }
    ?>
    <div class="section-portfolio__page-link">
        <a class="section-portfolio__page-link-text" href="<?php echo esc_url($projects_page_url); ?>">
            <?php esc_html_e('View All Projects', 'buildpro'); ?>
        </a>
        <img class="section-banner__item-button-icon"
            src="<?php echo esc_url(get_theme_file_uri('/assets/images/icon/Arrow_Right.png')); ?>" alt="<?php echo esc_attr__('Right arrow', 'buildpro'); ?>">
    </div>
    <?php if (empty($portfolio_items)) {
        return;
    } ?>
</section>