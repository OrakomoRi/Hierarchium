<?php

require_once '../app/services/AuthService.php';
require_once 'BaseController.php';

class AuthController extends BaseController
{
	private $authService;

	/**
	 * Constructor to initialize AuthService
	 * @param AuthService $authService - Service to handle authentication
	 */
	public function __construct($authService)
	{
		$this->authService = $authService;
	}

	/**
	 * Handle user login
	 * Displays login form or processes login attempt
	 */
	public function login()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$login = $_POST['username'] ?? '';
			$password = $_POST['password'] ?? '';

			if ($this->authService->authenticate($login, $password)) {
				$_SESSION['authenticated'] = true;
				$_SESSION['username'] = $login;
				if ($this->isAjaxRequest()) {
					echo json_encode(['success' => true]);
				} else {
					header('Location: /user');
				}
			} else {
				$error = 'Incorrect login or password';
				if ($this->isAjaxRequest()) {
					echo json_encode(['success' => false, 'error' => $error]);
				} else {
					include '../app/views/login.php';
				}
			}
			exit();
		}

		include '../app/views/login.php';
	}

	/**
	 * Handle user logout
	 * Clears session and redirects to login page
	 */
	public function logout()
	{
		unset($_SESSION['authenticated']);
		unset($_SESSION['username']);
		header('Location: /login');
		exit();
	}

	/**
	 * Check if the request is an AJAX request
	 * @return bool - True if request is AJAX, false otherwise
	 */
	private function isAjaxRequest()
	{
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
	}
}