<?php

require_once 'db.php';

$db = new DB();

$records = $db->get('stat', '1', 1);

$result = [];

$result['city'] = [];

foreach ($records as $record){
	
	$pieces = explode(':', $record['date']);
	
	if (!isset($result['time'][$pieces[0]])){
		$result['time'][$pieces[0]] = [];
	}
	
	$result['time'][$pieces[0]][$record['ip_id']] = 1;
	
	if (isset($result['city'][$record['city']])){
		$result['city'][$record['city']]++;
	}else{
		$result['city'][$record['city']] = 1;
	}
	
}
die(json_encode($result));