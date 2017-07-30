<?php 

	session_start();

	if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		require( 'functions/functions.php' );
		require( '.././connect.php' );


		call_user_func( filter_var($_POST['action'], FILTER_SANITIZE_STRING), array() );

		$con = NULL;
	} else {
		header('Location: http://www.tiequde.com/');

		exit();
	}
