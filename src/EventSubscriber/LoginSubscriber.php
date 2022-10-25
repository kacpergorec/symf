<?php
declare (strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Url;
use App\Entity\User;
use App\Repository\UrlRepository;
use App\Util\TwigMessage\TwigLinkMessage;
use App\Util\TwigMessage\TwigMessageRendererInterface;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginSubscriber implements EventSubscriberInterface
{


    private TwigMessageRendererInterface $twigMessageRenderer;
    private RouterInterface $router;
    private RequestStack $requestStack;
    private UrlRepository $urlRepository;

    public function __construct(TwigMessageRendererInterface $twigMessageRenderer, RouterInterface $router, RequestStack $requestStack, UrlRepository $urlRepository)
    {
        $this->twigMessageRenderer = $twigMessageRenderer;
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->urlRepository = $urlRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CheckPassportEvent::class => ['onCheckPassport', -10],
            LoginSuccessEvent::class => ['loginSuccess']
        ];
    }

    public function onCheckPassport(CheckPassportEvent $event): void
    {
        $passport = $event->getPassport();

        $user = $passport->getUser();
        if (!$user instanceof User) {
            throw new Exception('Unexpected user type');
        }

        if (!$user->isVerified()) {

            $resendLink = $this->router->generate('app_resend_verification', ['username' => $user]);

            $message = new TwigLinkMessage(
                'login.not_verified', [],
                'login.resend_activation_link',
                $resendLink,
            );

            throw new CustomUserMessageAuthenticationException(
                $this->twigMessageRenderer->render($message)
            );

        }
    }

    public function loginSuccess(LoginSuccessEvent $event)
    {
        $session = $this->requestStack->getSession();
        $user = $event->getUser();

        if ($urls = $session->get('urls')) {

            /**@var Url $url ; */
            foreach ($urls as $url) {
                $url->setUser($user);
                $this->urlRepository->save($url, true);
            }

            $session->remove('urls');
        }

    }
}