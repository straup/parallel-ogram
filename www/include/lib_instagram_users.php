<?php

	#################################################################

	function instagram_users_get_by_oauth_token($token){

		$enc_token = AddSlashes($token);

		$sql = "SELECT * FROM InstagramUsers WHERE oauth_token='{$enc_token}'";
		return db_single(db_fetch($sql));
	}

	#################################################################

	function instagram_users_get_by_user_id($user_id){

		$enc_id = AddSlashes($user_id);

		$sql = "SELECT * FROM InstagramUsers WHERE user_id='{$enc_id}'";
		return db_single(db_fetch($sql));
	}

	#################################################################

	function instagram_users_create_user($user){

		$hash = array();

		foreach ($user as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$rsp = db_insert('InstagramUsers', $hash);

		if (! $rsp['ok']){
			return null;
		}

		# $cache_key = "instagram_user_{$user['instagram_id']}";
		# cache_set($cache_key, $user, "cache locally");

		$cache_key = "instagram_user_{$user['id']}";
		cache_set($cache_key, $user, "cache locally");

		return $user;
	}

	#################################################################

	function instagram_users_update_user(&$instagram_user, $update){

		$hash = array();
		
		foreach ($update as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$enc_id = AddSlashes($instagram_user['user_id']);
		$where = "user_id='{$enc_id}'";

		$rsp = db_update('InstagramUsers', $hash, $where);

		if ($rsp['ok']){

			$instagram_user = array_merge($instagram_user, $update);

			# $cache_key = "instagram_user_{$instagram_user['instagram_id']}";
			# cache_unset($cache_key);

			$cache_key = "instagram_user_{$instagram_user['user_id']}";
			cache_unset($cache_key);
		}

		return $rsp;
	}

	#################################################################

?>
