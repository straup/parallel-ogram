<?php

	loadlib("instagram_api");
	loadlib("instagram_photos");
	loadlib("http");

	#################################################################

	function instagram_photos_import_for_user($user, $more=array()){

		$defaults = array(
			'force' => 0
		);

		$more = array_merge($defaults, $more);

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

				$photo_url = $d['images']['standard_resolution']['url'];

				# Is this really a photo secret? Who knows. I can not make heads or
				# tails of Instagram's freakish and overlapping ID schemes...
				# (20120321/straup)

				$photo_base = basename($photo_url);
				$photo_secret = str_replace("_7.jpg", "", $photo_base);

				$id = $d['id'];
				list($photo_id, $user_id) = explode("_", $id, 2);

				$id_path = instagram_photos_id_to_path($photo_id);
				$root_path = "{$GLOBALS['cfg']['instagram_static_path']}{$id_path}/";

				$full_path = "{$root_path}{$photo_id}_{$photo_secret}.jpg";

				if (! file_exists($root_path)){
					mkdir($root_path, 0755, true);
				}

				$rsp = http_get($photo_url);

				if (! $rsp['ok']){
					echo "failed to retrieve '{$photo_url}' : {$rsp['error']}\n";
					continue;
				}

				$fh = fopen($full_path, "wb");
				fwrite($fh, $rsp['body']);
				fclose($fh);

				echo "{$full_path}\n";

				$count_imported ++;
			}

			$ok = ($pg['next_max_id']) ? 1 : 0;

			$args['max_id'] = $pg['next_max_id'];
		}

		return okay(array(
			'count_imported' => $count_imported
		));

	}

	#################################################################

	function _instagram_photos_import_multi($reqs){

	}

?>
