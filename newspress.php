<?php 
/*
Plugin Name: Newspress, Newstex Publisher
Plugin URI: http://www.newstex.com
Description: Plugin for Publishing posts to Newstex
Author: Newstex, LLC
Version: 0.6
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
	$url = "http://content.newstex.us:80/nbsubmit/$post_ID";

	//Generate the PUT request
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	//Yes, we want the header returned
	curl_setopt($ch, CURLOPT_HEADER, 1);
	//Need to send authentication
	curl_setopt($ch, CURLOPT_USERPWD, get_option('newspress_user') . ":" . get_option('newspress_key'));
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	//Declare the encoding we need
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	//put in the data
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$curl_response = curl_exec($ch);
	echo $curl_response;

	return $response;
}

function create_json_blob($post_ID) {
	//put all the data into an array in order to JSON-ify it
	$post = get_post($post_ID);
	//pull out all the data we need
	//permalink
	$permalink = get_permalink($post_ID);
	//headline (Title of the post)
	$headline = $post->post_title;
	//subheadline
	//content (Actual content in html format)
	$content = $post->post_content;
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
		'post_id' => $post_ID
		);
	$json_data = json_encode($pre_json);
	//Might need extra encoding
	//$json_data = json_encode($pre_json, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP);
	return $json_data;
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

?>
