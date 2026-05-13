<?php
$logo_id = get_theme_mod('header_logo', 0);

$buildpro_is_customize = is_customize_preview();
$buildpro_cart_url = function_exists('buildpro_get_cart_page_url')
    ? buildpro_get_cart_page_url()
    : (function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/'));
$buildpro_cart_count = function_exists('WC') && WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;
?>

<header id="masthead" class="site-header">
    <?php if ($buildpro_is_customize): ?>
        <script>
            (function() {
                try {
                    window.headerData = window.headerData || {};
                } catch (e) {}
            })();
        </script>
    <?php endif; ?>
    <div class="site-branding">
        <div class="header-logo-part">
            <div class="header-logo-container">
                <?php if ($buildpro_is_customize): ?>
                    <div class="header__hover-outline" data-header-outline="logo"></div>
                <?php endif; ?>
                <?php if ($logo_id || $buildpro_is_customize): ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="header-logo">
                        <?= $logo_id ? wp_get_attachment_image($logo_id, 'full', false, array('class' => '')) : '' ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="header-navigation-part">
            <div class="header-nav-main">
                <?php if ($buildpro_is_customize): ?>
                    <div class="header__hover-outline" data-header-outline="menu"></div>
                <?php endif; ?>
                <nav id="site-navigation" class="main-navigation">
                    <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'menu-1',
                            'menu_id'        => 'primary-menu',
                        )
                    );
                    ?>
                </nav><!-- #site-navigation -->

            </div>

            <div class="header-cart-wrap">
                <a href="<?php echo esc_url($buildpro_cart_url); ?>" class="header-nav-button-cart">
                    <img class="header-nav-button-cart__icon"
                        src="<?php echo esc_url(get_theme_file_uri('/assets/images/icon/icon-cart.png')); ?>"
                        alt="<?php esc_attr_e('Cart', 'buildpro'); ?>">
                    <span
                        class="header-cart-count<?php echo $buildpro_cart_count === 0 ? ' header-cart-count--hidden' : ''; ?>"><?php echo $buildpro_cart_count; ?></span>
                </a>
                <?php if (class_exists('WooCommerce')) : ?>
                    <div class="header-cart-dropdown">
                        <?php get_template_part('template/template-parts/header/cart/index'); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <button class="mobile-menu-toggle" aria-expanded="false" aria-controls="mobile-sidebar" aria-label="<?php esc_attr_e('Menu', 'buildpro'); ?>">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <div id="mobile-sidebar" class="mobile-sidebar">
        <div class="mobile-sidebar-header">
            <button class="mobile-sidebar-close" aria-label="<?php esc_attr_e('Close Menu', 'buildpro'); ?>">✕</button>
        </div>
        <div class="header-mobile-nav">
            <?php if ($buildpro_is_customize): ?>
                <div class="header__hover-outline" data-header-outline="mobile-menu"></div>
            <?php endif; ?>
            <nav class="mobile-navigation">
                <?php
                wp_nav_menu(
                    array(
                        'theme_location' => 'menu-1',
                        'menu_id'        => 'mobile-primary-menu',
                    )
                );
                ?>
            </nav>
        </div>
        <div class="mobile-sidebar-actions">
            <div class="mobile-cart-wrap">
                <a href="<?php echo esc_url($buildpro_cart_url); ?>" class="mobile-cart-button">
                    <img class="mobile-cart-icon"
                        src="<?php echo esc_url(get_theme_file_uri('/assets/images/icon/icon-cart.png')); ?>"
                        alt="<?php esc_attr_e('Cart', 'buildpro'); ?>">
                    <span><?php esc_html_e('View Cart', 'buildpro'); ?></span>
                    <span
                        class="mobile-cart-count<?php echo $buildpro_cart_count === 0 ? ' mobile-cart-count--hidden' : ''; ?>"><?php echo $buildpro_cart_count; ?></span>
                </a>
            </div>
        </div>
    </div>
    <div class="mobile-sidebar-backdrop"></div>
</header><!-- #masthead -->
<?php if ($buildpro_is_customize) : ?>
    <script>
        (function() {
            var api = window.parent && window.parent.wp && window.parent.wp.customize;
            var currentPageId = <?php echo (int) get_queried_object_id(); ?>;
            var cfg = {
                frontId: <?php echo (int) get_option('page_on_front'); ?>,
                homeIds: <?php
                            $home_pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'home-page.php', 'number' => 50));
                            $home_ids = array();
                            foreach ($home_pages as $hp) {
                                $home_ids[] = (int) $hp->ID;
                            }
                            echo wp_json_encode($home_ids);
                            ?>
            };

            function appendCS(url) {
                try {
                    var uuid = api && api.settings && api.settings.changeset && api.settings.changeset.uuid;
                    if (!uuid) return url;
                    var u = new URL(url, window.location.origin);
                    if (!u.searchParams.get('customize_changeset_uuid')) {
                        u.searchParams.set('customize_changeset_uuid', uuid);
                    }
                    return u.toString();
                } catch (e) {
                    return url;
                }
            }

            function isHome(id) {
                id = parseInt(id || 0, 10);
                if (!id) return false;
                return (cfg.frontId && id === cfg.frontId) || (Array.isArray(cfg.homeIds) && cfg.homeIds.indexOf(id) > -1);
            }

            function focusSectionById(id) {
                try {
                    if (!api || !api.section) return;
                    var target = isHome(id) ? 'buildpro_banner_section' : 'buildpro_header_section';
                    var s = api.section(target);
                    if (s && s.focus) {
                        s.focus();
                    }
                } catch (e) {}
            }

            function bindHeaderOutlines() {
                var nodes = document.querySelectorAll('.header__hover-outline');
                for (var i = 0; i < nodes.length; i++) {
                    nodes[i].addEventListener('click', function() {
                        try {
                            if (api) {
                                var setting = api('buildpro_preview_page_id');
                                if (setting && typeof setting.set === 'function') {
                                    setting.set(currentPageId);
                                }
                                focusSectionById(currentPageId);
                            }
                        } catch (e) {}
                    });
                }
            }

            function bindMenuLinks() {
                var links = document.querySelectorAll('.main-navigation a, .mobile-navigation a');
                for (var j = 0; j < links.length; j++) {
                    links[j].addEventListener('click', function(ev) {
                        try {
                            if (!api) return;
                            var href = this.getAttribute('href') || '';
                            var objId = this.getAttribute('data-object-id') || '0';
                            objId = parseInt(objId || 0, 10);
                            if (href) {
                                var url = appendCS(href);
                                var did = false;
                                if (api.previewer) {
                                    if (api.previewer.previewUrl && typeof api.previewer.previewUrl.set ===
                                        'function') {
                                        api.previewer.previewUrl.set(url);
                                        did = true;
                                    } else if (typeof api.previewer.previewUrl === 'function') {
                                        api.previewer.previewUrl(url);
                                        did = true;
                                    } else if (api.previewer.url && typeof api.previewer.url.set === 'function') {
                                        api.previewer.url.set(url);
                                        did = true;
                                    }
                                    if (!did) {
                                        var frame = window.parent && window.parent.document && window.parent
                                            .document.querySelector('#customize-preview iframe');
                                        if (frame) {
                                            frame.src = url;
                                            did = true;
                                        }
                                    }
                                    if (did) {
                                        setTimeout(function() {
                                            try {
                                                if (api.previewer.refresh) {
                                                    api.previewer.refresh();
                                                }
                                            } catch (e) {}
                                        }, 100);
                                    }
                                }
                                if (objId > 0) {
                                    var setting = api('buildpro_preview_page_id');
                                    if (setting && typeof setting.set === 'function') {
                                        setting.set(objId);
                                    }
                                }
                                ev.preventDefault();
                            }
                        } catch (e) {}
                    });
                }
            }

            function init() {
                bindHeaderOutlines();
                bindMenuLinks();
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
        })();
    </script>
<?php endif; ?>