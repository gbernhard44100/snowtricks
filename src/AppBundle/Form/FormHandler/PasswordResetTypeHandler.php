<?php

namespace AppBundle\Form\FormHandler;

use AppBundle\Utils\Messenger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordResetTypeHandler
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
        if ($form->isSubmitted() && $form->isValid()) {
            $encoded = $this->encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);
            $user->setPasswordToken(null);
            $this->em->flush();
            return true;
        }
        return false;
    }
}
