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

		return true;
	}
	return false;
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
		return true;
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