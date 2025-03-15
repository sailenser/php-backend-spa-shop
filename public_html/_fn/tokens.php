<?php

	const SECRET_KEY = 'th4984$545$%6657g][bl=533DGGRh3pttyrHRR3j43potj';
	const REFRESH_STORAGE = __DIR__ . '/../_db/refresh.data'; // shit code
	
	function readTokenDataOrNull(){
		$token = readToken();
		
		if($token){
			$tokenData = getTokenData($token);
			
			if((int)$tokenData['exp'] <= time()){
				return null;
			}
			
			return $tokenData;
		}
		else{
			return null;
		}
	}
	
	function readTokenDataOr401(){
		$token = readToken();
		
		if($token){
			$tokenData = getTokenData($token);
			
			if((int)$tokenData['exp'] <= time()){
				throw new Exception('expire');
			}
			
			return $tokenData;
		}
		else{
			throw new Exception('no token');
		}
	}
	
	function readToken(){
		$headers = apache_request_headers();

		if(isset($headers['Authorization'])){
			$token = $headers['Authorization'];
		}
		else if(isset($headers['authorization'])){
			$token = $headers['authorization'];
		}
		else{
			$token = null;
		}

		$schema = 'Bearer ';
		
		if(strpos($token, $schema) === 0){
			$token = substr($token, strlen($schema));
		}
		else{
			$token = null;
		}
		
		return $token;
	}

	function getTokenData($token){
		$parts = explode('.', $token);

		if(count($parts) !== 3 || trim($parts[0]) === '' || trim($parts[1]) === '' || trim($parts[2]) === ''){
			throw new Exception('incorrect token format');
		}

		$header = json_decode(base64_decode($parts[0]), true);
		$payload = json_decode(base64_decode($parts[1]), true);

		if($parts[2] !== signToken($parts[0], $parts[1])){
			throw new Exception('incorrect sign');
		}

		return $payload;
	}

	function packTokenData($data){
		$headers = base64_encode(json_encode(['typ' => 'JWT', 'alg' => 'SHA256']));
		$payload = base64_encode(json_encode($data));
		return $headers . '.' . $payload . '.' . signToken($headers, $payload);
	}

	function signToken($headers, $payload){
		return hash('sha256', $headers . '.' . $payload . '.' . SECRET_KEY);
	}
	
	function createAccessToken($login, $name, $roles){
		$iat = time();
		$exp = $iat + ACCESS_EXP;
		return packTokenData([
			'login' => $login,
			'name' => $name,
			'roles' => $roles,
			'exp' => $exp,
			'iat' => $iat
		]);
	}
	
	function createRefreshToken(){
		return substr(bin2hex(random_bytes(128)), 0, 64);
	}
	
	function checkRefreshToken($token){
		try{
			$tokens = json_decode(file_get_contents(REFRESH_STORAGE), true);
		}
		catch(Exception $e){
			$tokens = [];
		}
		
		return isset($tokens[$token]) && $tokens[$token]['exp'] > time();
	}
	
	function getRefreshData($token){
		try{
			$tokens = json_decode(file_get_contents(REFRESH_STORAGE), true);
		}
		catch(Exception $e){
			$tokens = [];
		}
		
		return $tokens[$token] ?? null;
	}
	
	function updateRefreshToken($login, $token){
		try{
			$tokens = json_decode(file_get_contents(REFRESH_STORAGE), true);
		}
		catch(Exception $e){
			$tokens = [];
		}
		
		$now = time();

		foreach($tokens as $key => $info){
			if($info['login'] === $login || $info['exp'] < $now){
				unset($tokens[$key]);
			}
		}
		
		$tokens[$token] = ['login' => $login, 'exp' => $now + REFRESH_EXP];
		setRTCookie($token, $tokens[$token]['exp'] + 60);
		file_put_contents(REFRESH_STORAGE, json_encode($tokens));
	}
	
	function setRTCookie($token, $exp){
        try{
            //setcookie('refreshToken', $token, $exp, REFRESH_BASE_URI, '', false, true);
            // TODO: нужно параметр false который отвечает за https вернуть потом как на сервак выкатится
            setcookie('refreshToken', $token, $exp, '/auth/refresh', 'localhost:4052', false, true);
        }
        catch(Exception $e){
            echo "the request was not executed";
        }
	}
	
	function removeRTCookie(){
        try {
            //setcookie('refreshToken', '', time() - 1, REFRESH_BASE_URI, '', true, true);
            setcookie('refreshToken', '', time() - 1, '/auth/refresh', 'localhost:4052', false, true);
        }
        catch (Exception $e){
            echo "the request was not executed";
        }

	}