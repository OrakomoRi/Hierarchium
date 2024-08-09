<?php

class SectionService
{
	private $sectionRepository;

	/**
	 * Initialize SectionService with a section repository
	 * @param SectionRepository $sectionRepository - The section repository
	 */
	public function __construct($sectionRepository)
	{
		$this->sectionRepository = $sectionRepository;
	}

	/**
	 * Retrieve all sections for a specific user and build hierarchy
	 * @param string $username - The username to filter sections
	 * @return array - Hierarchical array of sections
	 */
	public function getSections($username)
	{
		$sections = $this->sectionRepository->getSectionsByUsername($username);
		return $this->buildHierarchy($sections);
	}

	/**
	 * Recursively build a hierarchical structure of sections
	 * @param array $sections - List of all sections
	 * @param int|null $parentId - Parent ID to filter sections
	 * @return array - Hierarchical array of sections
	 */
	private function buildHierarchy(array $sections, $parentId = null)
	{
		$branch = [];
		foreach ($sections as $section) {
			$section['parent_id'] = ($section['parent_id'] === 'none' || $section['parent_id'] === 'null') ? null : $section['parent_id'];

			if ($section['parent_id'] == $parentId) {
				$children = $this->buildHierarchy($sections, $section['id']);
				if ($children) {
					$section['subsections'] = $children;
				}
				$branch[] = $section;
			}
		}
		return $branch;
	}

	/**
	 * Generate a unique ID for a new section
	 * @param int $length - Length of the ID
	 * @return string - Unique ID
	 */
	private function generateUniqueId($length = 8)
	{
		do {
			$id = bin2hex(random_bytes($length / 2));
		} while ($this->sectionRepository->getSectionById($id));

		return $id;
	}

	/**
	 * Add a new section
	 * @param string $username - The username of the section owner
	 * @param string $title - The title of the section
	 * @param string $description - The description of the section
	 * @param int|null $parent_id - Parent ID of the section
	 * @return string|false - ID of the new section or false on failure
	 */
	public function addSection($username, $title, $description, $parent_id = null)
	{
		$id = $this->generateUniqueId();
		return $this->sectionRepository->addSection($id, $username, $title, $description, $parent_id) ? $id : false;
	}

	/**
	 * Update an existing section
	 * @param string $id - ID of the section to update
	 * @param string $title - New title of the section
	 * @param string $description - New description of the section
	 * @param int|null $parent_id - New parent ID of the section
	 * @return bool - Success or failure of the update
	 */
	public function updateSection($id, $title, $description, $parent_id = null)
	{
		return $this->sectionRepository->updateSection($id, $title, $description, $parent_id);
	}

	/**
	 * Delete a section and its subsections
	 * @param string $id - ID of the section to delete
	 * @return bool - Success or failure of the delete operation
	 */
	public function deleteSection($id)
	{
		return $this->sectionRepository->deleteSection($id);
	}

	/**
     * Retrieve a section by its ID
     * @param string $id - ID of the section to retrieve
     * @return array|null - Section data or null if not found
     */
    public function getSectionById($id)
    {
        return $this->sectionRepository->getSectionById($id);
    }
}