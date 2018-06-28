<?php

namespace GB\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PasswordValidator extends ConstraintValidator
{

    private $requestStack;
    private $em;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->requestStack = $requestStack;
        $this->em = $em;
    }

    public function validate($value, Constraint $constraint)
    {
        if ($value === null) {
            return;
        }

        $checkingUser = $this->em->getRepository('GBUserBundle:User')
                ->findOneBy(array(
            'userName' => $this->requestStack->getCurrentRequest()->request->get('gb_userbundle_user')['userName']));

        if (empty($checkingUser)) {
            return;
        }

        $password = hash('sha512', $value);
        if ($password !== $checkingUser->getPassword()) {
            $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ string }}', $value)
                    ->addViolation();
        }
    }

}
