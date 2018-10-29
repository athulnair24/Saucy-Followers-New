<?php

/**
 * Follow Functions
 *
 * @package     French Dip Following Plugin
 * @subpackage  Follow Functions
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

/**
 * Retrieves all users that the specified user follows
 *
 * Gets all users that $user_id followers
 *
 * @access      private
 * @since       1.0
 * @param 		int $user_id - the ID of the user to retrieve following for
 * @return      array
 */
function fdfp_get_following( $user_id = 0 ) {

	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}
	$following = get_user_meta( $user_id, '_fdfp_following', true );

	return apply_filters( 'fdfp_get_following', $following, $user_id );
}

/**
 * Retrieves users that follow a specified user
 *
 * Gets all users following $user_id
 *
 * @access      private
 * @since       1.0
 * @param 		int $user_id - the ID of the user to retrieve followers for
 * @return      array
 */
function fdfp_get_followers( $user_id = 0 ) {

	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}
	$followers = get_user_meta( $user_id, '_fdfp_followers', true );

	return apply_filters( 'fdfp_get_followers', $followers, $user_id );
}

/**
 * Follow a user
 *
 * Makes a user follow another user
 *
 * @access      private
 * @since       1.0
 * @param 		int $user_id        - the ID of the user that is doing the following
 * @param 		int $user_to_follow - the ID of the user that is being followed
 * @return      bool
 */
function fdfp_follow_user( $user_id = 0, $user_to_follow = 0 ) {

	// retrieve the IDs of all users who $user_id follows
	$following = fdfp_get_following( $user_id );

	if ( ! empty( $following ) && is_array( $following ) ) {
		$following[] = $user_to_follow;
	} else {
		$following = array();
		$following[] = $user_to_follow;
	}

	// retrieve the IDs of all users who are following $user_to_follow
	$followers = fdfp_get_followers( $user_to_follow );

	if ( ! empty( $followers ) && is_array( $followers ) ) {
		$followers[] = $user_id;
	} else {
		$followers = array();
		$followers[] = $user_id;
	}

	do_action( 'fdfp_pre_follow_user', $user_id, $user_to_follow );

	// update the IDs that this user is following
	$followed = update_user_meta( $user_id, '_fdfp_following', $following );

	// update the IDs that follow $user_to_follow
	$followers = update_user_meta( $user_to_follow, '_fdfp_followers', $followers );

	// increase the followers count
	$followed_count = fdfp_increase_followed_by_count( $user_to_follow );

	if ( $followed ) {

		do_action( 'fdfp_post_follow_user', $user_id, $user_to_follow );

		$check_mail = fdfp_notif_user_on_follow($user_to_follow, $user_id);

		// if( $check_mail )
		// 	return true;
		// else
		// 	return false;
	}
	return false;
}

/**
 * Mail a user
 *
 * Email notification to user on new follow
 *
 * @access      private
 * @since       1.0
 * @param 		int $user_to_follow - the ID of the user that is being followed
 * @param 		int $user_id        - the ID of the user that is doing the following
 * @return      bool
 */
function fdfp_notif_user_on_follow($user_to_follow , $user_id){
	
	// User Information
	// Site Url 
	$site_url = get_site_url();

	// To User 
	$to_user_info = get_userdata($user_to_follow);
	$to = $to_user_info->user_email;
	$name = $to_user_info->first_name.' '.$to_user_info->last_name;

	// Current User 
	$user_info = get_userdata($user_id);
	$user_info_link = $site_url."/?author=".$user_id;

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
	
	// Mail Information
	$subject = 'New Follower';

	// Get The Template 
	$body = file_get_contents(plugin_dir_path( __FILE__ ) . '../templates/email-template.html',true);

	// Message To print In Template
	$message = "Username : ".$user_info->user_login." is Following You.";
	
	// Body And Header Of mail
	$body = str_replace('[NameGoesHere]', $name, $body);
	$body = str_replace('[MessageGoesHere]', $message, $body);
	$body = str_replace('[WebSiteUrl]', $site_url, $body);
	$body = str_replace('[CompanyLogoHere]', $logo, $body);
	$body = str_replace('[LinkGoesHere]', $user_info_link, $body);
	$headers = array('Content-Type: text/html; charset=UTF-8','From: '.$from_name);
		
	// Mail Function
	wp_mail( $to, $subject, $body, $headers );
	
	return true;
}


/**
 * Mail a user
 *
 * Email notification on User To unfollow
 *
 * @access      private
 * @since       1.0
 * @param 		int $user_id        - the ID of the user that is doing the following
 * @param 		int $user_to_unfollow - the ID of the user that is being unfollowed
 * @return      bool
 */
function fdfp_notif_user_on_unfollow($user_to_unfollow , $user_id){

	// User Information
	// Site Url 
	$site_url = get_site_url();

	// To User 
	$to_user_info = get_userdata($user_to_unfollow);
	$to = $to_user_info->user_email;
	$name = $to_user_info->first_name.' '.$to_user_info->last_name;

	// Current User 
	$user_info = get_userdata($user_id);
	$user_info_link = $site_url."/?author=".$user_id;

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
	
	// Mail Information
	$subject = 'User Unfollow';

	// Get The Template 
	$body = file_get_contents(plugin_dir_path( __FILE__ ) . '../templates/email-template.html',true);

	// Message To print In Template
	$message = "Username : ".$user_info->user_login." just unfollowed you.";
	
	// Body And Header Of mail
	$body = str_replace('[NameGoesHere]', $name, $body);
	$body = str_replace('[MessageGoesHere]', $message, $body);
	$body = str_replace('[WebSiteUrl]', $site_url, $body);
	$body = str_replace('[CompanyLogoHere]', $logo, $body);
	$body = str_replace('[LinkGoesHere]', $user_info_link, $body);
	$headers = array('Content-Type: text/html; charset=UTF-8','From: '.$from_name);
		
	// Mail Function
	wp_mail( $to, $subject, $body, $headers );
	
	return true;
}



/**
 * Unfollow a user
 *
 * Makes a user unfollow another user
 *
 * @access      private
 * @since       1.0
 * @param 		int $user_id       - the ID of the user that is doing the unfollowing
 * @param 		int $unfollow_user - the ID of the user that is being unfollowed
 * @return      bool
 */
function fdfp_unfollow_user( $user_id = 0, $unfollow_user = 0 ) {

	do_action( 'fdfp_pre_unfollow_user', $user_id, $unfollow_user );

	// get all IDs that $user_id follows
	$following = fdfp_get_following( $user_id );

	if ( is_array( $following ) && in_array( $unfollow_user, $following ) ) {

		$modified = false;

		foreach ( $following as $key => $follow ) {
			if ( $follow == $unfollow_user ) {
				unset( $following[$key] );
				$modified = true;
			}
		}

		if ( $modified ) {
			if ( update_user_meta( $user_id, '_fdfp_following', $following ) ) {
				fdfp_decrease_followed_by_count( $unfollow_user );
			}
		}

	}

	// get all IDs that follow the user we have just unfollowed so that we can remove $user_id
	$followers = fdfp_get_followers( $unfollow_user );

	if ( is_array( $followers ) && in_array( $user_id, $followers ) ) {

		$modified = false;

		foreach ( $followers as $key => $follower ) {
			if ( $follower == $user_id ) {
				unset( $followers[$key] );
				$modified = true;
			}
		}

		if ( $modified ) {
			update_user_meta( $unfollow_user, '_fdfp_followers', $followers );
		}

	}

	if ( $modified ) {
		do_action( 'fdfp_post_unfollow_user', $user_id, $unfollow_user );
		
        // Mail To User Notification of unfollow
		$check_mail = fdfp_notif_user_on_unfollow($unfollow_user, $user_id);

		if( $check_mail )
			return true;
		else
			return false;
	}

	return false;
}

/**
 * Retrieve following count
 *
 * Gets the total number of users that the specified user is following
 *
 * @access      private
 * @since       1.0
 * @param 		int $user_id - the ID of the user to retrieve a count for
 * @return      int
 */
function fdfp_get_following_count( $user_id = 0 ) {

	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	$following = fdfp_get_following( $user_id );

	$count = 0;

	if ( $following ) {
		$count = count( $following );
	}

	return (int) apply_filters( 'fdfp_get_following_count', $count, $user_id );
}

/**
 * Retrieve follower count
 *
 * Gets the total number of users that are following the specified user
 *
 * @access      private
 * @since       1.0
 * @param 		int $user_id - the ID of the user to retrieve a count for
 * @return      int
 */
function fdfp_get_follower_count( $user_id = 0 ) {

	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}
	$followed_count = get_user_meta( $user_id, '_fdfp_followed_by_count', true );
	$count = 0;
	if ( $followed_count ) {
		$count = $followed_count;
	}

	return (int) apply_filters( 'fdfp_get_follower_count', $count, $user_id );
}



/**
 * Increase follower count
 *
 * Increments the total count for how many users a specified user is followed by
 *
 * @access      private
 * @since       1.0
 * @param 		int $user_id - the ID of the user to increease the count for
 * @return      int
 */

function fdfp_increase_followed_by_count( $user_id = 0 ) {

	do_action( 'fdfp_pre_increase_followed_count', $user_id );

	$followed_count = fdfp_get_follower_count( $user_id );

	if ( $followed_count !== false ) {

		$new_followed_count = update_user_meta( $user_id, '_fdfp_followed_by_count', $followed_count + 1 );

	} else {

		$new_followed_count = update_user_meta( $user_id, '_fdfp_followed_by_count', 1 );

	}

	do_action( 'fdfp_post_increase_followed_count', $user_id );

	return $new_followed_count;
}


/**
 * Decrease follower count
 *
 * Decrements the total count for how many users a specified user is followed by
 *
 * @access      private
 * @since       1.0
 * @param 		int $user_id - the ID of the user to decrease the count for
 * @return      int
 */

function fdfp_decrease_followed_by_count( $user_id = 0 ) {

	do_action( 'fdfp_pre_decrease_followed_count', $user_id );

	$followed_count = fdfp_get_follower_count( $user_id );

	if ( $followed_count ) {

		$count = update_user_meta( $user_id, '_fdfp_followed_by_count', ( $followed_count - 1 ) );

		do_action( 'fdfp_post_increase_followed_count', $user_id );

	}
	return $count;
}


/**
 * Check if a user is following another
 *
 * Increments the total count for how many users a specified user is followed by
 *
 * @access      private
 * @since       1.0
 * @param 		int $user_id       - the ID of the user doing the following
 * @param 		int $followed_user - the ID of the user to check if being followed by $user_id
 * @return      int
 */

function fdfp_is_following( $user_id = 0, $followed_user = 0 ) {

	$following = fdfp_get_following( $user_id );
	$ret = false; // is not following by default
	if ( is_array( $following ) && in_array( $followed_user, $following ) ) {
		$ret = true; // is following
	}
	return (bool) apply_filters( 'fdfp_is_following', $ret, $user_id, $followed_user );

}