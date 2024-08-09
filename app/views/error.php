<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="icon" type="image/svg" href="/images/svg/juice.svg">
	<title>Error</title>

	<link rel="stylesheet" href="/css/min/reset.min.css">
	<link rel="stylesheet" href="/css/min/variables.min.css">
	<link rel="stylesheet" href="/css/min/error.min.css">
</head>
<body>
	<div class="container">
		<h1>Error</h1>
		<p><?php echo htmlspecialchars($error_message); ?></p>
	</div>
	<div class="footer">
		<p>Take me back to
			<a href="/">
				<span>home</span>
			</a>
		</p>
	</div>
</body>
</html>