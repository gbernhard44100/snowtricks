<?php

namespace GB\TricksBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PictureFileExtensionValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint){
        if($value === null){
            return;
        }
        if (!preg_match('/(\.jpeg|\.jpg|\.png|.JPG|.JPEG|.PNG)$/', $value->getClientOriginalName(), $matches)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ string }}', $value)
                    ->addViolation();
            }
    }
}
