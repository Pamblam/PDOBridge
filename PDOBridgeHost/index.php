<?php

// must match the key used in the client
define('PDOB_KEY', 'My PDOBridge Key !@#');

// refuse all requests older than 15 seconds
define('PDOB_MAX_AGE', 15); 

$results = array(
	'error' => null,
	'data' => null
);

if(empty($_POST) || empty($_POST['data'])){
	error('No data provided.');
}

$decrypted = decrypt($_POST['data']);
if(false === $decrypted) error('Could not decrypt parameters. Client may be using wrong key.');
$params = json_decode($decrypted, true);
if(false === $params) error('Could not decode parameters. Client may be using wrong key.');

$required_params = ['created', 'sql', 'params', 'dsn', 'user', 'pass'];

foreach($required_params as $key){
	if(!array_key_exists($key, $params)){
		error("Missing parameter: $key");
	}
}

$earliest_created_time = time() - PDOB_MAX_AGE;
if($params['created'] < $earliest_created_time){
	error('This request has expired.');
}

$pdo = null;
try{
	$pdo = new PDO($params['dsn'], $params['user'], $params['pass']);
}catch(Exception $e){
	error($e->getMessage());
}

if(empty($pdo)) error('Unable to create PDO instance.');
	
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try{
	$stmt = $pdo->prepare($params['sql']);
	$stmt->execute($params['params']);
}catch(Exception $e){
	error($e->getMessage());
}
if(empty($stmt)) error('Unable to create PDO statement.');

$is_select = !!preg_match('/^select\s/', trim(strtolower($params['sql'])));
if($is_select){
	try{
		$results['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
	}catch(Exception $e){
		error($e->getMessage());
	}
}else{
	$results['data'] = [];
}

output();

function encrypt($string){ 
	$encryption_iv = '1234567891011121'; 
	$encryption_key = PDOB_KEY;
	return openssl_encrypt($string, "AES-128-CTR", $encryption_key, 0, $encryption_iv); 
}

function decrypt($string){
	$decryption_iv = '1234567891011121'; 
	$decryption_key = PDOB_KEY; 
	return openssl_decrypt($string, "AES-128-CTR", $decryption_key, 0, $decryption_iv);
}

function error($msg){
	$GLOBALS['results']['data'] = null;
	$GLOBALS['results']['error'] = $msg;
	output();
}

function output(){
	header('Content-Type: application/json');
	
	// Encode and encrypt data
	$data = $GLOBALS['results']['data'];
	if($data !== null){
		$encoded = json_encode($data);
		if(false === $encoded){
			$data = null;
			$results['error'] = 'Could not encode data.';
		}else{
			$encrypted = encrypt($encoded); 
			if(false === $encrypted){
				$data = null;
				$results['error'] = 'Could not encrypt data.';
			}
			$GLOBALS['results']['data'] = $encrypted;
		}
	}
	
	echo json_encode($GLOBALS['results']);
	exit;
}