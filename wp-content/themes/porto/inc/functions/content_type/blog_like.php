<?php

add_action('wp_ajax_porto_blog-like', 'porto_ajax_blog_like');

add_action('wp_ajax_nopriv_porto_blog-like', 'porto_ajax_blog_like');



function porto_ajax_blog_like() {

    if (isset($_POST['blog_id'])) {

        $blog_id = $_POST['blog_id'];

        $like_count = get_post_meta($blog_id, 'like_count', true);

        if (!isset($_COOKIE['porto_like_'. $blog_id]) || $like_count == 0) {

            $like_count++;

            setcookie('porto_like_'. $blog_id, $blog_id, time() * 20, '/');

            update_post_meta($blog_id, 'like_count', $like_count);

        }

        echo '<span class="like-text">'.__('Liked','porto').': </span><span class="blog-liked linked text-color-secondary" title="' . __('Already Liked', 'porto') . '" data-tooltip><i class="fa fa-heart"></i><span class="font-weight-semibold">'. $like_count . '</span></span>';

    }

    exit;

}



function porto_blog_like() {

    global $post;

    $blog_id = $post->ID;

    $like_count = get_post_meta($blog_id, 'like_count', true);

    if ($like_count && isset($_COOKIE['porto_like_'. $blog_id]) ) {

        $output = '<span class="like-text">'.__('Liked','porto').': </span><span class="blog-liked linked text-color-secondary" title="' . __('Already liked', 'porto') . '" data-tooltip><i class="fa fa-heart"></i><span class="font-weight-semibold">'. $like_count . '</span></span>';

    } else {

        $output = '<span class="like-text">'.__('Like','porto').': </span><span class="blog-like cur-pointer font-weight-semibold text-color-secondary" title="' . __('Like', 'porto') . '" data-tooltip data-id="' . $blog_id . '"><i class="fa fa-heart"></i> '. ($like_count ? $like_count : '0') . '</span>';

    }

    return $output;

}

