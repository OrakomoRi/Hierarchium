<?php

$config = require 'config/config.php';

try {
	$db = new PDO($config['db']['dsn']);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// Create table users
	$db->exec("CREATE TABLE IF NOT EXISTS users (
		username TEXT NOT NULL UNIQUE PRIMARY KEY,
		password TEXT NOT NULL
	)");

	// Create table sections
	$db->exec("CREATE TABLE IF NOT EXISTS sections (
		id TEXT UNIQUE PRIMARY KEY,
		username TEXT NOT NULL,
		parent_id TEXT,
		title TEXT NOT NULL,
		description TEXT NOT NULL,
		FOREIGN KEY (username) REFERENCES users(username)
	)");

	// Add default user
	$username = 'admin';
	$password = password_hash('admin123098', PASSWORD_DEFAULT);

	$stmt = $db->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
	$stmt->bindParam(':username', $username);
	$stmt->bindParam(':password', $password);

	$stmt->execute();

	echo "User created successfully.";
} catch (PDOException $e) {
	die('Database setup failed: ' . $e->getMessage());
}