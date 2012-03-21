<?php

	loadlib("instagram_api");
	loadlib("instagram_photos");

	function instagram_photos_import_for_user($user){

		$method = 'users/self/media/recent';

		$args = array(
			'access_token' => $user['oauth_token'],
		);

		$ok = 1;
		$count_imported = 0;

		while ($ok){

			$rsp = instagram_api_call($method, $args);

			if (! $rsp['ok']){
				return $rsp;
			}

			$pg = $rsp['rsp']['pagination'];

			foreach ($rsp['rsp']['data'] as $d){
				# echo  $d['images']['standard_resolution']['url'] . "<br />";

				$id = $d['id'];
				list($photo_id, $user_id) = explode("_", $id, 2);

				$id_path = instagram_photos_id_to_path($photo_id);
				$full_path = "{$GLOBALS['cfg']['instagram_static_path']}{$id_path}/{$photo_id}.jpg";

				$count_imported ++;
			}

			$ok = ($pg['next_max_id']) ? 1 : 0;

			$args['max_id'] = $pg['next_max_id'];
		}

		return okay(array(
			'count_imported' => $count_imported
		));

	}

?>
