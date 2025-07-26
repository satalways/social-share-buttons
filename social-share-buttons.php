<?php
/**
 * Plugin Name: Social Share Buttons
 * Plugin URI: https://github.com/satalways/social-share-buttons
 * Description: A simple and customizable social sharing buttons plugin for WordPress.
 * Version: 1.0.0
 * Author: Shakeel Ahmed (satalways)
 * Author URI: https://shakeel.pk
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: social-share-buttons
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('SSB_VERSION', '1.0.1');
define('SSB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SSB_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Register the settings page
 */
function ssb_add_admin_menu() {
    add_options_page(
        'Social Share Buttons Settings',
        'Social Share Buttons',
        'manage_options',
        'social-share-buttons',
        'ssb_options_page'
    );
}
add_action('admin_menu', 'ssb_add_admin_menu');

/**
 * Register settings
 */
function ssb_settings_init() {
    register_setting('ssb_settings_group', 'ssb_settings');

    add_settings_section(
        'ssb_settings_section',
        __('Social Share Buttons Settings', 'social-share-buttons'),
        'ssb_settings_section_callback',
        'social-share-buttons'
    );

    add_settings_field(
        'ssb_enabled_networks',
        __('Enabled Networks', 'social-share-buttons'),
        'ssb_enabled_networks_render',
        'social-share-buttons',
        'ssb_settings_section'
    );

    add_settings_field(
        'ssb_button_position',
        __('Button Position', 'social-share-buttons'),
        'ssb_button_position_render',
        'social-share-buttons',
        'ssb_settings_section'
    );

    add_settings_field(
        'ssb_button_style',
        __('Button Style', 'social-share-buttons'),
        'ssb_button_style_render',
        'social-share-buttons',
        'ssb_settings_section'
    );
}
add_action('admin_init', 'ssb_settings_init');

/**
 * Settings section callback
 */
function ssb_settings_section_callback() {
    echo __('Configure your social sharing buttons below.', 'social-share-buttons');
}

/**
 * Enabled networks field
 */
function ssb_enabled_networks_render() {
    $options = get_option('ssb_settings');
    $networks = [
        'facebook' => 'Facebook',
        'twitter' => 'Twitter/X',
        'linkedin' => 'LinkedIn',
        'pinterest' => 'Pinterest',
        'reddit' => 'Reddit',
        'email' => 'Email'
    ];

    foreach ($networks as $id => $name) {
        $checked = isset($options['networks'][$id]) ? checked($options['networks'][$id], 1, false) : '';
        echo '<label><input type="checkbox" name="ssb_settings[networks][' . $id . ']" value="1" ' . $checked . '> ' . $name . '</label><br>';
    }
}

/**
 * Button position field
 */
function ssb_button_position_render() {
    $options = get_option('ssb_settings');
    $position = isset($options['position']) ? $options['position'] : 'bottom';
    ?>
    <select name="ssb_settings[position]">
        <option value="top" <?php selected($position, 'top'); ?>><?php _e('Above Content', 'social-share-buttons'); ?></option>
        <option value="bottom" <?php selected($position, 'bottom'); ?>><?php _e('Below Content', 'social-share-buttons'); ?></option>
        <option value="both" <?php selected($position, 'both'); ?>><?php _e('Both Above and Below Content', 'social-share-buttons'); ?></option>
        <option value="none" <?php selected($position, 'none'); ?>><?php _e('None (Use Shortcode Only)', 'social-share-buttons'); ?></option>
    </select>
    <?php
}

/**
 * Button style field
 */
function ssb_button_style_render() {
    $options = get_option('ssb_settings');
    $style = isset($options['style']) ? $options['style'] : 'icon-text';
    ?>
    <select name="ssb_settings[style]">
        <option value="icon-text" <?php selected($style, 'icon-text'); ?>><?php _e('Icon and Text', 'social-share-buttons'); ?></option>
        <option value="icon-only" <?php selected($style, 'icon-only'); ?>><?php _e('Icon Only', 'social-share-buttons'); ?></option>
        <option value="text-only" <?php selected($style, 'text-only'); ?>><?php _e('Text Only', 'social-share-buttons'); ?></option>
    </select>
    <?php
}

/**
 * Settings page
 */
function ssb_options_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action='options.php' method='post'>
            <?php
            settings_fields('ssb_settings_group');
            do_settings_sections('social-share-buttons');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/**
 * Enqueue styles and scripts
 */
function ssb_enqueue_scripts() {
    wp_enqueue_style('ssb-styles', SSB_PLUGIN_URL . 'css/social-share-buttons.css', [], SSB_VERSION);
    wp_enqueue_script('ssb-scripts', SSB_PLUGIN_URL . 'js/social-share-buttons.js', ['jquery'], SSB_VERSION, true);
}
add_action('wp_enqueue_scripts', 'ssb_enqueue_scripts');

/**
 * Generate sharing buttons HTML
 */
function ssb_generate_buttons() {
    $options = get_option('ssb_settings');
    
    if (empty($options['networks'])) {
        return '';
    }
    
    $post_url = urlencode(get_permalink());
    $post_title = urlencode(get_the_title());
    
    $output = '<div class="ssb-container">';
    $output .= '<span class="ssb-title">' . __('Share this:', 'social-share-buttons') . '</span>';
    $output .= '<ul class="ssb-buttons">';
    
    foreach ($options['networks'] as $network => $enabled) {
        if (!$enabled) {
            continue;
        }
        
        $button_class = isset($options['style']) ? $options['style'] : 'icon-text';
        
        switch ($network) {
            case 'facebook':
                $url = 'https://www.facebook.com/sharer/sharer.php?u=' . $post_url;
                $label = __('Share on Facebook', 'social-share-buttons');
                break;
            case 'twitter':
                $url = 'https://twitter.com/intent/tweet?url=' . $post_url . '&text=' . $post_title;
                $label = __('Share on Twitter', 'social-share-buttons');
                break;
            case 'linkedin':
                $url = 'https://www.linkedin.com/sharing/share-offsite/?url=' . $post_url;
                $label = __('Share on LinkedIn', 'social-share-buttons');
                break;
            case 'pinterest':
                $thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
                $image = $thumbnail ? urlencode($thumbnail[0]) : '';
                $url = 'https://pinterest.com/pin/create/button/?url=' . $post_url . '&media=' . $image . '&description=' . $post_title;
                $label = __('Pin on Pinterest', 'social-share-buttons');
                break;
            case 'reddit':
                $url = 'https://www.reddit.com/submit?url=' . $post_url . '&title=' . $post_title;
                $label = __('Share on Reddit', 'social-share-buttons');
                break;
            case 'email':
                $url = 'mailto:?subject=' . $post_title . '&body=' . $post_url;
                $label = __('Share via Email', 'social-share-buttons');
                break;
            default:
                continue 2;
        }
        
        $output .= '<li class="ssb-' . $network . '">';
        $output .= '<a href="' . esc_url($url) . '" target="_blank" rel="nofollow noopener" class="ssb-button ssb-' . $button_class . '">';
        
        if ($button_class !== 'text-only') {
            $output .= '<span class="ssb-icon ssb-' . $network . '-icon"></span>';
        }
        
        if ($button_class !== 'icon-only') {
            $output .= '<span class="ssb-text">' . $label . '</span>';
        }
        
        $output .= '</a></li>';
    }
    
    $output .= '</ul></div>';
    
    return $output;
}

/**
 * Add buttons to content
 */
function ssb_add_buttons_to_content($content) {
    if (!is_singular('post')) {
        return $content;
    }
    
    $options = get_option('ssb_settings');
    $position = isset($options['position']) ? $options['position'] : 'bottom';
    
    if ($position === 'none') {
        return $content;
    }
    
    $buttons = ssb_generate_buttons();
    
    if ($position === 'top' || $position === 'both') {
        $content = $buttons . $content;
    }
    
    if ($position === 'bottom' || $position === 'both') {
        $content .= $buttons;
    }
    
    return $content;
}
add_filter('the_content', 'ssb_add_buttons_to_content');

/**
 * Register shortcode
 */
function ssb_shortcode() {
    return ssb_generate_buttons();
}
add_shortcode('social_share_buttons', 'ssb_shortcode');

/**
 * Plugin activation hook
 */
function ssb_activate() {
    // Set default options
    $default_options = [
        'networks' => [
            'facebook' => 1,
            'twitter' => 1,
            'linkedin' => 1,
            'pinterest' => 0,
            'reddit' => 0,
            'email' => 1
        ],
        'position' => 'bottom',
        'style' => 'icon-text'
    ];
    
    add_option('ssb_settings', $default_options);
}
register_activation_hook(__FILE__, 'ssb_activate');

/**
 * Plugin deactivation hook
 */
function ssb_deactivate() {
    // Cleanup if needed
}
register_deactivation_hook(__FILE__, 'ssb_deactivate');