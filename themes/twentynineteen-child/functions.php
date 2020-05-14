<?php
/**
 * Functions - Twentynineteen Child theme custom functions
 */


/**
 * Loads the parent stylesheet.
 */

add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );

function enqueue_parent_styles() {
   wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}

/****************************************************************************************************************/










/**
 * Remove Wordpress Logo.
 */
function example_admin_bar_remove_logo() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu( 'wp-logo' );
}
add_action( 'wp_before_admin_bar_render', 'example_admin_bar_remove_logo', 0 );

/**
 * Add ERMD Footer.
 */
  
function remove_footer_admin () 
{
    echo '<span id="footer-thankyou">For technical support please contact ERMD: <a href="mailto:ecms@epa.gov">ecms@epa.gov</a></span>';
}
 
add_filter('admin_footer_text', 'remove_footer_admin');


/**
 * Add jsgrid css and JavaScript.
 */

function jsgrid_load_scripts() {

	wp_enqueue_style('admin-jsgrid-css', get_parent_theme_file_uri().'/jsgrid/css/jsgrid.min.css');
    wp_enqueue_style('admin-jsgrid-css-theme', get_parent_theme_file_uri().'/jsgrid/css/jsgrid-theme.min.css');
	wp_enqueue_script('admin-jsgrid', get_parent_theme_file_uri().'/jsgrid/js/jsgrid.min.js');
	
}
add_action('admin_enqueue_scripts', 'jsgrid_load_scripts');


if(function_exists('add_db_table_editor')){
add_db_table_editor('title=Record Schedule Editor&table=wpqa_epa_record_schedule');
add_db_table_editor('title=File Folder Details&table=wpqa_wpsc_epa_folderdocinfo');
add_db_table_editor('title=Error Log&table=wpqa_epa_error_log');
}