<?php
$items = array();
$paged = max(1, get_query_var('paged'), get_query_var('page'));
$q = new WP_Query(array(
    'post_type' => 'project',
    'posts_per_page' => 6,
    'paged' => $paged,
    'orderby' => 'date',
    'order' => 'DESC',
    'post_status' => 'publish',
    'no_found_rows' => false,
));
if ($q->have_posts()) {
    while ($q->have_posts()) {
        $q->the_post();
        $pid = get_the_ID();
        $image_id = get_post_thumbnail_id($pid);
        $name = get_the_title($pid);
        $terms = get_the_terms($pid, 'project-contruction');
        $text = '';
        if (is_array($terms) && !empty($terms)) {
            $text = implode(', ', wp_list_pluck($terms, 'name'));
        }
        $location = get_post_meta($pid, 'location_project', true);
        $link_url = get_permalink($pid);
        $items[] = array(
            'image_id' => $image_id,
            'text' => $text,
            'name' => $name,
            'location' => $location,
            'link_url' => $link_url,
            'link_target' => '',
        );
    }
    wp_reset_postdata();
}
?>
<section class="project--section-list">
    <div class="section-portfolio__list">
        <?php foreach ($items as $item): ?>
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
                    <p class="section-portfolio__item-text"><?php echo esc_html($item['text']); ?></p>
                    <h3 class="section-portfolio__item-name"><?php echo esc_html($item['name']); ?></h3>
                    <div class="section-portfolio__item-location-wrapper">
                        <img src="<?php echo esc_url(get_theme_file_uri('/assets/images/icon/icon_location.png')); ?>"
                            alt="location" class="section-portfolio__item-location-icon">
                        <p class="section-portfolio__item-location"><?php echo esc_html($item['location']); ?></p>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
    <?php
    $big = 999999999;
    $max_pages = max(1, (int) $q->max_num_pages);
    $page_links = paginate_links(array(
        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format' => '?paged=%#%',
        'current' => max(1, $paged),
        'total' => $max_pages,
        'type' => 'array',
        'prev_next' => false,
    ));
    ?>
    <?php if (!empty($page_links)): ?>
        <nav class="project--pagination">
            <ul class="page-numbers">
                <?php if ($paged > 1): ?>
                    <li><a class="page-numbers prev" href="<?php echo esc_url(get_pagenum_link($paged - 1)); ?>">&lsaquo;</a></li>
                <?php else: ?>
                    <li><span class="page-numbers prev disabled">&lsaquo;</span></li>
                <?php endif; ?>
                <?php foreach ($page_links as $pl): ?>
                    <li><?php echo $pl; ?></li>
                <?php endforeach; ?>
                <?php if ($paged < $max_pages): ?>
                    <li><a class="page-numbers next" href="<?php echo esc_url(get_pagenum_link($paged + 1)); ?>">&rsaquo;</a></li>
                <?php else: ?>
                    <li><span class="page-numbers next disabled">&rsaquo;</span></li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
</section>