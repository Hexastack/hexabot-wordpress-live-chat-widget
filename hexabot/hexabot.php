<?php
/*
Plugin Name: Hexabot Chat Widget
Description: Embed Hexabot chat widget into WordPress.
Version: 1.0
Author: Hexastack
License: AGPLv3
*/


// Register settings for the plugin
function hexabot_chat_widget_register_settings() {
    add_option('hexabot_widget_url', 'https://cdn.jsdelivr.net/npm/hexabot-live-chat-widget@2.0.0-rc.1/dist');
    add_option('hexabot_api_url', 'https://hexabot-api.yourdomain.com');
    add_option('hexabot_channel', 'offline');
    add_option('hexabot_token', 'token123');
    register_setting('hexabot_chat_widget_options_group', 'hexabot_widget_url');
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
                    <th scope="row"><label for="hexabot_widget_url">Widget URL</label></th>
                    <td><input style="width: 512px;" type="text" id="hexabot_widget_url" name="hexabot_widget_url" value="<?php echo esc_attr(get_option('hexabot_widget_url')); ?>" /></td>
                </tr>
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

// Function to embed the Hexabot chat widget on all pages
function hexabot_chat_widget_embed() {
    $widget_url = get_option('hexabot_widget_url');
    $api_url = get_option('hexabot_api_url');
    $channel = get_option('hexabot_channel');
    $token = get_option('hexabot_token');

    echo '<div id="hb-chat-widget"></div>
    <script crossorigin src="https://cdn.jsdelivr.net/npm/react@18/umd/react.production.min.js"></script>
    <script crossorigin src="https://cdn.jsdelivr.net/npm/react-dom@18/umd/react-dom.production.min.js"></script>
    <script src="' . esc_url($widget_url) . '/hexabot-widget.umd.js"></script>
    <script>
      (function() {
        const widgetContainer = document.getElementById("hb-chat-widget");
        const shadowRoot = widgetContainer.attachShadow({ mode: "open" });

        const shadowContainer = document.createElement("div");
        shadowRoot.appendChild(shadowContainer);

        const linkElement = document.createElement("link");
        linkElement.rel = "stylesheet";
        linkElement.href = "' . esc_url($widget_url) . '/style.css";
        shadowRoot.appendChild(linkElement);

        const el = React.createElement;
        ReactDOM.render(
          el(HexabotWidget, {
            apiUrl: "' . esc_url($api_url) . '",
            channel: "' . esc_attr($channel) . '",
            token: "' . esc_attr($token) . '",
          }),
          shadowContainer
        );
      })();
    </script>';
}

// Hook the function to wp_footer to ensure it appears on all pages
add_action('wp_footer', 'hexabot_chat_widget_embed');
?>
