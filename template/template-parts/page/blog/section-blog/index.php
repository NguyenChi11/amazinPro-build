<section class="blog-section-blog" data-aos="fade-up">
    <div class="blog-section-blog__left">
        <?php
        $paged = max(1, get_query_var('paged') ? (int) get_query_var('paged') : (get_query_var('page') ? (int) get_query_var('page') : 1));
        $ppp = 4;
        $q = new WP_Query(array(
            'post_type' => 'post',
            'posts_per_page' => $ppp,
            'paged' => $paged,
            'orderby' => 'date',
            'order' => 'DESC',
            'post_status' => 'publish',
        ));
        if ($q->have_posts()) {
            ?>
        <div class="section-post__list">
            <?php
                while ($q->have_posts()) {
                    $q->the_post();
                    $id = get_the_ID();
                    $title = get_the_title($id);
                    $img = get_the_post_thumbnail_url($id, 'large');
                    $date = get_the_date('', $id);
                    $link = get_permalink($id);
                    $views_num = function_exists('buildpro_get_post_views') ? buildpro_get_post_views($id) : (int) get_post_meta($id, 'buildpro_post_views', true);
                    $views_txt = sprintf(esc_html__('%s views', 'buildpro'), function_exists('buildpro_format_views') ? buildpro_format_views($views_num) : (string) $views_num);
                    ?>
            <a class="section-post__item" href="<?= esc_url($link); ?>">
                <div class="section-post__item-image">
                    <?php if (!empty($img)) { ?>
                    <img src="<?= esc_url($img); ?>" alt="<?= esc_attr($title); ?>">
                    <?php } ?>
                </div>
                <div class="section-post__item-content">
                    <div class="section-post__item-top">
                        <?= buildpro_svg_icon('calendar-days', 'regular', 'section-post__item-icon'); ?>
                        <p class="section-post__item-date"><?= esc_html($date); ?></p>
                    </div>
                    <h3 class="section-post__item-title"><?= esc_html($title); ?></h3>
                    <div class="section-post__item-views"><?= esc_html($views_txt); ?></div>
                    <p class="section-post__item-desc">
                        <?= esc_html(get_post_meta($id, 'buildpro_post_description', true)); ?></p>
                </div>
                <div class="section-post__item-bottom">
                    <p class="section-post__item-readmore">
                        <?= esc_html__('Read more', 'buildpro'); ?>
                        <img src="<?= esc_url(get_theme_file_uri('/assets/images/icon/Arrow_Right_blue.png')); ?>"
                            alt="<?= esc_attr__('Right arrow', 'buildpro'); ?>"
                            class="section-services__item-link-icon">
                    </p>
                </div>
            </a>
            <?php } ?>
        </div>
        <?php
            $big = 999999999;
            $links = paginate_links(array(
                'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                'format' => '?paged=%#%',
                'current' => max(1, $paged),
                'total' => (int) $q->max_num_pages,
                'type' => 'array',
                'prev_next' => false,
            ));
            if (!empty($links) && is_array($links)) {
                ?>
        <nav class="blog--pagination">
            <ul class="page-numbers">
                <?php if ($paged > 1) { ?>
                <li><a class="page-numbers prev" href="<?= esc_url(get_pagenum_link($paged - 1)); ?>">&lsaquo;</a></li>
                <?php } else { ?>
                <li><span class="page-numbers prev disabled">&lsaquo;</span></li>
                <?php } ?>

                <?php foreach ($links as $lnk) { ?>
                <li><?= $lnk; ?></li>
                <?php } ?>

                <?php if ($paged < (int) $q->max_num_pages) { ?>
                <li><a class="page-numbers next" href="<?= esc_url(get_pagenum_link($paged + 1)); ?>">&rsaquo;</a></li>
                <?php } else { ?>
                <li><span class="page-numbers next disabled">&rsaquo;</span></li>
                <?php } ?>
            </ul>
        </nav>
        <?php
            }
            wp_reset_postdata();
        }
        ?>
    </div>
    <div class="blog-section-blog__right">
        <?php
        $limit = 3;
        $trending = [];
        $format_number = function ($n) {
            if ($n >= 1000000) {
                return round($n / 1000000, 1) . 'm';
            }
            if ($n >= 1000) {
                return round($n / 1000, 1) . 'k';
            }
            return $n;
        };
        $q1 = new WP_Query(array(
            'post_type' => 'post',
            'posts_per_page' => $limit,
            'meta_key' => 'buildpro_post_views',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
            'meta_query' => array(
                array(
                    'key' => 'buildpro_post_views',
                    'compare' => 'EXISTS',
                ),
            ),
            'post_status' => 'publish',
            'no_found_rows' => true,
        ));
        if ($q1->have_posts()) {
            while ($q1->have_posts()) {
                $q1->the_post();
                $id = get_the_ID();
                $views = (int) get_post_meta($id, 'buildpro_post_views', true);
                $trending[] = array(
                    'id' => $id,
                    'title' => get_the_title($id),
                    'link' => get_permalink($id),
                    'views' => $views,
                );
            }
            wp_reset_postdata();
        }
        if (count($trending) < $limit) {
            $remaining = $limit - count($trending);
            $exclude = wp_list_pluck($trending, 'id');
            $q2 = new WP_Query(array(
                'post_type' => 'post',
                'posts_per_page' => $remaining,
                'orderby' => 'comment_count',
                'order' => 'DESC',
                'post__not_in' => $exclude,
                'post_status' => 'publish',
                'no_found_rows' => true,
            ));
            if ($q2->have_posts()) {
                while ($q2->have_posts()) {
                    $q2->the_post();
                    $id = get_the_ID();
                    $views = (int) get_post_meta($id, 'buildpro_post_views', true);
                    $trending[] = array(
                        'id' => $id,
                        'title' => get_the_title($id),
                        'link' => get_permalink($id),
                        'views' => $views,
                    );
                }
                wp_reset_postdata();
            }
        }
        if (!empty($trending)) {
            ?>
        <aside class="blog-trending">
            <div class="blog-trending__header">
                <span class="blog-trending__title"><?= esc_html__('Trending Now', 'buildpro'); ?></span>
                <img class="blog-trending__icon"
                    src="<?= esc_url(get_theme_file_uri('/assets/images/icon/trend-up-svgrepo-com 1.png')); ?>"
                    alt="<?= esc_attr__('Trending', 'buildpro'); ?>" aria-hidden="true">
            </div>
            <ol class="blog-trending__list">
                <?php foreach ($trending as $i => $p) {
                        $idx = str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT);
                        $views_txt = sprintf(esc_html__('%s views', 'buildpro'), $format_number($p['views']));
                        ?>
                <li class="blog-trending__item">
                    <span class="blog-trending__index"><?= esc_html($idx); ?></span>
                    <div class="blog-trending__body">
                        <a href="<?= esc_url($p['link']); ?>"
                            class="blog-trending__link"><?= esc_html($p['title']); ?></a>
                        <div class="blog-trending__views"><?= esc_html($views_txt); ?></div>
                    </div>
                </li>
                <?php } ?>
            </ol>
        </aside>
        <?php
        }
        ?>
    </div>
</section>