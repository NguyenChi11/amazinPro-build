<?php
$page_id = get_queried_object_id();
$title = get_post_meta($page_id, 'projects_title', true);
$desc = get_post_meta($page_id, 'projects_description', true);
if (is_customize_preview()) {
    $data = get_theme_mod('buildpro_projects_title_data', array());
    if (is_string($data)) {
        $decoded = json_decode($data, true);
        if (is_array($decoded)) {
            $data = $decoded;
        }
    }
    if (is_array($data)) {
        if (isset($data['title']) && $data['title'] !== '') {
            $title = $data['title'];
        } else {
            $mod_title = get_theme_mod('projects_title', '');
            if ($mod_title !== '') {
                $title = $mod_title;
            }
        }
        if (isset($data['description']) && $data['description'] !== '') {
            $desc = $data['description'];
        } else {
            $mod_desc = get_theme_mod('projects_description', '');
            if ($mod_desc !== '') {
                $desc = $mod_desc;
            }
        }
    }
}
$title = is_string($title) ? $title : '';
$desc = is_string($desc) ? $desc : '';
$title = trim($title);
$desc = trim($desc);
if ($title === '' && $desc === '') {
    if (!is_customize_preview()) {
        return;
    }
}
?>
<section class="project--section-title">
    <?php if (is_customize_preview()): ?>
    <div class="project--section-title__hover-outline"></div>
    <div class="project--section-title__customize-shortcut">
        <button class="project--section-title__customize-button"
            data-target-section="buildpro_projects_title_section">Edit Title</button>
    </div>
    <script>
    (function() {
        var btn = document.querySelector('.project--section-title__customize-button');
        if (btn && window.parent && window.parent.wp && window.parent.wp.customize) {
            btn.addEventListener('click', function() {
                window.parent.wp.customize.section('buildpro_projects_title_section').focus();
            });
        }
    })();
    </script>
    <?php endif; ?>
    <?php if ($title !== '') : ?>
    <h2 class="project--section-title__title"><?php echo esc_html($title); ?></h2>
    <?php endif; ?>
    <?php if ($desc !== '') : ?>
    <p class="project--section-title__desc"><?php echo esc_html($desc); ?></p>
    <?php endif; ?>
</section>