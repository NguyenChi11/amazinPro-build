<?php
if (have_posts()) :
    while (have_posts()) :
        the_post();
?>
        <section class="privacy-policy">
            <div class="privacy-policy__shell">
                <div class="privacy-policy__card">
                    <header class="privacy-policy__header">
                        <p class="privacy-policy__eyebrow">Privacy Policy</p>
                        <h1 class="privacy-policy__title"><?php the_title(); ?></h1>
                        <div class="privacy-policy__meta">
                            <span class="privacy-policy__meta-label">Last updated</span>
                            <time class="privacy-policy__meta-value" datetime="<?php echo esc_attr(get_the_modified_date('c')); ?>">
                                <?php echo esc_html(get_the_modified_date()); ?>
                            </time>
                        </div>
                        <?php if (has_excerpt()) : ?>
                            <p class="privacy-policy__lead"><?php echo wp_kses_post(get_the_excerpt()); ?></p>
                        <?php endif; ?>
                    </header>
                    <div class="privacy-policy__content">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>
        </section>
<?php
    endwhile;
endif;
?>