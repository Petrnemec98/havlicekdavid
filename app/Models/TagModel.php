<?php

declare(strict_types=1);

namespace App\Models;

use Nette;
use Nette\Database\Explorer;

class TagModel {

	/**
	 * @var Nette\Database\Explorer
	 */
	private $db;

	/**
	 * Class constructor for injecting Database instance
	 * @param Nette\Database\Explorer $db Database Explorer instance
	 */
	public function __construct(Explorer $db){
		$this->db = $db;
	}
	
	/**
	* Retrieves all tags as key-value pairs, with the ID as the key and the name as the value.
	* @return array An array of tags with IDs as keys and names as values.
	*/
	public function getTagsPairs(){
		return $this->db->table('tag')->fetchPairs("id", "name");
	}
	
	/**
 	* Retrieves all tags from the database.
	* @return \Nette\Database\Table\Selection A selection of all tags.
	*/
	public function getAllTags(){
		return $this->db->table('tag');
	}

}
