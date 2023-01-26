<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;


final class ProjectPresenter extends \App\Presenters\BasePresenter
{

	public function renderDetail($projecturl)
	{
		$this->template->projectDetail = $this->projectModel->getProjectByUrl($projecturl);
	}

	public function renderDefault(){
		$this->template->projects = $this->projectModel->getAllProjects();
	}

	public function renderCreate(){
	}

	public function createComponentProjectForm() :Form {
		$form = new Form;

		$form->addText("name", "Název:")
			->setRequired()
			->setHtmlAttribute("class", "input input-title");
		$form->addText("project_date", "Datum:")->setHtmlType("date")->setRequired();
		$form->addSubmit("send", "Odeslat");
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
		//$form->addUpload("image_square", "Obrázek 1:1");
		//$form->addUpload("image_portrait", "Obrázek na výšku");
		//$form->addUpload("image_landscape", "Obrázek na šířku");
		$form->addTextArea("mediabox", "Prostor pro video");
		$form->addTextArea("footer", "Závěr:");
		$form->addCheckbox("visible", "zobrazit");
		$form->addSubmit("send", "Odeslat");

		$form->onSuccess[]=[$this, "editFormSuccess"];

		return $form;
	}

	public function editFormSuccess(array $data) : void{
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
		$this->getComponent('editForm')->setDefaults($defaults);
	}
}
