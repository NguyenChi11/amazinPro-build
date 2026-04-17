<?php
$pid = get_the_ID();
if (! $pid || get_post_type($pid) !== 'post') {
    return;
}
$banner_id = (int) get_post_meta($pid, 'buildpro_post_banner_id', true);
$banner_url = $banner_id ? wp_get_attachment_image_url($banner_id, 'full') : '';
$post_desc = get_post_meta($pid, 'buildpro_post_description', true);
$paragraph = get_post_meta($pid, 'buildpro_post_paragraph', true);
$quote_title = get_post_meta($pid, 'buildpro_post_quote_title', true);
$quote_desc = get_post_meta($pid, 'buildpro_post_quote_description', true);
$quote_gallery = get_post_meta($pid, 'buildpro_post_quote_gallery', true);
$quote_gallery = is_array($quote_gallery) ? $quote_gallery : array();
$quote_kv = get_post_meta($pid, 'buildpro_post_quote_kv', true);
$quote_kv = is_array($quote_kv) ? $quote_kv : array();
$quote_img_desc = get_post_meta($pid, 'buildpro_post_quote_desc_image_desc', true);
?>

<?php
// Include breadcrumb
get_template_part('template/template-parts/breadcrums/index');
?>

<article class="single-post-detail" id="post-<?php echo esc_attr($pid); ?>">
    <header class="single-post__header" data-aos="fade-up">
        <?php if (!empty($banner_url)) : ?>
            <div class="single-post__banner">
                <img src="<?php echo esc_url($banner_url); ?>" alt="<?php echo esc_attr(get_the_title($pid)); ?>">
            </div>
        <?php endif; ?>
        <div class="single-post__title-container">
            <h1 class="single-post__title"><?php echo esc_html(get_the_title($pid)); ?></h1>
            <div class="single-post__meta">
                <div class="single-post--title__items">
                    <i class="fa-regular fa-calendar single-post__icon" aria-hidden="true"></i>
                    <span class="single-post__text"><?php echo esc_html(get_the_date('', $pid)); ?></span>
                </div>
                <div class="single-post--title__items">
                    <i class="fa-regular fa-user single-post__icon" aria-hidden="true"></i>
                    <span class="single-post__text">
                        <?php echo esc_html(get_the_author_meta('display_name', get_post_field('post_author', $pid))); ?>
                    </span>
                </div>
                <div class="single-post--title__items">
                    <i class="fa-regular fa-bookmark single-post__icon" aria-hidden="true"></i>
                    <span
                        class="single-post__text"><?php echo wp_kses_post(get_the_category_list(', ', '', $pid)); ?></span>
                </div>
            </div>
        </div>
    </header>

    <section class="single-post__content-paragraph" data-aos="fade-up">
        <?php the_content(); ?>
    </section>

    <section class="single-post__paragraph" data-aos="fade-up">
        <div class="single-post-column"></div>
        <?php if (!empty($paragraph)) : ?>
            <div class="single-post__content"><?php echo wp_kses_post($paragraph); ?></div>
        <?php endif; ?>
    </section>

    <section class="single-post__key__value" data-aos="fade-up">
        <?php if (!empty($quote_title)) : ?>
            <h2 class="single-post__quote-title"><?php echo esc_html($quote_title); ?></h2>
        <?php endif; ?>
        <?php if (!empty($quote_kv)) : ?>
            <div class="single-post__quote-kv">
                <?php foreach ($quote_kv as $row) :
                    $k = isset($row['key']) ? (string) $row['key'] : '';
                    $v = isset($row['value']) ? (string) $row['value'] : '';
                    if ($k === '' && $v === '') continue;
                ?>
                    <div class="single-post__kv-item-container">
                        <img class="single-post__key-icon"
                            src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/icon/tick_green.png'); ?>"
                            alt="<?php echo esc_attr__('Check', 'buildpro'); ?>">
                        <div class="single-post__kv-item">
                            <?php if ($k !== '') : ?>
                                <span class="single-post__kv-key"><?php echo esc_html($k); ?>:</span>
                            <?php endif; ?>
                            <?php if ($v !== '') : ?>
                                <span class="single-post__kv-value"><?php echo esc_html($v); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <section class="single-post__quote--gallery" data-aos="fade-up">
        <div class="single-post__quote--gallery__container">
            <?php if (!empty($quote_gallery)) : ?>
                <div class="single-post__quote--gallery__items">
                    <?php foreach ($quote_gallery as $img_id) :
                        $u = wp_get_attachment_image_url((int) $img_id, 'large');
                        if (!$u) continue;
                    ?>
                        <figure class="single-post__gallery-item">
                            <img src="<?php echo esc_url($u); ?>" alt="<?php echo esc_attr__('Gallery image', 'buildpro'); ?>">
                        </figure>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($quote_img_desc)) : ?>
                <div class="single-post__image-desc">
                    <?php echo esc_html($quote_img_desc); ?>
                </div>
            <?php endif; ?>
        </div>
        <?php if (!empty($quote_desc)) : ?>
            <p class="single-post__quote-desc"><?php echo esc_html($quote_desc); ?></p>
        <?php endif; ?>
    </section>

    <!-- <section class="single-post__description">
        <?php if (!empty($post_desc)) : ?>
        <p><?php echo esc_html($post_desc); ?></p>
        <?php endif; ?>
    </section>

    <section class="single-post__featured">
        <?php
        $featured = get_the_post_thumbnail($pid, 'large');
        if (!empty($featured)) {
            echo $featured;
        }
        ?>
    </section> -->
    <?php
    $more_posts_q = new WP_Query(array(
        'post_type' => 'post',
        'posts_per_page' => 3,
        'post__not_in' => array($pid),
        'orderby' => 'date',
        'order' => 'DESC',
        'post_status' => 'publish',
        'no_found_rows' => true,
    ));
    if ($more_posts_q->have_posts()) :
    ?>
        <section class="single-post__related-posts" data-aos="fade-up">
            <h2 class="single-post__related-title"><?php echo esc_html__('More Posts', 'buildpro'); ?></h2>
            <div class="blog-section-blog__left">
                <div class="section-post__list">
                    <?php
                    while ($more_posts_q->have_posts()) :
                        $more_posts_q->the_post();
                        $more_id = get_the_ID();
                        $more_title = get_the_title($more_id);
                        $more_img = get_the_post_thumbnail_url($more_id, 'large');
                        $more_date = get_the_date('', $more_id);
                        $more_link = get_permalink($more_id);
                        $more_desc = get_post_meta($more_id, 'buildpro_post_description', true);
                        $more_views_num = function_exists('buildpro_get_post_views') ? buildpro_get_post_views($more_id) : (int) get_post_meta($more_id, 'buildpro_post_views', true);
                        $more_views_txt = sprintf(esc_html__('%s views', 'buildpro'), function_exists('buildpro_format_views') ? buildpro_format_views($more_views_num) : (string) $more_views_num);
                    ?>
                        <a class="section-post__item" href="<?php echo esc_url($more_link); ?>">
                            <div class="section-post__item-image">
                                <?php if (!empty($more_img)) : ?>
                                    <img src="<?php echo esc_url($more_img); ?>" alt="<?php echo esc_attr($more_title); ?>">
                                <?php endif; ?>
                            </div>
                            <div class="section-post__item-content">
                                <div class="section-post__item-top">
                                    <?php echo buildpro_svg_icon('calendar-days', 'regular', 'section-post__item-icon'); ?>
                                    <p class="section-post__item-date"><?php echo esc_html($more_date); ?></p>
                                </div>
                                <h3 class="section-post__item-title"><?php echo esc_html($more_title); ?></h3>
                                <div class="section-post__item-views"><?php echo esc_html($more_views_txt); ?></div>
                                <p class="section-post__item-desc"><?php echo esc_html($more_desc); ?></p>
                            </div>
                            <div class="section-post__item-bottom">
                                <p class="section-post__item-readmore">
                                    <?php echo esc_html__('Read more', 'buildpro'); ?>
                                    <img src="<?php echo esc_url(get_theme_file_uri('/assets/images/icon/Arrow_Right_blue.png')); ?>"
                                        alt="<?php echo esc_attr__('Right arrow', 'buildpro'); ?>"
                                        class="section-services__item-link-icon">
                                </p>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>
    <?php
    endif;
    wp_reset_postdata();
    ?>

</article>