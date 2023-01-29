<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\Models\HomepageModel;

final class HomepagePresenter extends \App\Presenters\BasePresenter
{

	protected $homepageModel;

	public function __construct(HomepageModel $homepageModel)
	{
		$this->homepageModel = $homepageModel;
	}

	public function renderDefault()
	{
		$this->template->projects = $this->homepageModel->getHomepageProjects();
	}

}

