<?php
$pid = get_the_ID();
if (!$pid || get_post_type($pid) !== 'project') {
    return;
}
$banner_id   = (int) get_post_meta($pid, 'project_banner_id', true);
$banner_url  = $banner_id ? wp_get_attachment_image_url($banner_id, 'full') : '';
$location    = get_post_meta($pid, 'location_project', true);
$about       = get_post_meta($pid, 'about_project', true);
$about_image_id = (int) get_post_meta($pid, 'about_image_project', true);
$about_image_url = $about_image_id ? wp_get_attachment_image_url($about_image_id, 'large') : '';
$price       = get_post_meta($pid, 'price_project', true);
$information = get_post_meta($pid, 'information_project', true);
$datetime    = get_post_meta($pid, 'date_time_project', true);
$gallery_ids = get_post_meta($pid, 'project_gallery_ids', true);
$gallery_ids = is_array($gallery_ids) ? array_values(array_filter(array_map('absint', $gallery_ids))) : array();
$standards   = get_post_meta($pid, 'project_standards', true);
$standards   = is_array($standards) ? $standards : array();
$total_area  = get_post_meta($pid, 'total_area_project', true);
$completion  = get_post_meta($pid, 'completion_project', true);
$arch_style  = get_post_meta($pid, 'architectural_style_project', true);
$contruction_terms = get_the_terms($pid, 'project-contruction');
?>

<?php
$projects_page = get_pages([
    'meta_key'   => '_wp_page_template',
    'meta_value' => 'projects-page.php'
]);
?>
<article class="single-project-detail" id="project-<?php echo esc_attr($pid); ?>">
    <?php if (!empty($projects_page)) : ?>
    <a href="<?php echo esc_url(get_permalink($projects_page[0]->ID)); ?>" class="single-project__back">
        <img class="single-project__back-icon"
            src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/icon/Arrow_Left.png'); ?>" alt="">
        <h1 class="single-project__title_back"><?php echo esc_html(get_the_title($pid)); ?></h1>
    </a>
    <?php endif; ?>

    <header class="single-project__header">
        <?php if (!empty($banner_url)) : ?>
        <div class="single-project__banner">
            <img src="<?php echo esc_url($banner_url); ?>" alt="<?php echo esc_attr(get_the_title($pid)); ?>">
        </div>
        <?php endif; ?>
        <div class="single-project__title-container">
            <?php if (!empty($contruction_terms) && !is_wp_error($contruction_terms)) : ?>
            <div class="single-project__taxonomy">
                <div class="single-project__taxonomy-list">
                    <?php foreach ($contruction_terms as $term) :
                            $t_name = isset($term->name) ? $term->name : '';
                            $t_link = get_term_link($term);
                            if ($t_name === '' || is_wp_error($t_link)) continue;
                        ?>
                    <a class="single-project__taxonomy-chip" href="<?php echo esc_url($t_link); ?>">
                        <?php echo esc_html($t_name); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            <h1 class="single-project__title"><?php echo esc_html(get_the_title($pid)); ?></h1>
            <div class="single-project__meta">
                <div class="single-project--title__items">
                    <i class="fa-regular fa-calendar single-project__icon" aria-hidden="true"></i>
                    <span class="single-project__text"><?php echo esc_html(get_the_date('', $pid)); ?></span>
                </div>
                <div class="single-project--title__items">
                    <i class="fa-regular fa-user single-project__icon" aria-hidden="true"></i>
                    <span class="single-project__text">
                        <?php echo esc_html(get_the_author_meta('display_name', get_post_field('post_author', $pid))); ?>
                    </span>
                </div>
                <?php if ($location !== '') : ?>
                <div class="single-project--title__items">
                    <i class="fa-regular fa-map single-project__icon" aria-hidden="true"></i>
                    <span class="single-project__text"><?php echo esc_html($location); ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <section class="single-project__content">
        <?php the_content(); ?>
    </section>


    <section class="single-project__facts">
        <div class="single-project__facts-grid">
            <?php if ($total_area !== '') : ?>
            <div class="single-project__fact-item"><span class="label">Total Area</span><span
                    class="value"><?php echo esc_html($total_area); ?></span></div>
            <?php endif; ?>
            <?php if ($completion !== '') : ?>
            <div class="single-project__fact-item"><span class="label">Completion</span><span
                    class="value"><?php echo esc_html($completion); ?></span></div>
            <?php endif; ?>
            <?php if ($arch_style !== '') : ?>
            <div class="single-project__fact-item"><span class="label">Architectural Style</span><span
                    class="value"><?php echo esc_html($arch_style); ?></span></div>
            <?php endif; ?>
            <!-- <?php if ($datetime !== '') : ?>
                <div class="single-project__fact-item"><span class="label">Date Time</span><span
                        class="value"><?php echo esc_html($datetime); ?></span></div>
            <?php endif; ?>
            <?php if ($price !== '') : ?>
                <div class="single-project__fact-item"><span class="label">Price</span><span
                        class="value"><?php echo esc_html($price); ?></span></div>
            <?php endif; ?> -->
        </div>
    </section>

    <?php if ($about !== '') : ?>
    <section class="single-project__about">
        <div class="single-project__about-content"><?php echo apply_filters('the_content', $about); ?></div>
        <?php if (!empty($about_image_url)) : ?>
        <div class="single-project__about-image">
            <img src="<?php echo esc_url($about_image_url); ?>" alt="<?php echo esc_attr(get_the_title($pid)); ?>">
        </div>
        <?php endif; ?>
    </section>
    <?php endif; ?>



    <?php if (!empty($gallery_ids)) : ?>
    <section class="single-project__gallery">
        <h2 class="single-project__section-title">Gallery</h2>
        <div class="single-project__gallery-items">
            <?php foreach ($gallery_ids as $img_id) :
                    $u = wp_get_attachment_image_url((int) $img_id, 'large');
                    if (!$u) continue;
                ?>
            <figure class="single-project__gallery-item">
                <img src="<?php echo esc_url($u); ?>" alt="">
            </figure>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if (!empty($standards)) : ?>
    <section class="single-project__standards">
        <h2 class="single-project__section-title">Standards</h2>
        <div class="single-project__standards-list">
            <?php foreach ($standards as $row) :
                    $img_id = isset($row['image_id']) ? absint($row['image_id']) : 0;
                    $title  = isset($row['title']) ? sanitize_text_field($row['title']) : '';
                    $desc   = isset($row['description']) ? sanitize_text_field($row['description']) : '';
                    if (!$img_id && $title === '' && $desc === '') continue;
                    $thumb = $img_id ? wp_get_attachment_image_url($img_id, 'medium') : '';
                ?>
            <div class="single-project__standard-item">
                <div class="single-project__standard-thumb">
                    <?php if ($thumb) : ?>
                    <img src="<?php echo esc_url($thumb); ?>" alt="">
                    <?php endif; ?>
                </div>
                <div class="single-project__standard-content">
                    <?php if ($title !== '') : ?>
                    <h3 class="single-project__standard-title"><?php echo esc_html($title); ?></h3>
                    <?php endif; ?>
                    <?php if ($desc !== '') : ?>
                    <p class="single-project__standard-desc"><?php echo esc_html($desc); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- <?php if ($information !== '') : ?>
        <section class="single-project__information">
            <h2 class="single-project__section-title">Information</h2>
            <div class="single-project__information-content"><?php echo wp_kses_post(wpautop($information)); ?></div>
        </section>
    <?php endif; ?> -->
</article>