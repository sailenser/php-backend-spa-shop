<?php

	include_once('_fn/system.php');
	include_once('_fn/tokens.php');
	
	checkCORS();
	
	if($_SERVER['REQUEST_METHOD'] !== 'GET'){
		sendHTTPCode(400, 'Bad Request');
		exit();
	}
	
	try{
		$tokenData = readTokenDataOr401();
		$response['orders'] = [1,2,3,4,5];
	}
	catch(Exception $e){
		sendHTTPCode(401, 'Unauthorized');
		exit();
	}

	echo json_encode($response);
?>