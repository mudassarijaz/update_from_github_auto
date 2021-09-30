<?php


/**
 * Plugin Name: UPdate from Git Hub example Plugin
 * Plugin URI: http://simplyct.co.il/update-from-github-plugin
 * Description: Example how to update from GitHub
 * Version: 1.0
 * Author: Roy
 * Author URI: http://www.simplyct.co.il
 */

add_action('the_content', 'my_thank_you_text');


function my_thank_you_text($content)
{
    return $content .= '<p>Thank you for reading!</p>';
}

//* Load the updater.
//echo ( plugin_dir_path( __FILE__ ) ). 'update_this_plugin.php';
require ( plugin_dir_path( __FILE__ ) ). 'update_this_plugin.php';

// Initialize your extension of the class passing in the current plugin version, directory and slug.
//Current Version, Directory name, Plugin_Slug (main file name without extensioni.e this current file )
$updater = new NexVis\WordPress\Update_This_Plugin( '1.0.0', 'update_from_github_auto', 'update_from_github' );

// Initialize the class which sets up the filters for `transient_update_plugins` and `site_transient_update_plugins`
$updater->init();