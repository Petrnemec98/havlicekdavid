<?php

declare(strict_types=1);

namespace App\Models;

use Nette;
use Nette\Utils\Strings;

class HomepageModel
{
	/**  @var Nette\Database\Explorer  Nette database connection */
	protected $db;

	/**
	 * Model constructor - for injecting database instance (DI)
	 * 
	 * @param Nette\Database\Explorer Database instance
	 */
	public function __construct(\Nette\Database\Explorer $db){
		$this->db = $db;
	}

	/**
	 * Gets all the projects on homepage ordered by homepage position
	 * 
	 * @return Nette\Database\Table\Selection
	 */
	public function getHomepageProjects() {
		return $this->db->table('homepage')->order('homepage.id DESC');
		
	}

	/**
	 * Update projects on homepage
	 * 
	 * @param array $data - Associated array (key = homepage.id, value = project.id)
	 * 
	 */
	public function updateHomepage ($data){
		$this->db->table("homepage")->where("id","project_id")->delete();

		for ($i=1;$i<=5;$i++){
			$this->db->table("homepage")->where("id", $i)->update(["project_id" => $data[$i]]);
		}
	}

}
