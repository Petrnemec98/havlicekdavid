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

	public function updateHomepage ($formdata){
		$data = $formdata;

		$this->db->table("homepage")->where("id","project_id")->delete();

		for ($i=1;$i<=5;$i++){
			$this->db->table("homepage")->where("id", $i)->update(["project_id" => $data[$i]]);
		}
	}

}
