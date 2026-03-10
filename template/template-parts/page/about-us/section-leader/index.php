<?php
$pid = get_queried_object_id();
$use_mod = is_customize_preview();
$enabled = $use_mod ? get_theme_mod('buildpro_about_leader_enabled', 1) : get_post_meta($pid, 'buildpro_about_leader_enabled', true);
$enabled = $enabled === '' ? 1 : (int) $enabled;
$title = $use_mod ? get_theme_mod('buildpro_about_leader_title', '') : get_post_meta($pid, 'buildpro_about_leader_title', true);
$text = $use_mod ? get_theme_mod('buildpro_about_leader_text', '') : get_post_meta($pid, 'buildpro_about_leader_text', true);
$executives = $use_mod ? get_theme_mod('buildpro_about_leader_executives', '') : get_post_meta($pid, 'buildpro_about_leader_executives', true);
$workforce = $use_mod ? get_theme_mod('buildpro_about_leader_workforce', '') : get_post_meta($pid, 'buildpro_about_leader_workforce', true);
$items = $use_mod ? get_theme_mod('buildpro_about_leader_items', array()) : get_post_meta($pid, 'buildpro_about_leader_items', true);
$items = is_array($items) ? $items : array();
if ($enabled && (!empty($items) || is_customize_preview())) :
?>
    <section class="about-leader" <?php echo empty($items) ? ' data-auto="1"' : ''; ?>>
        <?php if (is_customize_preview()) : ?>
            <div class="leader__hover-outline" aria-hidden="true"></div>
        <?php endif; ?>
        <div class="about-leader__inner">
            <div class="about-leader__header">
                <div class="about-leader__header-left">
                    <?php if ($title !== '') : ?>
                        <h2 class="about-leader__title"><?php echo esc_html($title); ?></h2>
                    <?php endif; ?>
                    <?php if ($text !== '') : ?>
                        <p class="about-leader__text"><?php echo esc_html($text); ?></p>
                    <?php endif; ?>
                </div>
                <div class="about-leader__stats">
                    <?php if ($executives !== '') : ?>
                        <div class="about-leader__stat">
                            <div class="about-leader__stat-value"><?php echo esc_html($executives); ?></div>
                            <div class="about-leader__stat-label">Core Executives</div>
                        </div>
                    <?php endif; ?>
                    <?php if ($workforce !== '') : ?>
                        <div class="about-leader__stat">
                            <div class="about-leader__stat-value"><?php echo esc_html($workforce); ?></div>
                            <div class="about-leader__stat-label">Total Workforce</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="about-leader__grid">
                <?php
                $display = array();
                if (!empty($items)) {
                    foreach ($items as $it) {
                        $icon_id = isset($it['icon_id']) ? (int)$it['icon_id'] : 0;
                        $icon_url = $icon_id ? wp_get_attachment_image_url($icon_id, 'large') : (isset($it['icon_url']) ? (string)$it['icon_url'] : '');
                        $it_name = isset($it['name']) ? (string)$it['name'] : '';
                        $it_position = isset($it['position']) ? (string)$it['position'] : '';
                        $it_description = isset($it['description']) ? (string)$it['description'] : '';
                        $it_url = isset($it['url']) ? (string)$it['url'] : '';
                        if ($icon_url || $it_name !== '' || $it_position !== '' || $it_description !== '' || $it_url !== '') {
                            $display[] = array(
                                'icon_id' => $icon_id,
                                'icon_url' => $icon_url,
                                'name' => $it_name,
                                'position' => $it_position,
                                'description' => $it_description,
                                'url' => $it_url,
                            );
                        }
                        if (count($display) >= 3) {
                            break;
                        }
                    }
                }
                ?>
                <?php if (!empty($display)) : ?>
                    <?php foreach ($display as $it) :
                        $icon_url = $it['icon_url'];
                        $it_name = $it['name'];
                        $it_position = $it['position'];
                        $it_description = $it['description'];
                        $it_url = $it['url'];
                    ?>
                        <div class="about-leader__card">
                            <div class="about-leader__avatar-wrap">
                                <div class="about-leader__avatar-ring"></div>
                                <?php if ($icon_url) : ?>
                                    <img class="about-leader__avatar" src="<?php echo esc_url($icon_url); ?>" alt="">
                                <?php else : ?>
                                    <div class="about-leader__avatar placeholder"></div>
                                <?php endif; ?>
                            </div>
                            <?php if ($it_position !== '') : ?>
                                <div class="about-leader__badge"><?php echo esc_html($it_position); ?></div>
                            <?php endif; ?>
                            <?php if ($it_name !== '') : ?>
                                <h3 class="about-leader__name"><?php echo esc_html($it_name); ?></h3>
                            <?php endif; ?>
                            <?php if ($it_description !== '') : ?>
                                <div class="about-leader__role"><?php echo esc_html($it_description); ?></div>
                            <?php endif; ?>
                            <?php if ($it_url !== '') : ?>
                                <a class="about-leader__link" href="<?php echo esc_url($it_url); ?>">
                                    <span>View Profile</span>
                                    <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>