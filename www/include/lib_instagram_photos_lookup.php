<?php

	#################################################################

	function instagram_photos_lookup_get_by_photo_id($photo_id){

		$enc_photo = AddSlashes($photo_id);
		$sql = "SELECT * FROM InstagramPhotosLookup WHERE photo_id='{$enc_photo}'";

		return db_single(db_fetch($sql));
	}

	#################################################################

	function instagram_photos_lookup_get_by_short_code($code){

		$enc_code = AddSlashes($code);
		$sql = "SELECT * FROM InstagramPhotosLookup WHERE short_code='{$enc_code}'";

		return db_single(db_fetch($sql));
	}

	#################################################################

	function instagram_photos_lookup_add_photo($photo_id, $user_id){

		$insert = array(
			'photo_id' => AddSlashes($photo_id),
			'user_id' => AddSlashes($user_id),
		);

		return db_insert('InstagramPhotosLookup', $insert);
	}

	#################################################################

	function instagram_photos_lookup_update($lookup, $update){

		$enc_photo = AddSlashes($lookup['photo_id']);
		$where = "photo_id='{$enc_photo}'";

		$insert = array();

		foreach ($update as $k => $v){
			$insert[$k] = AddSlashes($v);
		}

		return db_update('InstagramPhotosLookup', $insert, $where);
	}

	#################################################################
?>
