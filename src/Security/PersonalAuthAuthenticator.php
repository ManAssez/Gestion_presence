<?php

namespace App\Security;

use App\Entity\Personal;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

use Symfony\Component\HttpFoundation\{
    RedirectResponse,
    Request
};
use Symfony\Component\Security\Core\{
    Encoder\UserPasswordEncoderInterface,
    Exception\CustomUserMessageAuthenticationException,
    Exception\InvalidCsrfTokenException,
    User\UserInterface,
    User\UserProviderInterface,
    Security
};
use Symfony\Component\Security\Csrf\{
    CsrfToken,
    CsrfTokenManagerInterface
};

class PersonalAuthAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
    private $uid;
    private $entityManager;
    private $urlGenerator;
    private $csrfTokenManager;
    private $Encoder;

    public function __construct(
        EntityManagerInterface $entityManager, 
        UrlGeneratorInterface $urlGenerator, 
        CsrfTokenManagerInterface $csrfTokenManager,
        UserPasswordEncoderInterface $passwordEncoder
        )
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->Encoder = $passwordEncoder;
    }

    public function supports(Request $request)
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'username' => $request->request->get('username'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['username']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->entityManager->getRepository(Personal::class)->findOneBy(['username' => $credentials['username']]);

        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('Utilisateur n\'existe pas.');
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $logged = $this->Encoder->isPasswordValid($user, $credentials['password']);
        if($logged){
            if($user->getMac() == $this->mac())
            {
                $this->uid = $user->getId();
                return true;
            }
            else{
            throw new CustomUserMessageAuthenticationException('Vous n\'êtes pas autorisés sur cette machine.');
        }
        }
        else{
            
            throw new CustomUserMessageAuthenticationException('mot de passe incorrect.');
        }
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }
        return new RedirectResponse($this->urlGenerator->generate('users_home',['id' => $this->uid ]));
        // throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
    }

    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    protected function mac()
    {
    $string=exec('getmac');
    $mac=substr($string, 0, 17); 
    return $mac;
    }
}
