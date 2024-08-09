<?php

class BaseController
{
	/**
	 * Render an error page with a specific error message
	 * @param string $error_message - The error message to display
	 */
	public function renderError($error_message)
	{
		include '../app/views/error.php';  // Include error view
	}

	/**
	 * Render a 404 page for not found errors
	 */
	public function render404()
	{
		include '../app/views/404.php';  // Include 404 view
	}
}
