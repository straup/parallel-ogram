<?php

	#################################################################

	function instagram_push_topic_map($string_keys=0){

		$map = array(
			0 => 'user',
			1 => 'tag',
			2 => 'geography',
			3 => 'location',
		);

		if ($string_keys){
			$map = array_flip($map);
		}

		return $map;
	}

	#################################################################
?>
