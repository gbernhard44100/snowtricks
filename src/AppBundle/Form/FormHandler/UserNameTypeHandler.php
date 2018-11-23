<?php

namespace AppBundle\Form\FormHandler;

use AppBundle\Utils\Messenger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserNameTypeHandler
{
    private $em;
    private $encoder;
    private $messenger;
            
    public function __construct(
        EntityManagerInterface $em,
        Messenger $messenger,
        UserPasswordEncoderInterface $encoder
    ) {
        $this->em = $em;
        $this->messenger = $messenger;
        $this->encoder = $encoder;
    }
         
    public function handle(Form $form)
    {
        $user = $form->getData();
        if ($form->isSubmitted() && $form->isValid() && !is_null($user)) {
            $token = hash('sha512', session_id() . microtime());
            $user->setPasswordToken($token);
            $this->messenger->sendEmail('Snowtricks : mot de passe oublié', 'admin/forgot_password_email.html.twig', $user);
            $this->em->flush();
            return true;
        }
        return false;
    }
}
