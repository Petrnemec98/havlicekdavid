<?php

declare(strict_types=1);

namespace App\Models;

use Nette;
use Nette\Utils\Strings;
use Nette\Utils\Image;
use Nette\Database\Explorer;
use App\Services\Direr;
use Nette\InvalidStateException;

class ProjectModel
{
	/** @var Nette\Database\Explorer Database Instance */
	private $db;

	/** @var App\Services\Direr Direr service instance */
	private $dir;

	/**
	 * Model constructor - for injecting database instance (DI)
	 * 
	 * @param Nette\Database\Explorer $db Database instance
	 * @param App\Services\Direr $dir Direr instance
	 */
	public function __construct(Explorer $db, Direr $dir){
		$this->db = $db;
		$this->dir = $dir;
	}

	/**
	 * Gets project data from database by project url
	 * 
	 * @param string $url URL fragment
	 * @return Nette\Database\Table\ActiveRow
	 */
	public function getProjectByUrl($url){
		return $this->db->table('project')->where('url',$url)->fetch();
	}
	
	/**
	 * Gets project data from database by project id
	 * 
	 * @param int $id in project id
	 * @return Nette\Database\Table\ActiveRow
	 */
	public function getProjectById($id){
		return $this->db->table('project')->wherePrimary($id)->fetch();
	}

	/**
	 * Get all visible projects data from database ordered by order field. Can be filtered by tags.
	 * 
	 * @param null|string $filter If not null, projects are filtered by tag url
	 * @return Nette\Database\Table\Selection
	 */
	public function getVisibleProjects($filter=null) {
		$query= $this->db->table("project")->order("order, id DESC")->where("visible = 1");
		if($filter){
			return $query->where(":project_has_tag.tag.url", $filter);
		}
		return $query;
	}

	/**
	 * Get all projects (visible and invisible) data from database ordered by order field. 
	 * 
	 * @param null|string $filter If not null, projects are filtered by tag url
	 * @return Nette\Database\Table\Selection
	 */
	public function getAllProjects($filter=null) {
		$query= $this->db->table("project")->order("order, id DESC");
		return $query;
	}


	/**
	 * Creates new project record in database.
	 * 
	 * @param array associated array (key = field_name, value = value)
	 * @return int inserted row id
	 */
	public function createNewProject($data){
		$data["url"] = Strings::webalize($data["name"]);


		$this->db->beginTransaction();
		$row = $this->db->table("project")->insert($data);
		$this->db->commit();
		return $row->id;

	}

	/**
	 * Process uploaded image (project theme photo) in proper format 
	 * 	- check and create folder 
	 *  - resize and save webp thumbnail
	 * 
	 * @param  Nette\Http\FileUpload $image Image object form upload form
	 * @param string $format theme format (landscape, square, portrait)
	 * @param int $id Project ID
	 */
	public function processImage($image, $format, $projectId){
		$dir = $this->dir->wwwDir."/image/projects/$projectId";
		$this->makeFolder($dir);

		$image = Image::fromFile($image->temporaryFile);
		$image->resize(null, 720);
		$image->save("$dir/$format.webp");
	}

	/**
	 * Process uploaded image (project gallery picture)  
	 * 
 	 * 	- check and create folder 
	 *  - resize and save webp thumbnail
	 *  - resize and save target jpeg file
	 * 
	 * @param  Nette\Http\FileUpload $image Image object form upload form
	 * @param int $id Project ID
	 */
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

	/**
	 * Creates a folder with the specified directory path if it does not already exist.
	 * 
	 * @param string $dir The directory path where the folder should be created.
	 * @return bool Returns true upon success, otherwise an error will be thrown.
	 */
	private function makeFolder($dir){
		if(!is_dir($dir)){
			mkdir($dir,0777, true);
		}
		return true;

	}

	/**
	 * Updates a project with new data, processes and stores images, and updates project tags.
	 * 
	 * @param Nette\Utils\ArrayHash $formdata An object containing the new data for the project.
	 * @return void
	 * @throws Exception If any database operation fails during the update process.
	 */
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

	/**
	 * Moves a picture's position within a project's gallery in the specified direction.
	 * 
	 * @param int $pictureId The ID of the picture to move.
	 * @param int $direction The direction in which to move the picture (-1 or 1).
	 * @return void
	*/
	private function movePicture($pictureId, $direction) {
		$picture = $this->db->table("image")->wherePrimary($pictureId)->fetch();
		$i = 1;
		foreach ($this->db->table("image")->where("project_id", $picture->project_id)->order("order, id") as $row) {
			if ($row->id == $pictureId) {
				$row->update(["order" => $i + ($direction*3)]);
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

	/**
	 * Moves a picture's position within a project's gallery in the direction up.
	 * 
	 * @param int $pictureId The ID of the picture to move.
	 * @return void
	*/
	public function movePictureUp($pictureId) {
		$this->movePicture($pictureId, -1);
	}
	
	/**
	 * Moves a picture's position within a project's gallery in the direction down.
	 * 
	 * @param int $pictureId The ID of the picture to move.
	 * @return void
	*/
	public function movePictureDown($pictureId) {
		$this->movePicture($pictureId, 1);
	}

	/**
	 * Removes a picture from a project's gallery.
	 * 
	 * @param int $pictureId The ID of the picture to remove.
	 * @return void
	*/
	public function removePicture($pictureId) {
		$this->db->table("image")->wherePrimary($pictureId)->delete();
		//TODO: We need also unlink the file
	}


	/**
	 * Moves a picture's position within a project's gallery in the specified direction.
	 * 
	 * @param int $pictureId The ID of the picture to move.
	 * @param int $direction The direction in which to move the picture (-1 or 1).
	 * @return void
	*/
	private function moveProject($projectId, $direction) {
		$i = 1;
		foreach ($this->db->table("project")->order("order, id") as $row) {
			if ($row->id == $projectId) {
				$row->update(["order" => $i + ($direction*3)]);
			} else {
				$row->update(["order" => $i]);
			}

			$i+=2;
		}

		$i = 1;
		foreach ($this->db->table("image")->order("order, id") as $row) {
			$row->update(["order" => $i]);
			$i+=1;
		}
	}

	/**
	 * Moves a projects's position within a list of projects in the direction up.
	 * 
	 * @param int $projectId The ID of the project to move.
	 * @return void
	*/
	public function moveProjectUp($projectId,) {
		$this->moveProject($projectId, -1);
	}
	
	/**
	 * Moves a projects's position within a list of projects in the direction down.
	 * 
 	 * @param int $projectId The ID of the project to move.
	 * @return void
	*/
	public function moveProjectDown($projectId) {
		$this->moveProject($projectId, 1);
	}

	/**
	 * Set project's visibility to 1
	 * 
 	 * @param int $projectId The ID of the project to show.
	 * @return void
	*/
	public function showProject($projectId) {
		$this->db->table("project")->wherePrimary($projectId)->update(["visible" => "1"]);
	}

	/**
	 * Set project's visibility to 0
	 * 
 	 * @param int $projectId The ID of the project to hide.
	 * @return void
	*/
	public function hideProject($projectId) {
		$this->db->table("project")->wherePrimary($projectId)->update(["visible" => "0"]);
	}

	/**
	 * Remove project included project folders and images
	 * 
 	 * @param int $projectId The ID of the project to hide.
	 * @return void
	*/
	public function removeProject($projectId) {
		throw new InvalidStateException("Not implemented yet");
	}

}
