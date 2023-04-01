<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;


final class ProjectPresenter extends \App\Presenters\BasePresenter
{
	/**
	 * Render project detail
	 */
	public function renderDetail($projecturl)
	{
		$this->template->projectDetail = $this->projectModel->getProjectByUrl($projecturl);
	}

	/**
	 * Render list of projects - with AJAX filtering
	 */
	public function renderDefault($filter=null){
		$this->template->projects = $this->projectModel->getVisibleProjects($filter);
		$this->template->tags = $this->tagModel->getAllTags();
		$this->template->activeFilter = $filter;
		$this->redrawControl("filter");
		$this->redrawControl("projects");
	}

	/**
	 * Render create new project view
	 */
	public function renderCreate(){
		$this->protect();
	}

	/**
	 * Render edit project view
	 */
	public function renderEdit($id) :void{
		$this->protect();
		$this->template->projectDetail = $project = $this->projectModel->getProjectById($id);

		if(!$project){
			$this->error("neexistuje");
		}

		$defaults = $project->toArray();
		$defaults["project_date"] = $project->project_date->format("Y-m-d");
		$defaults["tags"] = $project->related("tag")->fetchPairs("id", "id");
		$this->getComponent('editForm')->setDefaults($defaults);
	}

	/** Factory for creating project form
	 * 
	 * @return Nette\Application\UI\Form
	 */
	public function createComponentProjectForm() :Form {
		$form = new Form;

		$form->addText("name", "")
			->setRequired()
			->setHtmlAttribute("class", "input input-title")
			->setHtmlAttribute("placeholder", "Název projektu");
		$form->addText("project_date", "")
			->setHtmlAttribute("placeholder", "Název projektu")
			->setHtmlType("date")
			->setRequired()
			->setHtmlAttribute("class", "input input-title");


		$form->addSubmit("send", "Vytvořit")
			->setHtmlAttribute("class", "center-button main-btn");
		$form->onSuccess[]=[$this, "addFormSuccess"];

		return $form;
	}

	/** Factory for creating edit project form
	 * 
	 * @todo Tady by stálo za to obě továrničky sjednotit a parametrizovat
	 * @return Nette\Application\UI\Form
	 */
	public function createComponentEditForm() :Form{
		$form = new Form;

		$form->addHidden("id");
		$form->addText("name", "Název:")
			->setRequired()
			->setHtmlAttribute("class", "input input-title");
		$form->addCheckboxList("tags","Kategorie:", $this->tagModel->getTagsPairs());
		$form->addText("project_date", "Datum:")->setHtmlType("date")->setRequired();
		$form->addTextArea("description", "Popis:")->setRequired();
		$form->addUpload("image_square", "Obrázek 1:1");
		$form->addUpload("image_portrait", "Obrázek na výšku");
		$form->addUpload("image_landscape", "Obrázek na šířku");
		$form->addMultiUpload('images', 'Fotky');
		$form->addTextArea("mediabox", "Prostor pro video");
		$form->addTextArea("footer", "Závěr:");
		$form->addCheckbox("visible", "zobrazit");
		$form->addSubmit("send", "Odeslat");

		$form->onSuccess[]=[$this, "editFormSuccess"];

		return $form;
	}

	/** Callback for project add form success
	 * @param array Array of values
	 */
	public function addFormSuccess(array $data) : void{
		$this->protect();
		$id = $this->projectModel->createNewProject($data);
		$this->redirect(":edit", ['id' => $id]);
	}

	/** Callback for project edit form success
	 * 
	 * @param array Array of values
	 */
	public function editFormSuccess(array $data) : void{
		$this->protect();
		$this->projectModel->updateProject($data);
		$this->redirect(":default");
	}

	/** Handler for moving picture order up
	 * @param int  Id of picture
	 */
	public function handleMoveUp($id) {
		$this->protect();
		$this->projectModel->movePictureUp($id);
		$this->redrawControl("gallery");
	}
	
	/** Handler for moving picture order down
	 * @param int  Id of picture
	 */
	public function handleMoveDown($id) {
		$this->protect();
		$this->projectModel->movePictureDown($id);
		$this->redrawControl("gallery");
	}

	/** Handler for removing picture from project
	 * @param int  Id of picture
	 */
	public function handleRemove($id) {
		$this->protect();
		$this->projectModel->removePicture($id);
		$this->redrawControl("gallery");
	}
}
