<?php

require_once '../app/services/SectionService.php';

class SectionController
{
	private $sectionService;

	/**
	 * Constructor to initialize SectionService
	 * @param SectionService $sectionService - Instance of SectionService
	 */
	public function __construct($sectionService)
	{
		$this->sectionService = $sectionService;
	}

	/**
	 * Render the user view if the user is authenticated
	 * Redirect to login if not authenticated
	 */
	public function getSections()
	{
		if (isset($_SESSION['username'])) {
			include __DIR__ . '/../views/user.php';
		} else {
			header('Location: /login');
			exit();
		}
	}

	/**
	 * Handle the addition of a new section
	 * Responds with JSON indicating success or failure
	 */
	public function addSection()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['username'])) {
			$title = htmlspecialchars($_POST['title']);
			$description = htmlspecialchars($_POST['description']);
			$parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;

			// Check for title and description length
			if (strlen($title) > 40 || strlen($description) > 400) {
				header('Content-Type: application/json');
				echo json_encode(['success' => false, 'error' => 'Title or description too long']);
				return;
			}

			$insertedId = $this->sectionService->addSection($_SESSION['username'], $title, $description, $parent_id);

			header('Content-Type: application/json');
			if ($insertedId) {
				$newSection = $this->sectionService->getSectionById($insertedId);
				echo json_encode(['success' => true, 'section' => $newSection]);
			} else {
				echo json_encode(['success' => false, 'error' => 'Failed to add section']);
			}
		} else {
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'error' => 'Invalid request']);
		}
	}

	/**
	 * Handle updating an existing section
	 * Responds with JSON indicating success or failure
	 */
	public function updateSection()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['username'])) {
			$id = $_POST['id'];
			$title = htmlspecialchars($_POST['title']);
			$description = htmlspecialchars($_POST['description']);
			$parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;

			// Check for title and description length
			if (strlen($title) > 40 || strlen($description) > 400) {
				header('Content-Type: application/json');
				echo json_encode(['success' => false, 'error' => 'Title or description too long']);
				return;
			}

			// Update section and respond with result
			$success = $this->sectionService->updateSection($id, $title, $description, $parent_id);
			header('Content-Type: application/json');
			echo json_encode(['success' => $success]);
		} else {
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'error' => 'Invalid request']);
		}
	}

	/**
	 * Handle deleting a section
	 * Responds with JSON indicating success or failure
	 */
	public function deleteSection()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['username'])) {
			$json = file_get_contents('php://input');
			$data = json_decode($json, true);

			// Validate section ID
			if (!isset($data['id']) || !is_string($data['id'])) {
				http_response_code(400);
				header('Content-Type: application/json');
				echo json_encode(['success' => false, 'error' => 'Invalid or missing section ID']);
				return;
			}

			$id = $data['id'];
			$success = $this->sectionService->deleteSection($id);

			header('Content-Type: application/json');
			echo json_encode(['success' => $success]);
		} else {
			http_response_code(400);
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'error' => 'Invalid request']);
		}
	}

	/**
	 * Fetch all sections for the authenticated user
	 * Responds with JSON containing sections or an error message
	 */
	public function fetchSections()
	{
		if (isset($_SESSION['username'])) {
			$sections = $this->sectionService->getSections($_SESSION['username']);
			header('Content-Type: application/json');
			echo json_encode(['success' => true, 'sections' => $sections]);
		} else {
			header('Content-Type: application/json');
			echo json_encode(['success' => false, 'error' => 'User not authenticated']);
		}
	}
}