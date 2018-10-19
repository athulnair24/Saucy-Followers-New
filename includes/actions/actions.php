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


/**
 * Email notification to user's followers on publish post
 *
 * @access      private
 * @since       1.0
 * @return      void
 */

function fdfp_notif_users_followers_on_publish_post( $ID, $post ) {
	$author = $post->post_author; /* Post author ID. */
	
	$followers_list = get_user_meta( $author, '_fdfp_followers', true );

	foreach($followers_list as $follower){
		$name = get_the_author_meta( 'display_name', $author );
		// $email = get_user_meta(  $follower, 'user_email', true );
		$email = get_userdata($follower);
		$title = $post->post_title;
		$permalink = get_permalink( $ID );
		$edit = get_edit_post_link( $ID, '' );
		$to = $email->user_email;
		$subject = sprintf( 'Article: %s', $title );
		$message = sprintf ('Your friend: %s! New article “%s” has been published.' . "\n\n", $name, $title);
		$message .= sprintf( 'View: %s', $permalink );		
		$headers = array('Content-Type: text/html; charset=UTF-8');
		wp_mail( $to, $subject, $message, $headers );	
	}
}
add_action( 'publish_post', 'fdfp_notif_users_followers_on_publish_post', 10, 2 );


/**
 * Email notification to author on post comment approved
 *
 * @access      private
 * @since       1.0
 * @return      void
 */
function fdfp_notif_author_on_post_comment_approved( $comment_ID, $comment_approved ) {
    if ( $comment_approved === 1 ) {
			$to = comment_author_email( $comment_ID );
			$subject = 'New Comment';
			$body = 'One new Comment on your post has been published.';
			$headers = array('Content-Type: text/html; charset=UTF-8');
			wp_mail( $to, $subject, $message, $headers );
    }
}
add_action( 'comment_post', 'fdfp_notif_author_on_post_comment_approved', 10, 2 );

