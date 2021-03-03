<?php


require "./PDOBridgeClient/PDObc.php";

// database to connect to
$database = 'test';

// mysql host, from the perspective of the server script.
// so 'lcoalhost' or '127.0.0.1' would refer to the server
// itself, rather than the client.
$host = 'localhost';

// database user
$user = 'root';

// database password
$pass = '...';

// The URL where the host script is located
$pdob_host = getDirectoryRootURL().'PDOBridgeHost/';

// The encrytion key that is used to encrypt and secure
// transactions between servers. This must match the key that is 
// hardcoded into the server script.
$pdob_key = 'My PDOBridge Key !@#';

// The dsn that the server will use to connenct to the DB.
$dsn = "mysql:dbname=$database;host=$host";

// Example usage.....
$pdob = new PDOBc($dsn, $user, $pass, $pdob_host, $pdob_key);
$q = $pdob->prepare("select * from sample_table where age = ?");
$q->execute([138]);

while($res = $q->fetch(PDO::FETCH_ASSOC)){
	echo $res['name']." is ".$res['age']." years old<br>";
}

function getDirectoryRootURL(){
	$here = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$parts = explode("/", $here);
	array_pop($parts);
	return implode("/", $parts)."/";
}