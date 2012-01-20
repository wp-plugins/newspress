<?php 
/*
Plugin Name: Newspress, Newstex Publisher
Plugin URI: http://www.newstex.com
Description: Plugin for Publishing posts to Newstex
Author: Newstex, LLC
Version: 0.8.0
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
	$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	if ($status_code >= 200 and $status_code < 300) {
		//SUCCESS!
		$_GET['message'] = 11;
	} elseif ($status_code == 401) {
		//Slightly less success
		$_GET['message'] = 12;
	}
	else {
		//Not sure what happened. Probably should let support know there's a problem.
		$_GET['message'] = 13;
	}
	$_GET['message'] = 11;

	return $curl_response;
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
		newspress_send_story($post->ID);
	}
}

//*************** Redirect post location ********************

function newspress_post_redirect_filter($location) {
	// http://stackoverflow.com/questions/5007748/modifying-wordpress-post-status-on-publish
	remove_filter('redirect_post_location', __FILTER__, '99');
	return add_query_arg('newspress_status', $_GET['message'], $location);
}

//*************** Message Alteration function ***************
add_filter('post_updated_messages', 'newspress_updated_messages');
function newspress_updated_messages( $messages ) {
	#Adds status codes for the possibilities added by newspress (curl failed with >=500 error, a <400, or success)
	$newspress_success = "\nStory successfully posted by Newspress plugin";
	$newspress_failure_creds = "\nConnection was successful, but credential check failed.</p><p>Please check your username/password and try again";
	$newspress_failure_other = "\nSomething went wrong. Update the story to resend. Contact support@newstex.us if problem persists";

	$messages['post'] = array(
	 0 => '', // Unused. Messages start at index 1.
	 1 => sprintf( __("Post updated. <a href=\"%s\">View post</a>$messages"), esc_url( get_permalink($post_ID) ) ),
	 2 => __('Custom field updated.'),
	 3 => __('Custom field deleted.'),
	 4 => __('Post updated.'),
	/* translators: %s: date and time of the revision */
	 5 => isset($_GET['revision']) ? sprintf( __('Post restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
	 6 => sprintf( __('Post published. <a href="%s">View post</a>'), esc_url( get_permalink($post_ID) ) ),
	 7 => __('Post saved.'),
	 8 => sprintf( __('Post submitted. <a target="_blank" href="%s">Preview post</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	 9 => sprintf( __('Post scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview post</a>'),
		// translators: Publish box date format, see http://php.net/date
		date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
	10 => sprintf( __('Post draft updated. <a target="_blank" href="%s">Preview post</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	11 => sprintf( __("Post published. <a href=\"%s\">View post</a>$newspress_success"), esc_url( get_permalink($post_ID) ) ),
	12 => sprintf( __("Post published. <a href=\"%s\">View post</a>$newspress_failure_creds"), esc_url( get_permalink($post_ID) ) ),
	13 => sprintf( __("Post published. <a href=\"%s\">View post</a>$newspress_failure_other"), esc_url( get_permalink($post_ID) ) ),
);

return $messages;
}

//*************** Admin function ***************
function newspress_admin() {
	include('newspress_admin.php');
}

function newspress_admin_actions() {
	add_options_page("Newspress Preferences", "Newspress", "manage_options", "Newspress_Preferences", "newspress_admin");
}

add_filter('post_updated_messages', 'newspress_updated_messages');
add_filter('redirect_post_location', 'newspress_post_redirect_filter', '99');
add_action('publish_post', 'newspress_send_story');
add_action('admin_menu', 'newspress_admin_actions');
add_action('transition_post_status', 'filter_action_publish_scheduled', 10, 3);


?>
