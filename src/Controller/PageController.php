<?php

namespace App\Controller;

use App\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{

    #[Route('/{slug}', name: 'app_page', methods: ['GET'], priority: -10)]
    public function index($slug, PageRepository $repository): Response
    {
        $page = $repository->findOneBy(['slug' => $slug]);

        if (!$page || !$page->isPublished()) {
            throw $this->createNotFoundException();
        }

        if ($page->getRedirectUrl()) {
            return $this->redirect($page->getRedirectUrl(), '301');
        }

        return $this->render('page/index.html.twig', [
            'page' => $page,
        ]);
    }

}
