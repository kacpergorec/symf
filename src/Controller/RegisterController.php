<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\RegisterUserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {


        $entityManager = $doctrine->getManager();

        $user = new User();


        $form = $this->createForm(RegisterUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $entityManager->persist($user);

            $entityManager->flush();

            return new Response('Saved new user with id ' . $user->getId());
        }

        return $this->renderForm('auth/register.html.twig', [
            'registerForm' => $form,

        ]);
    }
}
