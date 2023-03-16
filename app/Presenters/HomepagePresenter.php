<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\Models\HomepageModel;
use Nette\Application\UI\Form;

final class HomepagePresenter extends \App\Presenters\BasePresenter
{


	public function renderDefault()
	{
		$this->template->projects = $this->homepageModel->getHomepageProjects();
	}
	public function createComponentHomepageForm() :Form{
		$form = new Form;

		$form->addSelect("1","Pozice 1 - landscape", $this->projectModel->getVisibleProjects()->fetchPairs("id","name"));
		$form->addSelect("2","Pozice 2 - square", $this->projectModel->getVisibleProjects()->fetchPairs("id","name"));
		$form->addSelect("3","Pozice 3 - square", $this->projectModel->getVisibleProjects()->fetchPairs("id","name"));
		$form->addSelect("4","Pozice 4 - portrait", $this->projectModel->getVisibleProjects()->fetchPairs("id","name"));
		$form->addSelect("5","Pozice 5 - landscape", $this->projectModel->getVisibleProjects()->fetchPairs("id","name"));

		$form->setDefaults($this->homepageModel->getHomepageProjects()->fetchPairs("id","project_id"));

		$form->addSubmit("send", "Odeslat");

		$form->onSuccess[]=[$this, "editFormSuccess"];

		return $form;
	}

	public function editFormSuccess($form, $data) : void{
		$this->homepageModel->updateHomepage($data);
		$this->redirect(":default");
	}

	public function renderEdit() :void{
	}


}

