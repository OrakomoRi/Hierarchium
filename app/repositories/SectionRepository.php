<?php

class SectionRepository
{
	private $db; // Database connection

	/**
	 * Initialize SectionRepository with a database connection
	 * @param PDO $db - Database connection instance
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}

	/**
	 * Retrieve a section by its ID
	 * @param string $id - ID of the section to retrieve
	 * @return array|null - Section data or null if not found
	 */
	public function getSectionById($id)
	{
		$stmt = $this->db->prepare("SELECT * FROM sections WHERE id = :id");
		$stmt->bindParam(':id', $id);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * Retrieve all sections for a specific user
	 * @param string $username - The username to filter sections
	 * @return array - List of sections
	 */
	public function getSectionsByUsername($username)
	{
		$stmt = $this->db->prepare("SELECT * FROM sections WHERE username = :username ORDER BY parent_id ASC, id ASC");
		$stmt->bindParam(':username', $username);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Add a new section to the database
	 * @param string $id - The ID of the new section
	 * @param string $username - The username of the section owner
	 * @param string $title - The title of the section
	 * @param string $description - The description of the section
	 * @param int|null $parent_id - Parent ID of the section
	 * @return bool - Success or failure of the insert operation
	 */
	public function addSection($id, $username, $title, $description, $parent_id = null)
	{
		$stmt = $this->db->prepare("INSERT INTO sections (id, username, title, description, parent_id) VALUES (:id, :username, :title, :description, :parent_id)");
		$stmt->bindParam(':id', $id);
		$stmt->bindParam(':username', $username);
		$stmt->bindParam(':title', $title);
		$stmt->bindParam(':description', $description);
		$stmt->bindParam(':parent_id', $parent_id);
		return $stmt->execute();
	}

	/**
	 * Update an existing section
	 * @param string $id - ID of the section to update
	 * @param string $title - New title of the section
	 * @param string $description - New description of the section
	 * @param int|null $parent_id - New parent ID of the section
	 * @return bool - Success or failure of the update operation
	 */
	public function updateSection($id, $title, $description, $parent_id = null)
	{
		$stmt = $this->db->prepare("UPDATE sections SET title = :title, description = :description, parent_id = :parent_id WHERE id = :id");
		$stmt->bindParam(':title', $title);
		$stmt->bindParam(':description', $description);
		$stmt->bindParam(':parent_id', $parent_id);
		$stmt->bindParam(':id', $id);
		return $stmt->execute();
	}

	/**
	 * Delete a section and its subsections
	 * @param string $id - ID of the section to delete
	 * @return bool - Success or failure of the delete operation
	 */
	public function deleteSection($id)
	{
		$stmt = $this->db->prepare("DELETE FROM sections WHERE id = :id OR parent_id = :id");
		$stmt->bindParam(':id', $id);
		return $stmt->execute();
	}
}