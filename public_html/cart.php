<?php

	include_once('_fn/system.php');
	include_once('_fn/cart.php');
	
	checkCORS();
	randomExit(0);
	define('CART_STORAGE_PATH', '_db/carts');

	switch($_SERVER['REQUEST_METHOD']){
		case 'GET':
			$response = actionLoad();
			break;
		case 'POST':
			$response = actionAdd();
			break;
		case 'PUT':
			$response = actionChange();
			break;
		case 'DELETE':
			$isOne = isset($_GET['id']);
			
			if($isOne){
				$response = actionRemove();
			}
			else{
				$response = actionClean();
			}
			break;
		default: 
			sendHTTPCode(400, 'Bad Request');
			exit();
	}
	
	echo json_encode($response);
	
	function actionLoad(){
		$res = [
			'cart' => [],
			'token' => '',
			'needUpdate' => false
		];
		
		$token = $_GET['token'] ?? null;
		$storePath = CART_STORAGE_PATH;
		
		if(!$token || !checkCartToken($token) || !file_exists("$storePath/$token")){
			$res['token'] = generateCartToken($storePath);
			$res['needUpdate'] = true;
		}
		else{
			$res['token'] = $token;
			$cartObj = json_decode(file_get_contents("$storePath/$token"), true);
			$res['cart'] = array_values($cartObj);
		}
		
		return $res;
	}
	
	function actionAdd(){
		$body = json_decode(readPHPInput(), true);
		$token = $body['token'] ?? null;
		$id = $body['id'] ?? null;
		$storePath = CART_STORAGE_PATH;

		if(!$token || !$id || !checkCartToken($token) || !checkInt($id) || !file_exists("$storePath/$token")){
			return false;
		}

		$cart = json_decode(file_get_contents("$storePath/$token"), true);
		
		if(isset($cart[$id])){
			return false;
		}
		
		$cart[$id] = [
			'id' => (int)$id,
			'cnt' => 1
		];
		
		file_put_contents("$storePath/$token", json_encode($cart));
		return true;
	}
	
	function actionRemove(){
		$token = $_GET['token'] ?? null;
		$id = $_GET['id'] ?? null;
		$storePath = CART_STORAGE_PATH;
		
		if(!$token || !$id || !checkCartToken($token) || !checkInt($id) || !file_exists("$storePath/$token")){
			return false;
		}
		
		$cart = json_decode(file_get_contents("$storePath/$token"), true);
		
		if(!isset($cart[$id])){
			return false;
		}
		
		unset($cart[$id]);
		
		file_put_contents("$storePath/$token", json_encode($cart));
		return true;
	}
	
	function actionChange(){
		$body = json_decode(readPHPInput(), true);
		$token = $body['token'] ?? null;
		$id = $body['id'] ?? null;
		$cnt = $body['cnt'] ?? null;
		$storePath = CART_STORAGE_PATH;
		
		if(!$token || !$id || !$cnt || 
			!checkCartToken($token) || !checkInt($id) || !checkInt($cnt) ||
			!file_exists("$storePath/$token")){
			return false;
		}
		
		$cart = json_decode(file_get_contents("$storePath/$token"), true);
		
		if(!isset($cart[$id])){
			return false;
		}
		
		$cart[$id]['cnt'] = (int)$cnt;
		
		file_put_contents("$storePath/$token", json_encode($cart));
		return true;
	}
	
	function actionClean(){
		$res = false;
		$token = $_GET['token'] ?? null;
		$storePath = CART_STORAGE_PATH;
		
		if(!$token || !checkCartToken($token) || !file_exists("$storePath/$token")){
			return false;
		}

		file_put_contents("$storePath/$token", json_encode([]));
		return true;
	}
?>