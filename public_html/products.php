<?php

	include_once('_fn/system.php');
	checkCORS();
	randomExit(0);
	echo file_get_contents('_db/products.data');

?>