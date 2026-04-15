<?php
// Include breadcrumb
get_template_part('template/template-parts/breadcrums/index');
?>

<?php
if (have_posts()) :
    while (have_posts()) :
        the_post();
?>
        <section class="terms-of-service">
            <div class="terms-of-service__shell">
                <div class="terms-of-service__card">
                    <header class="terms-of-service__header">
                        <p class="terms-of-service__eyebrow">Terms of Service</p>
                        <h1 class="terms-of-service__title"><?php the_title(); ?></h1>
                        <div class="terms-of-service__meta">
                            <span class="terms-of-service__meta-label">Last updated</span>
                            <time class="terms-of-service__meta-value"
                                datetime="<?php echo esc_attr(get_the_modified_date('c')); ?>">
                                <?php echo esc_html(get_the_modified_date()); ?>
                            </time>
                        </div>
                        <?php if (has_excerpt()) : ?>
                            <p class="terms-of-service__lead"><?php echo wp_kses_post(get_the_excerpt()); ?></p>
                        <?php endif; ?>
                    </header>
                    <div class="terms-of-service__content">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>
        </section>
<?php
    endwhile;
endif;
?>