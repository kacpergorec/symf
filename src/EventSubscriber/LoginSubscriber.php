<?php
declare (strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginSubscriber implements EventSubscriberInterface
{

    private RequestStack $requestStack;
    private UrlGeneratorInterface $router;

    public function __construct(RequestStack $requestStack, UrlGeneratorInterface $router)
    {

        $this->requestStack = $requestStack;
        $this->router = $router;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLogin',
            CheckPassportEvent::class => ['onCheckPassport', -10],
        ];
    }

    public function onLogin(): void
    {
        $this->requestStack
            ->getCurrentRequest()
            ->getSession()
            ->getFlashBag()
            ->add('info', 'Welcome back!');

    }

    public function onCheckPassport(CheckPassportEvent $event): void
    {
        $passport = $event->getPassport();

        $user = $passport->getUser();
        if (!$user instanceof User) {
            throw new \Exception('Unexpected user type');
        }

        if (!$user->isVerified()) {

            $resendLink = $this->router->generate('app_resend_verification', ['username' => $user]);

            throw new CustomUserMessageAuthenticationException(
                "Please verify your account before logging in. <a href='{$resendLink}'>Resend activation link</a>"
            );
        }
    }
}