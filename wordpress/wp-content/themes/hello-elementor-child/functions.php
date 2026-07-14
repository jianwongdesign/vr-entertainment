<?php
/**
 * Hello Elementor Child Theme functions
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue parent and child theme stylesheets.
 */
function hello_elementor_child_enqueue_styles() {
    // Parent stylesheet.
    wp_enqueue_style(
        'hello-elementor-parent',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme( 'hello-elementor' )->get( 'Version' )
    );

    // Child stylesheet — must load after ALL parent styles, including the
    // theme's reset.css ('hello-elementor' handle) and theme.css, so the
    // child's equal-specificity overrides (link/button color neutralizers)
    // win the cascade.
    wp_enqueue_style(
        'hello-elementor-child',
        get_stylesheet_uri(),
        array( 'hello-elementor-parent', 'hello-elementor', 'hello-elementor-theme-style' ),
        wp_get_theme()->get( 'Version' )
    );
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_enqueue_styles' );

/**
 * Blog index: 9 posts per page (3x3 card grid), with numbered pagination.
 * Scoped to the main blog query only — archives, search and custom
 * queries keep their own settings.
 */
function hello_elementor_child_blog_per_page( $query ) {
    if ( ! is_admin() && $query->is_main_query() && $query->is_home() ) {
        $query->set( 'posts_per_page', 9 );
    }
}
add_action( 'pre_get_posts', 'hello_elementor_child_blog_per_page' );
