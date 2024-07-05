<?php
/**
 * Plugin Name: Admin Blog Notice
 * Plugin URI: https://www.linkedin.com/in/maju-comendador-40b349a6/
 * Description: Wordpress Plugin that displays Blog Status Notice in Admin. Developed by Maju Comendador
 * Version: 1.0
 * Author: Maju Comendador
 * Author URI: https://www.linkedin.com/in/maju-comendador-40b349a6/
 * License: GPL2
 */

// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Function to run on plugin activation
 */
function mabn_notice_activate() {
    // Code to execute on plugin activation
    // For example, you can add default options, create custom tables, etc.
    // add_option('mabn_notice_option', 'default_value');
}
register_activation_hook(__FILE__, 'mabn_notice_activate');

/**
 * Function to run on plugin deactivation
 */
function mabn_deactivate() {
    // Code to execute on plugin deactivation
    // For example, you can clean up settings, remove custom tables, etc.
    // delete_option('mabn_notice_option');
}
register_deactivation_hook(__FILE__, 'mabn_deactivate');

/**
 * Function to enqueue styles
 */
function mabn_enqueue_admin_styles() {
    wp_enqueue_style( 'maju-admin-blog-notice', plugin_dir_url( __FILE__ ) . 'admin/css/maju-admin-blog-notice-admin.css', array(), '1.0.0', 'all' );
}
add_action('admin_enqueue_scripts', 'mabn_enqueue_admin_styles');

/**
 * Function to query posts
 * @since 1.0.0
 * @param int   $posts_per_page     Number of posts to be fetched
 * @param string    $post_type      Post type of posts to be fetched
 * @param string    $post_status    Status of the post to be fetched
 * 
 */
function mabn_get_blog_posts($posts_per_page = 10, $post_type = 'post', $post_status = 'publish') {
	global $wpdb;

    // Prepare the query to get posts with odd IDs
    $query = $wpdb->prepare("SELECT * FROM $wpdb->posts WHERE post_type = %s AND post_status = %s AND MOD(ID, 2) = 1 ORDER BY post_modified DESC LIMIT %d", $post_type, $post_status, $posts_per_page);
    $posts = $wpdb->get_results($query);

    $map = []; // necessary data stored here

    if ($posts) {
    	foreach ($posts as $post) {
    		$map[] = array(
    			'id' => $post->ID,
    			'title' => $post->post_title,
    			'date' => $post->post_modified
    		);
    	}
    }

    return $map;
}

/**
 * Main function of the plugin
 */
function mabn_function() {
    ob_start();

    $blogs = mabn_get_blog_posts();
    ?>

    <div class="notice notice-info is-dismissible mabn">
        <h2><?php _e('Last Modified and Published Blog Post Titles', 'mabn'); ?></h2>
        <?php if ($blogs) : ?>
	        <ul>
        		<?php foreach ($blogs as $blog) : ?>
        			<li>
	        			<a href="<?php echo get_permalink($blog['id']); ?>" target="_blank"><?php echo $blog['title']; ?></a>
        			</li>
        		<?php endforeach; ?>
	        </ul>
	    <?php else : ?>
	    	<p><?php _e('No posts yet.', 'mabn'); ?></p>
	    <?php endif; ?>
    </div>

    <?php
    echo ob_get_clean();
}

// Hook the main function to the WordPress 'init' action
add_action('admin_notices', 'mabn_function');
