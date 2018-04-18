<?php

namespace GB\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use GB\UserBundle\Entity\User;
use GB\UserBundle\Form\UserType;
use GB\UserBundle\Form\LoginType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class UserController extends Controller
{
    public function loginAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            return $this->redirectToRoute('gb_tricks_homepage');
        }

        $form = $this->get('form.factory')->create(LoginType::class);
        
        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if($form->isValid()){
                $user = $this->getDoctrine()->getManager()->getRepository("GBUserBundle:User")
                    ->findOneBy(array('userName' => $form->getData()['userName']));

                $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                $this->get('security.token_storage')->setToken($token);
                $event = new InteractiveLoginEvent($request, $token);
                $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
                return $this->redirectToRoute('gb_tricks_homepage');
            }
        }
              
        return $this->render('GBUserBundle:User:login.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    public function registerAction(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->get('form.factory')->create(UserType::class, $user);
        $form->remove('file');
        
        if($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            if($form->isValid()){              
                $user->setPassword(hash('sha512',$user->getPassword()));
                $token = hash('sha256',uniqid());
                $user->setValidationToken($token);
                /**
                 * Envoi d'un email avec le service swiftmailer, puis persistence du user.
                 */
                $message = (new \Swift_Message())
                        ->setSubject('Ton inscription à Snowtricks')
                        ->setFrom([$this->getParameter('mailer_user')])
                        ->setTo([$user->getEmail()])
                        ->setBody($this->render('GBUserBundle:User:ValidationEmail.txt.twig', 
                                array('user' => $user)));
                        ;
                $mailer = $this->get('mailer');
                $mailer->send($message);               
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                $request->getSession()->getFlashBag()->add('info',
                        'Votre inscription a bien été pris en compte. Un email pour demande de validation de votre compte vous a été envoyé.');
                return $this->redirectToRoute('gb_tricks_homepage');                
            }
        }
        return $this->render('GBUserBundle:User:register.html.twig', 
                array('form' => $form->createView()));
    }

    public function validAction(Request $request)
    {
        /**
         * Recherche de l'utilisateur ayant le token correspondant à get('validationToken')
         * (si pas de User correspondant renvoi vers la page d'accueil avec flashbag erreur)
         */
        $em = $this->getDoctrine()->getManager();
        $userRepository = $em->getRepository('GBUserBundle:User');
        $user = $userRepository->findOneBy(array('token' => $request->query->get('Token')));
        if(empty($user))
        {
            $request->getSession()->getFlashBag()->add('error', 'Le token n\'est pas valide');
            return $this->redirectToRoute('gb_tricks_homepage');
        }
        /**
         * Mise à null du token du User correspondant
         * Et renvoi vers la page d'accueil avec flashbag de confirmation du compte.
         */
        $user->setToken(null);
        $em->flush();
        $request->getSession()->getFlashBag()->add('notice', 'Votre inscription est validée');
        
        return $this->redirectToRoute('gb_tricks_homepage');              
    }
    
    public function forgotAction($param)
    {
        
    }
}
