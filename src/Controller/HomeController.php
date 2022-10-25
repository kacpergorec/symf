<?php

namespace App\Controller;

use App\Entity\Url;
use App\Form\Type\Url\UrlSubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {

        $urlForm = $this->createForm(UrlSubmitType::class, new Url(), [
            'action' => $this->generateUrl('app_url_shorten')
        ]);

        return $this->renderForm('home/index.html.twig', [
            'urlForm' => $urlForm,
        ]);
    }
}
