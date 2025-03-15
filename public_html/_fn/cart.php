<?php

	function generateCartToken($storePath){
		$token = hash('sha256', mt_rand(1, 1000000) . time());
		
		while(file_exists("$storePath/$token")){
			$token = hash('sha256', mt_rand(1, 1000000) . time());
		}
		
		file_put_contents("$storePath/$token", json_encode([]));
		return $token;
	}
	
	function checkCartToken($token){
		return (bool)preg_match('/^[a-z0-9]{64,64}$/', $token);
	}