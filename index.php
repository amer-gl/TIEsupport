<?php
	session_start();
	$pageTitle = 'TIEQUDE Support | Login';

	if ( isset( $_SESSION['user_name']) || isset( $_COOKIE[ 'tiequde_uid' ] ) ) {

		header('Location: dashboard.php'); // Redirect To Dashboard Page
		exit();
		
	}

	include 'init.php';

?>
			<main>
				
				<div class="container">
					
					<div class="main-container">
						
						<div class="support-form login" data-url="http://www.tiequde.com/support/includes/ajax.php">

							<div class="error-container">
								
								<div class="toggle-errors">

									<div class="close-error"><i class="ion-close"></i></div>

									<div class="errors">

										<div class="error">
											<span class="error-icon"><i class="ion-close"></i></span>
											<span class="error-message">Your username doesn't exits!</span>
										</div> <!-- .error -->

									</div> <!-- .errors -->

									<div class="error-forget-password">
										
										<a class="transition" href="#">Forget your password?</a>

									</div> <!-- .error-forget-password -->

								</div> <!-- .toggle-errors -->

								<div class="error-bg transition">
								

								</div> <!-- .error-bg -->

							</div> <!-- .error-container -->
								
							<div class="form-title">
								<h3>Login</h3>
							</div>
							
							<form >
								<div class="support-input">
									<input type="text" name="user_name" id="user_name">
									<span class="transition">Username</span>
									<div class="hover-border transition"></div>
								</div>
								<div class="support-input">
									<input type="password" name="user_pass" id="user_pass">
									<span class="transition">Password</span>
									<div class="hover-border transition"></div>
								</div>
								<div class="remember">
									<input type="hidden" id="remember_me" name="remember_me" value="0" class="hidden">
									<label class="transition" for="remember_me"><span class="transition"></span>Remember Me</label>
								</div>
								<div class="support-button login-button">
									<button id="login-button">Login</button>
									<a href="#" class="forget text-center transition">Forget your password?</a>
								</div>
							</form>

							<div class="check-information">
								<div class="icon"><i class="ion-gear-b"></i></div>
							</div>

							<div class="register"><i class="ion-person-add"></i></div>
							<div class="back-login"><i class="ion-log-in"></i></div>

							<div class="support-form register-form">
								
								<div class="form-title">
									<h3>Register</h3>
								</div>
								
								<form id="register-step1">
									<div class="support-input">
										<input type="text" name="purchase_code" id="purchase_code" required>
										<span class="transition">Purchase code</span>
										<div class="hover-border transition"></div>
									</div>
									<a href="#" class="purchase transition text-center">How can I find my purchase code?</a>
									<div class="support-button next-register-button">
										<button id="next-button">Next</button>
									</div>
								</form>

								<div class="support-form register-form-step2">
									
									<div class="form-title">
										<h3>Register</h3>
									</div>
									
									<form id="register-step2">
										<div class="support-input">
											<input type="text" name="reg_name" id="reg_name">
											<span class="transition">Username</span>
											<div class="hover-border transition"></div>
										</div>
										<div class="support-input">
											<input type="email" name="reg_email" id="reg_email">
											<span class="transition">Email</span>
											<div class="hover-border transition"></div>
										</div>
										<div class="support-input">
											<input type="password" name="reg_pass" id="reg_pass">
											<span class="transition">Password</span>
											<div class="hover-border transition"></div>
										</div>
										<div class="support-button register-button">
											<button id="register-button">Register</button>
										</div>
									</form>

								</div> <!-- .Register-step2 -->

							</div> <!-- .Register -->

						</div> <!-- .login -->

					</div> <!-- .main-container -->

				</div> <!-- .container -->

			</main>

<?php 

	include $tpl . 'footer.php'; ?>