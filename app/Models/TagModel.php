<?php

declare(strict_types=1);

namespace App\Models;

use Nette;
use Nette\Utils\Strings;

class TagModel{

	private $db;
	public function __construct(\Nette\Database\Explorer $db){
		$this->db = $db;
	}
	public function getTagsPairs(){
		return $this->db->table('tag')->fetchPairs("id", "name");
	}

}
