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
    <header class="single-post__header">
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

    <section class="single-post__content-paragraph">
        <?php the_content(); ?>
    </section>

    <section class="single-post__paragraph">
        <div class="single-post-column"></div>
        <?php if (!empty($paragraph)) : ?>
            <div class="single-post__content"><?php echo wp_kses_post($paragraph); ?></div>
        <?php endif; ?>
    </section>

    <section class="single-post__key__value">
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
                            alt="">
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

    <section class="single-post__quote--gallery">
        <div class="single-post__quote--gallery__container">
            <?php if (!empty($quote_gallery)) : ?>
                <div class="single-post__quote--gallery__items">
                    <?php foreach ($quote_gallery as $img_id) :
                        $u = wp_get_attachment_image_url((int) $img_id, 'large');
                        if (!$u) continue;
                    ?>
                        <figure class="single-post__gallery-item">
                            <img src="<?php echo esc_url($u); ?>" alt="">
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

</article>