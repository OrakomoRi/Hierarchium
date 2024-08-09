<?php

class UserRepository
{
	private $db; // Database connection

	/**
	 * Initialize UserRepository with a database connection
	 * @param PDO $db - Database connection
	 */
	public function __construct($db)
	{
		$this->db = $db; // Set the database connection
	}

	/**
	 * Get user data by username
	 * @param string $username - User's login name
	 * @return array|null - User data as an associative array or null if not found
	 */
	public function getUserByUsername($username)
	{
		// Prepare SQL statement to select user by username
		$stmt = $this->db->prepare('SELECT * FROM users WHERE username = :username');
		$stmt->bindParam(':username', $username); // Bind username parameter
		$stmt->execute(); // Execute the statement

		// Fetch user data
		$user = $stmt->fetch(PDO::FETCH_ASSOC);

		// Return user data or null if user not found
		return $user ?: null;
	}

	/**
	 * Create a new user in the database
	 * @param string $username - New user's login name
	 * @param string $password - New user's hashed password
	 * @return bool - True if user creation is successful, false otherwise
	 */
	public function createUser($username, $password)
	{
		// Prepare SQL statement to insert new user
		$stmt = $this->db->prepare('INSERT INTO users (username, password) VALUES (:username, :password)');
		$stmt->bindParam(':username', $username);
		$stmt->bindParam(':password', $password);

		// Execute the statement and return result
		return $stmt->execute();
	}
}