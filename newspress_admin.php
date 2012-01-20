<?php 
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

	if($_POST['newspress_hidden'] == 'Y') {
		//Form data sent
		$npuser = $_POST['newspress_user'];
		update_option('newspress_user', $npuser);

		$nppwd = $_POST['newspress_key'];
		update_option('newspress_key', $nppwd);
		?>
		<div class="updated"><p><?php _e('Options saved.' ); ?></p>
		<p><?php 
			$url = "http://content.newstex.us/nbsubmit";
			//Do a GET request to make sure we have the right server and that our credentials are valid
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
			//Yes, we want the header returned
			curl_setopt($ch, CURLOPT_HEADER, 1);
			//Need to send authentication
			curl_setopt($ch, CURLOPT_USERPWD, get_option('newspress_user') . ":" . get_option('newspress_key'));
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			$curl_response = curl_exec($ch);
			$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$article_list = '';
			$post_str = substr($curl_response, curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD) * -1);
			foreach(explode(", ", $post_str) as $pid) {
				$title = get_the_title($pid);
				$article_list .= "<li>$title</li>";
			}

			//get the WP ID's of the files that are successfully in the bucket

			if ($status_code == 200 ) {
				//SUCCESS!
				_e("Connection test successful, credentials validated.");
			} elseif ($status_code == 401) {
				//Slightly less success
				_e("Connection was successful, but credential check failed.</p><p>Please check your username/password and try again");
			}
			else {
				//Not sure what happened. Probably should let support know there's a problem.
				_e("Something went wrong. The error code is $status_code, please try again, or contact support@newstex.com if problems persist.");
			}
		?></p>
		</div>
		<?php
	} else {
		//Normal page display
		$npuser = get_option('newspress_user');
		$nppwd = get_option('newspress_key');
	}


?>

<div class="wrap">
<?php    echo "<h2>" . __( 'Newspress Options', 'newspress_trdom' ) . "</h2>"; ?>

<form name="newspress_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<input type="hidden" name="newspress_hidden" value="Y">
	<?php    echo "<h4>" . __( 'Newstex Publisher Settings', 'newspress_trdom' ) . "</h4>"; ?>
	<p><?php _e("Username: " ); ?><input type="text" name="newspress_user" value="<?php echo $npuser; ?>" size="20"><?php _e(" ex: NEWS" ); ?></p>
	<p><?php _e("Password: " ); ?><input type="text" name="newspress_key" value="<?php echo $nppwd; ?>" size="20"><?php _e(" ex: " ); ?></p>
	<p><?php _e("Newstex Post URL: " ); ?><?php _e("http://content.newstex.us"); ?></p>

	<p class="submit">
	<input type="submit" name="Submit" value="<?php _e('Save and Test Options', 'newspress_trdom' ) ?>" />
	</p>
	<p class="storylist">
		<h4>Recently Submitted Stories</h4>
		<ol>
		<?php 
			$url = "http://content.newstex.us/nbsubmit";
			//Do a GET request to make sure we have the right server and that our credentials are valid
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
			//Yes, we want the header returned
			curl_setopt($ch, CURLOPT_HEADER, 1);
			//Need to send authentication
			curl_setopt($ch, CURLOPT_USERPWD, get_option('newspress_user') . ":" . get_option('newspress_key'));
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			$curl_response = curl_exec($ch);
			$article_list = '';
			$post_str = substr($curl_response, curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD) * -1);
			foreach(explode(", ", $post_str) as $pid) {
				$title = get_the_title($pid);
				$article_list .= "<li>$title</li>";
			}
			echo $article_list;
		?>
		</ol>
	</p>
</form>
</div>
