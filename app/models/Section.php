<?php

class Section
{
	public $id;
	public $username;
	public $title;
	public $description;
	public $parent_id;

	/**
	 * Constructor to initialize a Section object
	 * @param string $id - Section ID
	 * @param string $username - Username of the section owner
	 * @param string $title - Title of the section
	 * @param string $description - Description of the section
	 * @param int|null $parent_id - Parent ID of the section
	 */
	public function __construct($id, $username, $title, $description, $parent_id = null)
	{
		$this->id = $id;
		$this->username = $username;
		$this->title = $title;
		$this->description = $description;
		$this->parent_id = $parent_id;
	}
}