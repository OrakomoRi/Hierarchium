<?php

class Router {
	private $authController;
	private $registerController;
	private $userController;
	private $sectionController;

	public function __construct($authController, $registerController, $userController, $sectionController) {
		$this->authController = $authController;
		$this->registerController = $registerController;
		$this->userController = $userController;
		$this->sectionController = $sectionController;
	}

	public function route($request) {
		switch ($request) {
			case '/login':
			case '/':
				$this->authController->login();
				break;

			case '/signup':
				$this->registerController->register();
				break;

			case '/user':
				$this->userController->index();
				break;

			case '/logout':
				$this->authController->logout();
				break;

			case '/sections/create':
				if ($_SERVER['REQUEST_METHOD'] === 'POST') {
					$this->sectionController->addSection();
				}
				break;

			case '/sections/update':
				if ($_SERVER['REQUEST_METHOD'] === 'POST') {
					$this->sectionController->updateSection();
				}
				break;

			case '/sections/delete':
				if ($_SERVER['REQUEST_METHOD'] === 'POST') {
					$this->sectionController->deleteSection();
				}
				break;

			case '/sections/get':
				if ($_SERVER['REQUEST_METHOD'] === 'GET') {
					$this->sectionController->fetchSections();
				}
				break;

			default:
				// Handle 404 error
				http_response_code(404);
				$controller = new BaseController();
				$controller->render404();
				break;
		}
	}
}