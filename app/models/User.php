<?php

class User
{
	// Public properties for login and password
	public $login;
	public $password;

	/**
	 * Initialize User with login and password
	 * @param string $username - User login
	 * @param string $password - User password
	 */
	public function __construct($username, $password)
	{
		$this->login = $username; // Set the login property
		$this->password = $password; // Set the password property
	}
}