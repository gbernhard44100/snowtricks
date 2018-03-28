<?php

namespace GB\TricksBundle\Validator;

use Symfony\Component\Validator\Constraint;
/**
 * @Annotation
 */
class PictureFileExtension extends Constraint
{
    public $message = 'Le fichier est invalide : il doit être d\'extension .jpeg, .jpg ou .png';
    
    public function validatedBy(){
        return get_class($this).'Validator';
    }
}
