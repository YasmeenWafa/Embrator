<?php
/**
 * Author: Ole Fredrik Lie
 * URL: http://olefredrik.com
 *
 * FoundationPress functions and definitions
 *
 * Set up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * @link https://codex.wordpress.org/Theme_Development
 * @package FoundationPress
 * @since FoundationPress 1.0.0
 */

/** Various clean up functions */
require_once( 'library/cleanup.php' );

/** Required for Foundation to work properly */
require_once( 'library/foundation.php' );

/** Format comments */
require_once( 'library/class-foundationpress-comments.php' );

/** Register all navigation menus */
require_once( 'library/navigation.php' );

/** Add menu walkers for top-bar and off-canvas */
require_once( 'library/class-foundationpress-top-bar-walker.php' );
require_once( 'library/class-foundationpress-mobile-walker.php' );

/** Create widget areas in sidebar and footer */
require_once( 'library/widget-areas.php' );

/** Return entry meta information for posts */
require_once( 'library/entry-meta.php' );

/** Enqueue scripts */
require_once( 'library/enqueue-scripts.php' );

/** Add theme support */
require_once( 'library/theme-support.php' );

/** Add Nav Options to Customer */
require_once( 'library/custom-nav.php' );

/** Change WP's sticky post class */
require_once( 'library/sticky-posts.php' );

/** Configure responsive image sizes */
require_once( 'library/responsive-images.php' );

/** If your site requires protocol relative url's for theme assets, uncomment the line below */
// require_once( 'library/class-foundationpress-protocol-relative-theme-assets.php' );


//Allow WP  to upload SVG files
function cc_mime_types($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
  }
  add_filter('upload_mimes', 'cc_mime_types');


define('FS_METHOD', 'direct');

add_theme_support( 'woocommerce' );

add_filter('show_admin_bar', '__return_false');

add_theme_support( 'post-thumbnails' );

function get_excerpt_trim($num_words='2000', $more='Apply Now'){
    $excerpt = get_the_content().'<a class="moretag" href="'. get_permalink($post->ID) . '"> Apply Now </a>';
    // $excerpt = wp_trim_words( get_the_excerpt() , $more );
    return $excerpt;
}

function loadMorefn (){
    global $wp_query;
    wp_enqueue_script('jquery');
    // wp_register_script($handle, $src, $deps, $in_footer)
    wp_register_script('loadmore',get_stylesheet_directory_uri().'/src/assets/js/modules/loadMore.js',array('jquery'));
    // wp_localize_script($handle, $object_name, $l10n)
    wp_localize_script('loadmore', '$loadmore_params', array(
        'ajaxurl'=>site_url().'/wp-admin/admin-ajax.php', //wordpress ajax
        'posts'=>json_encode($wp_query->query_vars),
        'current_page'=>get_query_var('paged')?get_query_var('paged'):1,
        'max_page'=>$wp_query->max_num_pages
    ));
    wp_enqueue_script('loadmore');
}

// add_action($tag, $function_to_add, $priority, $accepted_args)
add_action('wp_enqueue_scripts','loadMorefn');


function loadmore_ajax_handler (){
    $args = json_decode(stripslashes($_POST['query']),true);
    $args['paged'] = $_POST['page']+1;
    $args['post_status'] = 'publish';

    query_posts($args);

    if (have_posts()) : 
        while(have_posts()) : the_post();
            get_template_part('template-parts/content',get_post_format());
        endwhile;
    endif;
    die;
}
add_action('wp_ajax_loadmore','loadmore_ajax_handler');
add_action('wp_ajax_nopriv_loadmore','loadmore_ajax_handler');