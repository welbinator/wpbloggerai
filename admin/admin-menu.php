<?php
function wpblogger_add_menu_pages() {
    add_menu_page('WP Blogger Settings', 'WP Blogger', 'manage_options', 'wpblogger-settings', 'wpblogger_settings_page');
    add_submenu_page('wpblogger-settings', 'API Key', 'API Key', 'manage_options', 'wpblogger-api-key', 'wpblogger_api_key_page');
    add_submenu_page('wpblogger-settings', 'Blogger Tools', 'Blogger Tools', 'manage_options', 'wpblogger-blogger-tools', 'wpblogger_blogger_tools_page');

}

add_action('admin_menu', 'wpblogger_add_menu_pages');

function wpblogger_settings_page() {
    // Settings page content (currently empty).
    echo '<h1>WP Blogger Settings</h1>';
}

function wpblogger_api_key_page() {
    ?>
    <div class="wrap">
        <h1>WP Blogger API Key</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('wpblogger-api-key-group');
            do_settings_sections('wpblogger-api-key');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function wpblogger_blogger_tools_page() {
    $args = array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    );
    $posts_query = new WP_Query($args);
    ?>
    <div class="wrap">
        <h1>Blogger Tools</h1>
        <h2>Create Blog Post</h2>
        <form id="wpblogger-create-post-form" method="post">
            <?php wp_nonce_field('wpblogger_create_blog_post', 'wpblogger_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="wpblogger_blog_title">Blog Title</label>
                    </th>
                    <td>
                        <input type="text" name="wpblogger_blog_title" id="wpblogger_blog_title" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="wpblogger_keyword">Keyword/Keyphrase</label>
                    </th>
                    <td>
                        <input type="text" name="wpblogger_keyword" id="wpblogger_keyword" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="wpblogger_system_message">What's the blog post about?</label>
                    </th>
                    <td>
                        <textarea name="wpblogger_system_message" id="wpblogger_system_message" rows="3" class="large-text"></textarea>
                    </td>
                </tr>
            </table>
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Create Blog Post" />
        </form>
        <h2>Perform SEO Audit</h2>
        <div class="wpblogger-seo-audit">
            <label for="wpblogger_posts">Select a Blog Post:</label>
            <select id="wpblogger_posts">
                <?php
                while ($posts_query->have_posts()) {
                    $posts_query->the_post();
                    echo '<option value="' . get_the_ID() . '">' . get_the_title() . '</option>';
                }
                wp_reset_postdata();
                ?>
            </select>
            <button id="wpblogger_seo_audit" class="button button-primary">Perform SEO Audit</button>
            <div id="wpblogger_seo_audit_result"></div>
        </div>
    </div>
    <?php
}




function wpblogger_enqueue_admin_assets() {
    wp_enqueue_style('wpblogger-admin-css', WPBLOGGER_PLUGIN_URL . 'admin/css/admin.css');
    wp_enqueue_script('wpblogger-admin-js', WPBLOGGER_PLUGIN_URL . 'admin/js/admin.js', array('jquery'), '1.0', true);
}

add_action('admin_enqueue_scripts', 'wpblogger_enqueue_admin_assets');

function wpblogger_register_settings() {
    register_setting('wpblogger-api-key-group', 'wpblogger_api_key');

    add_settings_section('wpblogger-api-key-section', 'API Key Settings', null, 'wpblogger-api-key');
    add_settings_field('wpblogger-api-key', 'API Key', 'wpblogger_api_key_callback', 'wpblogger-api-key', 'wpblogger-api-key-section');
}

add_action('admin_init', 'wpblogger_register_settings');

function wpblogger_api_key_callback() {
    $api_key = esc_attr(get_option('wpblogger_api_key'));
    echo '<input type="text" name="wpblogger_api_key" value="' . $api_key . '" class="regular-text" />';
}


// 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

