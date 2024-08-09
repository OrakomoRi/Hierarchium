<?php

class AuthService
{
	private $userRepository; // Repository for accessing user data

	/**
	 * Initialize AuthService with a user repository
	 * @param UserRepository $userRepository - Repository for user data
	 */
	public function __construct($userRepository)
	{
		$this->userRepository = $userRepository; // Set the user repository
	}

	/**
	 * Authenticate user by username and password
	 * @param string $username - User's login name
	 * @param string $password - User's password
	 * @return bool - True if authentication is successful, false otherwise
	 */
	public function authenticate($username, $password)
	{
		// Get user data from the repository
		$user = $this->userRepository->getUserByUsername($username);

		// Verify password and return authentication result
		return $user && password_verify($password, $user['password']);
	}
}