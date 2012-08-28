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
			$response = wp_remote_get($url,array(
				'headers' => array(
					'Authorization' => 'Basic ' . base64_encode(get_option('newspress_user') . ":" . get_option('newspress_key')),
					'Content-Type' => 'application/json',
				)
			));
			if( is_wp_error($response) ){
				$status_code = $response['response']['code'];
				if ($status_code == 401) {
					//Slightly less success
					_e("Connection was successful, but credential check failed.</p><p>Please check your username/password and try again");
				} else {
					//Not sure what happened. Probably should let support know there's a problem.
					_e("Something went wrong. The error code is $status_code, please try again, or contact support@newstex.com if problems persist.");
				}
			} else {
				//SUCCESS!
				_e("Connection test successful, credentials validated.");
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

	<p class="submit">
	<input type="submit" name="Submit" value="<?php _e('Save and Test Options', 'newspress_trdom' ) ?>" />
	</p>
</form>
</div>
