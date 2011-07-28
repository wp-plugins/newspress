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
		<div class="updated"><p><?php _e('Options saved.' ); ?></p></div>
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
	<input type="submit" name="Submit" value="<?php _e('Save Options', 'newspress_trdom' ) ?>" />
	</p>
</form>
</div>
