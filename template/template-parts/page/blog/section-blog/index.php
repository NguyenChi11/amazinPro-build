<section class="blog-section-blog">
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
            echo '<div class="section-post__list">';
            while ($q->have_posts()) {
                $q->the_post();
                $id = get_the_ID();
                $title = get_the_title($id);
                $img = get_the_post_thumbnail_url($id, 'large');
                $date = get_the_date('', $id);
                $link = get_permalink($id);
                $views_num = function_exists('buildpro_get_post_views') ? buildpro_get_post_views($id) : (int) get_post_meta($id, 'buildpro_post_views', true);
                $views_txt = (function_exists('buildpro_format_views') ? buildpro_format_views($views_num) : (string) $views_num) . ' views';
                echo '<a class="section-post__item" href="' . esc_url($link) . '">';
                echo '  <div class="section-post__item-image">';
                if (!empty($img)) {
                    echo '    <img src="' . esc_url($img) . '" alt="' . esc_attr($title) . '">';
                }
                echo '  </div>';
                echo '  <div class="section-post__item-content">';
                echo '    <div class="section-post__item-top">';
                echo          buildpro_svg_icon('calendar-days', 'regular', 'section-post__item-icon');
                echo '      <p class="section-post__item-date">' . esc_html($date) . '</p>';
                echo '    </div>';
                echo '    <h3 class="section-post__item-title">' . esc_html($title) . '</h3>';
                echo '    <div class="section-post__item-views">' . esc_html($views_txt) . '</div>';
                echo '    <p class="section-post__item-desc">' . esc_html(get_post_meta($id, 'buildpro_post_description', true)) . '</p>';
                echo '  </div>';
                echo '  <div class="section-post__item-bottom">';
                echo '    <p class="section-post__item-readmore">Read more';
                echo '      <img src="' . esc_url(get_theme_file_uri('/assets/images/icon/Arrow_Right_blue.png')) . '" alt="right arrow" class="section-services__item-link-icon">';
                echo '    </p>';
                echo '  </div>';
                echo '</a>';
            }
            echo '</div>';
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
                echo '<nav class="product--pagination"><ul class="page-numbers">';
                if ($paged > 1) {
                    echo '<li><a class="page-numbers prev" href="' . esc_url(get_pagenum_link($paged - 1)) . '">&lsaquo;</a></li>';
                } else {
                    echo '<li><span class="page-numbers prev disabled">&lsaquo;</span></li>';
                }
                foreach ($links as $lnk) {
                    echo '<li>' . $lnk . '</li>';
                }
                if ($paged < (int) $q->max_num_pages) {
                    echo '<li><a class="page-numbers next" href="' . esc_url(get_pagenum_link($paged + 1)) . '">&rsaquo;</a></li>';
                } else {
                    echo '<li><span class="page-numbers next disabled">&rsaquo;</span></li>';
                }
                echo '</ul></nav>';
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
            echo '<aside class="blog-trending">';
            echo '  <div class="blog-trending__header">';
            echo '    <span class="blog-trending__title">Trending Now</span>';
            echo '    <img class="blog-trending__icon" src="' . esc_url(get_theme_file_uri('/assets/images/icon/trend-up-svgrepo-com 1.png')) . '" alt="trending" aria-hidden="true">';
            echo '  </div>';
            echo '  <ol class="blog-trending__list">';
            foreach ($trending as $i => $p) {
                $idx = str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT);
                $views_txt = $format_number($p['views']) . ' views';
                echo '    <li class="blog-trending__item">';
                echo '      <span class="blog-trending__index">' . esc_html($idx) . '</span>';
                echo '      <div class="blog-trending__body">';
                echo '        <a href="' . esc_url($p['link']) . '" class="blog-trending__link">' . esc_html($p['title']) . '</a>';
                echo '        <div class="blog-trending__views">' . esc_html($views_txt) . '</div>';
                echo '      </div>';
                echo '    </li>';
            }
            echo '  </ol>';
            echo '</aside>';
        }
        ?>
    </div>
</section>