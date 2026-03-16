<?php

/**
 * Plugin Activation - TGM Plugin Activation Integration
 *
 * Automatically install and activate required plugins when theme is activated
 *
 * @package BuildPro
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Include TGM Plugin Activation class
 */
require_once get_template_directory() . '/import/TGM-Plugin-Activation-develop/class-tgm-plugin-activation.php';

add_action('tgmpa_register', 'buildpro_register_required_plugins');

/**
 * Register required plugins for BuildPro theme
 *
 * Auto install: Flamingo, Contact Form 7, Classic Editor, WooCommerce
 */
function buildpro_register_required_plugins()
{

    /*
     * Array of required plugins
     * Plugins will be downloaded from WordPress.org repository
     */
    $plugins = array(

        // Contact Form 7 - Contact form plugin
        array(
            'name'     => 'Contact Form 7',
            'slug'     => 'contact-form-7',
            'required' => true,
        ),

        // Flamingo - Store messages from Contact Form 7
        array(
            'name'     => 'Flamingo',
            'slug'     => 'flamingo',
            'required' => true,
        ),

        // Classic Editor - WordPress classic editor
        array(
            'name'     => 'Classic Editor',
            'slug'     => 'classic-editor',
            'required' => true,
        ),

        // WooCommerce - E-commerce plugin
        array(
            'name'     => 'WooCommerce',
            'slug'     => 'woocommerce',
            'required' => true,
        ),

    );

    /*
     * TGM Plugin Activation configuration
     */
    $config = array(
        'id'           => 'buildpro',                    // Unique ID for theme
        'default_path' => '',                            // Default path for bundled plugins
        'menu'         => 'tgmpa-install-plugins',       // Menu slug
        'parent_slug'  => 'themes.php',                  // Parent menu slug
        'capability'   => 'edit_theme_options',          // Capability needed to view plugin install page
        'has_notices'  => true,                          // Show admin notices
        'dismissable'  => true,                          // Allow user to dismiss notices
        'dismiss_msg'  => '',                            // Message when dismissable = false
        'is_automatic' => false,                         // Automatically activate plugins after installation
        'message'      => '',                            // Message displayed at top of plugins table

        'strings'      => array(
            'page_title'                      => __('Install Required Plugins', 'buildpro'),
            'menu_title'                      => __('Install Plugins', 'buildpro'),
            'installing'                      => __('Installing Plugin: %s', 'buildpro'),
            'updating'                        => __('Updating Plugin: %s', 'buildpro'),
            'oops'                            => __('Something went wrong with the plugin API.', 'buildpro'),
            'notice_can_install_required'     => _n_noop(
                'This theme requires the following plugin: %1$s.',
                'This theme requires the following plugins: %1$s.',
                'buildpro'
            ),
            'notice_can_install_recommended'  => _n_noop(
                'This theme recommends the following plugin: %1$s.',
                'This theme recommends the following plugins: %1$s.',
                'buildpro'
            ),
            'notice_ask_to_update'            => _n_noop(
                'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.',
                'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.',
                'buildpro'
            ),
            'notice_ask_to_update_maybe'      => _n_noop(
                'There is an update available for: %1$s.',
                'There are updates available for the following plugins: %1$s.',
                'buildpro'
            ),
            'notice_can_activate_required'    => _n_noop(
                'The following required plugin is currently inactive: %1$s.',
                'The following required plugins are currently inactive: %1$s.',
                'buildpro'
            ),
            'notice_can_activate_recommended' => _n_noop(
                'The following recommended plugin is currently inactive: %1$s.',
                'The following recommended plugins are currently inactive: %1$s.',
                'buildpro'
            ),
            'install_link'                    => _n_noop(
                'Begin installing plugin',
                'Begin installing plugins',
                'buildpro'
            ),
            'update_link'                     => _n_noop(
                'Begin updating plugin',
                'Begin updating plugins',
                'buildpro'
            ),
            'activate_link'                   => _n_noop(
                'Begin activating plugin',
                'Begin activating plugins',
                'buildpro'
            ),
            'return'                          => __('Return to Required Plugins Installer', 'buildpro'),
            'plugin_activated'                => __('Plugin activated successfully.', 'buildpro'),
            'activated_successfully'          => __('The following plugin was activated successfully:', 'buildpro'),
            'plugin_already_active'           => __('No action taken. Plugin %1$s was already active.', 'buildpro'),
            'plugin_needs_higher_version'     => __('Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.', 'buildpro'),
            'complete'                        => __('All plugins installed and activated successfully. %1$s', 'buildpro'),
            'dismiss'                         => __('Dismiss this notice', 'buildpro'),
            'notice_cannot_install_activate'  => __('There are one or more required or recommended plugins to install, update or activate.', 'buildpro'),
            'contact_admin'                   => __('Please contact the administrator of this site for help.', 'buildpro'),
            'nag_type'                        => '',
        ),
    );

    tgmpa($plugins, $config);
}