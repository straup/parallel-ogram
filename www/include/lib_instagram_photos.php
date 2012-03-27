<?php

	loadlib("instagram_photos_lookup");

	#################################################################

	function instagram_photos_privacy_map($string_keys){

		$map = array(
			0 => 'not public',
			1 => 'public'
		);

		if ($string_keys){
			$map = array_flip($map);
		}

		return $map;
	}

	#################################################################

	function instagram_photos_get_by_id($photo_id){

		$lookup = instagram_photos_lookup_get_by_photo_id($photo_id);

		if (! $lookup){
			return null;
		}

		$user = users_get_by_id($lookup['user_id']);
		$cluster_id = $user['cluster_id'];

		$enc_photo = AddSlashes($photo_id);

		$sql = "SELECT * FROM InstagramPhotos WHERE id='{$enc_photo}'";
		return db_single(db_fetch_users($cluster_id, $sql));
	}

	#################################################################

	function instagram_photos_get_by_short_code($code){

		$lookup = instagram_photos_lookup_get_by_short_code($code);

		if (! $lookup){
			return null;
		}

		$user = users_get_by_id($lookup['user_id']);
		$cluster_id = $user['cluster_id'];

		$enc_photo = AddSlashes($lookup['photo_id']);

		$sql = "SELECT * FROM InstagramPhotos WHERE id='{$enc_photo}'";
		return db_single(db_fetch_users($cluster_id, $sql));
	}

	#################################################################

	function instagram_photos_for_user($user, $more=array()){

		$cluster_id = $user['cluster_id'];

		$enc_user = AddSlashes($user['id']);

		$sql = "SELECT * FROM InstagramPhotos WHERE user_id='{$enc_user}'";

		if (isset($more['filter'])){
			$enc_filter = AddSlashes($more['filter']);
			$sql .= " AND filter='{$enc_filter}'";
		}

		$sql .= " ORDER BY created DESC";

		$rsp = db_fetch_paginated_users($cluster_id, $sql, $more);
		return $rsp;
	}

	#################################################################

	function instagram_photos_add_photo($photo){

		$user = users_get_by_id($photo['user_id']);
		$cluster_id = $user['cluster_id'];

		$photo['imported'] = time();

		$insert = array();

		foreach ($photo as $k => $v){
			$insert[$k] = AddSlashes($v);
		}

		$rsp = db_insert_users($cluster_id, 'InstagramPhotos', $insert);

		if (! $rsp['ok']){
			return $rsp;
		}

		$rsp_lookup = instagram_photos_lookup_add_photo($photo['id'], $user['id']);

		if (! $rsp_lookup){
			return not_okay("photo row added; lookup failed: {$rsp_lookup['error']}");
		}

		$rsp['photo'] = $photo;
		return $rsp;
	}

	#################################################################

	function instagram_photos_update_photo($photo, $update){

		$user = users_get_by_id($photo['user_id']);
		$cluster_id = $user['cluster_id'];

		$insert = array();

		foreach ($update as $k => $v){
			$insert[$k] = AddSlashes($v);
		}

		$enc_photo = AddSlashes($photo['id']);
		$where = "id='{$enc_photo}'";

		$rsp = db_update_users($cluster_id, 'InstagramPhotos', $insert, $where);

		if ($rsp['ok']){

			$photo = array_merge($photo, $update);
			$rsp['photo'] = $photo;
		}

		return $rsp;
	}

	#################################################################
?>
