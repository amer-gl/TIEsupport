<?php

	$dsn = 'mysql:host=shareddb1d.hosting.stackcp.net;dbname=wordpress-323119e6';
	$user = 'wordpress-323119e6';
	$pass = 'e9322dfc3559';
	$option = array(
		PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
	);
	
	try {
		$con = new PDO($dsn, $user, $pass, $option);
		$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	catch(PDOException $e) {
		echo 'Failed To Connect :' . $e->getMessage();
	}