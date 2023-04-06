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
		$this->template->backLink = $this->getBacklink();
		$this->template->backLinkHistory = $this->getBacklinkHistory();
	}

	/**
	 * Return relative part of URL as a href of the backlink button.
	 * 
	 * @return string
	 */
	protected function getBacklink() {
		$httpRequest = $this->getHttpRequest();
		$referer = $httpRequest->getReferer();
		if ($referer === null) {
			return $this->getDefaultBacklink();
		}

		if ($referer->host != $httpRequest->getUrl()->host) {
			return $this->getDefaultBacklink();
		}

		return $referer->path.($referer->query?"?".$referer->query:"");
	}

	/**
	 * Return true if we can use history.go(-1) function instead of link on the backlink button.
	 * 
	 * @return bool
	 */
	protected function getBacklinkHistory() {
		$httpRequest = $this->getHttpRequest();
		$referer = $httpRequest->getReferer();
		if ($referer === null) {
			return false;
		}

		if ($referer->host != $httpRequest->getUrl()->host) {
			return false;
		}

		return true;
	}

	/**
	 * Get URL of default BackLink (in case we can`t use referer or history.go function)
	 * 
	 * @return string
	 * 
	 */
	private function getDefaultBacklink() {
		if ($this->action == "default") {
			return $this->link("Homepage:default");
		} 
		return $this->link(":default");
	}
}
