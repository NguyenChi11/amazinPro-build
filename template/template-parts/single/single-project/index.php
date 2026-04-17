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
$information = get_post_meta($pid, 'information_project', true);
$project_overview = trim((string) get_post_meta($pid, 'project_overview_project', true));
$project_key_info_raw = get_post_meta($pid, 'project_key_infomation', true);
$project_key_info_raw = is_array($project_key_info_raw) ? $project_key_info_raw : array();
$project_the_vision = trim((string) get_post_meta($pid, 'the_vision_project', true));
$project_architectural_design = trim((string) get_post_meta($pid, 'architectural_design_project', true));
$project_highlights_raw = get_post_meta($pid, 'project_highlight_options', true);
$project_highlights_raw = is_array($project_highlights_raw) ? $project_highlights_raw : array();
$post_content = trim((string) get_post_field('post_content', $pid));
$gallery_ids = get_post_meta($pid, 'project_gallery_ids', true);
$gallery_ids = is_array($gallery_ids) ? array_values(array_filter(array_map('absint', $gallery_ids))) : array();
$total_area  = get_post_meta($pid, 'total_area_project', true);
$completion  = get_post_meta($pid, 'completion_project', true);
$arch_style  = get_post_meta($pid, 'architectural_style_project', true);
$featured_image_id = (int) get_post_thumbnail_id($pid);
$featured_image_url = $featured_image_id ? wp_get_attachment_image_url($featured_image_id, 'large') : '';
$featured_image_alt = $featured_image_id ? get_post_meta($featured_image_id, '_wp_attachment_image_alt', true) : '';
$featured_image_alt = trim((string) $featured_image_alt);
$contruction_terms = get_the_terms($pid, 'project-contruction');

$project_key_info = array();
foreach ($project_key_info_raw as $project_row) {
    $key = isset($project_row['key']) ? trim((string) $project_row['key']) : '';
    $value = isset($project_row['value']) ? trim((string) $project_row['value']) : '';
    if ($key !== '' || $value !== '') {
        $project_key_info[] = array(
            'key' => $key,
            'value' => $value,
        );
    }
}

$project_highlights = array();
foreach ($project_highlights_raw as $highlight_item) {
    $highlight_item = trim((string) $highlight_item);
    if ($highlight_item !== '') {
        $project_highlights[] = $highlight_item;
    }
}
?>

<?php
// Include breadcrumb
get_template_part('template/template-parts/breadcrums/index');
?>

<article class="single-project-detail" id="project-<?php echo esc_attr($pid); ?>">
    <header class="single-project__header" data-aos="fade-up">
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
            <?php if ($location !== '') : ?>
            <div class="single-project__meta single-project__meta--location">
                <div class="single-project--title__items">
                    <i class="fa-solid fa-location-dot single-project__icon" aria-hidden="true"></i>
                    <span class="single-project__text"><?php echo esc_html($location); ?></span>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </header>

    <section class="single-project__facts" data-aos="fade-up">
        <div class="single-project__facts-grid">
            <?php if ($total_area !== '') : ?>
            <div class="single-project__fact-item"><span
                    class="label"><?php esc_html_e('Total Area', 'buildpro'); ?></span><span
                    class="value"><?php echo esc_html($total_area); ?></span></div>
            <?php endif; ?>
            <?php if ($completion !== '') : ?>
            <div class="single-project__fact-item"><span
                    class="label"><?php esc_html_e('Completion', 'buildpro'); ?></span><span
                    class="value"><?php echo esc_html($completion); ?></span></div>
            <?php endif; ?>
            <?php if ($arch_style !== '') : ?>
            <div class="single-project__fact-item"><span
                    class="label"><?php esc_html_e('Architectural Style', 'buildpro'); ?></span><span
                    class="value"><?php echo esc_html($arch_style); ?></span></div>
            <?php endif; ?>
        </div>
    </section>

    <?php if ($post_content !== '' && $project_overview === '') : ?>
    <section class="single-project__content" data-aos="fade-up">
        <?php the_content(); ?>
    </section>
    <?php endif; ?>

    <?php if ($project_overview !== '') : ?>
    <section class="single-project__content" data-aos="fade-up">
        <h2 class="single-project__section-title"><?php esc_html_e('Project Overview', 'buildpro'); ?></h2>
        <?php echo apply_filters('the_content', $project_overview); ?>
    </section>
    <?php endif; ?>

    <?php if (!empty($project_key_info) || $project_the_vision !== '' || !empty($featured_image_url)) : ?>
    <section class="single-project__split" data-aos="fade-up">
        <div class="single-project__split-left">
            <?php if (!empty($project_key_info)) : ?>
            <section class="single-project__facts">
                <h2 class="single-project__section-title"><?php esc_html_e('Key Information', 'buildpro'); ?></h2>
                <ul class="single-project__info-list">
                    <?php foreach ($project_key_info as $project_info_item) : ?>
                    <li>
                        <?php if ($project_info_item['key'] !== '') : ?>
                        <strong><?php echo esc_html($project_info_item['key']); ?>:</strong>
                        <?php endif; ?>
                        <span><?php echo esc_html($project_info_item['value']); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </section>
            <?php endif; ?>

            <?php if ($project_the_vision !== '') : ?>
            <section class="single-project__content single-project__content--vision">
                <h2 class="single-project__section-title"><?php esc_html_e('The Vision', 'buildpro'); ?></h2>
                <?php echo apply_filters('the_content', $project_the_vision); ?>
            </section>
            <?php endif; ?>
        </div>

        <?php if (!empty($featured_image_url)) : ?>
        <div class="single-project__split-right">
            <figure class="single-project__featured-image">
                <img src="<?php echo esc_url($featured_image_url); ?>"
                    alt="<?php echo esc_attr($featured_image_alt !== '' ? $featured_image_alt : get_the_title($pid)); ?>">
            </figure>
        </div>
        <?php endif; ?>
    </section>
    <?php endif; ?>


    <?php if ($about !== '') : ?>
    <section class="single-project__about" data-aos="fade-up">
        <?php if (!empty($about_image_url)) : ?>
        <div class="single-project__about-image">
            <img src="<?php echo esc_url($about_image_url); ?>" alt="<?php echo esc_attr(get_the_title($pid)); ?>">
        </div>
        <?php endif; ?>
        <div class="single-project__about-content"><?php echo apply_filters('the_content', $about); ?></div>
    </section>
    <?php endif; ?>





    <?php if ($project_architectural_design !== '') : ?>
    <section class="single-project__content" data-aos="fade-up">
        <h2 class="single-project__section-title"><?php esc_html_e('Architectural Design', 'buildpro'); ?></h2>
        <?php echo apply_filters('the_content', $project_architectural_design); ?>
    </section>
    <?php endif; ?>

    <?php if (!empty($project_highlights)) : ?>
    <section class="single-project__content" data-aos="fade-up">
        <h2 class="single-project__section-title"><?php esc_html_e('Highlights', 'buildpro'); ?></h2>
        <ul>
            <?php foreach ($project_highlights as $highlight_value) : ?>
            <li><?php echo esc_html($highlight_value); ?></li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php endif; ?>

    <?php if ($information !== '') : ?>
    <section class="single-project__content" data-aos="fade-up">
        <h2 class="single-project__section-title"><?php esc_html_e('Information', 'buildpro'); ?></h2>
        <?php echo wp_kses_post(wpautop($information)); ?>
    </section>
    <?php endif; ?>

    <?php if (!empty($gallery_ids)) : ?>
    <section class="single-project__gallery" data-aos="fade-up">
        <h2 class="single-project__section-title"><?php esc_html_e('Gallery', 'buildpro'); ?></h2>
        <div class="single-project__gallery-items">
            <?php foreach ($gallery_ids as $img_id) :
                    $u = wp_get_attachment_image_url((int) $img_id, 'large');
                    if (!$u) continue;
                ?>
            <figure class="single-project__gallery-item">
                <img src="<?php echo esc_url($u); ?>" alt="<?php echo esc_attr__('Gallery image', 'buildpro'); ?>">
            </figure>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</article>