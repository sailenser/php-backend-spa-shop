<?php
    include_once('../_fn/system.php');
	include_once('../_fn/tokens.php');
	include_once('../_config.php');
    checkCORS();

	if($_SERVER['REQUEST_METHOD'] !== 'POST'){
		sendHTTPCode(400, 'Bad Request');
		exit();
	}
	
	$response = [];
	$body = json_decode(readPHPInput(), true);
	$login = trim($body['login'] ?? '');
	$password = trim($body['password'] ?? '');

	if($login === '' || $password === ''){
		$response = ['res' => false, 'errors' => 'Enter your username and password'];
	}
	else{
		$users = json_decode(file_get_contents('../_db/users.data'), true);

		if(isset($users[$login]) && $users[$login]['password'] === simpleHash($password)){
			$accessToken = createAccessToken($login, $users[$login]['name'], $users[$login]['roles']);
			$refreshToken = createRefreshToken();
			updateRefreshToken($login, $refreshToken);
			
			$response = [
				'res' => true,
				'accessToken' => $accessToken
			];
		}
		else{
			$response = ['res' => false, 'errors' => 'Incorrect username or password'];
		}
	}

	echo json_encode($response);