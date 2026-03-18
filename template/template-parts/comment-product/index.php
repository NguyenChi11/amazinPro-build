<?php
if (post_password_required()) {
    return;
}
?>


<div id="comments" class="comments-area">
    <div class="comments-shell">

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

            <div class="comments-stream" id="comments-stream" data-chunk="20" data-order="<?php echo esc_attr((string) get_option('comment_order', 'asc')); ?>" role="region" aria-label="<?php echo esc_attr__('Comments', 'buildpro'); ?>">
                <ol class="comment-list" id="comments-list">
                    <?php
                    if (!function_exists('buildpro_comment_callback')) {
                        function buildpro_comment_callback($comment, $args, $depth)
                        {
                            $tag = ($args['style'] === 'div') ? 'div' : 'li';
                    ?>
                            <<?php echo esc_attr($tag); ?> id="comment-<?php comment_ID(); ?>"
                                <?php comment_class('comment-item', $comment); ?>>
                                <article class="comment-body">
                                    <footer class="comment-meta">
                                        <div class="comment-author">
                                            <?php
                                            $avatar_size = isset($args['avatar_size']) ? (int) $args['avatar_size'] : 56;
                                            echo get_avatar($comment, $avatar_size, '', '', ['class' => 'comment-avatar']);
                                            ?>
                                            <div class="comment-author-info">
                                                <span class="comment-author-name"><?php comment_author(); ?></span>
                                                <time class="comment-date" datetime="<?php comment_date(DATE_W3C); ?>">
                                                    <?php comment_date(); ?>
                                                </time>
                                            </div>
                                        </div>
                                        <?php if ('0' == $comment->comment_approved) : ?>
                                            <p class="comment-awaiting-moderation">
                                                <?php esc_html_e('Your comment is awaiting moderation.', 'buildpro'); ?></p>
                                        <?php endif; ?>
                                    </footer>
                                    <div class="comment-content">
                                        <?php comment_text(); ?>
                                    </div>
                                    <?php
                                    comment_reply_link(array_merge($args, [
                                        'depth'     => $depth,
                                        'max_depth' => $args['max_depth'],
                                        'before'    => '<div class="reply">',
                                        'after'     => '</div>',
                                    ]));
                                    ?>
                                </article>
                        <?php
                        }
                    }
                    wp_list_comments([
                        'style'             => 'ol',
                        'short_ping'        => true,
                        'avatar_size'       => 56,
                        'callback'          => 'buildpro_comment_callback',
                        'per_page'          => 0,
                        'reverse_top_level' => false,
                        'reverse_children'  => false,
                    ]);
                        ?>
                </ol>
            </div>

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
        $buildpro_comment_fields = [
            'author' => '<p class="comment-form-author"><label for="author" class="comment-label">' . __('Name', 'buildpro') . '</label><input id="author" name="author" type="text" class="comment-input" placeholder="' . esc_attr__('Your name', 'buildpro') . '" size="30" autocomplete="name" required></p>',
            'email'  => '<p class="comment-form-email"><label for="email" class="comment-label">' . __('Email', 'buildpro') . '</label><input id="email" name="email" type="email" class="comment-input" placeholder="' . esc_attr__('Your email', 'buildpro') . '" size="30" autocomplete="email" required></p>',
            'url'    => '<p class="comment-form-url"><label for="url" class="comment-label">' . __('Website', 'buildpro') . '</label><input id="url" name="url" type="url" class="comment-input" placeholder="' . esc_attr__('Website (optional)', 'buildpro') . '" size="30" autocomplete="url"></p>',
        ];

        comment_form([
            'class_form'           => 'comment-form buildpro-comment-form',
            'fields'               => $buildpro_comment_fields,
            'title_reply'          => __('Write a comment', 'buildpro'),
            'title_reply_before'   => '<h3 class="comments-form-title">',
            'title_reply_after'    => '</h3>',
            'logged_in_as'         => '',
            'comment_notes_before' => '<p class="comments-notes">' . __('Be respectful and keep your comment concise.', 'buildpro') . '</p>',
            'comment_notes_after'  => '',
            'label_submit'         => __('Submit', 'buildpro'),
            'class_submit'         => 'comment-submit-btn',
            'comment_field'        => '<p class="comment-form-comment"><label for="comment" class="comment-label">' . esc_html_x('Comment', 'noun', 'buildpro') . '</label><textarea id="comment" name="comment" cols="45" rows="4" class="comment-textarea" placeholder="' . esc_attr__('Write a public comment...', 'buildpro') . '" required></textarea></p>',
        ]);
        ?>
    </div>
</div><!-- #comments -->