<?php

require_once 'BaseController.php';

class UserController extends BaseController
{
	/**
	 * Handle the request to view the user page
	 */
	public function index()
	{
		// Check if the user is authenticated
		if (empty($_SESSION['authenticated'])) {
			// Render error if not authenticated
			$this->renderError('You must be logged in to view this page');
		} else {
			// Include user view if authenticated
			include '../app/views/user.php';
		}
	}
}