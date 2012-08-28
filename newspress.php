<?php 
/*
Plugin Name: Newspress, Newstex Publisher
Plugin URI: http://www.newstex.com
Description: Plugin for Publishing posts to Newstex
Author: Newstex, LLC
Version: 0.9.2
Author URI: http://www.newstex.com
*/

/* This file is part of Newspress
 *
 * Newspress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * */


function newspress_send_story($post_ID) {
	//Get the post and package it up to be sent
	$json_data = create_json_blob($post_ID);
	$url = "http://content.newstex.us/nbsubmit/$post_ID";
	return wp_remote_post($url, array(
		'method' => 'PUT',
		'timeout' => 15,
		'blocking' => false,
		'headers' => array(
			'Authorization' => 'Basic ' . base64_encode(get_option('newspress_user') . ":" . get_option('newspress_key')),
			'Content-Type' => 'application/json',
		),
		'body' => $json_data

	));
}

function create_json_blob($post_ID) {
	//put all the data into an array in order to JSON-ify it
	$post = get_post($post_ID);
	//pull out all the data we need
	//Create a list of categories
	//get the categories
	$category_arr = get_the_category($post_ID);
	$name_arr = array();
	foreach($category_arr as $cat) {
		//loop through them and get only their name
		$name_arr[] = $cat->name;
	}
	//get the tags
	$raw_tag_array = wp_get_post_tags($post_ID);
	foreach($raw_tag_array as $tag) {
		//loop through them and get only their name
		$name_arr[] = $tag->name;
	}
	//language
	$lang = get_bloginfo('language');
	//permalink
	$permalink = get_permalink($post_ID);
	//headline (Title of the post)
	$headline = $post->post_title;
	//subheadline
	//content (Actual content in html format)
	$content = wpautop($post->post_content);
	//excerpt (Usually selected by the user, otherwise seems like the first sentence)
	$excerpt = $post->post_excerpt;
	//source (name of the blog)
	$source = get_bloginfo('name');
	//byline (User should have their first and last name set in their wordpress user settings)
	global $current_user;
	get_currentuserinfo();
	$byline = $current_user->first_name . " " . $current_user->last_name;
	//Check that you have a newstex publisher ID and such.
	//make a JSON object of all the data
	$pre_json = array (
		'post_permalink' => $permalink,
		'post_headline' => $headline,
		'post_content' => $content,
		'post_excerpt' => $excerpt,
		'post_source' => $source,
		'post_byline' => $byline,
		'post_id' => $post_ID,
		'post_categories' => $name_arr,
		'post_language' => $lang
		);
	$json_data = json_encode($pre_json);
	//Might need extra encoding
	//$json_data = json_encode($pre_json, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP);
	return $json_data;
}

//******************* Publish Scheduled Posts Function ******
function filter_action_publish_scheduled( $new_status, $old_status, $post ) {
	if( 'publish' == $new_status && 'future' == $old_status ) {
		newspress_send_story($post);
	}
}


//*************** Admin function ***************
function newspress_admin() {
	include('newspress_admin.php');
}

function newspress_admin_actions() {
	add_options_page("Newspress Preferences", "Newspress", "manage_options", "Newspress_Preferences", "newspress_admin");
}

add_action('publish_post', 'newspress_send_story');
add_action('admin_menu', 'newspress_admin_actions');
add_action('transition_post_status', 'filter_action_publish_scheduled', 10, 3);


?>
