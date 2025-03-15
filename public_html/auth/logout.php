<?php
	
	include_once('../_config.php');
	include_once('../_fn/system.php');
	include_once('../_fn/tokens.php');

	checkCORS();
		
	if($_SERVER['REQUEST_METHOD'] !== 'GET'){
		sendHTTPCode(400, 'Bad Request');
		exit();
	}	
	
	removeRTCookie();
	echo json_encode(['res'=>true]);