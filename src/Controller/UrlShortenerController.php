<?php

namespace App\Controller;

use App\Entity\Url;
use App\Form\Type\Url\UrlSubmitType;
use App\Service\UrlShortener;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class UrlShortenerController extends AbstractController
{
    #[Route('/shorten', name: 'app_shorten')]
    public function index(Security $security, Request $request, UrlShortener $shortener): Response
    {
        $form = $this->createForm(UrlSubmitType::class);

        $form->handleRequest($request);

        /**
         * @var Url $url
         */
        if ($form->isSubmitted() && $form->isValid()) {
            $url = $form->getData();
            $url->setUser($security->getUser());

            dd($url);
            $this->addFlash('success', 'success');
        }

        return $this->renderForm('shorten/index.html.twig', [
            'urlForm' => $form
        ]);
    }
}
