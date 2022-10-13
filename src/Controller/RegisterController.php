<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\RegisterUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, VerifyEmailHelperInterface $verifyEmailHelper): Response
    {

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

            $signatureComponents = $verifyEmailHelper->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()]
            );

            $this->addFlash('success', 'Confirm your email at: ' . $signatureComponents->getSignedUrl());

        }


        return $this->renderForm('auth/register.html.twig', [
            'registerForm' => $form,
        ]);
    }


    #[Route("/verify", name: "app_verify_email")]
    public function verifyUserEmail(Request $request, EntityManagerInterface $entityManager, VerifyEmailHelperInterface $verifyEmailHelper, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($request->query->get('id'));
        if (!$user) {
            throw $this->createNotFoundException();
        }

        try {
            $verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                $user->getId(),
                $user->getEmail()
            );
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('danger', $e->getReason());
        }

        $user->setVerified(true);
        $entityManager->flush();

        $this->addFlash('success', 'Your account is now active and ready!');

        return $this->redirectToRoute('app_register');
    }

}
