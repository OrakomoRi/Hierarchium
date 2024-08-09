<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="icon" type="image/svg" href="/images/svg/juice.svg">
	<title>User page</title>

	<link rel="stylesheet" href="/css/min/reset.min.css">
	<link rel="stylesheet" href="/css/min/variables.min.css">
	<link rel="stylesheet" href="/css/min/notification.min.css">
	<link rel="stylesheet" href="/css/min/user.min.css">

	<script src="/js/min/notification.min.js" defer></script>
	<script src="/js/min/user.min.js" defer></script>
</head>
<body>
	<div class="container">
		<!-- Container for sections -->
	</div>
	<div class="splitter"></div>
	<aside class="sidebar">
		<div class="user_profile">
			<img src="/images/png/min/therock.png" alt="User avatar" class="profile_picture">
			<p><?= htmlspecialchars($_SESSION['username']) ?></p>
		</div>
		<div class="buttons">
			<a href="/logout" class="logout">
				<svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><path d="m201.959 358.402 39.817 39.82L383.999 256 241.776 113.777l-39.817 39.821 73.959 73.959H0v56.886h274.485z"/><path d="M455.112 0H56.888C25.598 0 0 25.598 0 56.888v113.779h56.888V56.89h398.224v398.223H56.888v-113.78H0v113.779C0 486.402 25.598 512 56.888 512h398.224c31.29 0 56.888-25.598 56.888-56.888V56.888C512 25.598 486.402 0 455.112 0"/></svg>
				<span>Logout</span>
			</a>
			<button class="create">
				<svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><path d="M387.692 281.846H281.846v105.846h-51.692V281.846H124.308v-51.692h105.846V124.308h51.692v105.846h105.846z"/><path d="M256 51.692c54.523 0 105.846 21.293 144.492 59.816 38.523 38.646 59.816 89.969 59.816 144.492s-21.293 105.846-59.816 144.492c-38.646 38.523-89.969 59.816-144.492 59.816s-105.846-21.293-144.492-59.816C72.985 361.846 51.692 310.523 51.692 256s21.293-105.846 59.816-144.492C150.154 72.985 201.477 51.692 256 51.692M256 0C114.585 0 0 114.585 0 256s114.585 256 256 256 256-114.585 256-256S397.415 0 256 0"/></svg>
				<span>Create</span>
			</button>
		</div>
	</>
</body>
</html>