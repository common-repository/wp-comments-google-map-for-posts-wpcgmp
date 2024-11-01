<?php
/*
Plugin Name: WP Comments Google Map for Posts
Plugin URI: http://www.cmstactics.com
Description: WP Comments Google Map for Posts adds a Google map to your posts and plots a marker to the map for the location of each user that comments on that post.
Version: 1.0
Author: Alex Rayan & Kaysten Mazerino
Author URI: http://www.cmstactics.com
License: GPL2
*/

/*  Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : PLUGIN AUTHOR EMAIL)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if(is_admin()) {

	function wpcgmp_admin_actions() {
		add_options_page('Comments Map Options', 'Comments Map Options','manage_options', 'comments-map-options', 'wpcgmp_admin');
	}
	add_action('admin_menu', 'wpcgmp_admin_actions');	
	
	function wpcgmp_admin() {
		include('wpcgmp_admin.php');
	}

	function wpcgmp_admin_scripts() {
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		wp_register_script('wpcgmp-upload', plugins_url('/js/', __FILE__) . '/wpcgmp_scripts.js', array('jquery','media-upload','thickbox'));
		wp_enqueue_script('wpcgmp-upload');
	}

	function wpcgmp_admin_styles() {
		wp_enqueue_style('thickbox');
	}
	
	if(isset($_GET['page'])&&$_GET['page']=='comments-map-options')
	{
		add_action('admin_print_scripts', 'wpcgmp_admin_scripts');
		add_action('admin_print_styles', 'wpcgmp_admin_styles');
	}

}
function wpcgmp_head_includes() {
	//Add Google Maps API Key
	$gmaps_api_key = get_option('wpcgmp_gmaps_api');
	echo "<script src=\"http://maps.google.com/maps?file=api&amp;v=2&amp;key=" . $gmaps_api_key . "\" type=\"text/javascript\"></script>";

	//Enqueue gmap jquery plugin
	echo "<script type=\"text/javascript\" src=\"" . plugins_url('/js/', __FILE__) . "jquery.gmap-1.1.0.js\"></script>";
}
add_action('wp_head', 'wpcgmp_head_includes');


function wpcgmp_init($content) {
	//Include wpcgmp functions file
	require_once( dirname(__FILE__) . '/wpcgmp_functions.php' );
	$wpcgmp_map = wpcgmp_get_comments_map();
	return $content.$wpcgmp_map;
}	
add_filter('the_content', 'wpcgmp_init');

add_shortcode("wpcgmp_google_map_display", "wpcgmp_map_display_handler");
function wpcgmp_map_display_handler($atts) {
	require_once( dirname(__FILE__) . '/wpcgmp_functions.php' );
	extract(shortcode_atts(array(
			'width'=>'625',
			'height'=>'400',
	),$atts));
	$wpcgmp_map = wpcgmp_do_shortcode_map($width,$height);
	return $wpcgmp_map;
}
?>
