<?php

namespace GB\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;
/**
 * @Annotation
 */
class UserName extends Constraint
{
    public $userNameMessage = 'Ce nom d\'utilisateur n\'existe pas.';
    
    public $tokenMessage = 'Le compte de cet utilisateur n\'est pas valide.';
    
    public function validatedBy(){
        return 'gb_user_username';
    }
}
