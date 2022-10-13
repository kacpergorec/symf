<?php
declare (strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginSubscriber implements EventSubscriberInterface
{

    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {

        $this->requestStack = $requestStack;
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
            throw new CustomUserMessageAuthenticationException(
                'Please verify your account before logging in.'
            );
        }
    }
}