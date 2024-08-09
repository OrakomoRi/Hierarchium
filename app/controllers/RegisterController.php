<?php

require_once '../app/models/User.php';
require_once '../app/services/RegisterService.php';
require_once 'BaseController.php';

class RegisterController extends BaseController
{
	private $registerService;

	/**
	 * Constructor to initialize RegisterService
	 * @param RegisterService $registerService - Service to handle registration
	 */
	public function __construct($registerService)
	{
		$this->registerService = $registerService;
	}

	/**
	 * Handle user registration
	 * Processes registration attempt and provides feedback
	 */
	public function register()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$username = $_POST['username'] ?? '';
			$password = $_POST['password'] ?? '';

			$result = $this->registerService->register($username, $password);

			if ($result === true) {
				$_SESSION['authenticated'] = true;
				$_SESSION['username'] = $username;
				if ($this->isAjaxRequest()) {
					echo json_encode(['success' => true]);
				} else {
					header('Location: /user');
				}
			} else {
				if ($this->isAjaxRequest()) {
					echo json_encode(['success' => false, 'error' => $result]);
				} else {
					include '../app/views/login.php';
				}
			}
			exit();
		}
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