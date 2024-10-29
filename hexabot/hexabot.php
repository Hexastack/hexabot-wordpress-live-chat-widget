<?php
/*
Plugin Name: Hexabot Chat Widget
Description: Embed Hexabot chat widget into WordPress.
Version: 2.0.1
Author: Hexastack
License: AGPLv3
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Define plugin version for cache busting
define('HEXABOT_CHAT_WIDGET_VERSION', '2.0.1');

// Register settings for the plugin
function hexabot_chat_widget_register_settings() {
    add_option('hexabot_api_url', 'https://hexabot-api.yourdomain.com');
    add_option('hexabot_channel', 'web-channel');
    add_option('hexabot_token', 'token123');
    register_setting('hexabot_chat_widget_options_group', 'hexabot_api_url');
    register_setting('hexabot_chat_widget_options_group', 'hexabot_channel');
    register_setting('hexabot_chat_widget_options_group', 'hexabot_token');
}
add_action('admin_init', 'hexabot_chat_widget_register_settings');

// Add settings page to the admin menu
function hexabot_chat_widget_register_options_page() {
    add_options_page('Hexabot Chat Widget Settings', 'Hexabot Chat Widget', 'manage_options', 'hexabot_chat_widget', 'hexabot_chat_widget_options_page');
}
add_action('admin_menu', 'hexabot_chat_widget_register_options_page');

// Render the settings page
function hexabot_chat_widget_options_page() {
    ?>
    <div>
        <h2>Hexabot Chat Widget Settings</h2>
        <form method="post" action="options.php">
            <?php settings_fields('hexabot_chat_widget_options_group'); ?>
            <table>
                <tr valign="top">
                    <th scope="row"><label for="hexabot_api_url">API URL</label></th>
                    <td><input style="width: 512px;" type="text" id="hexabot_api_url" name="hexabot_api_url" value="<?php echo esc_attr(get_option('hexabot_api_url')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="hexabot_channel">Channel</label></th>
                    <td><input type="text" id="hexabot_channel" name="hexabot_channel" value="<?php echo esc_attr(get_option('hexabot_channel')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="hexabot_token">Token</label></th>
                    <td><input type="text" id="hexabot_token" name="hexabot_token" value="<?php echo esc_attr(get_option('hexabot_token')); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}


// Function to embed the chat widget container and scripts in the footer
function hexabot_chat_widget_embed() {
    $widget_url = get_option('hexabot_widget_url');
    $api_url = get_option('hexabot_api_url');
    $channel = get_option('hexabot_channel');
    $token = get_option('hexabot_token');

    // Output the chat widget div
    echo '<div id="hb-chat-widget"></div>';

    // Enqueue React and React DOM in compatibility mode (they are loaded as window.React and window.ReactDOM)
    wp_enqueue_script('react');
    wp_enqueue_script('react-dom');

    // Enqueue Hexabot widget
    wp_enqueue_script('hexabot-widget', plugin_dir_url(__FILE__) . 'assets/hexabot-widget.umd.js', array('react', 'react-dom'), HEXABOT_CHAT_WIDGET_VERSION, true);

    // Add inline script to initialize the widget after the div
    wp_add_inline_script('hexabot-widget', "
        (function() {
            const createElement = (tag, props = {}) => Object.assign(document.createElement(tag), props);
            const shadowContainer = createElement('div');
            document
                .getElementById('hb-chat-widget')
                .attachShadow({ mode: 'open' })
                .append( 
                    shadowContainer,
                    createElement('link', {
                        rel: 'stylesheet',
                        href: '" . plugin_dir_url(__FILE__) . "assets/hexabot-widget.css'
                    })
                );
            ReactDOM.render(
              React.createElement(HexabotWidget, {
                apiUrl: '" . esc_url($api_url) . "',
                channel: '" . esc_attr($channel) . "',
                token: '" . esc_attr($token) . "',
              }),
              shadowContainer
            );
        })();
    ");
}

// Hook the function to embed the widget container and enqueue scripts to wp_footer
add_action('wp_footer', 'hexabot_chat_widget_embed');
?>
