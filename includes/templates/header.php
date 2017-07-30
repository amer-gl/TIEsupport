<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title><?php echo $pageTitle; ?></title>
		<link rel="stylesheet" href="<?php echo $css ?>bootstrap.min.css" />
		<link rel="stylesheet" href="<?php echo $css ?>ionicons.min.css" />
		<link rel="stylesheet" href="<?php echo $css ?>support.css" />
	</head>
	<?php 	$body = '';
			if( isset( $_SESSION['user_name'] ) || isset( $_COOKIE[ 'tiequde_uid' ] ) ){ $body = 'support-dashboard'; } 
	?>
	<body class="<?php echo $body; ?>">
		<?php if( !isset( $_SESSION['user_name'] ) && !isset( $_COOKIE[ 'tiequde_uid' ] ) ): ?>
				<header class="entry-header">

				<div class="header-bg">

					<div class="content">

						<div class="top-nav">

							<div class="logo"><a href="/">TIEQUDE</a></div>

							<nav class="tiequde-navigation pull-right">

								<ul>
									<li><a href="/" class="transition">Home</a></li>
									<li><a href="/contact" class="transition">Contact Us</a></li>
									<li class="current_page_item"><a href="#" class="transition">Support</a></li>
								</ul>

								<div class="toggle-menu">
									<i class="ion-android-menu"></i>
								</div>

							</nav>

						</div> <!-- .top-nav -->

						<div class="header-content">

							<h1 class="text-center">We're ready to <strong>help</strong> You!<span>|</span></h1>

						</div> <!-- .header-content -->

					</div> <!-- .content -->

				</div> <!-- .header-bg -->

			</header>
		<?php 
			endif; // if( !logged user )
		?>