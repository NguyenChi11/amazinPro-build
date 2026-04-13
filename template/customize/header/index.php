<div class="wrap">
    <h1><?php echo esc_html__('Header', 'buildpro'); ?></h1>
    <form method="post" action="<?= esc_url(admin_url('admin-post.php')) ?>">
        <input type="hidden" name="action" value="buildpro_save_header" />
        <?php wp_nonce_field('buildpro_header_save'); ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="header_logo"><?php echo esc_html__('Logo', 'buildpro'); ?></label>
                    </th>
                    <td>
                        <input type="hidden" id="header_logo" name="header_logo" value="<?= esc_attr($logo_id) ?>" />
                        <button type="button" class="button"
                            id="select_header_logo"><?php echo esc_html__('Choose Image', 'buildpro'); ?></button>
                        <button type="button" class="button"
                            id="remove_header_logo"><?php echo esc_html__('Remove', 'buildpro'); ?></button>
                        <div id="header_logo_preview">
                            <?php if ($logo_url): ?>
                                <img src="<?= esc_url($logo_url) ?>" />
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label
                            for="buildpro_header_quote_text"><?php echo esc_html__('Quote Button Text', 'buildpro'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="buildpro_header_quote_text" name="buildpro_header_quote_text"
                            class="regular-text" value="<?= esc_attr($quote_text) ?>" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label
                            for="buildpro_header_quote_url"><?php echo esc_html__('Quote Button URL', 'buildpro'); ?></label>
                    </th>
                    <td>
                        <input type="url" id="buildpro_header_quote_url" name="buildpro_header_quote_url"
                            class="regular-text" value="<?= esc_attr($quote_url) ?>"
                            placeholder="https://example.com/contact" />
                        <button type="button" class="button choose-link-single" data-url="#buildpro_header_quote_url"
                            data-title="#buildpro_header_quote_text"><?php echo esc_html__('Choose Link', 'buildpro'); ?></button>
                        <p class="description">
                            <?php echo esc_html__('Leave blank to use the About page contact anchor automatically.', 'buildpro'); ?>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php submit_button(); ?>

        <div id="buildpro-custom-link-backdrop"></div>
        <div id="buildpro-custom-link-modal">
            <div class="buildpro-custom-link-header"><?php echo esc_html__('Choose Link', 'buildpro'); ?></div>
            <div class="buildpro-custom-link-body">
                <div class="buildpro-custom-link-grid">
                    <div>
                        <p class="buildpro-custom-link-row">
                            <label><?php echo esc_html__('URL', 'buildpro'); ?></label>
                            <input type="url" id="buildpro_custom_link_url" class="regular-text"
                                placeholder="https://...">
                        </p>
                        <p class="buildpro-custom-link-row">
                            <label><?php echo esc_html__('Link text', 'buildpro'); ?></label>
                            <input type="text" id="buildpro_custom_link_title" class="regular-text" placeholder="">
                        </p>
                    </div>
                    <div>
                        <p class="buildpro-custom-link-row">
                            <label><?php echo esc_html__('Search', 'buildpro'); ?></label>
                            <input type="search" id="buildpro_custom_link_search" class="regular-text"
                                placeholder="<?php echo esc_attr__('Enter keyword...', 'buildpro'); ?>">
                        </p>
                        <p class="buildpro-custom-link-row">
                            <label><?php echo esc_html__('Source', 'buildpro'); ?></label>
                            <select id="buildpro_custom_link_source">
                                <option value="all"><?php echo esc_html__('All', 'buildpro'); ?></option>
                                <option value="page"><?php echo esc_html__('Page', 'buildpro'); ?></option>
                                <option value="post"><?php echo esc_html__('Post', 'buildpro'); ?></option>
                            </select>
                        </p>
                        <div id="buildpro_custom_link_results"></div>
                    </div>
                </div>
            </div>
            <div class="buildpro-custom-link-actions">
                <button type="button" class="button"
                    id="buildpro_custom_link_cancel"><?php echo esc_html__('Cancel', 'buildpro'); ?></button>
                <button type="button" class="button button-primary"
                    id="buildpro_custom_link_apply"><?php echo esc_html__('Apply', 'buildpro'); ?></button>
            </div>
        </div>
    </form>
</div>