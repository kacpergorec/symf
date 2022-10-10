<?php
declare (strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\AuthenticationEvents;

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
            AuthenticationEvents::AUTHENTICATION_SUCCESS => 'onLogin',
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
}