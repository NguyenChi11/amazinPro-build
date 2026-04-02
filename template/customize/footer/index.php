    <div class="wrap">
        <h1><?php echo esc_html__('Footer', 'buildpro'); ?></h1>
        <h2 class="nav-tab-wrapper">
            <a href="#tab-banner" class="nav-tab nav-tab-active"><?php echo esc_html__('Banner', 'buildpro'); ?></a>
            <a href="#tab-information" class="nav-tab"><?php echo esc_html__('Information', 'buildpro'); ?></a>
            <a href="#tab-contact" class="nav-tab"><?php echo esc_html__('Contact', 'buildpro'); ?></a>
            <a href="#tab-contact-link" class="nav-tab"><?php echo esc_html__('Contact Link', 'buildpro'); ?></a>
            <a href="#tab-create-build" class="nav-tab"><?php echo esc_html__('Create Build', 'buildpro'); ?></a>
            <a href="#tab-policy" class="nav-tab"><?php echo esc_html__('Policy', 'buildpro'); ?></a>
            <a href="#tab-servicer" class="nav-tab"><?php echo esc_html__('Servicer', 'buildpro'); ?></a>
        </h2>
        <form method="post" action="<?= esc_url(admin_url('admin-post.php')) ?>">
            <input type="hidden" name="action" value="buildpro_save_footer" />
            <?php wp_nonce_field('buildpro_footer_save'); ?>
            <div id="buildpro-footer-sections">
                <div id="tab-banner" class="buildpro-footer-section active">
                    <div class="buildpro-block">
                        <h3><?php echo esc_html__('Banner', 'buildpro'); ?></h3>
                        <div class="buildpro-field">
                            <input type="hidden" id="footer_banner_image_id" name="footer_banner_image_id"
                                value="<?= esc_attr($banner_image_id) ?>">
                            <button type="button" class="button"
                                id="select_footer_banner_image"><?php echo esc_html__('Choose Image', 'buildpro'); ?></button>
                            <button type="button" class="button"
                                id="remove_footer_banner_image"><?php echo esc_html__('Remove', 'buildpro'); ?></button>
                        </div>
                        <div id="footer_banner_preview" class="image-preview">
                            <?= $banner_thumb ? '<img src="' . esc_url($banner_thumb) . '" style="max-height:80px;">' : '<span style="color:#888">' . esc_html__('No image selected', 'buildpro') . '</span>' ?>
                        </div>
                    </div>
                </div>
                <div id="tab-information" class="buildpro-footer-section">
                    <div class="buildpro-block">
                        <h3><?php echo esc_html__('Information', 'buildpro'); ?></h3>
                        <div class="buildpro-grid">
                            <div></div>
                            <div>
                                <p class="buildpro-field">
                                    <label><?php echo esc_html__('Description', 'buildpro'); ?></label>
                                    <textarea name="footer_information_description" rows="4"
                                        class="large-text"><?= esc_textarea($info_description) ?></textarea>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="tab-contact" class="buildpro-footer-section">
                    <div class="buildpro-block">
                        <h3><?php echo esc_html__('Contact', 'buildpro'); ?></h3>
                        <p class="buildpro-field">
                            <label><?php echo esc_html__('Location', 'buildpro'); ?></label>
                            <input type="text" name="footer_contact_location" class="regular-text"
                                value="<?= esc_attr($contact_location) ?>">
                        </p>
                        <p class="buildpro-field">
                            <label><?php echo esc_html__('Phone', 'buildpro'); ?></label>
                            <input type="text" name="footer_contact_phone" class="regular-text"
                                value="<?= esc_attr($contact_phone) ?>">
                        </p>
                        <p class="buildpro-field">
                            <label><?php echo esc_html__('Email', 'buildpro'); ?></label>
                            <input type="email" name="footer_contact_email" class="regular-text"
                                value="<?= esc_attr($contact_email) ?>">
                        </p>
                        <p class="buildpro-field">
                            <label><?php echo esc_html__('Time', 'buildpro'); ?></label>
                            <input type="text" name="footer_contact_time" class="regular-text"
                                value="<?= esc_attr($contact_time) ?>">
                        </p>
                    </div>
                </div>
                <div id="tab-contact-link" class="buildpro-footer-section">
                    <div class="buildpro-block">
                        <h3><?php echo esc_html__('Contact Link', 'buildpro'); ?></h3>
                        <div id="footer-contact-links-wrapper">
                            <?php $cl_index = 0;
                            foreach ($contact_links as $cl):
                                $cl_icon_id = isset($cl['icon_id']) ? absint($cl['icon_id']) : 0;
                                $cl_icon_thumb = $cl_icon_id ? wp_get_attachment_image_url($cl_icon_id, 'thumbnail') : '';
                                $cl_url = isset($cl['url']) ? esc_url($cl['url']) : '';
                                $cl_title = isset($cl['title']) ? sanitize_text_field($cl['title']) : '';
                                $cl_target = isset($cl['target']) ? sanitize_text_field($cl['target']) : '';
                            ?>
                                <div class="buildpro-block" data-index="<?= esc_attr($cl_index) ?>">
                                    <p class="buildpro-field">
                                        <label><?php echo esc_html__('Icon', 'buildpro'); ?></label>
                                        <input type="hidden"
                                            name="footer_contact_links[<?= esc_attr($cl_index) ?>][icon_id]"
                                            value="<?= esc_attr($cl_icon_id) ?>">
                                        <button type="button"
                                            class="button select-contact-icon"><?php echo esc_html__('Select photo', 'buildpro'); ?></button>
                                        <button type="button"
                                            class="button remove-contact-icon"><?php echo esc_html__('Remove photo', 'buildpro'); ?></button>
                                    </p>
                                    <div class="image-preview contact-icon-preview">
                                        <?= $cl_icon_thumb ? '<img src="' . esc_url($cl_icon_thumb) . '" style="max-height:80px;">' : '<span style="color:#888">' . esc_html__('No image selected', 'buildpro') . '</span>' ?>
                                    </div>
                                    <p class="buildpro-field">
                                        <label><?php echo esc_html__('URL', 'buildpro'); ?></label>
                                        <input type="url" name="footer_contact_links[<?= esc_attr($cl_index) ?>][url]"
                                            class="regular-text" value="<?= esc_attr($cl_url) ?>" placeholder="https://...">
                                        <button type="button"
                                            class="button choose-link"><?php echo esc_html__('Choose Link', 'buildpro'); ?></button>
                                    </p>
                                    <p class="buildpro-field">
                                        <label><?php echo esc_html__('Button Label', 'buildpro'); ?></label>
                                        <input type="text" name="footer_contact_links[<?= esc_attr($cl_index) ?>][title]"
                                            class="regular-text" value="<?= esc_attr($cl_title) ?>">
                                    </p>
                                    <p class="buildpro-field">
                                        <label><?php echo esc_html__('Link Target', 'buildpro'); ?></label>
                                    <div class="checkbox-label">
                                        <input type="checkbox"
                                            name="footer_contact_links[<?= esc_attr($cl_index) ?>][target]" value="_blank"
                                            <?php checked($cl_target, '_blank'); ?>>
                                        <?php echo esc_html__('Open in new tab', 'buildpro'); ?>
                                    </div>
                                    </p>
                                    <div class="buildpro-actions">
                                        <button type="button"
                                            class="button remove-row"><?php echo esc_html__('Remove', 'buildpro'); ?></button>
                                    </div>
                                </div>
                            <?php $cl_index++;
                            endforeach; ?>
                        </div>
                        <button type="button" class="button button-primary"
                            id="footer-contact-links-add"><?php echo esc_html__('Add Item', 'buildpro'); ?></button>
                    </div>
                </div>
                <div id="tab-create-build" class="buildpro-footer-section">
                    <div class="buildpro-block">
                        <h3><?php echo esc_html__('Create Build', 'buildpro'); ?></h3>
                        <p class="buildpro-field">
                            <label><?php echo esc_html__('Text', 'buildpro'); ?></label>
                            <input type="text" name="footer_create_build_text" class="regular-text"
                                value="<?= esc_attr($create_build_text) ?>">
                        </p>
                    </div>
                </div>
                <div id="tab-policy" class="buildpro-footer-section">
                    <div class="buildpro-block">
                        <h3><?php echo esc_html__('Policy', 'buildpro'); ?></h3>
                        <p class="buildpro-field">
                            <label><?php echo esc_html__('Text', 'buildpro'); ?></label>
                            <input type="text" name="footer_policy_text" class="regular-text"
                                value="<?= esc_attr($policy_text) ?>">
                        </p>
                        <p class="buildpro-field">
                            <label><?php echo esc_html__('URL', 'buildpro'); ?></label>
                            <input type="url" id="footer_policy_link_url" name="footer_policy_link[url]"
                                class="regular-text" value="<?= esc_attr($policy_link['url']) ?>"
                                placeholder="https://...">
                            <button type="button" class="button choose-link-single" data-url="#footer_policy_link_url"
                                data-title="#footer_policy_link_title"
                                data-target="#footer_policy_link_target"><?php echo esc_html__('Choose Link', 'buildpro'); ?></button>
                        </p>
                        <p class="buildpro-field">
                            <label><?php echo esc_html__('Button Label', 'buildpro'); ?></label>
                            <input type="text" id="footer_policy_link_title" name="footer_policy_link[title]"
                                class="regular-text" value="<?= esc_attr($policy_link['title']) ?>">
                        </p>
                        <p class="buildpro-field">
                            <label><?php echo esc_html__('Link Target', 'buildpro'); ?></label>
                        <div class="checkbox-label">
                            <input type="checkbox" id="footer_policy_link_target" name="footer_policy_link[target]"
                                value="_blank" <?php checked($policy_link['target'], '_blank'); ?>>
                            <?php echo esc_html__('Open in new tab', 'buildpro'); ?>
                        </div>
                        </p>
                    </div>
                </div>
                <div id="tab-servicer" class="buildpro-footer-section">
                    <div class="buildpro-block">
                        <h3><?php echo esc_html__('Servicer', 'buildpro'); ?></h3>
                        <p class="buildpro-field">
                            <label><?php echo esc_html__('Text', 'buildpro'); ?></label>
                            <input type="text" name="footer_servicer_text" class="regular-text"
                                value="<?= esc_attr($servicer_text) ?>">
                        </p>
                        <p class="buildpro-field">
                            <label><?php echo esc_html__('URL', 'buildpro'); ?></label>
                            <input type="url" id="footer_servicer_link_url" name="footer_servicer_link[url]"
                                class="regular-text" value="<?= esc_attr($servicer_link['url']) ?>"
                                placeholder="https://...">
                            <button type="button" class="button choose-link-single" data-url="#footer_servicer_link_url"
                                data-title="#footer_servicer_link_title"
                                data-target="#footer_servicer_link_target"><?php echo esc_html__('Choose Link', 'buildpro'); ?></button>
                        </p>
                        <p class="buildpro-field">
                            <label><?php echo esc_html__('Button Label', 'buildpro'); ?></label>
                            <input type="text" id="footer_servicer_link_title" name="footer_servicer_link[title]"
                                class="regular-text" value="<?= esc_attr($servicer_link['title']) ?>">
                        </p>
                        <p class="buildpro-field">
                            <label><?php echo esc_html__('Link Target', 'buildpro'); ?></label>
                        <div class="checkbox-label">
                            <input type="checkbox" id="footer_servicer_link_target" name="footer_servicer_link[target]"
                                value="_blank" <?php checked($servicer_link['target'], '_blank'); ?>>
                            <?php echo esc_html__('Open in new tab', 'buildpro'); ?>
                        </div>
                        </p>
                    </div>
                </div>
            </div>
            <?php submit_button(); ?>
            <div id="buildpro-custom-link-backdrop"></div>
            <div id="buildpro-custom-link-modal">
                <div class="buildpro-custom-link-header"><?php echo esc_html__('Choose Link', 'buildpro'); ?></div>
                <div class="buildpro-custom-link-body">
                    <div class="buildpro-custom-link-grid">
                        <div>
                            <p class="buildpro-custom-link-row">
                                <label><?php echo esc_html__('URL', 'buildpro'); ?></label><input type="url"
                                    id="buildpro_custom_link_url" class="regular-text" placeholder="https://...">
                            </p>
                            <p class="buildpro-custom-link-row">
                                <label><?php echo esc_html__('Link text', 'buildpro'); ?></label><input type="text"
                                    id="buildpro_custom_link_title" class="regular-text" placeholder="">
                            </p>
                            <p class="buildpro-custom-link-row"><label><input type="checkbox"
                                        id="buildpro_custom_link_target">
                                    <?php echo esc_html__('Open in new tab', 'buildpro'); ?></label></p>
                        </div>
                        <div>
                            <p class="buildpro-custom-link-row">
                                <label><?php echo esc_html__('Search', 'buildpro'); ?></label><input type="search"
                                    id="buildpro_custom_link_search" class="regular-text"
                                    placeholder="<?php echo esc_attr__('Enter keyword...', 'buildpro'); ?>">
                            </p>
                            <p class="buildpro-custom-link-row">
                                <label><?php echo esc_html__('Source', 'buildpro'); ?></label><select
                                    id="buildpro_custom_link_source">
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