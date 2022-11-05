<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UrlsSessionHandler;
use Endroid\QrCodeBundle\Controller\GenerateController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class QrController extends AbstractController
{
    #[Route('/qr/{builder}/{shortUrl}', name: 'app_qr', requirements: ['shortUrl' => '[\\w\\W]+'])]
    public function index($builder, $shortUrl, Security $security, UrlsSessionHandler $urlsSessionHandler, Request $request): Response
    {
        /**
         * @var $user User
         */
        if ($user = $security->getUser()) {
            $urlKeys = $user->getUrlKeys();

        } else {
            $urlKeys = $urlsSessionHandler->getKeys();
        }

        $urlKeys = array_map(static fn($key) => $request->getUriForPath("/$key"), $urlKeys);

        //generate qr only if user has url generated
        if (!in_array($shortUrl, $urlKeys, true)) {
            throw $this->createAccessDeniedException();
        }

        return $this->forward(GenerateController::class . ':__invoke', [
            'builder' => $builder,
            'data' => $shortUrl,
        ]);
    }
}
