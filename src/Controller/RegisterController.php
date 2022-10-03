<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('auth/register.html.twig', [
            'controller_name' => 'RegisterController',
        ]);
    }

    #[Route('/register', name: 'app_register_post', methods: ['POST'])]
    public function register(Request $request): Response
    {
        dd($request->get('email'));

        return new Response('test post');
    }
}
