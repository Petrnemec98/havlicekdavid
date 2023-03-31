<?php
namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;

<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD

final class AdminPresenter extends BasePresenter

=======
final class AdminPresenter extends BasePresenter
>>>>>>> parent of 8276b94 (Footer + obr)
=======
final class AdminPresenter extends BasePresenter
>>>>>>> parent of 8276b94 (Footer + obr)
=======
final class AdminPresenter extends BasePresenter
>>>>>>> parent of 8276b94 (Footer + obr)
=======
final class AdminPresenter extends BasePresenter
>>>>>>> parent of 8276b94 (Footer + obr)
=======
final class AdminPresenter extends BasePresenter
>>>>>>> parent of 8276b94 (Footer + obr)
{
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
	public function signInFormSucceeded(Form $form, \stdClass $data): void
	{
		try {
			$this->getUser()->login($data->username, $data->password);
			$this->redirect('Homepage:default');

		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError('Nesprávné přihlašovací jméno nebo heslo.');
		}
	}
	public function actionOut(): void
	{
		$this->getUser()->logout();
		$this->flashMessage('Odhlášení bylo úspěšné.');
		$this->redirect('Homepage:default');
	}
}
