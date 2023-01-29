<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;


abstract class BasePresenter extends Nette\Application\UI\Presenter
{
	protected $projectModel;
	protected $tagModel;
	protected $homepageModel;

	public function __construct(\App\Models\ProjectModel $projectModel, \App\Models\TagModel $tagModel, \App\Models\HomepageModel $homepageModel)
	{
		$this->projectModel = $projectModel;
		$this->tagModel = $tagModel;
		$this->homepageModel = $homepageModel;
	}

}
