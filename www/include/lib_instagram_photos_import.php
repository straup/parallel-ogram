<?php

	loadlib("instagram_api");
	loadlib("instagram_photos");
	loadlib("storage");
	loadlib("http");

	#################################################################

	# http://instagram.com/developer/endpoints/users/#get_users_media_recent
	# TO DO: account for and set 'min_timestamp' parameter
 
	function instagram_photos_import_for_user($user, $more=array()){

		$defaults = array(
			'force' => 0
		);

		$more = array_merge($defaults, $more);

		$method = 'users/self/media/recent';

		$args = array(
			'access_token' => $user['oauth_token'],
		);

		$count_imported = 0;
		$count_skipped = 0;
		$count_failed = 0;

		$ok = 1;

		while ($ok){

			$rsp = instagram_api_call($method, $args);

			if (! $rsp['ok']){
				return $rsp;
			}

			$pg = $rsp['rsp']['pagination'];

			$to_fetch = array();

			foreach ($rsp['rsp']['data'] as $d){

				$photo_url = $d['images']['standard_resolution']['url'];

				# Is this really a photo secret? Who knows. I can not make heads or
				# tails of Instagram's freakish and overlapping ID schemes...
				# (20120321/straup)

				$photo_base = basename($photo_url);
				$photo_secret = str_replace("_7.jpg", "", $photo_base);

				$id = $d['id'];
				list($photo_id, $user_id) = explode("_", $id, 2);

				$id_path = storage_id_to_path($photo_id);
				$root_path = "{$GLOBALS['cfg']['instagram_static_path']}{$id_path}/";

				$full_path = "{$root_path}{$photo_id}_{$photo_secret}.jpg";

				if ((file_exists($full_path)) && (! $more['force'])){
					$count_skipped ++;
					continue;
				}

				$rsp = http_get($photo_url);

				if (! $rsp['ok']){
					log_rawr("failed to retrieve '{$photo_url}' : {$rsp['error']}");

					$count_failed ++;
					continue;
				}

				$rsp = storage_write_file($full_path, $rsp['body']);

				if (! $rsp['ok']){
					log_rawr("failed to write photo to disk: {$rsp['error']}");

					$count_failed ++;
					continue;
				}

				$data = array(
					'id' => $photo_id,
					'user_id' => $user['user_id'],
					'secret' => $photo_secret,
					'filter' => $d['filter'],
					'created' => $d['created_time'],

					# punting on these for now and may drop them
					# altogether... (20120321/straup)

					# 'caption' => $d['caption'],
				);

				if ($photo = instagram_photos_get_by_id($photo_id)){
					# $rsp = instagram_photos_update_photo($photo, $data);
				}

				else {
					$rsp = instagram_photos_add_photo($data);
				}

				if (! $rsp['ok']){
					log_rawr("failed to add photo to the database: {$rsp['error']}");

					$count_failed ++;
					continue;
				}

				echo "{$full_path}\n";

				$count_imported ++;
			}

			$ok = ($pg['next_max_id']) ? 1 : 0;

			$args['max_id'] = $pg['next_max_id'];
		}

		$rsp = array(
			'count_imported' => $count_imported,
			'count_skipped' => $count_skipped,
			'count_failed' => $count_failed,
		);

		return ($count_failed) ? not_okay($rsp) : okay($rsp);
	}

	#################################################################

?>
