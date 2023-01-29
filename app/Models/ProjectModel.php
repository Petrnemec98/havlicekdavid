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

	public function getAllProjects() {
		return $this->db->table("project")->order("id DESC")->fetchAll();
	}

	public function getVisibleProjects() {
		return $this->db->table("project")->order("id DESC")->where("visible = 1");
	}


	public function createNewProject($data){
		$data["url"] = Strings::webalize($data["name"]);


		$this->db->beginTransaction();
		$row = $this->db->table("project")->insert($data);
		$this->db->commit();
		return $row->id;
	}

	public function updateProject($data){
		$tags=$data["tags"];
		$id = $data["id"];
		unset($data["tags"]);
		unset($data["id"]);

		// BeginTransaction => Dělá z toho jednu operaci "atomickou operaci", byť je zapis do více tabulek
		$this->db->beginTransaction();
		$this->db->table('project')->wherePrimary($id)->update($data);

		//Používáme foreach pro, aby se dalo použít více tagů k jednomu projektu = doi více řádků
		foreach ($tags as $key => $value){
			$this->db->table("project_has_tag")->insert([
				"project_id" => $id,
				"tag_id"=> $value
			]);
		}
		$this->db->commit();

	}

}
