<?php

	loadlib("instagram_photos");

	#################################################################

	function instagram_likes_has_liked_photo($photo, $viewer_id){

		$user = users_get_by_id($viewer_id);
		$cluster_id = $user['cluster_id'];

		$enc_photo = AddSlashes($photo['id']);
		$enc_user = AddSlashes($user['id']);

		$sql = "SELECT * FROM InstagramLikes WHERE user_id='{$enc_user}' AND photo_id='{$enc_photo}'";
		return db_single(db_fetch_users($cluster_id, $sql));
	}

	#################################################################

	function instagram_likes_for_user($user, $more=array()){

		$cluster_id = $user['cluster_id'];

		$enc_user = AddSlashes($user['id']);

		$sql = "SELECT * FROM InstagramLikes WHERE user_id='{$enc_user}'";
		$sql .= " ORDER BY photo_id DESC";

		$rsp = db_fetch_paginated_users($cluster_id, $sql, $more);

		if (! $rsp['ok']){
			return $rsp;
		}

		$photos = array();

		foreach ($rsp['rows'] as $row){
			$photo = instagram_photos_get_by_id($row['photo_id']);
			$photo['owner'] = users_get_by_id($row['owner_id']);

			$photos[] = $photo;
		}

		$rsp['rows'] = $photos;
		return $rsp;
	}

	#################################################################

	function instagram_likes_add_photo($photo, $user){

		$cluster_id = $user['cluster_id'];

		$like = array(
			'photo_id' => $photo['id'],
			'owner_id' => $photo['user_id'],
			'user_id' => $user['id'],
			'created' => time(),
		);

		$insert = array();

		foreach ($like as $k => $v){
			$insert[$k] = $v;
		}

		$rsp = db_insert_users($cluster_id, 'InstagramLikes', $insert);

		if ($rsp['ok']){
			$rsp['like'] = $like;
		}

		return $rsp;
	}

	#################################################################

?>
