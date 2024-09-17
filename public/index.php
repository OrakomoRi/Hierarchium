<?php

// Include configuration and necessary classes
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

require_once '../app/core/Router.php';

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

// Initialize and call the router
$router = new Router($authController, $registerController, $userController, $sectionController);
$router->route($request);