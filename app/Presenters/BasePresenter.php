<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\Controls\ContactFormControl;

abstract class BasePresenter extends Nette\Application\UI\Presenter
{
	/** @var App\Models\ProjectModel  */
	protected $projectModel;

	/** @var App\Models\TagModel  */
	protected $tagModel;

	/** @var App\Models\HomepageModel  */
	protected $homepageModel;

	/** @var Nette\Mail\Mailer @inject */
	public $mailer;

	/**
	 * Constructor for base presenter 
	 * - used for model injection
	 */
	public function __construct(\App\Models\ProjectModel $projectModel, \App\Models\TagModel $tagModel, \App\Models\HomepageModel $homepageModel)
	{
		$this->projectModel = $projectModel;
		$this->tagModel = $tagModel;
		$this->homepageModel = $homepageModel;
	}


	/**
	 * 	Contact form component factory
	 * 
	 *  @author wnc
	 *  @return App\Controls\ContactFormControl
	 */
	public function createComponentContactForm() {
		return new ContactFormControl($this->mailer);
	}

	/**
	 * This function chcecks if the user loged-in. If not, redirects to log-in form and prevent next action.
	 */
	protected function protect() {
		if (!$this->user->isLoggedIn()) {
			$this->flashMessage("Nejste přihlášen. Prosím přihlašte se.");
			$this->redirect("Admin:");
		}
	}

	/**
	 * Before render lifecycle step. Assign default values of SEO variables
	 */
	public function beforeRender() {
		$this->template->description = "";
		$this->template->keywords = "";
		$this->template->heading = "";
	}

}
