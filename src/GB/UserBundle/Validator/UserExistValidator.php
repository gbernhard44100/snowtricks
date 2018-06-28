<?php

namespace GB\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManagerInterface;

class UserExistValidator extends ConstraintValidator
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function validate($value, Constraint $constraint)
    {
        if ($value === null) {
            return;
        }

        $checkingUser = $this->em->getRepository('GBUserBundle:User')
                ->findOneBy(array('userName' => $value));

        if (empty($checkingUser)) {
            $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ string }}', $value)
                    ->addViolation();
        }
    }

}
