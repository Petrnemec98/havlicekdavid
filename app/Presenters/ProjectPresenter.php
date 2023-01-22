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
		$form->getElementPrototype()->addAttributes(["class"=>"form form-page"]);

		$form->addText("name", "Název:")
			->setRequired()
			->setHtmlAttribute("class", "input input-title");

		$form->addText("project_date", "Datum:")->setType("date")->setRequired();
		$form->addTextArea("description", "Popis:")->setRequired();
		//$form->addUpload("image_square", "Obrázek 1:1");
		//$form->addUpload("image_portrait", "Obrázek na výšku");
		//$form->addUpload("image_landscape", "Obrázek na šířku");
		$form->addTextArea("mediabox", "Prostor pro video");
		$form->addTextArea("footer", "Závěr:");
		$form->addSubmit("send", "Odeslat");
		$form->onSuccess[]=[$this, "sendSuccessed"];

		return $form;
	}

	public function sendSuccessed(array $data) : void{
		$this->projectModel->createNewProject($data);
		$this->redirect(":default");
	}
}
