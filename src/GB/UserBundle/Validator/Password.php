<?php

namespace GB\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;
/**
 * @Annotation
 */
class Password extends Constraint
{
    public $message = 'Le mot de passe est incorrect';
    
    public function validatedBy(){
        return 'gb_user_password';
    }
}
