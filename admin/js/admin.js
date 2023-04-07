jQuery(document).ready(function($) {
    if ($('#wpblogger-create-post-form').data('listener-added') !== true) {
        $('#wpblogger-create-post-form').data('listener-added', true);
        $('#wpblogger-create-post-form').on('submit', function(e) {
            e.preventDefault();
            console.log('Form Submitted');

            let formData = new FormData(this);
            formData.append('action', 'wpblogger_create_blog_post');

            $.ajax({
                url: wpblogger_ajax_obj.ajax_url,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error: ' + status + ' ' + error + ' ' + xhr.responseText);
                },
            });
        });
    };

    $('#wpblogger_seo_audit').on('click', function(e) {
        e.preventDefault();

        let postId = $('#wpblogger_posts').val();

        $.ajax({
            url: wpblogger_ajax_obj.ajax_url,
            type: 'POST',
            data: {
                action: 'wpblogger_perform_seo_audit',
                post_id: postId,
            },
            async: false,
            success: function(response) {
                $('#wpblogger_seo_audit_result').html(response);
            },
            error: function(xhr, status, error) {
                console.error('Error: ' + status + ' ' + error + ' ' + xhr.responseText);
            },
        });
    });
});



    

