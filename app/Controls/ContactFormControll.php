<?php

declare(strict_types=1);

namespace App\Controls;

use Nette;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Application\Attributes\Persistent;



class ContactFormControl extends Control {


    public bool $showForm = true;
    public bool $showThanks = false;

    private $mailer;

    public function __construct($mailer) {
        $this->mailer = $mailer;
    }

    public function render () {

        $this->template->showForm = $this->showForm;
        $this->template->showThanks = $this->showThanks;
        $this->template->render(__DIR__.'/contactForm.latte');
    }

    public function createComponentCForm() {
        $form = new Form();
		$form->addText("subject", "Předmět");
        $form->addText("name", "Jméno");
        $form->addText("email", "E-mail");
        $form->addTextArea("message", "Zpráva");
        $form->onSuccess[] = [$this, "formSuccess"];

        return $form;
    }

    public function formSuccess($form, $data) {
        $name = $data->name;
        $email = $data->email;
		$subject= $data->subject;
        $message = nl2br($data->message);
		// Definujeme proměnnou pro stylizaci emailu
		$emailStyle = 'style="font-family: Arial, sans-serif; font-size: 16px;"';

// Vytvoříme proměnnou s HTML zprávou
		$htmlBody = '
  <html>
    <head>
      <meta charset="utf-8">
      <title>'.$subject.'</title>
    </head>
    <body '.$emailStyle.'>
      <h1 style="color: #0066cc;">'.$subject.'</h1>
      <p>Dobrý den,</p>
      <p>Uživatel <strong>'.$name.'</strong> ('.$email.') odeslal tuto poptávku:</p>
      <blockquote style="background-color: #f5f5f5; padding: 10px 20px; margin: 20px 0;">
        <p style="font-style: italic; margin-bottom: 0;">'.$message.'</p>
      </blockquote>
      <p>S pozdravem,</p>
      <p>Vaše webová aplikace</p>
    </body>
  </html>
';

        $mail = new Nette\Mail\Message;

        $mail->setFrom("$name <noreply@davidhlavicek.cz>")
            ->addTo('dahavlicek@gmail.com')
            ->addReplyTo("$email")
            ->setSubject("$subject")
            ->setHTMLBody($htmlBody);
        $this->mailer->send($mail);

        $this->showForm = false;
        $this->showThanks = true;
        $this->redrawControl("content");

    }





}
