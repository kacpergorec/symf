<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\RegisterUserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function index(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
    {

        $entityManager = $doctrine->getManager();

        $user = new User();

        $form = $this->createForm(RegisterUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );

            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'info',
                "Congratulations {$user}! You are now a part of growing <b>Symf</b> community! <br>
                An activation link to <b>{$user->getEmail()}</b> was <u>not</u> sent because mailing is not implemented. 
                &nbsp <small>(...yet)</small>"
            );

            return $this->redirectToRoute('app_home');
        }


        return $this->renderForm('auth/register.html.twig', [
            'registerForm' => $form,
        ]);
    }
}
