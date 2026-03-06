<div class="wrap">
    <h1>Header</h1>
    <form method="post" action="<?= esc_url(admin_url('admin-post.php')) ?>">
        <input type="hidden" name="action" value="buildpro_save_header" />
        <?php wp_nonce_field('buildpro_header_save'); ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="header_logo">Logo</label>
                    </th>
                    <td>
                        <input type="hidden" id="header_logo" name="header_logo" value="<?= esc_attr($logo_id) ?>" />
                        <button type="button" class="button" id="select_header_logo">Select Image</button>
                        <button type="button" class="button" id="remove_header_logo">Remove</button>
                        <div id="header_logo_preview">
                            <?php if ($logo_url): ?>
                                <img src="<?= esc_url($logo_url) ?>" />
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="buildpro_header_title">Title</label>
                    </th>
                    <td>
                        <input type="text" id="buildpro_header_title" name="buildpro_header_title" class="regular-text"
                            value="<?= esc_attr($text) ?>" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="buildpro_header_description">Description</label>
                    </th>
                    <td>
                        <textarea id="buildpro_header_description" name="buildpro_header_description" class="large-text"
                            rows="4"><?= esc_textarea($desc) ?></textarea>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php submit_button('submit change'); ?>
    </form>
</div>