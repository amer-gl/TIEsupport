<?php

	ob_start(); // Output Buffering Start

	session_start();

	if ( isset($_SESSION['user_name']) || isset( $_COOKIE[ 'tiequde_uid' ] ) ) {

		$pageTitle = 'TIEQUDE Support | Dashboard';

		include 'init.php';

		/* Start Dashboard Page */
			$themesArr = array(
				'14348971' => 'Timeline - Blogger',
				'18626965' => 'Timeline - WordPress'
			);

			if( !isset( $_SESSION['user_name'] ) && isset( $_COOKIE[ 'tiequde_uid' ] ) && isset( $_COOKIE[ 'tiequde_gid' ] ) ) {
				$user_name 				= $_COOKIE[ 'tiequde_uid' ];
				$hashed_password	= sha1( str_replace( 'andof1', '', str_replace( 'fRm2', '', $_COOKIE[ 'tiequde_gid' ] ) ) );
				$stmt = $con->prepare("SELECT 
											*
										FROM 
											users 
										WHERE 
											user_name = ?
										AND 
											user_password = ?
										LIMIT 1");
				$stmt->execute( array( $user_name, $hashed_password ) );
				$result = $stmt->fetch();
				$count 	= $stmt->rowCount();

				if( $count ){

					$_SESSION['user_data']  = $result;
					$_SESSION['user_name']  = $user_name;
					tiequde_user_dashboard();

				} else {

					header('Location: index.php');

					exit();
				}
			} elseif ( isset( $_SESSION['user_name'] ) ) {
				tiequde_user_dashboard();
			} else {
			
				header('Location: index.php');

				exit();
			}
			
		?>

		<?php

		/* End Dashboard Page */

		include $tpl . 'footer.php';

	} else {

		header('Location: index.php');

		exit();
	}

	ob_end_flush(); // Release The Output

?>