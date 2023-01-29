<?php

declare(strict_types=1);

namespace App\Models;

use Nette;
use Nette\Utils\Strings;

class HomepageModel
{
	protected $db;
	public function __construct(\Nette\Database\Explorer $db){
		$this->db = $db;
	}

	public function getHomepageProjects() {
		return $this->db->table('homepage')->order('homepage.id DESC');
	}
}
