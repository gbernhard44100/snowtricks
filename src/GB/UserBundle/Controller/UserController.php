<?php

namespace GB\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use GB\UserBundle\Entity\User;
use GB\UserBundle\Form\RegistrationType;
use GB\UserBundle\Form\LoginType;
use GB\UserBundle\Form\UserNameType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class UserController extends Controller
{

    public function loginAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('gb_tricks_homepage');
        }

        $user = new User();
        $form = $this->get('form.factory')->create(LoginType::class, $user);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $user = $this->getDoctrine()->getManager()->getRepository("GBUserBundle:User")
                        ->findOneByUserName($user->getUserName());
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
        $form = $this->get('form.factory')->create(RegistrationType::class, $user);
        $form->remove('file');
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $user->setPassword(password_hash($user->getPassword(), PASSWORD_BCRYPT));
                $token = hash('sha512',session_id(). microtime());
                $user->setValidationToken($token);
                /**
                 * Envoi d'un email avec le service swiftmailer, puis persistence du user.
                 */
                $message = (new \Swift_Message())
                        ->setSubject('Ton inscription à Snowtricks')
                        ->setFrom([$this->getParameter('mailer_user')])
                        ->setTo([$user->getEmail()])
                        ->setBody($this->render('GBUserBundle:User:ValidationEmail.txt.twig', array('user' => $user)));
                ;
                $mailer = $this->get('mailer');
                $mailer->send($message);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                $request->getSession()->getFlashBag()->add('info', 'Votre inscription a bien été pris en compte. Un email pour demande de validation de votre compte vous a été envoyé.');
                return $this->redirectToRoute('gb_tricks_homepage');
            }
        }
        return $this->render('GBUserBundle:User:register.html.twig', array('form' => $form->createView()));
    }

    public function validAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $userRepository = $em->getRepository('GBUserBundle:User');
        $user = $userRepository->findOneBy(array('validationToken' => $request->query->get('Token')));
        if (empty($user)) {
            $request->getSession()->getFlashBag()->add('error', 'Le token n\'est pas valide');
            return $this->redirectToRoute('gb_tricks_homepage');
        }

        $user->setValidationToken(null);
        $em->flush();
        $request->getSession()->getFlashBag()->add('success', 'Votre inscription est validée');

        return $this->redirectToRoute('gb_tricks_homepage');
    }

    public function forgotAction(Request $request)
    {
        $user = new User();
        $form = $this->get('form.factory')->create(UserNameType::class, $user);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $user = $em->getRepository('GBUserBundle:User')->findOneByUserName($user->getUserName());
                $token = hash('sha512',session_id(). microtime());
                $user->setPasswordToken($token);
                /**
                 * Envoi d'un email avec le service swiftmailer.
                 */
                $message = (new \Swift_Message())
                        ->setSubject('Compte Snowtricks : mot de passe oublié')
                        ->setFrom([$this->getParameter('mailer_user')])
                        ->setTo([$user->getEmail()])
                        ->setBody($this->render('GBUserBundle:User:ForgotPasswordEmail.txt.twig', array('user' => $user)));
                ;
                $mailer = $this->get('mailer');
                $mailer->send($message);
                $em->flush();
                $request->getSession()->getFlashBag()->add('info', 'Un email vous a été envoyé pour renouveler votre mot de passe.');
                return $this->redirectToRoute('gb_tricks_homepage');
            }
        }

        return $this->render('GBUserBundle:User:forgot.html.twig', array('form' => $form->createView()));
    }

    public function resetPasswordAction(Request $request)
    {
        /**
         * Recherche de l'utilisateur ayant le token correspondant à get('passwordToken')
         * (si pas de User correspondant renvoi vers la page d'accueil avec flashbag erreur)
         */
        $em = $this->getDoctrine()->getManager();
        $userRepository = $em->getRepository('GBUserBundle:User');
        $user = $userRepository->findOneBy(array('passwordToken' => $request->query->get('Token')));
        if (empty($user)) {
            $request->getSession()->getFlashBag()->add('error', 'Le token n\'est pas valide');
            return $this->redirectToRoute('gb_tricks_homepage');
        }

        if ($request->isMethod('POST')) {
            $user->setPassword(password_hash($request->request->get('password'), PASSWORD_BCRYPT));
            $user->setPasswordToken(NULL);
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', 'Le mot de passe a bien été réinitialisé.');
            return $this->redirectToRoute('gb_tricks_homepage');
        }

        return $this->render('GBUserBundle:User:reset.html.twig', array('user' => $user));
    }

    public function showAction()
    {
        $user = $this->getUser();
        if (is_null($user)) {
            $request->getSession()->getFlashBag()->add('error', 'Aucun utilisateur n\'est connecté.');
            return $this->redirectToRoute('gb_tricks_homepage');
        }
        return $this->render('GBUserBundle:User:profil.html.twig', array('user' => $user));
    }

    public function updateAction(Request $request)
    {
        $user = $this->getUser();
        if (is_null($user)) {
            $request->getSession()->getFlashBag()->add('error', 'Aucun utilisateur n\'est connecté.');
            return $this->redirectToRoute('gb_tricks_homepage');
        }
        $form = $this->get('form.factory')->create(RegistrationType::class, $user)
                ->remove('password');

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                $request->getSession()->getFlashBag()->add('success', 'La modification de votre profil a bien été réalisé.');
                return $this->redirectToRoute('gb_tricks_homepage');
            }
        }
        return $this->render('GBUserBundle:User:update.html.twig', array('form' => $form->createView()));
    }

}
