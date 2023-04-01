<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;

final class AdminPresenter extends BasePresenter
{
	/**
	 * Factory for login form
	 * 
	 * @return Nette\Application\UI\Form
	 * 
	 */
	protected function createComponentSignInForm(): Form
	{
		$form = new Form;
		$form->addText('username', 'Jméno')
			->setRequired('Prosím vyplňte své uživatelské jméno.');

		$form->addPassword('password', 'Heslo')
			->setRequired('Prosím vyplňte své heslo.');

		$form->addSubmit('send', 'Přihlásit');

		$form->onSuccess[] = [$this, 'signInFormSucceeded'];
		return $form;
	}

	/**
	 * Callback for successfully filled sign-in form.
	 * 
	 * @param Nette\Application\UI\Form Form instance
	 * @param stdclass Data values 
	 */
	public function signInFormSucceeded(Form $form, \stdClass $data): void
	{
		try {
			$this->getUser()->login($data->username, $data->password);
			$this->redirect('Homepage:default');

		} catch (AuthenticationException $e) {
			$form->addError('Nesprávné přihlašovací jméno nebo heslo.');
		}
	}

	/**
	 * Action for log-out
	 */
	public function actionOut(): void
	{
		$this->getUser()->logout();
		$this->flashMessage('Odhlášení bylo úspěšné.');
		$this->redirect('Homepage:default');
	}
}
