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

function fdfp_post_published_notification( $ID, $post ) {
	
	// Site Url 
	$site_url = get_site_url();

	/* Post author ID. */
	$author = $post->post_author; 
	
	/* Post author Follower List. */
	$followers_list = get_user_meta( $author, '_fdfp_followers', true );
	
	/* Post author Name. */
	$name = get_the_author_meta( 'display_name', $author );
	
	/* Post Title. */
	$title = $post->post_title;

	// To Get Company Logo
	$email_notif_settings = json_decode( get_option( 'email_notif_settings' ) );

	// Logo
	if(!empty($email_notif_settings->logo)){
		$logo = "<img src=".$email_notif_settings->logo." width='150px' height='100px'>";
	}else{
		$logo = "My Digital Sauce";
	}

	// Set the value of FROM in header from admin panel
	$from_name = "";
	// set name 
	if(!empty($email_notif_settings->from_name)){
		$from_name .= $email_notif_settings->from_name;
	}

	// set email
	if(!empty($email_notif_settings->from_email)){
		$from_name .= " <".$email_notif_settings->from_email.">";
	}

	// If both the value of admin panel is empty
	if(empty($from_name)){
		$from_name = "My Digital Sauce <example@example.com>";
	}
	
	// Post Link
	$permalink = get_permalink( $ID );

	// Mail Information
	$subject = sprintf( 'New Post: %s', $title );					

	foreach($followers_list as $follower){
		// Get Information of follower
		$to_user = get_userdata($follower);
		$user_name = $to_user->first_name.' '.$to_user->last_name;

		// Get The Template 
		$body = file_get_contents(plugin_dir_path( __FILE__ ) . '../templates/email-template.html',true);

		// Message To print In Template
		$message = "Author: ".$name."! New ".$post->post_type." has been published.";
		
		// Body And Header Of mail
		$body = str_replace('[NameGoesHere]', $user_name, $body);
		$body = str_replace('[MessageGoesHere]', $message, $body);
		$body = str_replace('[WebSiteUrl]', $site_url, $body);
		$body = str_replace('[CompanyLogoHere]', $logo, $body);
		$body = str_replace('[LinkGoesHere]', $permalink, $body);
		$headers = array('Content-Type: text/html; charset=UTF-8','From: '.$from_name);	
		
		$to = $to_user->user_email;
		wp_mail( $to, $subject, $body, $headers );	
	}
}
add_action( 'publish_post', 'fdfp_post_published_notification', 10, 2 );



/**
 * Email notification to author on post comment approved
 *
 * @access      private
 * @since       1.0
 * @return      void
 */
// Change Email Text using filter
function fdfp_change_comment_email( $body, $comment_id ) {	
		// Site Url 	
		$site_url = get_site_url();	

		// Get the Post
		$post = get_post($comment_id);

		// To Get Company Logo
		$email_notif_settings = json_decode( get_option( 'email_notif_settings' ) );

		// Logo
		if(!empty($email_notif_settings->logo)){
			$logo = "<img src=".$email_notif_settings->logo." width='150px' height='100px'>";
		}else{
			$logo = "My Digital Sauce";
		}

		// Get Information of follower
		$to_user = get_userdata($post->post_author);
		$user_name = $to_user->first_name.' '.$to_user->last_name;

   		// Get The Template 
		$body = file_get_contents(plugin_dir_path( __FILE__ ) . '../templates/email-template.html',true);

		// Message To print In Template
		$message = "Your Friend ".get_comment_author($comment_id)." ! has Commented on a ".$post->post_type;

		// Post Link
		$permalink = get_comment_link( $comment_id );
		
		// Body And Header Of mail
		$body = str_replace('[NameGoesHere]', $user_name, $body);
		$body = str_replace('[MessageGoesHere]', $message, $body);
		$body = str_replace('[WebSiteUrl]', $site_url, $body);
		$body = str_replace('[CompanyLogoHere]', $logo, $body);
		$body = str_replace('[LinkGoesHere]', $permalink, $body);
		
		return $body;
}
// add the filter Change Email Text 
add_filter( 'comment_moderation_text', 'fdfp_change_comment_email', 20, 2 );
add_filter( 'comment_notification_text', 'fdfp_change_comment_email', 20, 2 );

// define the comment_notification_headers callback 
function fdfp_filter_comment_notification_headers( $message_headers, $comment_comment_id ) { 
	
 		// To Get Company Logo
		 $email_notif_settings = json_decode( get_option( 'email_notif_settings' ) );	
		 // Set the value of FROM in header from admin panel
		 $from_name = "";
		 // set name 
		 if(!empty($email_notif_settings->from_name)){
			 $from_name .= $email_notif_settings->from_name;
		 }
 
		 // set email
		 if(!empty($email_notif_settings->from_email)){
			 $from_name .= " <".$email_notif_settings->from_email.">";
		 }
 
		 // If both the value of admin panel is empty
		 if(empty($from_name)){
			 $from_name = "My Digital Sauce <example@example.com>";
		 }
 
	 $message_headers = 'Content-Type: text/html; charset=UTF-8;From: '.$from_name;

    // make filter magic happen here... 
    return $message_headers; 
};     
// add the filter comment_notification_headers
add_filter( 'comment_notification_headers', 'fdfp_filter_comment_notification_headers', 10, 2 ); 