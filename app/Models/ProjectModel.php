<?php

declare(strict_types=1);

namespace App\Models;

use Nette;
use Nette\Utils\Strings;

class ProjectModel
{
	private $db;
	public function __construct(\Nette\Database\Explorer $db){
		$this->db = $db;
	}

	public function getProjectByUrl($url){
		return $this->db->table('project')->where('url',$url)->fetch();
	}

	public function getProjectById($id){
		return $this->db->table('project')->wherePrimary($id)->fetch();
	}

	public function getAllProjects(){
		return $this->db->table("project")->order("id DESC");
	}

	public function createNewProject($data){
		$data["url"] = Strings::webalize($data["name"]);
		$this->db->table("project")->insert($data);
	}

}
