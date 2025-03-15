<?php

	function sendHTTPCode($code, $text){
		header("{$_SERVER['SERVER_PROTOCOL']} $code $text");
	}
	
	function randomExit($level = 1){
		if(mt_rand(1, 100) <= $level){
			sendHTTPCode(500, 'Internal Server Error');
			exit();
		}
	}
	
	function checkInt($some){
		return (bool)preg_match('/^[0-9]+$/', $some);
	}
	
	function readPHPInput(){
		return trim(file_get_contents('php://input'));
	}
	
	function simpleHash($str){
		return hash('sha256', $str . SALT);
	}
	
	function checkCORS(){
		if (isset($_SERVER['HTTP_ORIGIN']) && 
			($_SERVER['HTTP_ORIGIN'] === 'http://localhost:5174' ||
            $_SERVER['HTTP_ORIGIN'] === 'http://localhost:5175' ||
			$_SERVER['HTTP_ORIGIN'] === 'http://localhost:8080' ||
			$_SERVER['HTTP_ORIGIN'] === 'http://localhost:8081' ||
			$_SERVER['HTTP_ORIGIN'] === 'https://localhost:3000' ||
			$_SERVER['HTTP_ORIGIN'] === 'https://localhost:8080' ||
			$_SERVER['HTTP_ORIGIN'] === 'https://localhost:8081'
			)) {
			header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
			header('Access-Control-Allow-Credentials: true');
			header('Access-Control-Max-Age: 86400');    // cache for 1 day
			header('Content-Type: text/html; charset=UTF-8');
		}
		
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])){
				header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
			}
				         
			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])){
				header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
			}
			
			exit();
		}
	}