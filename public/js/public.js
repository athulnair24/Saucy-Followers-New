(function( $ ) {
	'use strict';

$(document).ready( function() {
	/*******************************
	follow / unfollow a user
	*******************************/
	$( '.fdfp-follow-links a' ).on('click', function(e) {
		e.preventDefault();

		var $this = $(this);

		if( fdfp_vars.logged_in != 'undefined' && fdfp_vars.logged_in != 'true' ) {
			alert( fdfp_vars.login_required );
			return;
		}

		var data      = {
			action:    $this.hasClass('follow') ? 'follow' : 'unfollow',
			user_id:   $this.data('user-id'),
			follow_id: $this.data('follow-id'),
			nonce:     fdfp_vars.nonce
		};

		$('img.fdfp-ajax').show();

		$.post( fdfp_vars.ajaxurl, data, function(response) {
			if( response == 'success' ) {
				$('.fdfp-follow-links a').toggle();
			} else {
				alert( fdfp_vars.processing_error );
			}
			$('img.fdfp-ajax').hide();
		} );
	});
});

})( jQuery );
