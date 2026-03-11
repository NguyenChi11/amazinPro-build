<?php
if (post_password_required()) {
    return;
}
?>
<?php
$buildpro_comments_css = get_template_directory_uri() . '/template-parts/comment-product/style.css';
echo '<link rel="stylesheet" href="' . esc_url($buildpro_comments_css) . '" media="all" />';
?>

<div id="comments" class="comments-area">

    <?php if (have_comments()) : ?>
        <h2 class="comments-title">
            <?php
            $buildpro_comment_count = get_comments_number();
            if ('1' === $buildpro_comment_count) {
                printf(
                    esc_html__('comments “%s”', 'buildpro'),
                    '<span class="font-semibold">' . wp_kses_post(get_the_title()) . '</span>'
                );
            } else {
                printf(
                    esc_html(_nx(
                        '%1$s comments “%2$s”',
                        '%1$s comments “%2$s”',
                        $buildpro_comment_count,
                        'comments title',
                        'buildpro'
                    )),
                    number_format_i18n($buildpro_comment_count),
                    '<span class="font-semibold">' . wp_kses_post(get_the_title()) . '</span>'
                );
            }
            ?>
        </h2>

        <?php the_comments_navigation([
            'prev_text' => __('← Older comments', 'buildpro'),
            'next_text' => __('Newer comments →', 'buildpro'),
        ]); ?>

        <ol class="comment-list">
            <?php
            wp_list_comments([
                'style'       => 'ol',
                'short_ping'  => true,
                'avatar_size' => 56,
                'callback'    => 'buildpro_comment_callback',
            ]);
            ?>
        </ol>

        <?php the_comments_navigation([
            'prev_text' => __('← Older comments', 'buildpro'),
            'next_text' => __('Newer comments →', 'buildpro'),
        ]); ?>

        <?php if (!comments_open()) : ?>
            <p class="no-comments"><?php esc_html_e('Comments have been closed.', 'buildpro'); ?></p>
        <?php endif; ?>

    <?php endif; // have_comments() 
    ?>

    <?php
    comment_form([
        'title_reply'          => __('Write a comment', 'buildpro'),
        'title_reply_before'   => '<h3 class="comments-form-title">',
        'title_reply_after'    => '</h3>',
        'comment_notes_before' => '<p class="comments-notes">' . __('Your email address will not be published.') . '</p>',
        'label_submit'         => __('Submit', 'buildpro'),
        'class_submit'         => 'comment-submit-btn',
        'comment_field'        => '<p class="comment-form-comment"><label for="comment" class="comment-label">' . _x('Comment', 'noun') . '</label><textarea id="comment" name="comment" cols="45" rows="8" class="comment-textarea" required></textarea></p>',
    ]);
    ?>

</div><!-- #comments -->