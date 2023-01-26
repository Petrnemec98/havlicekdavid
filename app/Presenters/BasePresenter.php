<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;


abstract class BasePresenter extends Nette\Application\UI\Presenter
{
	protected $projectModel;
	protected $tagModel;

	public function __construct(\App\Models\ProjectModel $projectModel, \App\Models\TagModel $tagModel)
	{
		$this->projectModel = $projectModel;
		$this->tagModel = $tagModel;
	}

}
