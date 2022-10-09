<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\LoginUserType;
use App\Form\Type\RegisterUserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils, FormFactoryInterface $formFactory, Request $req): Response
    {

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        $user = new User();

        $form = $formFactory->createNamed('', LoginUserType ::class, $user);

        return $this->renderForm('auth/login.html.twig', [
            'lastUsername' => $lastUsername,
            'error' => $error,
            'loginForm' => $form,
        ]);
    }
}
