<?php
if (!function_exists('buildpro_product_sections_get_map')) {
    function buildpro_product_sections_get_map()
    {
        return array(
            'product--section-title' => 'template/template-parts/page/product/section-title/index',
            'product--section-products' => 'template/template-parts/page/product/section-products/index',
        );
    }
}

if (!function_exists('buildpro_product_sections_get_default_order')) {
    function buildpro_product_sections_get_default_order()
    {
        return array_keys(buildpro_product_sections_get_map());
    }
}

if (!function_exists('buildpro_product_sections_get_labels')) {
    function buildpro_product_sections_get_labels()
    {
        return array(
            'product--section-title' => __('Title', 'buildpro'),
            'product--section-products' => __('List', 'buildpro'),
        );
    }
}

if (!function_exists('buildpro_product_sections_get_aliases')) {
    function buildpro_product_sections_get_aliases()
    {
        return array(
            'product-section-title' => 'product--section-title',
            'product-section-products' => 'product--section-products',
        );
    }
}

if (!function_exists('buildpro_product_sections_normalize_key')) {
    function buildpro_product_sections_normalize_key($key, $allowed)
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

        $aliases = function_exists('buildpro_product_sections_get_aliases') ? buildpro_product_sections_get_aliases() : array();
        if (isset($aliases[$key]) && isset($allowed[$aliases[$key]])) {
            return $aliases[$key];
        }

        return '';
    }
}

if (!function_exists('buildpro_product_sections_parse_order')) {
    function buildpro_product_sections_parse_order($raw)
    {
        $map = buildpro_product_sections_get_map();

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
                $normalized = buildpro_product_sections_normalize_key($part, $map);
                if ($normalized !== '' && !in_array($normalized, $order, true)) {
                    $order[] = $normalized;
                }
            }
        }

        $default = buildpro_product_sections_get_default_order();
        foreach ($default as $k) {
            if (!in_array($k, $order, true)) {
                $order[] = $k;
            }
        }

        return $order;
    }
}

if (!function_exists('buildpro_product_sections_get_order')) {
    function buildpro_product_sections_get_order()
    {
        $raw = get_theme_mod('buildpro_product_sections_order', '');
        $order = buildpro_product_sections_parse_order($raw);
        return apply_filters('buildpro_product_sections_order', $order);
    }
}

if (!function_exists('buildpro_render_product_sections')) {
    function buildpro_render_product_sections()
    {
        $map = buildpro_product_sections_get_map();
        $order = buildpro_product_sections_get_order();

        foreach ($order as $key) {
            if (isset($map[$key])) {
                get_template_part($map[$key]);
            }
        }
    }
}

if (!function_exists('buildpro_product_sections_order_sanitize')) {
    function buildpro_product_sections_order_sanitize($value)
    {
        $order = buildpro_product_sections_parse_order($value);
        return implode("\n", $order);
    }
}

if (!function_exists('buildpro_product_sections_order_active_callback')) {
    function buildpro_product_sections_order_active_callback()
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
            return false;
        }

        $tpl = get_page_template_slug($selected_id);
        if ($tpl === 'product-page.php' || $tpl === 'products-page.php') {
            return true;
        }

        return false;
    }
}

if (!function_exists('buildpro_product_sections_order_customize_register')) {
    function buildpro_product_sections_order_customize_register($wp_customize)
    {
        if (!($wp_customize instanceof WP_Customize_Manager)) {
            return;
        }

        if (!class_exists('BuildPro_Product_Sections_Order_Control') && class_exists('WP_Customize_Control')) {
            class BuildPro_Product_Sections_Order_Control extends WP_Customize_Control
            {
                public $type = 'buildpro_product_sections_order';

                public function render_content()
                {
                    $labels = function_exists('buildpro_product_sections_get_labels') ? buildpro_product_sections_get_labels() : array();
                    $map = function_exists('buildpro_product_sections_get_map') ? buildpro_product_sections_get_map() : array();
                    $order = function_exists('buildpro_product_sections_parse_order') ? buildpro_product_sections_parse_order($this->value()) : array_keys($map);

                    echo '<span class="customize-control-title">' . esc_html($this->label) . '</span>';
                    if (!empty($this->description)) {
                        echo '<p class="description">' . esc_html($this->description) . '</p>';
                    }

                    echo '<ul class="buildpro-product-sections-sortable">';
                    foreach ($order as $key) {
                        if (!isset($map[$key])) {
                            continue;
                        }
                        $text = isset($labels[$key]) ? $labels[$key] : $key;
                        echo '<li class="buildpro-product-sections-item button button-secondary" data-section-key="' . esc_attr($key) . '">';
                        echo esc_html($text) . ' (' . esc_html($key) . ')';
                        echo '</li>';
                    }
                    echo '</ul>';

                    echo '<input type="hidden" class="buildpro-product-sections-order-input" ';
                    $this->link();
                    echo ' value="' . esc_attr($this->value()) . '" />';
                }
            }
        }

        $section_id = 'buildpro_product_sections_order_section';
        $wp_customize->add_section($section_id, array(
            'title' => __('Product Sections Order', 'buildpro'),
            'priority' => 6,
            'active_callback' => 'buildpro_product_sections_order_active_callback',
        ));

        $setting_id = 'buildpro_product_sections_order';
        $wp_customize->add_setting($setting_id, array(
            'default' => implode("\n", buildpro_product_sections_get_default_order()),
            'type' => 'theme_mod',
            'transport' => 'refresh',
            'sanitize_callback' => 'buildpro_product_sections_order_sanitize',
        ));

        $wp_customize->add_control(new BuildPro_Product_Sections_Order_Control($wp_customize, $setting_id, array(
            'section' => $section_id,
            'label' => __('Sections order', 'buildpro'),
            'description' => __('Drag and drop to reorder sections on the Products page.', 'buildpro'),
        )));
    }
}

add_action('customize_register', 'buildpro_product_sections_order_customize_register', 20);

if (!function_exists('buildpro_product_sections_order_customize_controls_assets')) {
    function buildpro_product_sections_order_customize_controls_assets()
    {
        wp_enqueue_script('jquery-ui-sortable');

        $css = <<<'CSS'
.buildpro-product-sections-sortable{margin:10px 0 0;padding:0}
.buildpro-product-sections-sortable .buildpro-product-sections-item{display:block;width:100%;box-sizing:border-box;margin:0 0 6px;cursor:move;text-align:left}
CSS;
        wp_add_inline_style('customize-controls', $css);

        $js = <<<'JS'
(function($){
	function initBuildProProductSectionsSortable(container){
		var $container = $(container || document);
		$container.find('.buildpro-product-sections-sortable').each(function(){
			var $list = $(this);
			if ($list.data('buildpro-sortable-init')) return;
			$list.data('buildpro-sortable-init', true);

			$list.sortable({
				axis: 'y',
				tolerance: 'pointer',
				update: function(){
					var order = [];
					$list.find('.buildpro-product-sections-item').each(function(){
						var key = $(this).data('section-key');
						if (key) order.push(key);
					});
					var value = order.join("\n");
					var $input = $list.closest('.customize-control').find('input.buildpro-product-sections-order-input');
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

		$('.buildpro-product-sections-sortable').each(function(){
			var $list = $(this);
			var $items = $list.children('.buildpro-product-sections-item');
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

			$items.each(function(){
				var key = $(this).data('section-key');
				if (key && order.indexOf(key) === -1) {
					$list.append($(this));
				}
			});
		});
	}

	$(document).ready(function(){
		initBuildProProductSectionsSortable(document);

		if (window.wp && wp.customize) {
			wp.customize('buildpro_product_sections_order', function(setting){
				setting.bind(function(value){
					syncSortableList(value);
				});
			});
		}

		$(document).on('click', '.customize-section-title, .accordion-section-title', function(){
			window.setTimeout(function(){ initBuildProProductSectionsSortable(document); }, 0);
		});
	});
})(jQuery);
JS;

        wp_add_inline_script('jquery-ui-sortable', $js, 'after');
    }
}

add_action('customize_controls_enqueue_scripts', 'buildpro_product_sections_order_customize_controls_assets');

if (!function_exists('buildpro_product_sections_order_customize_preview_assets')) {
    function buildpro_product_sections_order_customize_preview_assets()
    {
        wp_enqueue_script('jquery-ui-sortable');

        wp_register_style('buildpro-product-sections-order-preview', false);
        wp_enqueue_style('buildpro-product-sections-order-preview');

        $css = <<<'CSS'
.buildpro-product-preview-sort-hint{outline:2px dashed rgba(0,0,0,.15);outline-offset:4px}
.buildpro-product-preview-sort-hint{cursor:move}
.buildpro-product-preview-sort-placeholder{background:rgba(0,0,0,.04);border:2px dashed rgba(0,0,0,.2);margin:0 0 12px;min-height:40px}
.buildpro-product-preview-sort-helper{opacity:.9;box-shadow:0 6px 20px rgba(0,0,0,.12)}
CSS;
        wp_add_inline_style('buildpro-product-sections-order-preview', $css);

        $js = <<<'JS'
(function($){
	var SECTION_KEYS = [
		'product--section-title',
		'product--section-products'
	];

	function findProductSections(){
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
			if (window.parent && window.parent.wp && window.parent.wp.customize) {
				window.parent.wp.customize('buildpro_product_sections_order', function(setting){
					setting.set(value);
				});
				return;
			}
			if (window.wp && wp.customize) {
				wp.customize('buildpro_product_sections_order', function(setting){
					setting.set(value);
				});
			}
		} catch (e) {}
	}

	function initSortable(){
		var $sections = findProductSections();
		if (!$sections.length) return;

		var parent = $sections.first().parent();
		if (!parent || !parent.length) return;

		var $siblings = parent.children().filter(function(){
			return this.tagName && this.tagName.toLowerCase() === 'section' && getKeyFromEl(this);
		});
		if (!$siblings.length) return;

		$siblings.addClass('buildpro-product-preview-sort-hint');

		if (parent.data('buildpro-product-preview-sortable')) {
			try { parent.sortable('refresh'); } catch (e) {}
			return;
		}
		parent.data('buildpro-product-preview-sortable', true);

		var itemsSelector = SECTION_KEYS.map(function(k){ return 'section.' + k; }).join(',');
		parent.sortable({
			items: itemsSelector,
			axis: 'y',
			tolerance: 'pointer',
			cancel: 'a,button,input,textarea,select,option,label',
			placeholder: 'buildpro-product-preview-sort-placeholder',
			forcePlaceholderSize: true,
			helper: function(e, item){
				return item.clone().addClass('buildpro-product-preview-sort-helper');
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

add_action('customize_preview_init', 'buildpro_product_sections_order_customize_preview_assets', 30);
