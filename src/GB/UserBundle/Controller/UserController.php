<?php

namespace GB\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
    public function loginAction()
    {
        return $this->render('GBUserBundle:User:login.html.twig');
    }
    
    public function registerAction()
    {
        
        return $this->render('GBUserBundle:User:register.html.twig');
    }    
}
