<?php

	session_start(); // Start The Session

	session_unset(); // Unset The Data

	session_destroy(); // Destory The Session

	setrawcookie('tiequde_uid', '', time() - 10, "/", ".tiequde.com" );
	setrawcookie('tiequde_gid', '', time() - 10, "/", ".tiequde.com" );

	header('Location: http://www.tiequde.com/');

	exit();