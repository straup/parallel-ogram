<?php

	#################################################################

	function instagram_backups_type_map($string_keys=0){

		$map = array(
			0 => 'photos',
			1 => 'likes',
		);

		if ($string_keys){
			$map = array_flip($map);
		}

		return $map;
	}

	#################################################################

	function instagram_backups_for_user($user){

		$enc_user = AddSlashes($user['id']);

		$sql = "SELECT * FROM InstagramBackups WHERE user_id='{$enc_user}'";
		$rsp = db_fetch($sql);

		return $rsp;
	}

	#################################################################

	function instagram_backups_register_user($user, $types=null){

		if (! $types){
			$map = instagram_backups_type_map();
			$types = array_keys($map);
		}

		$rsp = array();

		foreach ($types as $type_id){

			$backup = array(
				'user_id' => $user['id'],
				'type_id' => $type_id
			);

			$_rsp = instagram_backups_add($backup);
			$rsp[$type_id] = (($_rsp['ok']) || ($rsp['error_code'] == 1062)) ? 1 : 0;
		}

		return okay($rsp);
	}

	#################################################################

	function instagram_backups_disable_user($user){

		$enc_user = AddSlashes($user['id']);

		$sql = "UPDATE InstagramBackups SET disabled=1 WHERE user_id='{$enc_user}'";
		$rsp = db_write($sql);

		return $rsp;
	}

	#################################################################

	function instagram_backups_reenable_user($user){

		$enc_user = AddSlashes($user['id']);

		$sql = "UPDATE InstagramBackups SET disabled=0 WHERE user_id='{$enc_user}'";
		$rsp = db_write($sql);

		return $rsp;
	}

	#################################################################

	function instagram_backups_add($backup){

		$backup['created'] = time();

		$insert = array();

		foreach ($backup as $k => $v){
			$insert[$k] = AddSlashes($v);
		}

		$rsp = db_insert('InstagramBackups', $insert);

		if ($rsp['ok']){
			$rsp['backup'] = $backup;
		}

		return $rsp;
	}

	#################################################################

	function instagram_backups_update($backup, $update){

		$update['last_update'] = time();

		$insert = array();
		
		foreach ($update as $k => $v){
			$insert[$k] = AddSlashes($v);
		}

		$enc_user = AddSlashes($backup['user_id']);
		$enc_type = AddSlashes($backup['type_id']);

		$where = "user_id='{$enc_user}' AND type_id='{$enc_type}'";

		$rsp = db_update('InstagramBackups', $insert, $where);
		return $rsp;
	}

	#################################################################
?>
