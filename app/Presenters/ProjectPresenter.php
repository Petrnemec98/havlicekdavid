<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;


final class ProjectPresenter extends \App\Presenters\BasePresenter
{

	public function renderDetail($projecturl)
	{
		$this->template->projectDetail = $this->projectModel->getProjectByUrl($projecturl);
	}

	public function renderDefault($filter=null){
		$this->template->projects = $this->projectModel->getVisibleProjects($filter);
		$this->template->tags = $this->tagModel->getAllTags();
		$this->template->activeFilter = $filter;
		$this->redrawControl("filter");
		$this->redrawControl("projects");
	}

	public function renderCreate(){
	}

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
		$form->onSuccess[]=[$this, "sendSuccessed"];

		return $form;
	}

	public function sendSuccessed(array $data) : void{
		$id = $this->projectModel->createNewProject($data);
		$this->redirect(":edit", ['id' => $id]);
	}

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

	public function editFormSuccess($form, $data) : void{
		$this->projectModel->updateProject($data);
		$this->redirect(":default");
	}

	public function renderEdit($id) :void{
		$project = $this->projectModel->getProjectById($id);

		if(!$project){
			$this->error("neexistuje");
		}

		$defaults = $project->toArray();
		$defaults["project_date"] = $project->project_date->format("Y-m-d");
		$defaults["tags"] = $project->related("tag")->fetchPairs("id", "id");
		$this->getComponent('editForm')->setDefaults($defaults);
	}

}
