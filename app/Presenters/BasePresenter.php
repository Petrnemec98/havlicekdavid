<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;


abstract class BasePresenter extends Nette\Application\UI\Presenter
{
	protected $projectModel;
	public function __construct(\App\Models\ProjectModel $projectModel)
	{
		$this->projectModel = $projectModel;
	}
}
