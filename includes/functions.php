<?php
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

function wpblogger_create_blog_post() {
    error_log('wpblogger_create_blog_post function called');
    error_log('wpblogger_create_blog_post function executed');

    // Check nonce and user capabilities.
    if (!isset($_POST['wpblogger_nonce']) || !wp_verify_nonce($_POST['wpblogger_nonce'], 'wpblogger_create_blog_post') || !current_user_can('publish_posts')) {
        wp_send_json_error('Unauthorized request');
    }
    error_log(print_r($_POST, true));
    error_log(print_r($_FILES, true));

    $api_key = esc_attr(get_option('wpblogger_api_key'));
    $blog_title = sanitize_text_field($_POST['wpblogger_blog_title']);
    $keyword = isset($_POST['wpblogger_keyword']) ? sanitize_text_field($_POST['wpblogger_keyword']) : '';

    $prompt = 'What\'s the blog post about? ' . $blog_title;
    
    if (!empty($keyword)) {
        $prompt .= ' Try to use ' . $keyword . ' in the content at least 3 times.';
    }

    // Call the ChatGPT API with the given API key and prompt, and create a draft post with the returned content.
    $client = new Client([
        'base_uri' => 'https://api.openai.com/',
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
        ],
    ]);

    $params = [
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            [
                'role' => 'system',
                'content' => $prompt,
            ],
        ],
    ];

    try {
        $response = $client->post('v1/chat/completions', [
            'json' => $params,
        ]);

        if ($response->getStatusCode() == 200) {
            $data = json_decode($response->getBody());
            $post_content = $data->choices[0]->message->content;

            // Create a new blog post and set it as a draft.
            $new_post = array(
                'post_title' => $blog_title,
                'post_content' => $post_content,
                'post_status' => 'draft',
                'post_type' => 'post',
            );

            $post_id = wp_insert_post($new_post);

            if ($post_id) {
                wp_send_json_success(admin_url('post.php?post=' . $post_id . '&action=edit'));
                exit;
            } else {
                wp_die('Error creating the blog post.');
            }
        } else {
            wp_die('Error calling the ChatGPT API.');
        }
    } catch (RequestException $e) {
        wp_die('Error calling the ChatGPT API: ' . $e->getMessage());
    }
}


add_action('wp_ajax_wpblogger_create_blog_post', 'wpblogger_create_blog_post');


function wpblogger_get_api_key() {
    return get_option('wpblogger_api_key');
}

function wpblogger_save_api_key() {
    if (!current_user_can('manage_options') || !check_admin_referer('wpblogger_save_api_key')) {
       

        wp_die('Unauthorized request');
    }

    if (isset($_POST['wpblogger_api_key'])) {
        $api_key = sanitize_text_field($_POST['wpblogger_api_key']);
        update_option('wpblogger_api_key', $api_key);
    }

    wp_redirect(admin_url('admin.php?page=wpblogger-settings&settings-updated=true'));
    exit;
}

add_action('wp_ajax_wpblogger_create_blog_post', 'wpblogger_create_blog_post');

