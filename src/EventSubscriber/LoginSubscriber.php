<?php
declare (strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use App\Util\TwigMessage\TwigLinkMessage;
use App\Util\TwigMessage\TwigMessage;
use App\Util\TwigMessage\TwigMessageRenderer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;

class LoginSubscriber implements EventSubscriberInterface
{


    private TwigMessageRenderer $twigMessageRenderer;
    private RouterInterface $router;

    public function __construct(TwigMessageRenderer $twigMessageRenderer, RouterInterface $router)
    {
        $this->twigMessageRenderer = $twigMessageRenderer;
        $this->router = $router;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CheckPassportEvent::class => ['onCheckPassport', -10],
        ];
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

            $message = new TwigLinkMessage(
                'login.not_verified', [
                    'email' => $user->getEmail(),
                    'username' => $user->getUsername()
                ],
                'login.resend_activation_link',
                $resendLink,
            );

            throw new CustomUserMessageAuthenticationException(
                $this->twigMessageRenderer->render($message)
            );

//
//            throw new CustomUserMessageAuthenticationException(
//                $this->twig->render('components/message_link.html.twig', [
//                        'message' => 'login.not_verified',
//                        'link' => $resendLink,
//                        'linkMessage' => 'login.resend_activation_link'
//                    ]
//                )
//            );
        }
    }
}