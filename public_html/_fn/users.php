<?php

	/*
		$userRoles - simple array as sample: ['revizor', 'customer']
		$allowedRoles - assoc array: ['revizor' => true, 'admin' => true]
	*/
	function checkUserRole($userRoles, $allowedRoles){
		foreach($userRoles as $role){
			if(isset($allowedRoles[$role])){
				return true;
			}
		}
		
		return false;
	}