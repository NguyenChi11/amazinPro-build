<?php
$page_id = get_queried_object_id();
$title = get_post_meta($page_id, 'products_title', true);
$desc = get_post_meta($page_id, 'products_description', true);

if (is_customize_preview()) {
    $data = get_theme_mod('buildpro_products_title_data', array());
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
            $mod_title = get_theme_mod('products_title', '');
            if ($mod_title !== '') {
                $title = $mod_title;
            }
        }

        if (isset($data['description']) && $data['description'] !== '') {
            $desc = $data['description'];
        } else {
            $mod_desc = get_theme_mod('products_description', '');
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
<section class="product--section-title" data-aos="fade-up">
    <?php if (is_customize_preview()) : ?>
        <div class="product--section-title__hover-outline"></div>
        <div class="product--section-title__customize-shortcut">
            <button class="product--section-title__customize-button"
                data-target-section="buildpro_products_title_section"><?php esc_html_e('Edit Title', 'buildpro'); ?></button>
        </div>
        <script>
            (function() {
                var btn = document.querySelector('.product--section-title__customize-button');
                if (btn && window.parent && window.parent.wp && window.parent.wp.customize) {
                    btn.addEventListener('click', function() {
                        window.parent.wp.customize.section('buildpro_products_title_section').focus();
                    });
                }
            })();
        </script>
    <?php endif; ?>

    <?php if ($title !== '') : ?>
        <h2 class="product--section-title__title"><?php echo esc_html($title); ?></h2>
    <?php endif; ?>

    <?php if ($desc !== '') : ?>
        <p class="product--section-title__desc"><?php echo esc_html($desc); ?></p>
    <?php endif; ?>
</section>