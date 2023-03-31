<?php

declare(strict_types=1);

namespace App\Models;

use Nette;
use Nette\Utils\Strings;
use Nette\Utils\Image;

class ProjectModel
{
	private $db;
	private $dir;
	public function __construct(\Nette\Database\Explorer $db, \App\Services\Direr $dir){
		$this->db = $db;
		$this->dir = $dir;
	}

	public function getProjectByUrl($url){
		return $this->db->table('project')->where('url',$url)->fetch();
	}

	public function getProjectById($id){
		return $this->db->table('project')->wherePrimary($id)->fetch();
	}

	public function getAllProjects() {
		return $this->db->table("project")->order("order, id")->fetchAll();
	}

	public function getVisibleProjects($filter=null) {
		$query= $this->db->table("project")->order("order DESC, id DESC")->where("visible = 1");
		if($filter){
			return $query->where(":project_has_tag.tag.url", $filter);
		}
		return $query;
	}


	public function createNewProject($data){
		$data["url"] = Strings::webalize($data["name"]);


		$this->db->beginTransaction();
		$row = $this->db->table("project")->insert($data);
		$this->db->commit();
		return $row->id;
	}

	public function processImage($image, $format, $projectId){
		$dir = $this->dir->wwwDir."/image/projects/$projectId";
		$this->makeFolder($dir);

		$image = Image::fromFile($image->temporaryFile);
		$image->resize(null, 720);
		$image->save("$dir/$format.webp");
	}

	public function processGalleryImage($image, $projectId){
		$dir = $this->dir->wwwDir."/image/projects/$projectId";


		$imageId = $this->db->table("image")->insert([
			"project_id"=> $projectId
		]);

		$image = Image::fromFile($image->temporaryFile);
		$image->resize(null, 1920);
		$image->save("$dir/jpg/$imageId.jpg");

		$image->resize(null, 720);
		$image->save("$dir/webp/$imageId.webp");
	}

	private function makeFolder($dir){
		if(!is_dir($dir)){
			mkdir($dir,0777, true);
		}
		return true;

	}

	public function updateProject($formdata){
		$data = $formdata;
		$tags=$data["tags"];
		$id = $data["id"];
		unset($data["tags"]);
		unset($data["id"]);


		if ($formdata->image_landscape->isOk()){
			$data['image_landscape']=$this->processImage($formdata->image_landscape, "landscape", $id);
		}
		if ($formdata->image_square->isOk()){
			$data['image_square']=$this->processImage($formdata->image_square, "square", $id);
		}
		if ($formdata->image_portrait->isOk()){
			$data['image_portrait']=$this->processImage($formdata->image_portrait, "portrait", $id);
		}
		unset($data['image_landscape']);
		unset($data['image_square']);
		unset($data['image_portrait']);

		$this->makeFolder($this->dir->wwwDir."/image/projects/$id/webp");
		$this->makeFolder($this->dir->wwwDir."/image/projects/$id/jpg");

		foreach($data->images as $image){
			$this->processGalleryImage($image, $id);
		}
		unset($data['images']);

		// BeginTransaction => Dělá z toho jednu operaci "atomickou operaci", byť je zapis do více tabulek
		$this->db->beginTransaction();
		$this->db->table('project')->wherePrimary($id)->update($data);

		//Používáme foreach pro, aby se dalo použít více tagů k jednomu projektu = doi více řádků
		$this->db->table("project_has_tag")->where("project_id",$id)->delete();
		foreach ($tags as $key => $value){
			$this->db->table("project_has_tag")->insert([
				"project_id" => $id,
				"tag_id"=> $value
			]);
		}
		$this->db->commit();
	}

	private function movePicture($pictureId, $direction) {
		$picture = $this->db->table("image")->wherePrimary($pictureId)->fetch();
		$i = 1;
		foreach ($this->db->table("image")->where("project_id", $picture->project_id)->order("order, id") as $row) {
			if ($row->id == $pictureId) {
				$row->update(["order" => $i + $direction]);
			} else {
				$row->update(["order" => $i]);
			}

			$i+=2;
		}

		$i = 1;
		foreach ($this->db->table("image")->where("project_id", $picture->project_id)->order("order, id") as $row) {
			$row->update(["order" => $i]);
			$i+=1;
		}
	}

	public function movePictureUp($pictureId) {
		$this->movePicture($pictureId, -3);
	}

	public function movePictureDown($pictureId) {
		$this->movePicture($pictureId, 3);
	}

	public function removePicture($pictureId) {
		$this->db->table("image")->wherePrimary($pictureId)->delete();

	}


}
