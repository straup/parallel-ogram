<?php

	loadlib("instagram_api");
	loadlib("instagram_photos");
	loadlib("instagram_photos_lookup");
	loadlib("storage");
	loadlib("http");

	#################################################################

	# http://instagram.com/developer/endpoints/users/#get_users_liked_feed

	# note: this does not appear to have any way to filter on just new
	# stuff...

	function instagram_likes_import_for_user($user, $more=array()){

		$defaults = array(
			'force' => 0,
		);

		$more = array_merge($defaults, $more);

		$args = array(
			'access_token' => $insta_user['oauth_token'],
		);

		$insta_user = instagram_users_get_by_user_id($user['id']);

		$method = 'users/self/media/liked';

		$args = array(
			'access_token' => $insta_user['oauth_token'],
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

				# TO DO: merge all this with photo import
				# TO DO: create 'likes' entry (and db tables)
				# TO DO: create new users sans-token
	
				$photo_url = $d['images']['standard_resolution']['url'];

				echo $photo_url . "\n";

				# TO DO: put this in a library (see also: _photos_import.php)

				$photo_base = basename($photo_url);
				$photo_secret = str_replace("_7.jpg", "", $photo_base);

				$id = $d['id'];
				list($photo_id, $ignore) = explode("_", $id, 2);

				$photo = instagram_photos_get_by_id($photo_id);

				$owner_id = $d['user']['id'];
				$owner = instagram_users_get_by_id($owner_id);

				if (! $owner){
					$owner = instagram_users_register_user($owner_id, $d['user']['username']);
				}

				$id_path = storage_id_to_path($photo_id);
				$root_path = "{$GLOBALS['cfg']['instagram_static_path']}{$id_path}/";

				$full_path = "{$root_path}{$photo_id}_{$photo_secret}.jpg";

				if ((file_exists($full_path) && $photo) && (! $more['force'])){

					log_rawr("skipping photo ID #{$photo_id}, {$full_path} already exists");
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

				# see this: it's the photo row, not the 'like' row

				# TO DO: put me in a function ...

				$data = array(
					'id' => $photo_id,
					'user_id' => $owner['user_id'],
					'secret' => $photo_secret,
					'filter' => $d['filter'],
					'created' => $d['created_time'],
					'caption' => $d['caption']['text'],
					'perms' => 0,
				);

				if ($loc = $d['location']){
					$data['latitude'] = $loc['latitude'];
					$data['longitude'] = $loc['longitude'];
					$data['place_id'] = $loc['id'];
				}

				if ($photo){
					$rsp = instagram_photos_update_photo($photo, $data);
				}

				else {
					$rsp = instagram_photos_add_photo($data);
				}

				if ($link = $d['link']){

					$short_code = basename($link);

					$lookup = instagram_photos_lookup_get_by_photo_id($photo_id);

					if ($lookup['short_code'] != $short_code){
						$update = array('short_code' => $short_code);
						instagram_photos_lookup_update($lookup, $update);
					}
				}

				if (! $rsp['ok']){
					log_rawr("failed to add photo to the database: {$rsp['error']}");

					$count_failed ++;
					continue;
				}

				log_rawr("imported {$full_path}");

				$count_imported ++;
			}

			$ok = ($pg['next_max_like_id']) ? 1 : 0;

			$args['max_like_id'] = $pg['next_max_like_id'];
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
