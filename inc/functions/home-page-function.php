<?php
function buildpro_find_page_by_templates_or_slugs($templates, $slugs)
{
    foreach ($templates as $tpl) {
        $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => $tpl, 'number' => 1));
        if (!empty($pages)) {
            return (int)$pages[0]->ID;
        }
    }
    foreach ($slugs as $slug) {
        $p = get_page_by_path($slug, OBJECT, 'page');
        if ($p) {
            return (int)$p->ID;
        }
    }
    return 0;
}
function buildpro_ensure_ordered_primary_menu()
{
    $location = 'menu-1';
    $menu_name = 'Primary Menu';
    $menu = wp_get_nav_menu_object($menu_name);
    if (!$menu) {
        $menu_id = wp_create_nav_menu($menu_name);
    } else {
        $menu_id = (int) $menu->term_id;
    }
    $locs = get_nav_menu_locations();
    if (!isset($locs[$location]) || (int) $locs[$location] !== $menu_id) {
        $locs[$location] = $menu_id;
        set_theme_mod('nav_menu_locations', $locs);
    }
    $targets = array(
        array('templates' => array('home-page.php'), 'slugs' => array('home', 'trang-chu', 'homepage')),
        array('templates' => array('project-page.php', 'projects-page.php'), 'slugs' => array('projects', 'project', 'projects')),
        array('templates' => array('product-page.php', 'products-page.php'), 'slugs' => array('products', 'product', 'products')),
        array('templates' => array('blogs-page.php', 'blog-page.php'), 'slugs' => array('blogs', 'blog', 'blog')),
        array('templates' => array('about-page.php', 'about-us-page.php'), 'slugs' => array('about', 'about-us', 'about-us')),
    );
    $page_ids = array();
    foreach ($targets as $t) {
        $pid = buildpro_find_page_by_templates_or_slugs($t['templates'], $t['slugs']);
        if ($pid > 0 && !in_array($pid, $page_ids, true)) {
            $page_ids[] = $pid;
        }
    }

    // Defer final bootstrap until target pages exist (e.g. after demo import).
    if (empty($page_ids)) {
        return false;
    }

    $existing = wp_get_nav_menu_items($menu_id);
    if (is_array($existing)) {
        foreach ($existing as $it) {
            if (!empty($it->ID)) {
                wp_delete_post((int) $it->ID, true);
            }
        }
    }

    $position = 1;
    foreach ($page_ids as $pid) {
        wp_update_nav_menu_item($menu_id, 0, array(
            'menu-item-object-id' => $pid,
            'menu-item-object' => 'page',
            'menu-item-type' => 'post_type',
            'menu-item-status' => 'publish',
            'menu-item-position' => $position,
        ));
        $position++;
    }

    return true;
}
function buildpro_bootstrap_primary_menu_once()
{
    if (get_option('buildpro_primary_menu_created') === '1') {
        return;
    }

    $created = buildpro_ensure_ordered_primary_menu();
    if ($created) {
        update_option('buildpro_primary_menu_created', '1');
    }
}

add_action('after_switch_theme', 'buildpro_bootstrap_primary_menu_once', 20);
add_action('init', 'buildpro_bootstrap_primary_menu_once', 30);

if (!function_exists('buildpro_home_sections_get_map')) {
    function buildpro_home_sections_get_map()
    {
        return array(
            'section-banner' => 'template/template-parts/page/home/section-banner/index',
            'section-product' => 'template/template-parts/page/home/section-products/index',
            'section-portfolio' => 'template/template-parts/page/home/section-projects/index',
            'section-post' => 'template/template-parts/page/home/section-post/index',
        );
    }
}

if (!function_exists('buildpro_home_sections_get_default_order')) {
    function buildpro_home_sections_get_default_order()
    {
        return array_keys(buildpro_home_sections_get_map());
    }
}

if (!function_exists('buildpro_home_sections_get_labels')) {
    function buildpro_home_sections_get_labels()
    {
        return array(
            'section-banner' => __('Banner', 'buildpro'),
            'section-product' => __('Products', 'buildpro'),
            'section-portfolio' => __('Projects', 'buildpro'),
            'section-post' => __('Posts', 'buildpro'),
        );
    }
}

if (!function_exists('buildpro_home_sections_get_aliases')) {
    function buildpro_home_sections_get_aliases()
    {
        return array(
            // Back-compat for earlier key names.
            'section-products' => 'section-product',
            'section-projects' => 'section-portfolio',
        );
    }
}

if (!function_exists('buildpro_home_sections_normalize_key')) {
    function buildpro_home_sections_normalize_key($key, $allowed)
    {
        if (!is_string($key)) {
            return '';
        }

        $key = trim($key);
        if ($key === '') {
            return '';
        }

        if (isset($allowed[$key])) {
            return $key;
        }

        $aliases = function_exists('buildpro_home_sections_get_aliases') ? buildpro_home_sections_get_aliases() : array();
        if (isset($aliases[$key]) && isset($allowed[$aliases[$key]])) {
            return $aliases[$key];
        }

        $prefixed = 'section-' . $key;
        if (isset($allowed[$prefixed])) {
            return $prefixed;
        }

        if (isset($aliases[$prefixed]) && isset($allowed[$aliases[$prefixed]])) {
            return $aliases[$prefixed];
        }

        return '';
    }
}

if (!function_exists('buildpro_home_sections_parse_order')) {
    function buildpro_home_sections_parse_order($raw)
    {
        $map = buildpro_home_sections_get_map();

        if (is_array($raw)) {
            $parts = $raw;
        } else {
            $raw = is_string($raw) ? $raw : '';
            $normalized = str_replace(array("\r\n", "\r", "\n", ';', '|'), ',', $raw);
            $parts = explode(',', $normalized);
        }

        $order = array();
        if (is_array($parts)) {
            foreach ($parts as $part) {
                $normalized = buildpro_home_sections_normalize_key($part, $map);
                if ($normalized !== '' && !in_array($normalized, $order, true)) {
                    $order[] = $normalized;
                }
            }
        }

        $default = buildpro_home_sections_get_default_order();
        foreach ($default as $k) {
            if (!in_array($k, $order, true)) {
                $order[] = $k;
            }
        }

        return $order;
    }
}

if (!function_exists('buildpro_home_sections_get_order')) {
    function buildpro_home_sections_get_order()
    {
        $raw = get_theme_mod('buildpro_home_sections_order', '');
        $order = buildpro_home_sections_parse_order($raw);
        return apply_filters('buildpro_home_sections_order', $order);
    }
}

if (!function_exists('buildpro_render_home_sections')) {
    function buildpro_render_home_sections()
    {
        $map = buildpro_home_sections_get_map();
        $order = buildpro_home_sections_get_order();

        foreach ($order as $key) {
            if (isset($map[$key])) {
                get_template_part($map[$key]);
            }
        }
    }
}

if (!function_exists('buildpro_home_sections_order_sanitize')) {
    function buildpro_home_sections_order_sanitize($value)
    {
        $order = buildpro_home_sections_parse_order($value);
        return implode("\n", $order);
    }
}

if (!function_exists('buildpro_home_sections_order_active_callback')) {
    function buildpro_home_sections_order_active_callback()
    {
        $selected_id = 0;

        if (function_exists('wp_get_current_user')) {
            global $wp_customize;
            if ($wp_customize && $wp_customize instanceof WP_Customize_Manager) {
                $setting = $wp_customize->get_setting('buildpro_preview_page_id');
                if ($setting) {
                    $selected_id = absint($setting->value());
                }
            }
        }

        if ($selected_id <= 0) {
            $selected_id = (int) get_option('page_on_front');
        }

        if ($selected_id > 0) {
            $tpl = get_page_template_slug($selected_id);
            if ($tpl === 'home-page.php') {
                return true;
            }

            if ((int) get_option('page_on_front') === $selected_id) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('buildpro_home_sections_order_customize_register')) {
    function buildpro_home_sections_order_customize_register($wp_customize)
    {
        if (!($wp_customize instanceof WP_Customize_Manager)) {
            return;
        }

        if (!class_exists('BuildPro_Home_Sections_Order_Control') && class_exists('WP_Customize_Control')) {
            class BuildPro_Home_Sections_Order_Control extends WP_Customize_Control
            {
                public $type = 'buildpro_home_sections_order';

                public function render_content()
                {
                    $labels = function_exists('buildpro_home_sections_get_labels') ? buildpro_home_sections_get_labels() : array();
                    $map = function_exists('buildpro_home_sections_get_map') ? buildpro_home_sections_get_map() : array();
                    $order = function_exists('buildpro_home_sections_parse_order') ? buildpro_home_sections_parse_order($this->value()) : array_keys($map);

                    echo '<span class="customize-control-title">' . esc_html($this->label) . '</span>';
                    if (!empty($this->description)) {
                        echo '<p class="description">' . esc_html($this->description) . '</p>';
                    }

                    echo '<ul class="buildpro-home-sections-sortable">';
                    foreach ($order as $key) {
                        if (!isset($map[$key])) {
                            continue;
                        }
                        $text = isset($labels[$key]) ? $labels[$key] : $key;
                        echo '<li class="buildpro-home-sections-item button button-secondary" data-section-key="' . esc_attr($key) . '">';
                        echo esc_html($text) . ' (' . esc_html($key) . ')';
                        echo '</li>';
                    }
                    echo '</ul>';

                    echo '<input type="hidden" class="buildpro-home-sections-order-input" ';
                    $this->link();
                    echo ' value="' . esc_attr($this->value()) . '" />';
                }
            }
        }

        $section_id = 'buildpro_home_sections_order_section';
        $wp_customize->add_section($section_id, array(
            'title' => __('Home Sections Order', 'buildpro'),
            'priority' => 5,
            'active_callback' => 'buildpro_home_sections_order_active_callback',
        ));

        $setting_id = 'buildpro_home_sections_order';
        $wp_customize->add_setting($setting_id, array(
            'default' => implode("\n", buildpro_home_sections_get_default_order()),
            'type' => 'theme_mod',
            'transport' => 'refresh',
            'sanitize_callback' => 'buildpro_home_sections_order_sanitize',
        ));

        $wp_customize->add_control(new BuildPro_Home_Sections_Order_Control($wp_customize, $setting_id, array(
            'section' => $section_id,
            'label' => __('Sections order', 'buildpro'),
            'description' => __('Drag and drop to reorder sections on the Home page.', 'buildpro'),
        )));
    }
}

add_action('customize_register', 'buildpro_home_sections_order_customize_register', 20);

if (!function_exists('buildpro_home_sections_order_customize_controls_assets')) {
    function buildpro_home_sections_order_customize_controls_assets()
    {
        wp_enqueue_script('jquery-ui-sortable');

        $css = <<<'CSS'
.buildpro-home-sections-sortable{margin:10px 0 0;padding:0}
.buildpro-home-sections-sortable .buildpro-home-sections-item{display:block;width:100%;box-sizing:border-box;margin:0 0 6px;cursor:move;text-align:left}
CSS;
        wp_add_inline_style('customize-controls', $css);

        $js = <<<'JS'
(function($){
    function initBuildProHomeSectionsSortable(container){
        var $container = $(container || document);
        $container.find('.buildpro-home-sections-sortable').each(function(){
            var $list = $(this);
            if ($list.data('buildpro-sortable-init')) return;
            $list.data('buildpro-sortable-init', true);

            $list.sortable({
                axis: 'y',
                tolerance: 'pointer',
                update: function(){
                    var order = [];
                    $list.find('.buildpro-home-sections-item').each(function(){
                        var key = $(this).data('section-key');
                        if (key) order.push(key);
                    });
                    var value = order.join("\n");
                    var $input = $list.closest('.customize-control').find('input.buildpro-home-sections-order-input');
                    if ($input.length) {
                        $input.val(value).trigger('change');
                    }
                }
            });
        });
    }

    function parseOrderValue(value){
        if (typeof value !== 'string') return [];
        var parts = value.split(/[\r\n,;|]+/);
        var order = [];
        for (var i = 0; i < parts.length; i++) {
            var key = $.trim(parts[i]);
            if (key && order.indexOf(key) === -1) order.push(key);
        }
        return order;
    }

    function syncSortableList(value){
        var order = parseOrderValue(value);
        if (!order.length) return;

        $('.buildpro-home-sections-sortable').each(function(){
            var $list = $(this);
            var $items = $list.children('.buildpro-home-sections-item');
            if (!$items.length) return;

            var byKey = {};
            $items.each(function(){
                var key = $(this).data('section-key');
                if (key) byKey[key] = $(this);
            });

            for (var i = 0; i < order.length; i++) {
                var k = order[i];
                if (byKey[k]) {
                    $list.append(byKey[k]);
                }
            }

            // Append any remaining items that were not in the saved order.
            $items.each(function(){
                var key = $(this).data('section-key');
                if (key && order.indexOf(key) === -1) {
                    $list.append($(this));
                }
            });
        });
    }

    $(document).ready(function(){
        initBuildProHomeSectionsSortable(document);

        if (window.wp && wp.customize) {
            wp.customize('buildpro_home_sections_order', function(setting){
                setting.bind(function(value){
                    syncSortableList(value);
                });
            });
        }

        // Customizer can re-render controls; re-init when expanded.
        $(document).on('click', '.customize-section-title, .accordion-section-title', function(){
            window.setTimeout(function(){ initBuildProHomeSectionsSortable(document); }, 0);
        });
    });
})(jQuery);
JS;

        wp_add_inline_script('jquery-ui-sortable', $js, 'after');
    }
}

add_action('customize_controls_enqueue_scripts', 'buildpro_home_sections_order_customize_controls_assets');

if (!function_exists('buildpro_home_sections_order_customize_preview_assets')) {
    function buildpro_home_sections_order_customize_preview_assets()
    {
        wp_enqueue_script('jquery-ui-sortable');

        wp_register_style('buildpro-home-sections-order-preview', false);
        wp_enqueue_style('buildpro-home-sections-order-preview');

        $css = <<<'CSS'
    .buildpro-home-preview-sort-hint{outline:2px dashed rgba(0,0,0,.15);outline-offset:4px}
    .buildpro-home-preview-sort-hint{cursor:move}
    .buildpro-home-preview-sort-placeholder{background:rgba(0,0,0,.04);border:2px dashed rgba(0,0,0,.2);margin:0 0 12px;min-height:40px}
    .buildpro-home-preview-sort-helper{opacity:.9;box-shadow:0 6px 20px rgba(0,0,0,.12)}
CSS;
        wp_add_inline_style('buildpro-home-sections-order-preview', $css);

        $js = <<<'JS'
(function($){
    var SECTION_KEYS = [
        'section-banner',
        'section-product',
        'section-portfolio',
        'section-post'
    ];

    function findHomeSections(){
        var selectors = SECTION_KEYS.map(function(k){ return 'section.' + k; }).join(',');
        return $(selectors);
    }

    function getKeyFromEl(el){
        if (!el || !el.classList) return '';
        for (var i = 0; i < SECTION_KEYS.length; i++) {
            if (el.classList.contains(SECTION_KEYS[i])) return SECTION_KEYS[i];
        }
        return '';
    }

    function setCustomizerValue(value){
        try {
            // Prefer setting in the parent Customizer window so it becomes "dirty" and is saved on Publish.
            if (window.parent && window.parent.wp && window.parent.wp.customize) {
                window.parent.wp.customize('buildpro_home_sections_order', function(setting){
                    setting.set(value);
                });
                return;
            }
            if (window.wp && wp.customize) {
                wp.customize('buildpro_home_sections_order', function(setting){
                    setting.set(value);
                });
            }
        } catch (e) {}
    }

    function initSortable(){
        var $sections = findHomeSections();
        if (!$sections.length) return;

        // Require all sections to share a common parent for sortable.
        var parent = $sections.first().parent();
        if (!parent || !parent.length) return;

        var $siblings = parent.children().filter(function(){
            return this.tagName && this.tagName.toLowerCase() === 'section' && getKeyFromEl(this);
        });
        if (!$siblings.length) return;

        $siblings.addClass('buildpro-home-preview-sort-hint');

        if (parent.data('buildpro-home-preview-sortable')) {
            try { parent.sortable('refresh'); } catch (e) {}
            return;
        }
        parent.data('buildpro-home-preview-sortable', true);

        var itemsSelector = SECTION_KEYS.map(function(k){ return 'section.' + k; }).join(',');
        parent.sortable({
            items: itemsSelector,
            axis: 'y',
            tolerance: 'pointer',
            cancel: 'a,button,input,textarea,select,option,label',
            placeholder: 'buildpro-home-preview-sort-placeholder',
            forcePlaceholderSize: true,
            helper: function(e, item){
                return item.clone().addClass('buildpro-home-preview-sort-helper');
            },
            opacity: 0.85,
            distance: 5,
            revert: 150,
            scroll: true,
            scrollSensitivity: 80,
            scrollSpeed: 20,
            start: function(e, ui){
                ui.placeholder.height(ui.item.outerHeight());
            },
            update: function(){
                var order = [];
                parent.children('section').each(function(){
                    var key = getKeyFromEl(this);
                    if (key) order.push(key);
                });
                if (order.length) {
                    setCustomizerValue(order.join("\n"));
                }
            }
        });
    }

    $(function(){
        initSortable();

        // After selective refresh / partial renders.
        if (window.wp && wp.customize && wp.customize.selectiveRefresh) {
            wp.customize.selectiveRefresh.bind('partial-content-rendered', function(){
                window.setTimeout(initSortable, 0);
            });
        }
    });
})(jQuery);
JS;

        wp_add_inline_script('jquery-ui-sortable', $js, 'after');
    }
}

add_action('customize_preview_init', 'buildpro_home_sections_order_customize_preview_assets', 30);
