<?php
return array(
	'body' => json_encode(json_decode(file_get_contents( __DIR__ . '/../slswc/activate-success.json' ))),
	'response' => array(
		'code' => 200,
	)
);