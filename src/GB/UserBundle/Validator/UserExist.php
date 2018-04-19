<?php

namespace GB\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;
/**
 * @Annotation
 */
class UserExist extends Constraint
{
    public $message = 'Ce nom d\'utilisateur n\'existe pas.';
    
    public function validatedBy(){
        return 'gb_user_userexist';
    }
}
