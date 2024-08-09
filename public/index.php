<?php

// Include configuration and necessary classes
require_once '../config/config.php';
require_once '../app/controllers/AuthController.php';
require_once '../app/controllers/BaseController.php';
require_once '../app/controllers/RegisterController.php';
require_once '../app/controllers/UserController.php';
require_once '../app/controllers/SectionController.php';
require_once '../app/services/AuthService.php';
require_once '../app/services/RegisterService.php';
require_once '../app/services/SectionService.php';
require_once '../app/repositories/SectionRepository.php';
require_once '../app/repositories/UserRepository.php';

// Start session
session_start();

// Load configuration
$config = require '../config/config.php';

try {
	// Set up database connection
	$db = new PDO($config['db']['dsn']);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// Initialize repositories
	$userRepository = new UserRepository($db);
	$sectionRepository = new SectionRepository($db);

	// Initialize services with repositories
	$authService = new AuthService($userRepository);
	$registerService = new RegisterService($userRepository);
	$sectionService = new SectionService($sectionRepository);
} catch (PDOException $e) {
	// Handle database connection error
	$controller = new BaseController();
	$controller->renderError('Database connection failed: ' . $e->getMessage());
	exit();
}

// Initialize controllers
$authController = new AuthController($authService);
$registerController = new RegisterController($registerService);
$userController = new UserController();
$sectionController = new SectionController($sectionService);

// Get request URI
$request = strtok($_SERVER['REQUEST_URI'], '?');

try {
	// Route based on request URI
	switch ($request) {
		case '/login':
		case '/':
			$authController->login();
			break;

		case '/signup':
			$registerController->register();
			break;
		
		case '/user':
			$userController->index();
			break;
		
		case '/logout':
			$authController->logout();
			break;
		
		case '/sections/create':
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$sectionController->addSection();
			}
			break;
		
		case '/sections/update':
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$sectionController->updateSection();
			}
			break;
		
		case '/sections/delete':
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$sectionController->deleteSection();
			}
			break;
		
		case '/sections/get':
			if ($_SERVER['REQUEST_METHOD'] === 'GET') {
				$sectionController->fetchSections();
			}
			break;
		
		default:
			// Handle 404 error
			http_response_code(404);
			$controller = new BaseController();
			$controller->render404();
			break;
	}
} catch (Exception $e) {
	// Handle general exceptions
	$controller = new BaseController();
	$controller->renderError($e->getMessage());
}