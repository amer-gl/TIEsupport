<?php

function tiequde_user_login() {
		global $con;

		$output = array(
				'errors' => '',
				'success' => ''
		);

		$username = $_POST['user_name'];
		$password = $_POST['user_pass'];
		$hashedPass = sha1($password);
		$remember  = $_POST['remember_me'];

		if (isset($username)) {

			$filterdUser = filter_var($username, FILTER_SANITIZE_STRING);

			if (empty($filterdUser)) {

				$output[ 'errors' ] .= '<div class="error"><span class="error-icon"><i class="ion-close"></i></span><span class="error-message">Username Can\'t Be Empty</span></div>';

			}

		}

		if ( isset($password) ) {

			if (empty($password)) {

				$output[ 'errors' ] .= '<div class="error"><span class="error-icon"><i class="ion-close"></i></span><span class="error-message">Password Cant Be Empty</span></div>';

			}

		}
		// Check If The User Exist In Database

		if ( empty( $output[ 'errors' ] ) ) {
			$stmt = $con->prepare("SELECT 
										*
									FROM 
										users 
									WHERE 
										user_name = ?
									LIMIT 1");

			$stmt->execute( array( $username ) );  
			$result = $stmt->rowCount();

			// If Count > 0 This Mean The Database Contain Record About This Username

			if ( $result ) {
				$stmt = $con->prepare("SELECT 
											*
										FROM 
											users 
										WHERE 
											user_name = ?
										AND 
											user_password = ?
										LIMIT 1");

				$stmt->execute( array( $username, $hashedPass ) );
				$result = $stmt->fetch();
				$count = $stmt->rowCount();
				if( $count ){

					$_SESSION['user_data']  = $result;
					$_SESSION['user_name']  = $username;

					if( $remember == 1 ) {

						setrawcookie ( 'tiequde_uid', encode_cookie_value( $username ) , time() + (3600 * 24 * 7), "/", ".tiequde.com" );
						setrawcookie ( 'tiequde_gid' , 'andof1' . encode_cookie_value( $password ) . 'fRm2', time() + (3600 * 24 * 7), "/", ".tiequde.com" );

					}

					// Echo Success Message
					$output[ 'success' ] .= '<div class="error"><span class="error-icon"><i class="ion-android-done"></i></span><span class="error-message">You are logged in successfully</span></div><div class="error"><span class="error-icon"><i style="font-size: 18px; position: relative; top: 2px;" class="ion-ios-refresh-empty"></i></span><span class="error-message">You\'ll redirect to your dashboard within 3 seconds</span></div>';
				} else {
					$output[ 'errors' ]  .= '<div class="error"><span class="error-icon"><i class="ion-close"></i></span><span class="error-message">Sorry, password you entered does\'t match</span></div>';
				}		
				
			} else{
				$output[ 'errors' ] .= '<div class="error"><span class="error-icon"><i class="ion-close"></i></span><span class="error-message">Sorry, This user does not exist</span></div>';

			}
		}
		echo json_encode($output);

		
}


function tiequde_user_register() {
	global $con;

	$colors = array( "#26A65B", "#1fada6", "#c32c2c", "#a90094", "#ada009", "#8a847b", "#d26e08", "#828282", "#cc197c", "#ce1260", "#5a12ce", "#80a904", "#06b503", "#b503b0", "#bd1e1e" );

	$output = array(
				'errors' => '',
				'success' => ''
	);

	$username 		= $_POST['reg_name'];
	$password 		= $_POST['reg_pass'];
	$email 			= $_POST['reg_email'];
	$purchase	 	= $_POST['purchase'];
	$item_id	 	= $_POST['item_id'];
	$support_time 	= $_POST['support_time'];


	if (isset($username)) {

		$filterdUser = filter_var($username, FILTER_SANITIZE_STRING);

		if (strlen($filterdUser) < 4) {

			$output[ 'errors' ] .= '<div class="error"><span class="error-icon"><i class="ion-close"></i></span><span class="error-message">Username Must Be Larger Than 4 Characters</span></div>';

		}

	}

	if ( isset($password) ) {

		if (empty($password)) {

			$output[ 'errors' ] .= '<div class="error"><span class="error-icon"><i class="ion-close"></i></span><span class="error-message">Sorry Password Cant Be Empty</span></div>';

		}

	}
	if ( isset($purchase) ) {

		if (empty($purchase)) {

			$output[ 'errors' ] .= '<div class="error"><span class="error-icon"><i class="ion-close"></i></span><span class="error-message">Sorry Purchase Code Cant Be Empty</span></div>';

		}

	}

	if (isset($email)) {

		$filterdEmail = filter_var($email, FILTER_SANITIZE_EMAIL);

		if (filter_var($filterdEmail, FILTER_VALIDATE_EMAIL) != true) {

			$output[ 'errors' ] .= '<div class="error"><span class="error-icon"><i class="ion-close"></i></span><span class="error-message">This Email Is Not Valid</span></div>';

		}

	}

	// Check If There's No Error Proceed The User Add

	if (empty($output[ 'errors' ])) {

		// Check If User Exist in Database

		$statement = $con->prepare("SELECT * FROM users WHERE user_name = ?" );

		$statement->execute( array( $username ) );
		$check = $statement->rowCount();

		if ( $check  ) {

			$output[ 'errors' ] .= '<div class="error"><span class="error-icon"><i class="ion-close"></i></span><span class="error-message">Sorry This User Is Exists</span></div>';

		} else {

			// Insert Userinfo In Database

			$check_purchase = $con->prepare( "INSERT INTO purchase(purchase_code) VALUES( :purcode )" );

			$check_purchase->execute( array( 'purcode' => $purchase ) );

			$userBg 		= $colors[ rand( 0, count( $colors ) ) ];

			$chk = $con->prepare( "INSERT INTO 
											users 
												( user_id,  user_name, user_password, purchiscode, email, support_time, group_id, user_status, themes, background) 
											VALUES 
												(NULL, :name, :pass, :purcode, :mail, :supp_time, :gid, :user_stat, :uthemes, :BG ) "
								  );
			$chk->execute( array( 
								'name' => $username,
								'pass' => sha1($password),
								'purcode' => json_encode( array( $purchase => $item_id ) ),
								'mail' => $email,
								'supp_time'	=> json_encode( array( $item_id => $support_time ) ),
								'gid' => 0,
								'user_stat' => 1,
								'uthemes'	=> json_encode( array( $item_id => $item_id ) ),
								'BG' => $userBg
							)
				);
			$stmt = $con->prepare( "SELECT AUTO_INCREMENT
									FROM  INFORMATION_SCHEMA.TABLES
									WHERE TABLE_SCHEMA = ?
									AND   TABLE_NAME   = ?" );
			$stmt->execute( array( 'cl19-a-wordp-2fn', 'users' ) );

			$insert_id = $stmt->fetch();
			$insert_id = $insert_id['AUTO_INCREMENT'] - 1 ;

			if ( $chk && $check_purchase ) {


				$_SESSION['user_data']  = array(
											'user_name' => $username,
											'user_id'  => $insert_id,
											'email' => $email,
											'group_id' => 0,
											'purchiscode' => json_encode( array( $purchase => $item_id ) ),
											'support_time'	=> json_encode( array( $item_id => $support_time ) ),
											'themes'	=> json_encode( array( $item_id => $item_id ) ),
											'background' => $userBg
				);
				$_SESSION['user_name']	= $username;

				// Echo Success Message

				$output[ 'success' ] .= '<div class="error"><span class="error-icon"><i class="ion-android-done"></i></span><span class="error-message">Congrats You Are Now Registerd User</span></div>';

			} else {
				$output[ 'errors' ] .= '<div class="error"><span class="error-icon"><i class="ion-close"></i></span><span class="error-message">Sorry , There\'s an Error</span></div>';
			}

		}

	}

	echo json_encode($output);


}




function tiequde_get_noti() {

	global $con;
	if( isset( $_SESSION['user_name'] ) ){
		$user = $_SESSION['user_data'];

		$output = array( 
			'status' => 0,
			'noti_list' => ''
		);


		$stmt = $con->prepare("SELECT 
									*
								FROM 
									notifications 
								WHERE 
									user_id = ?
								ORDER BY noti_id DESC
								");

		$stmt->execute( array( $user['user_id'] ) );
		$notifications = $stmt->fetchAll();

		foreach ( $notifications as $noti ) {
			$output['noti_list'] .= 
			'
				<li class="transition  '. ( $noti['noti_status'] == 0 ? 'unread' : '' ) .'">
					<a class="'. ( isset( $noti['redirect'] ) ? 'redirect-ticket" data-id="'. $noti['redirect'] .'"' : '' ) .'>
						<div class="title-container">
							<span>'. $noti['noti_title'] . '</span>
							<span class="title">'. $noti['noti_content'] .'</span>
							'. ( $noti['icon'] == 'ion-android-done' ? '<span> Closed by TIEQUDE Support</span>' : '' ) .'
						</div>
						<div class="date"><div class="icon"><i class="'. $noti['icon'] .'"></i></div>' . human_time_diff( intval($noti['noti_time']) ) .'</div>
					</a>
				</li>
			';
			if( $noti['noti_status'] == 0 ) {
				$output['status'] = $output['status'] + 1 ;
			}
		}

		echo json_encode( $output );
	}
	
}


function tiequde_update_noti() {

	global $con;
	$user = $_SESSION['user_data'];

	$stmt = $con->prepare("UPDATE notifications SET noti_status = ? WHERE user_id = ?");

	$check = $stmt->execute( array( 1, $user['user_id'] ) );

	if( $check !== NULL ) { 
		echo 'ok';
	} else { 
		echo 'la6eze';
	}
	
}



function tiequde_check_purchase() {
	global $con;

	$purchase = $_POST['purchase'];

	$stmt = $con->prepare("SELECT 
								*
							FROM 
								purchase 
							WHERE 
								purchase_code = ?
							");

	$stmt->execute( array( $purchase ) );
	$check = $stmt->fetch();
	if( $check != NULL ) { 
		echo 'exist';
	} else { 
		echo 'not_exist';
	}
	
}


function tiequde_new_ticket() {

	global $con;
	$user = $_SESSION['user_data'];

	$ticket_title 	= $_POST['ticket_title'];
	$ticket_theme 	= $_POST['ticket_theme'];
	$ticket_content = $_POST['ticket_content'];
	$output = array(
		'errors' => '',
		'ticket_id' => 0
	);

	if (isset($ticket_title)) {

		$filteredTitle = filter_var($ticket_title, FILTER_SANITIZE_STRING);

		if ( empty( $filteredTitle ) ) {

			$output[ 'errors' ] .= '<div class="error"><span class="error-icon"><i class="ion-close"></i></span><span class="error-message">Title can\'t be empty</span></div>';

		}

	}

	if (isset($ticket_content)) {

		$filterdContent =  addslashes( strip_tags( $ticket_content , '<br><strong><i><img><a>') );

		if ( empty( $filterdContent ) ) {

			$output[ 'errors' ] .= '<div class="error"><span class="error-icon"><i class="ion-close"></i></span><span class="error-message">Please specify your issue</span></div>';

		}

	}

	if (isset($ticket_theme)) {

		if ( $ticket_theme == 'CHOOSE YOUR THEME' ) {

			$output[ 'errors' ] .= '<div class="error"><span class="error-icon"><i class="ion-close"></i></span><span class="error-message">Please select your theme</span></div>';

		}

	}

	if( empty( $output['errors'] ) ) {
	
		$chk = $con->prepare( "INSERT INTO 
										tickets 
											( title,  user_id, ticket_date, ticket_status, theme ) 
										VALUES 
											( :ticket_title, :uid, :tick_date, :tick_stat, :tick_theme )"
							  );
		$chk->execute( array( 
							'ticket_title' => $ticket_title,
							'uid' => $user['user_id'],
							'tick_date' => time(),
							'tick_stat' => 1,
							'tick_theme' => $ticket_theme,
						)
			);
		$chk = $chk->rowCount();

		$stmt = $con->prepare( "SELECT AUTO_INCREMENT
								FROM  INFORMATION_SCHEMA.TABLES
								WHERE TABLE_SCHEMA = ?
								AND   TABLE_NAME   = ?" );
		$stmt->execute( array( 'cl19-a-wordp-2fn', 'tickets' ) );

		$ticketID = $stmt->fetch();
		$ticketID = $ticketID['AUTO_INCREMENT'] - 1;

		
		if ( $chk ) {
			$check = $con->prepare( "INSERT INTO 
											chat 
												( content,  user_id, ticket_id, message_date, admin_stat ) 
											VALUES 
												( :chat_content, :uid, :tick_id, :chat_date, :isAdmin )"
								  );
			$check->execute( array( 
								'chat_content' => $filterdContent,
								'uid' => $user['user_id'],
								'tick_id' => $ticketID,
								'chat_date' => time(),
								'isAdmin' => 0,
							)
				);

			$output['ticket_id'] = $ticketID;

		} else {
			$output[ 'errors' ] .= '<div class="error"><span class="error-icon"><i class="ion-close"></i></span><span class="error-message">Sorry , There\'s an Error</span></div>';
		}

	}

	echo json_encode( $output );
	
}


function tiequde_close_ticket() {
	global $con;

	$ticket_id = $_SESSION['chat'];

	$stmt = $con->prepare("SELECT * FROM tickets WHERE ticket_id = ? LIMIT 1");
	$stmt->execute( array( $ticket_id  ) );
	$ticket = $stmt->fetch();

	$stmt = $con->prepare("UPDATE tickets SET ticket_status = ? WHERE ticket_id = ?");

	$stmt->execute( array( 0, $ticket_id ) );

	$messageTitle = 'Your ticket : ';
	
	$check = $con->prepare( "INSERT INTO 
									notifications 
										( icon,  noti_title, noti_content, noti_time, redirect, user_id ) 
									VALUES 
										( :noti_icon, :title, :content, :noti_at, :go_to, :uid )"
						  );
	$check->execute( array( 
						'noti_icon' => 'ion-android-done',
						'title'		=> $messageTitle,
						'content' => $ticket['title'],
						'noti_at' => time(),
						'go_to' => $_SESSION['chat'],
						'uid' => $ticket['user_id'],
					)
		);
	tiequde_deredirect_chat();

	
}


function tiequde_redirect_chat() {

	$ticket_id = $_POST['ticket_id'];

	$_SESSION['chat'] = $ticket_id;

	
}


function tiequde_deredirect_chat() {

	if( isset( $_SESSION['chat'] ) ) {
		$_SESSION['chat'] = NULL;
	}

	
}



function tiequde_insert_message() {
	global $con;
	$user = $_SESSION['user_data'];

	$filterdMessage =  addslashes( strip_tags( $_POST['message'] , '<br><strong><i><img><a>') );
	$isAdmin = ( $user['group_id'] == 1 ? 1 : 0 );

	$stmt = $con->prepare("SELECT * FROM tickets WHERE ticket_id = ? LIMIT 1");
	$stmt->execute( array( $_SESSION['chat'] ) );
	$ticket = $stmt->fetch();

	if( $isAdmin == 1 ) {

		$check = $con->prepare( "INSERT INTO 
										chat 
											( content,  user_id, ticket_id, message_date, admin_stat ) 
										VALUES 
											( :chat_content, :uid, :tick_id, :chat_date, :isAdmin )"
							  );
		$check->execute( array( 
							'chat_content' => $filterdMessage,
							'uid' => $ticket['user_id'],
							'tick_id' => $_SESSION['chat'],
							'chat_date' => time(),
							'isAdmin' => $isAdmin,
						)
			);

		$stmt = $con->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY noti_id DESC LIMIT 1" );
		$stmt->execute( array( $ticket['user_id'] ) );
		$lastNoti = $stmt->fetch();

		if( $lastNoti['redirect'] == $_SESSION['chat'] ) {

			$stmt = $con->prepare("UPDATE notifications SET noti_status = ? WHERE noti_id = ?");

			$check = $stmt->execute( array( 0, $lastNoti['noti_id'] ) );

		} else {

			$messageTitle = 'New message on: ';
			$check = $con->prepare( "INSERT INTO 
											notifications 
												( icon,  noti_title, noti_content, noti_time, redirect, user_id ) 
											VALUES 
												( :noti_icon, :title, :content, :noti_at, :go_to, :uid )"
								  );
			$check->execute( array( 
								'noti_icon' => 'ion-chatboxes',
								'title'		=> $messageTitle,
								'content' => $ticket['title'],
								'noti_at' => time(),
								'go_to' => $_SESSION['chat'],
								'uid' => $ticket['user_id'],
							)
				);
		}

		echo '';

	} else { 

		$check = $con->prepare( "INSERT INTO 
										chat 
											( content,  user_id, ticket_id, message_date, admin_stat ) 
										VALUES 
											( :chat_content, :uid, :tick_id, :chat_date, :isAdmin )"
							  );
		$check->execute( array( 
							'chat_content' => $filterdMessage,
							'uid' => $ticket['user_id'],
							'tick_id' => $_SESSION['chat'],
							'chat_date' => time(),
							'isAdmin' => $isAdmin,
						)
			);
		echo '';
	}

	
}


function tiequde_get_messages() {


	global $con;
	if( isset( $_SESSION['user_name'] ) ) {
		$user = $_SESSION['user_data'];

		$output = array(
			'chat' => array(),
			'status' => 'open',
			'num' => 0
		);

		if ( isset( $_SESSION['chat'] ) ){

			$ticket_id = $_SESSION['chat'];
			$user_id   = $user['user_id'];
			$user_name = $user['user_name'];

			$stmtArgs 		= ( $user['group_id'] == 0 ? 'WHERE user_id = ? AND ticket_id = ? ' : 'WHERE ticket_id = ? ' );
			$stmtArgsValues = ( $user['group_id'] == 0 ? array( $user_id, $ticket_id ) : array( $ticket_id ) );

			$stmt = $con->prepare("SELECT * FROM chat " . $stmtArgs );
			$stmt->execute( $stmtArgsValues );
			$messages = $stmt->fetchAll();

			$stmt = $con->prepare("SELECT * FROM tickets " . $stmtArgs );
			$stmt->execute( $stmtArgsValues );
			$ticket = $stmt->fetch();

			$stmt = $con->prepare("SELECT * FROM users WHERE user_id = ?" );
			$stmt->execute( array( $ticket['user_id'] ) );
			$chatUser = $stmt->fetch();


			if( $ticket['ticket_status'] == 0 ) { $output['status'] = 'closed'; }

					$i = 0;
					foreach ($messages as $message) {
						$i++;
					$output['chat'][] = 
							'<div class="message '. ( $message['admin_stat'] == 1 ? 'admin ' : '' ) . ( count( $messages ) == $i ? 'last' : ( count( $messages ) - 1 == $i ? 'before-last' : '' ) ) . '">

								<div class="message-title">
									<div class="left pull-left"><span '. ( $message['admin_stat'] == 0 ? 'style="background-color: ' . $chatUser[ 'background' ] . ';"' : '' ) .' class="user-image"><span class="letter">'. strtoupper( substr( ( $message['admin_stat'] == 1 ? 'T' : $chatUser['user_name'] ), 0, 1 ) ) .'</span><span class="letter letter-level-2">'. strtoupper( substr( ( $message['admin_stat'] == 1 ? 'TE' : $chatUser['user_name'] ), 1, 1 ) ) .'</span></span>'. ( $message['admin_stat'] == 1 ? 'TIEQUDE Support' : $chatUser['user_name'] ) .'</span></div>
									<div class="right pull-right"><div class="date">'. human_time_diff( intval($message['message_date']) ) .'</div></div>
									<div class="clear"></div>
								</div> <!-- .message-title -->

								<div class="message-content">
									'. stripslashes( $message['content'] ) .'
								</div> <!-- .message-content -->

							</div> <!-- .message -->
						';
					}
					$output['num'] = $i;
		}

		echo json_encode( $output );
	}
	
}


function tiequde_get_tickets() {


	global $con;


	if ( isset( $_SESSION['user_name'] ) ){

		$stmt = $con->prepare("SELECT * FROM tickets ORDER BY ticket_date DESC ");
		$stmt->execute();
		$tickets = $stmt->fetchAll();

			foreach ( $tickets as $ticket ) {
				$stmt = $con->prepare("SELECT user_name FROM users WHERE user_id = ? LIMIT 1");
				$stmt->execute( array( $ticket['user_id'] ) );
				$ticketPoster = $stmt->fetch();
				$ticketPoster = $ticketPoster['user_name'];

				$stmt = $con->prepare("SELECT * FROM chat WHERE ticket_id = ? ORDER BY message_date DESC LIMIT 1");
				$stmt->execute( array( $ticket['ticket_id'] ) );
				$isNew = $stmt->fetch();

				echo 
					'
						<tr>
							<td><a class="redirect-ticket" data-id="'. $ticket['ticket_id'] .'">'. ( $ticket['ticket_status'] == 1 ? ( $isNew['admin_stat'] == 0 ? '<span class="noti new"></span>' : '' ) : '' ) .'<i class="'. ( $ticket['ticket_status'] == 1 ? 'onprogress ion-help-buoy' : 'done ion-android-done' ) .'"></i> '. $ticket['title'] .'</a></td>
							<td>'. $ticket['theme'] .'</td>
							<td>'. $ticketPoster .'</td>
							<td>'. human_time_diff( intval($ticket['ticket_date']) ) .'</td>
						</tr>
					';
			}

	}
	
}

function tiequde_user_header( $group_id, $user ){

if( $group_id == 0 ) {
		echo '<header>

				<div class="top-nav">

					<div class="logo"><a href="http://www.tiequde.com/">TIEQUDE</a></div>

					<nav class="tiequde-navigation pull-right">

						<ul>
							<li class="notifications">
								<span class="num"><i class="ion-android-notifications-none"></i></span>
								<div class="notifications-container">
									<ul class="notifications-list">
									</ul> <!-- ul.notifications-list -->
								</div>
							</li>
							<li class="active"><a class="transition redirect-dashboard"><span style="background-color: ' . $user['background'] . ';" class="user-image"><span class="letter">'. strtoupper( substr( $user['user_name'], 0, 1 ) ) .'</span><span class="letter letter-level-2">'. strtoupper( substr( $user['user_name'], 1, 1 ) ) .'</span></span>'.$user['user_name'].'</a></li>
							<li><a href="http://www.tiequde.com/support/logout.php" class="transition logout">Log out</a></li>
						</ul>

						<div class="toggle-menu">
							<i class="ion-android-menu"></i>
						</div>

					</nav>

				</div> <!-- .top-nav -->

			</header>';
} else {
	echo    
		'<header>

			<div class="top-nav">

				<div class="logo"><a href="http://www.tiequde.com/">TIEQUDE</a></div>

				<nav class="tiequde-navigation pull-right">

					<ul>
						<li><a href="http://www.tiequde.com/" class="transition">View Site</a></li>
						<li><a href="http://www.tiequde.com/wp-admin.php" class="transition">Wordpress Dashboard</a></li>
						<li><a class="transition redirect-dashboard">Support Dashboard</a></li>
						<li><a href="http://www.tiequde.com/support/logout.php" class="transition logout">Log out</a></li>
					</ul>

					<div class="toggle-menu">
						<i class="ion-android-menu"></i>
					</div>

				</nav>

			</div> <!-- .top-nav -->

		</header>';
}
}

function tiequde_chat_body( $con, $user, $ticket_id ){

$user_name = $user['user_name'];
$user_id   = $user['user_id'];


$stmtArgs 		= ( $user['group_id'] == 0 ? 'WHERE user_id = ? AND ticket_id = ? ' : 'WHERE ticket_id = ? ' );
$stmtArgsValues = ( $user['group_id'] == 0 ? array( $user_id, $ticket_id ) : array( $ticket_id ) );

$stmt = $con->prepare("SELECT * FROM chat " . $stmtArgs );
$stmt->execute( $stmtArgsValues );
$messages = $stmt->fetchAll();

$stmt = $con->prepare("SELECT * FROM tickets " . $stmtArgs );
$stmt->execute( $stmtArgsValues );
$ticket = $stmt->fetch();

$stmt = $con->prepare("SELECT * FROM users WHERE user_id = ?" );
$stmt->execute( array( $ticket['user_id'] ) );
$chatUser = $stmt->fetch();



if( $user_id != $ticket['user_id'] && $user['group_id'] == 0 ){
	echo '
		<main>
			<div class="check-information">
				<div class="icon"><i class="ion-gear-b"></i></div>
			</div>
			<div class="container">
				<h2 class="text-center" style="margin-top: 20%;">Permission Denied</h2>
				<h4 class="text-center">Sorry, You are not allowed to acces this page. </h4>
			</div>
		</main>
	';
} else {
	echo '
		<main>
			<div class="check-information">
				<div class="icon"><i class="ion-gear-b"></i></div>
			</div>
			<div class="container">
				<div class="messages wraper">';
				
				$i = 0; $messageHeader = ''; $checkHeader = 0;
				foreach ($messages as $message) {
					$i++;
					echo '
						<div class="message '. ( $message['admin_stat'] == 1 ? 'admin ' : '' ) . ( count( $messages ) == $i ? 'last' : ( count( $messages ) - 1 == $i ? 'before-last' : '' ) ) . '">

							<div class="message-title">
								<div class="left pull-left"><span '. ( $message['admin_stat'] == 0 ? 'style="background-color: ' . $chatUser[ 'background' ] . ';"' : '' ) .' class="user-image"><span class="letter">'. strtoupper( substr( ( $message['admin_stat'] == 1 ? 'T' : $chatUser['user_name'] ), 0, 1 ) ) .'</span><span class="letter letter-level-2">'. strtoupper( substr( ( $message['admin_stat'] == 1 ? 'TE' : $chatUser['user_name'] ), 1, 1 ) ) .'</span></span>'. ( $message['admin_stat'] == 1 ? 'TIEQUDE Support' : $chatUser['user_name'] ) .'</span></div>
								<div class="right pull-right"><div class="date">'. human_time_diff( intval($message['message_date']) ) .'</div></div>
								<div class="clear"></div>
							</div> <!-- .message-title -->

							<div class="message-content">
								'. stripslashes( $message['content'] ) .'
							</div> <!-- .message-content -->

						</div> <!-- .message -->
					';
					if( $checkHeader == 0 ) {
						$messageHeader = 
								'
									<div class="left pull-left"><span ' . ( $user['group_id'] == 0 ? 'style="background-color: ' . $user[ 'background' ] : '' ). ';"  class="user-image"><span class="letter">'. strtoupper( substr( ( $user['group_id'] == 0 ? $user['user_name'] : 'T' ), 0, 1 ) ) .'</span><span class="letter letter-level-2">'. strtoupper( substr( ( $user['group_id'] == 0 ? $user['user_name'] : 'TE' ), 1, 1 ) ) .'</span></span>'. ( $user['group_id'] == 0 ? $user['user_name'] : 'TIEQUDE Support' ) .'</span></div>
								';
						$checkHeader = 1;
					}
				}

echo	'		</div> <!-- .messages.wraper -->
				<div class="message_header hide" data-bool="' . ( $user['group_id'] == 1 ? 1 : 0 ) . '">'. $messageHeader .'</div>
				<div class="messages send">
					<div class="send">
						<div class="send-message-container"><textarea id="message_editor" placeholder="YOUR REPLY"></textarea><div class="top border transition"></div><div class="right border transition"></div><div class="bottom border transition"></div></div>
						<div class="buttons pull-right">
							'. ( $user['group_id'] == 1 ? '<button class="close-ticket" data-id="'. $ticket_id .'">Close ticket</button>' : '' ) . ( $ticket['ticket_status'] == 1 ? '<button class="send-message">Send</button>' : '<button class="closed-ticket">Ticket Closed</button>' ) . '
						</div>
					</div> <!-- .send -->
				</div> <!-- .messages.send -->
		</div> <!-- .container -->
	</main>
	';
}

}

function tiequde_user_dashboard( ) {
	global $con,$themesArr;

		
		$user = $_SESSION['user_data'];

		tiequde_user_header( $user['group_id'], $user );

		if ( isset( $_SESSION['chat'] ) ) {
			tiequde_chat_body( $con, $user, $_SESSION['chat'] );
		} else {
			if ( $user['group_id'] == 0 ) {
				$userThemes = json_decode( $user['themes'], true );
				$supportUserItems = json_decode( $user['support_time'] );
				$supportedItems = '';
				
				foreach ( $userThemes as $item ) {
					if( !tiequde_support_time( $supportUserItems-> $item ) ) {
						$supportedItems .= '<li title="'.$supportUserItems-> $item.'">- Your support on <strong>'.$themesArr[$item].'</strong> had finished.</li>';
					} elseif ( tiequde_support_time( $supportUserItems-> $item ) == 'Warning' ) {
						$supportedItems .= '<li title="'.$supportUserItems-> $item.'">- Your support on <strong>'.$themesArr[$item].'</strong> will finish whithin 24 hours.</li>';
					} else {
						$supportedItems .= '<li title="'.$supportUserItems-> $item.'">- Your support on <strong>'.$themesArr[$item].'</strong> will finish '. tiequde_support_time( $supportUserItems-> $item ) .' later.</li>';
					}
				}

				$stmt = $con->prepare("SELECT 
											*
										FROM 
											tickets 
										WHERE 
											user_id = ?
										ORDER BY ticket_date DESC
										");
				$stmt->execute( array( $user['user_id'] ) );
				$tickets = $stmt->fetchAll();

				echo '
					<main>
						<div class="check-information">
							<div class="icon"><i class="ion-gear-b"></i></div>
						</div>
						<div class="container user-dashboard">
							<div class="row">

								<div class="col-md-8">

									<div class="new-ticket">


										<div class="support-form new-ticket-form">
											<div class="check-information">
												<div class="icon"><i class="ion-gear-b"></i></div>
											</div>
											<div class="error-container" style="display:none;">
							
												<div class="toggle-errors">

													<div class="close-error"><i class="ion-close"></i></div>

													<div class="errors">

														<div class="error">
															<span class="error-icon"><i class="ion-close"></i></span>
															<span class="error-message">Your username doesn\'t exits!</span>
														</div> <!-- .error -->

													</div> <!-- .errors -->


												</div> <!-- .toggle-errors -->

												<div class="error-bg transition">
												

												</div> <!-- .error-bg -->

											</div> <!-- .error-container -->

											<div class="title">
												<h3>New Support ticket</h3>
											</div> <!-- .title -->
											
											<form class="ticket-form">
												<div class="support-input">
													<input type="text" id="ticket_title" name="ticket_title" required>
													<span class="transition">Title</span>
													<div class="hover-border transition"></div>
												</div>

												<div class="select-wrapper">
													<span class="caret">▼</span>
													<input type="text" class="select-dropdown transition" readonly="true" id="ticket_theme" name="ticket_theme" value="CHOOSE YOUR THEME">
													<ul id="select-options" class="dropdown-content select-dropdown" data-support="finished">
														<li class="disabled active"><span>CHOOSE YOUR THEME</span></li>';
													foreach ( $userThemes as $item ) {
														$check = tiequde_support_time( $supportUserItems-> $item );
														echo '<li class="'. ( $check == false ? 'disabled' : '' ) .'" data-support="'. ( $check == false ? 'finished' : 'open' ) .'"><span>'.$themesArr[$item] . ( $check == false ? '<small style="color: red;"> - Your support time for this item had finished</small>' : '' ) .'</span></li>';
													}
													
											echo	'</ul>
												</div> <!-- .select-wrapper -->

												<div class="support-input">
													<textarea class="ticket-message" id="ticket_content" name="ticket_content" required></textarea>
													<span class="transition">Your issue</span>
													<div class="hover-border transition"></div>
												</div>
												<div class="send-ticket">
													<button id="new-ticket">Submit</button>
												</div>
											</form>

										</div> <!-- .login -->

									</div> <!-- .new-ticket -->

								</div> <!-- .col-md-8 -->

								<div class="col-md-4">

									<div class="sidebar">

										<div class="widget tickets-widget">
											<div class="title"><h3>Your latest tickets</h3></div>
											<hr>
											<div class="widget-content">
												<ul>';
										foreach ( $tickets as $ticket ) {
											echo '<li title="'.( $ticket['ticket_status'] == 1 ? 'Open' : 'Closed' ).'"><a class="redirect-ticket" data-id="'. $ticket['ticket_id'] .'"><span class="icon transition"><i class="'.( $ticket['ticket_status'] == 1 ? 'ion-help-buoy' : 'ion-android-done' ).'"></i></span><span class="content transition">'. $ticket['title'] .'<small> '. $ticket['theme'] .'</small></span></a></li>';
										}


									echo	'</ul>
											</div>
										</div> <!-- .widget -->

										<div class="widget">
											<div class="title"><h3>Support time</h3></div>
											<hr>
											<div class="widget-content">
												<ul class="supported-items">
													'. $supportedItems .'
												</ul>
											</div>
										</div> <!-- .widget -->

										<div class="widget widget-noti">
											<div class="title"><h3>Notifications</h3></div>
											<hr>
											<div class="widget-content">
												<div class="no-content">
													<ul>
													</ul>
												</div>
											</div>
										</div> <!-- .widget -->

									</div> <!-- .sidebar -->
									
								</div> <!-- .col-md-4 -->

							</div> <!-- .row -->
						</div>
					</main>
					';

			} else { 
				$stmt = $con->prepare("SELECT * FROM tickets ORDER BY ticket_id DESC");
				$stmt->execute();
				$tickets = $stmt->fetchAll();

				echo 
				'<main>
					<div class="check-information">
						<div class="icon"><i class="ion-gear-b"></i></div>
					</div>
					<div class="container">
						<table class="table support-table">
							<thead>
								<tr>
									<th style="width: 45%;">Latest Tickets</th>
									<th>Theme</th>
									<th>Poster Name</th>
									<th>Posted</th>
								</tr>
							</thead>
							<tbody>';

							foreach ( $tickets as $ticket ) {
								$stmt = $con->prepare("SELECT user_name FROM users WHERE user_id = ? LIMIT 1");
								$stmt->execute( array( $ticket['user_id'] ) );
								$ticketPoster = $stmt->fetch();
								$ticketPoster = $ticketPoster['user_name'];

								$stmt = $con->prepare("SELECT * FROM chat WHERE ticket_id = ? ORDER BY message_date DESC LIMIT 1");
								$stmt->execute( array( $ticket['user_id'] ) );
								$isNew = $stmt->fetch();

								echo 
									'
										<tr>
											<td><a class="redirect-ticket" data-id="'. $ticket['ticket_id'] .'">'. ( $ticket['ticket_status'] == 1 ? ( $isNew['admin_stat'] == 0 ? '<span class="noti new"></span>' : '' ) : '' ) .'<i class="'. ( $ticket['ticket_status'] == 1 ? 'onprogress ion-help-buoy' : 'done ion-android-done' ) .'"></i> '. $ticket['title'] .'</a></td>
											<td class="text-center">'. $ticket['theme'] .'</td>
											<td>'. $ticketPoster .'</td>
											<td>'. human_time_diff( intval($ticket['ticket_date']) ) .'</td>
										</tr>
									';
							}

				echo		'</tbody>
						</table>
					</div>
				</main>
				';
			}
		}
	
}

function tiequde_support_time( $end ) {

		 	$dateArr		= explode( ' ', $end );
			$stat		= 1;
			switch( $dateArr[1] ) {
				case "Jan": $dateArr[1] = 01; break;
				case "Feb": $dateArr[1] = 02; break;
				case "Mar": $dateArr[1] = 03; break;
				case "Apr": $dateArr[1] = 04; break;
				case "May": $dateArr[1] = 05; break;
				case "Jun": $dateArr[1] = 06; break;
				case "Jul": $dateArr[1] = 07; break;
				case "Aug": $dateArr[1] = 08; break;
				case "Sep": $dateArr[1] = 09; break;
				case "Oct": $dateArr[1] = 10; break;
				case "Nov": $dateArr[1] = 11; break;
				case "Dec": $dateArr[1] = 12; break;
			}
			$year		= intval( $dateArr[5] ) - intval( date('Y') );
			$month		= intval( $dateArr[1] ) - intval( date('m') );
			$day		= intval( $dateArr[2] ) - intval( date('d') );

			if( $year < 0 ) { $stat = 0; } else if( $year == 0 && $month < 0 ) { $stat = 0; } else if( $year == 0 && $month == 0  && $day < 0 ) { $stat = 0; } else if ( $year == 0 && $month == 0 && $day == 0 ) { $stat = "Warning"; }
			if( $year > 0 ) { if( $month < 0 ) { $year--; $month = 12 + $month; } if( $month != 0 ) { if( $day < 0 ) { $month--; $day = 31 + $day; } } }
			$out = ( $year > 0 ? $year . " year" . ( $year == 1 ? '' : 's' ) . " " : "" ) . (  $month > 0 ? $month . " month" . ( $month == 1 ? '' : 's' ) . " " : "" ) . (  $day > 0 ? $day . " day" . ( $day == 1 ? '' : 's' ) : "" );
			if( !$stat )
				return false;
			else if( $stat == "Warning" )
				return $stat;
			else
				return $out;
}

function formatCount($n, $singular, $plural, $none = '0')
{
	if ($n == 0) {
		return "{$none}&nbsp;{$plural}";
	} elseif ($n == 1) {
		return "{$n}&nbsp;{$singular}";
	} else {
		return "{$n}&nbsp;{$plural}";
	}
}
 
function human_time_diff( $from, $to = '' ) {
	if ( empty($to) )
		$to = time();
	$diff = (int) abs($to - $from);
	if ($diff <= 3600) {
		$mins = round($diff / 60);
		if ($mins <= 1) {
			$mins = 1;
		}
		/* translators: min=minute */
		$since = formatCount($mins, 'min', 'mins');
	} else if (($diff <= 86400) && ($diff > 3600)) {
		$hours = round($diff / 3600);
		if ($hours <= 1) {
			$hours = 1;
		}
		$since = formatCount($hours, 'hour', 'hours');
	} elseif ($diff >= 86400) {
		$days = round($diff / 86400);
		if ($days <= 1) {
			$days = 1;
		}
		$since = formatCount($days, 'day', 'days');
	}
	$formatted = (($to-$from) < 0) ? ("in ".$since) : ($since." ago");
	return $formatted;
}

function encode_cookie_value($value) {
	return strtr($value,
	               array_combine(str_split($tmp=",; \t\r\n\013\014"),
	                             array_map('rawurlencode', str_split($tmp))
	                            )
	              );
}