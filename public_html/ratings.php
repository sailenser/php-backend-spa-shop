<?php

	include_once('_fn/system.php');
	include_once('_fn/users.php');
	include_once('_fn/tokens.php');
	
	checkCORS();
	randomExit(0);
	const RATING_EDIT_ROLES = ['auditor' => true];
	const RATINGS_STORAGE = '_db/ratings.data';
	
	switch($_SERVER['REQUEST_METHOD']){
		case 'GET':
			$response = actionGetRating();
			break;
		case 'PUT':
			$response = actionChangeMark();
			break;
		default: 
			sendHTTPCode(400, 'Bad Request');
			exit();
	}
	
	echo json_encode($response);
	
	function actionGetRating(){
		if(!isset($_GET['id'])){
			sendHTTPCode(400, 'Bad Request');
			exit();
		}
		
		$id = (int)$_GET['id'];
		$ratings = json_decode(file_get_contents(RATINGS_STORAGE), true);
		
		if(!isset($ratings[$id])){
			return ['count' => 0, 'average' => 0, 'your' => null];
		}
		
		$tokenData = readTokenDataOrNull();
		$your = null;
		$stat = ['count' => 0, 'sum' => 0];
		
		foreach($ratings[$id]['marks'] as $login => $value){
			$stat['count']++;
			$stat['sum'] += $value;
			
			if($tokenData !== null && $login === $tokenData['login']){
				$your = $value;
			}
		}

		return [
			'count' => $stat['count'], 
			'average' => ($stat['sum'] / $stat['count']),
			'your' => $your
		];
	}
	
	function actionChangeMark(){
		$tokenData = _readTokenWithCheckRoles(RATING_EDIT_ROLES);
		$body = json_decode(readPHPInput(), true);
		
		if(!isset($body['id']) || !isset($body['mark'])){
			sendHTTPCode(400, 'Bad Request');
			exit();
		}
		
		// конечно, в реальном api ещё нужно проверить наличие товара с таким id
		$id = (int)$body['id'];
		$mark = max(1, min((int)$body['mark'], 5));
		
		$ratings = json_decode(file_get_contents(RATINGS_STORAGE), true);
		
		if(!isset($ratings[$id])){
			$ratings[$id] = ['marks' => []];
		}
		
		$ratings[$id]['marks'][$tokenData['login']] = $mark;
		file_put_contents(RATINGS_STORAGE, json_encode($ratings));
		return true;
	}
	
	function _readTokenWithCheckRoles($allowedRoles){
		try{
			$tokenData = readTokenDataOr401();
			
			if(!checkUserRole($tokenData['roles'], RATING_EDIT_ROLES)){
				sendHTTPCode(403, 'Forbidden');
				exit();
			}
		}
		catch(Exception $e){
			sendHTTPCode(401, 'Unauthorized');
			exit();
		}
		
		return $tokenData;
	}
?>