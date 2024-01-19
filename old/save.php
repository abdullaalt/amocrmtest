<?php

require_once 'db.php';

$db = new DB();

$record = $db->first('ip', 'ip', $_REQUEST['ip']);

if (!$record) $id = $db->insert('ip', ['ip' => $_REQUEST['ip']]);
else{
	$id = $record['id'];
}

$db->insert('stat', [
	'ip_id' => $id,
	'city' => $_REQUEST['city'],
	'gadget' => $_REQUEST['gadget']
]);

