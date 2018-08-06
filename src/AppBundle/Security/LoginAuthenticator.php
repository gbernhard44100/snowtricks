<?php

namespace AppBundle\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class LoginAuthenticator extends AbstractGuardAuthenticator
{
    
    private $router;
    private $csrfTokenManager;

    public function __construct(RouterInterface $router, CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->router = $router;
        $this->csrfTokenManager = $csrfTokenManager;
    }
    
    public function supports(Request $request)
    {
        return $request->request->has('_csrf_token');
    }

    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     */
    public function getCredentials(Request $request)
    {
        $csrfToken = $request->request->get('_csrf_token');

        if (false === $this->csrfTokenManager->isTokenValid(new CsrfToken('authenticate', $csrfToken))) {
            throw new InvalidCsrfTokenException('Token CSRF invalide.');
        }
        return array(
            'username' => $request->request->get('_username'),
            'password' => $request->request->get('_password'),
        );
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $username = $credentials['username'];

        if (empty($username)) {
            throw new CustomUserMessageAuthenticationException(
                    'Aucun utilisateur n\'est renseigné.');
        }

        return $userProvider->loadUserByUserName($username);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        if(!password_verify($credentials['password'],$user->getPassword())) {
            throw new CustomUserMessageAuthenticationException('Mot de passe incorrect');
        }
        if($user->getValidationToken()) {
            throw new CustomUserMessageAuthenticationException(
                    'Votre compte n\'est pas encore validé.');
        }
        // return true to cause authentication success
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        $url = $this->router->generate('homepage');
        return new RedirectResponse($url);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        $url = $this->router->generate('login');
        return new RedirectResponse($url);
    }

    public function start(Request $request, AuthenticationException $authException = NULL)
    {
        $url = $this->router->generate('login');
        return new RedirectResponse($url); 
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
