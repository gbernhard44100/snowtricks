<?php

namespace AppBundle\Form\FormHandler;

use AppBundle\Utils\Messenger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationTypeHandler
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
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $encoded = $this->encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);
            $token = hash('sha512', session_id() . microtime());
            $user->setValidationToken($token);
            $this->em->persist($user);
            $this->em->flush();
            $this->messenger->sendEmail('Ton inscription à Snowtricks', 'admin/validation_email.html.twig', $user);
            return true;
        }
        return false;
    }
}
