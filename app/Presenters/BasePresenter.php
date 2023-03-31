<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Control as ControlAlias;
use App\Controls\ContactFormControl;

abstract class BasePresenter extends Nette\Application\UI\Presenter
{
	protected $projectModel;
	protected $tagModel;
	protected $homepageModel;

	/** @var Nette\Mail\Mailer @inject */
	public $mailer;

	public function __construct(\App\Models\ProjectModel $projectModel, \App\Models\TagModel $tagModel, \App\Models\HomepageModel $homepageModel)
	{
		$this->projectModel = $projectModel;
		$this->tagModel = $tagModel;
		$this->homepageModel = $homepageModel;
	}

	public function setFormRenderer(Nette\Application\UI\Form $form){
		//$renderer = $form->getRenderer();
		//$renderer->wrappers['pair']['container']="div class=input-contain";
	}

	/**
	 * 	Továrna pro vytvoření komponenty kontaktního formuláře
	 *  @author wnc
	 */
	public function createComponentContactForm() {
		return new ContactFormControl($this->mailer);
	}

	protected function protect() {
		if (!$this->user->isLoggedIn()) {
			$this->flashMessage("Nejste přihlášen. Prosím přihlašte se.");
			$this->redirect("Admin:");
		}
	}

}
