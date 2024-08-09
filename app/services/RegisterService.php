<?php

class RegisterService
{
	private $userRepository;

	/**
	 * Initialize RegisterService with a user repository
	 * @param UserRepository $userRepository - The user repository
	 */
	public function __construct($userRepository)
	{
		$this->userRepository = $userRepository;
	}

	/**
	 * Register a new user
	 * @param string $username - New user's login name
	 * @param string $password - New user's password
	 * @return bool|string - True if registration is successful, or error message otherwise
	 */
	public function register($username, $password)
	{
		// Check if username already exists
		$user = $this->userRepository->getUserByUsername($username);
		if ($user) {
			return 'The user already exists';
		}

		// Validate password requirements
		if (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{10,}$/', $password)) {
			return 'The password should contain at least 10 characters, 1 uppercase letter, 1 lowercase letter';
		}

		// Hash the password
		$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

		// Create new user in the database
		if ($this->userRepository->createUser($username, $hashedPassword)) {
			return true;
		} else {
			return 'Registration failed';
		}
	}
}