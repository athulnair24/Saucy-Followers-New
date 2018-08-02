<?php

/**
 * Ajax Actions
 *
 * @package     French Dip Following Plugin
 * @subpackage  Ajax Actions
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/


/**
 * Processes the ajax request to follow a user
 *
 * @access      private
 * @since       1.0
 * @return      void
 */

function fdfp_process_new_follow() {
	if ( isset( $_POST['user_id'] ) && isset( $_POST['follow_id'] ) ) {
		if( fdfp_follow_user( absint( $_POST['user_id'] ), absint( $_POST['follow_id'] ) ) ) {
			echo 'success';
		} else {
			echo 'failed';
		}
	}
	die();
}
add_action('wp_ajax_follow', 'fdfp_process_new_follow');


/**
 * Processes the ajax request to unfollow a user
 *
 * @access      private
 * @since       1.0
 * @return      void
 */

function fdfp_process_unfollow() {
	if ( isset( $_POST['user_id'] ) && isset( $_POST['follow_id'] ) ) {
		if( fdfp_unfollow_user( absint( $_POST['user_id'] ), absint( $_POST['follow_id'] ) ) ) {
			echo 'success';
		} else {
			echo 'failed';
		}
	}
	die();
}
add_action('wp_ajax_unfollow', 'fdfp_process_unfollow');