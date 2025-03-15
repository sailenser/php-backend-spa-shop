<?php

	include_once('../../_fn/system.php');
	include_once('../../_fn/tokens.php');
	include_once('../../_config.php');
	
	checkCORS();
	
	$response = ['res' => false];
	
	try{
		$token = $_COOKIE['refreshToken'] ?? null;
		$deviceId = readPHPInput();

		if($token && checkRefreshToken($token, $deviceId)){
			$data = getRefreshData($token);
			$login = $data['login'];
			$users = json_decode(file_get_contents('../../_db/users.data'), true);
			$accessToken = createAccessToken($login, $users[$login]['name'], $users[$login]['roles']);
			
			$response = [
				'res' => true,
				'accessToken' => $accessToken
			];
			
			$refreshToken = createRefreshToken();
			updateRefreshToken($login, $refreshToken);
		}
		else{
			//removeRTCookie();
		}
	}
	catch(Exception $e){
		// silence is gold
	}

	echo json_encode($response);