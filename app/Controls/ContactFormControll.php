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
        $form->addText("name", "Jméno");
        $form->addText("email", "E-mail");
        $form->addTextArea("message", "Zpráva");
        $form->onSuccess[] = [$this, "formSuccess"];

        return $form;
    }

    public function formSuccess($form, $data) {
        $name = $data->name;
        $email = $data->email;
        $message = nl2br($data->message);

        $mail = new Nette\Mail\Message;

        $mail->setFrom("$name <noreply@davidhlavicek.cz>")
            ->addTo('petr.nemec.1998@gmail.com')
            ->addReplyTo("$email")
            ->setSubject('Poptávky z webu')
            ->setHTMLBody("<p>Uživatel $name ($email) odeslal tuto poptávku:</p> <p>$message</p>");
        $this->mailer->send($mail);

        $this->showForm = false;
        $this->showThanks = true;
        $this->redrawControl("content");

    }





}
