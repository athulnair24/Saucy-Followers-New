<?php

/**
 * Outputs the follow / unfollow links
 *
 * @access      public
 * @since       1.0
 * @param 	    int $user_id - the ID of the user to display follow / unfollow links for
 * @return      void
 */
function fdfp_follow_unfollow_links( $follow_id = null ) {

	echo fdfp_get_follow_unfollow_links( $follow_id );
}


/**
 * Retrieves the follow / unfollow links
 *
 * @access      public
 * @since       1.0
 * @param 	    int $user_id - the ID of the user to display follow / unfollow links for
 * @return      string
 */
function fdfp_get_follow_unfollow_links( $follow_id = null ) {

	global $user_ID;

	if( empty( $follow_id ) )
		return;

	if( ! is_user_logged_in() )
		return;

	if ( $follow_id == $user_ID )
		return;

	ob_start(); ?>
	<div class="fdfp-follow-links">
		<?php if ( fdfp_is_following( $user_ID, $follow_id ) ) { ?>
			<a href="#" class="unfollow followed" data-user-id="<?php echo $user_ID; ?>" data-follow-id="<?php echo $follow_id; ?>"><?php _e( 'unfollow', 'fdfp' ); ?></a>
			<a href="#" class="follow" style="display:none;" data-user-id="<?php echo $user_ID; ?>" data-follow-id="<?php echo $follow_id; ?>"><?php _e( 'follow', 'fdfp' ); ?></a>
		<?php } else { ?>
			<a href="#" class="follow" data-user-id="<?php echo $user_ID; ?>" data-follow-id="<?php echo $follow_id; ?>"><?php _e( 'follow', 'fdfp' ); ?></a>
			<a href="#" class="followed unfollow" style="display:none;" data-user-id="<?php echo $user_ID; ?>" data-follow-id="<?php echo $follow_id; ?>"><?php _e( 'unfollow', 'fdfp' ); ?></a>
		<?php } ?>
		<img src="<?php echo plugin_dir_url( __FILE__ ); ?>../../public/img/loading.gif" class="fdfp-ajax" style="display:none;"/>
	</div>
	<?php
	return ob_get_clean();
}