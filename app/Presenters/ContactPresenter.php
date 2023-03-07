<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;



final class ContactPresenter extends Nette\Application\UI\Presenter
{
	public function createComponentContactForm()
	{
		$form = $this->createForm();

		return $form;
	}

	private function createForm()
	{
		$form = new Form;

		$form->addText('name', 'Your name:')
			->setRequired('Please enter your name.');

		$form->addEmail('email', 'Your email address:')
			->setRequired('Please enter your email address.')
			->addRule(Form::EMAIL, 'Please enter a valid email address.');

		$form->addTextArea('message', 'Your message:')
			->setRequired('Please enter your message.');

		$form->addSubmit('send', 'Send message');

		$form->onSuccess[] = [$this, 'processContactForm'];

		return $form;
	}


	public function processContactForm(Form $form)
	{
		$values = $form->getValues();

		$mail = new Message;
		$mail->setFrom('noreply@havlicekdavid.cz')//musí být nějkaá defaul odesílací adresa (info@davidhavlice.cz)
			->setReturnPath($values->email)
			->addTo('petr.nemec.1998@gmail.com')
			->setSubject('New message from website')
			->setBody("Name: $values->name\nEmail: $values->email\nMessage: $values->message"); //musím přidat replay to

		$mailer = new SendmailMailer;
		$mailer->send($mail);

		$this->flashMessage('Thank you for your message. We will get back to you soon.', 'success');
		$this->redirect('this');
	}

}
