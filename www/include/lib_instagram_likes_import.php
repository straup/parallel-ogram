<?php

	# http://instagram.com/developer/endpoints/users/#get_users_liked_feed
	# note: this does not appear to have any way to filter on just new
	# stuff...

	#################################################################

	function instragram_likes_import_for_user($user, $more=array()){

		# please write me...

		$method = 'users/self/media/liked';

		$args = array(
			'access_token' => $user['oauth_token'],
		);

	}

	#################################################################
?>
