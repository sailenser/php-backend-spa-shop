<?php
	
	include_once('../_fn/system.php');
	include_once('../_fn/tokens.php');
	include_once('../_config.php');

	checkCORS();
	
	if($_SERVER['REQUEST_METHOD'] !== 'GET'){
		sendHTTPCode(400, 'Bad Request');
		exit();
	}	
	
	if(isset($_GET['sleep'])){
		sleep(1);
	}
	
	$response = ['res' => true];

	try{
		$tokenData = readTokenDataOr401();
	}
	catch(Exception $e){
		sendHTTPCode(401, 'Unauthorized');
		exit();
	}

	echo json_encode($response);