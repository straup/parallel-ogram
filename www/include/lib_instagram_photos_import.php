<?php

	loadlib("instagram_api");
	loadlib("instagram_users");
	loadlib("instagram_photos");
	loadlib("instagram_photos_lookup");
	loadlib("storage");
	loadlib("http");

	#################################################################

	function instagram_photos_import_for_user($user, $more=array()){

		$insta_user = instagram_users_get_by_user_id($user['id']);

		$defaults = array(
			'force' => 0,
		);

		$more = array_merge($defaults, $more);

		$method = 'users/self/media/recent';

		$args = array(
			'access_token' => $insta_user['oauth_token'],
		);

		if (isset($more['min_timestamp'])){
			$args['min_timestamp'] = $more['min_timestamp'];
		}

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

				$photo_base = basename($photo_url);
				$photo_secret = str_replace("_7.jpg", "", $photo_base);

				$id = $d['id'];
				list($photo_id, $user_id) = explode("_", $id, 2);

				$photo = instagram_photos_get_by_id($photo_id);

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

				$data = array(
					'id' => $photo_id,
					'user_id' => $user['id'],
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

				# See above

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

	# WORK IN PROGRESS... this will eventually replace all that stuff above

	function instagram_photos_import_api_photo($row, $more=array()){

		$defaults = array(
			'force' => 0,
		);

		$more = array_merge($defaults, $more);

		$photo_url = $row['images']['standard_resolution']['url'];

		# TO DO: put this bit in a function...

		# Is this really a photo secret? Who knows. I can not make heads or
		# tails of Instagram's freakish and overlapping ID schemes...
		# (20120321/straup)

		$photo_base = basename($photo_url);
		$photo_secret = str_replace("_7.jpg", "", $photo_base);

		$id = $row['id'];
		list($photo_id, $ignore) = explode("_", $id, 2);

		$photo = instagram_photos_get_by_id($photo_id);

		$owner_id = $row['user']['id'];
		$insta_user = instagram_users_get_by_id($owner_id);

		if (! $insta_user){
			$insta_user = instagram_users_register_user($owner_id, $row['user']['username']);
		}

		if (! $insta_user){
			return not_okay();
		}

		$user = users_get_by_id($insta_user['user_id']);

		$id_path = storage_id_to_path($photo_id);
		$root_path = "{$GLOBALS['cfg']['instagram_static_path']}{$id_path}/";

		$full_path = "{$root_path}{$photo_id}_{$photo_secret}.jpg";

		if ((file_exists($full_path) && $photo) && (! $more['force'])){

			return okay(array(
				'skipped' => 1
			));
		}

		$rsp = http_get($photo_url);

		if (! $rsp['ok']){
			return $rsp;
		}

		$rsp = storage_write_file($full_path, $rsp['body']);

		if (! $rsp['ok']){
			return $rsp;
		}

		$data = array(
			'id' => $photo_id,
			'user_id' => $user['id'],
			'secret' => $photo_secret,
			'filter' => $row['filter'],
			'created' => $row['created_time'],
			'caption' => $row['caption']['text'],

			# Some day there might be a way to tell whether a person's
			# photos are public or not. Apparently 'links' are generateed
			# whenever you send a photo to another service (like Flickr)
			# which I'm guessing are meant to operate like a casual privacy
			# through obscurity you never knew about... (20120322/straup)

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
			return $rsp;
		}

		return okay(array(
			'photo' => $photo,
			'path' => $full_path
		));

	}

	#################################################################
?>
