<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="icon" type="image/svg" href="/images/svg/juice.svg">
	<title>Login</title>

	<link rel="stylesheet" href="/css/min/reset.min.css">
	<link rel="stylesheet" href="/css/min/variables.min.css">
	<link rel="stylesheet" href="/css/min/notification.min.css">
	<link rel="stylesheet" href="/css/min/login.min.css">

	<script src="/js/min/notification.min.js" defer></script>
	<script src="/js/min/login.min.js" defer></script>
</head>
<body>
	<div class="wrapper">
		<div class="background"></div>
		<form class="signin_form" method="post" action="/login">
			<div class="form_inner">
				<span>Sign in</span>
				<div class="username">
					<input type="text" id="login_username" name="username" placeholder="Username.." required>
				</div>
				<div class="password">
					<input type="password" id="login_password" name="password" placeholder="Password.." required>
				</div>
				<button type="submit">Sign in</button>
				<p>Don't have an account? <a href="#" class="to_signup">Sign up</a></p>
			</div>
		</form>

		<!-- Signup form (initially hidden) -->
		<form class="signup_form" method="post" action="/signup" style="display: none;">
			<div class="form_inner">
				<span>Sign up</span>
				<div class="username">
					<input type="text" id="signup_username" name="username" placeholder="Username.." required>
				</div>
				<div class="password">
					<input type="password" id="signup_password" name="password" placeholder="Password.." required>
				</div>
				<button type="submit">Sign up</button>
				<p>Already registered? <a href="#" class="to_signin">Sign in</a></p>
			</div>
		</form>
	</div>
</body>
</html>