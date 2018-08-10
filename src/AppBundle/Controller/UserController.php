<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\PasswordResetType;
use AppBundle\Form\RegistrationType;
use AppBundle\Form\UserNameType;
use AppBundle\Utils\Messenger;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends Controller
{

    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request, AuthorizationChecker $checker, AuthenticationUtils $utils)
    {
        if ($checker->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('homepage');
        }
        $error = $utils->getLastAuthenticationError();
        $message = null;
        if (!is_null($error)) {
            $message = $error->getMessageKey();
        }
        return $this->render('admin/login.html.twig', array(
            'last_username' => $utils->getLastUsername(),
            'message' => $message,
        ));
    }

    /**
     * @Route("/registration", name="registration")
     */
    public function registerAction(
        Request $request,
        EntityManagerInterface $em,
        Messenger $messenger,
        UserPasswordEncoderInterface $encoder
    ) {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user)->remove('file');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $encoded = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);
            $token = hash('sha512', session_id() . microtime());
            $user->setValidationToken($token);
            $em->persist($user);
            $em->flush();
            $messenger->sendEmail('Ton inscription à Snowtricks', 'admin/validation_email.html.twig', $user);
            $request->getSession()->getFlashBag()->add(
                'info',
                'Votre inscription a bien été pris en compte. Un email pour demande de validation de votre compte vous a été envoyé.'
            );
            return $this->redirectToRoute('login');
        }
        return $this->render('admin/register.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/uservalidation", name="user_validation")
     */
    public function validAction(Request $request, EntityManagerInterface $em)
    {
        $userRepository = $em->getRepository('AppBundle:User');
        $user = $userRepository->findOneBy(array('validationToken' => $request->request->get('Token')));
        if (empty($user)) {
            $request->getSession()->getFlashBag()->add('error', 'Le token n\'est pas valide');
            return $this->redirectToRoute('login');
        }
        $user->setValidationToken(null);
        $em->flush();
        $request->getSession()->getFlashBag()->add('success', 'Votre inscription est validée');
        return $this->redirectToRoute('login');
    }
    
    /**
     * @Route("/forgotpassword", name="forgot_password")
     */
    public function forgotAction(Request $request, EntityManagerInterface $em, Messenger $messenger)
    {
        $user = new User();
        $form = $this->createForm(UserNameType::class, $user);
        $form->handleRequest($request);
        $user = $em->getRepository('AppBundle:User')->findOneByUserName($user->getUserName());
        if ($form->isSubmitted() && $form->isValid() && !is_null($user)) {
            $token = hash('sha512', session_id() . microtime());
            $user->setPasswordToken($token);
            $messenger->sendEmail('Snowtricks : mot de passe oublié', 'admin/forgot_password_email.html.twig', $user);
            $em->flush();
            $request->getSession()->getFlashBag()->add(
                    'info',
                    'Un email vous a été envoyé pour renouveler votre mot de passe.');
            return $this->redirectToRoute('login');
        }
        return $this->render('admin/forgot.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/resetPassword", name="password_reset")
     */
    public function resetPasswordAction(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('passwordToken' => $request->request->get('passwordToken')));
        if (empty($user)) {
            $request->getSession()->getFlashBag()->add('error', 'Le token n\'est pas valide');
            return $this->redirectToRoute('login');
        }
        $form = $this->createForm(PasswordResetType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $encoded = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);
            $user->setPasswordToken(null);
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', 'Le mot de passe a bien été réinitialisé.');
            return $this->redirectToRoute('login');
        }
        return $this->render('admin/reset_password.html.twig', array('user' => $user, 'form' => $form->createView()));
    }

    /**
     * @Route("/user", name="user_show")
     * @Security("has_role('ROLE_USER')")
     */
    public function showUserAction()
    {
        return $this->render('admin/profil_show.html.twig', array('user' => $this->getUser()));
    }

    /**
     * @Route("/user/update", name="user_update")
     * @Security("has_role('ROLE_USER')")
     */
    public function updateAction(Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(RegistrationType::class, $this->getUser())->remove('password');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', 'La modification de votre profil a bien été réalisé.');
            return $this->redirectToRoute('user_show');
        }
        return $this->render('admin/profil_update.html.twig', array('form' => $form->createView(), 'user' => $this->getUser()));
    }
}
