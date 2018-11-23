<?php

namespace AppBundle\Utils;

use AppBundle\Entity\User;

class Messenger
{    
    private $templating;
    private $mailerUser;
    private $mailer;
    
    public function __construct($mailerUser, $templating, \Swift_Mailer $mailer)
    {
        $this->templating = $templating;
        $this->mailerUser = $mailerUser;
        $this->mailer = $mailer;
    }
    
    public function sendEmail(string $subject, string $template, User $user)
    {
        $message = (new \Swift_Message())
            ->setSubject($subject)
            ->setFrom([$this->mailerUser])
            ->setTo([$user->getEmail()])
            ->setContentType("text/html")
            ->setBody($this->templating->render($template, array('user' => $user)))
        ;
        $this->mailer->send($message);
    }
}
