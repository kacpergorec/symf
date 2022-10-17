<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\UserRegisterType;
use App\Repository\UserRepository;
use App\Service\VerificationLinkMailerHelper;
use App\Service\VerificationURLValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends BaseController
{
    private string $sendFaliureMessage = 'An activation link was not sent. Please contact with the Administrator.';

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, VerificationLinkMailerHelper $verificationLinkMailerHelper): Response
    {

        $user = new User();

        $form = $this->createForm(UserRegisterType::class, $user);
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

            if ($verificationLinkMailerHelper->send($user)) {
                $this->addFlash(
                    'info',
                    "Congratulations {$user}! You are now a part of growing <b>Symf</b> community! <br>
                An activation link to <b>{$user->getEmail()}</b> was sent."
                );
            } else {
                $this->addFlash('warning', $this->sendFaliureMessage);
            }

        }

        return $this->renderForm('auth/register.html.twig', [
            'registerForm' => $form,
        ]);
    }

    #[Route('/resend-verification', name: 'app_resend_verification')]
    public function resend(UserRepository $userRepository, Request $request, VerificationLinkMailerHelper $verificationLinkMailerHelper): Response
    {

        $username = $request->get('username');
        $user = $userRepository->findOneBy(['username' => $username]);

        if ($user && !$user->isVerified()) {
            if ($verificationLinkMailerHelper->send($user)) {
                $this->addFlash('success', 'Activation link was sent again.');
            } else {
                $this->addFlash('warning', $this->sendFaliureMessage);
            }
        } else {
            throw new BadRequestHttpException();
        }

        // Will not add this message because of privacy reasons
        // $this->addFlash('danger','This account is already verified');

        return $this->redirectToRoute('app_login');
    }

    #[Route("/verify", name: "app_verify_email")]
    public function verify(Request $request, VerificationURLValidator $URLValidator, EntityManagerInterface $entityManager): Response
    {

        $URLValidator->validate($request);

        if ($URLValidator->isValid()) {
            $user = $URLValidator->getUser();

            if (!$user->isVerified()) {
                $user->setVerified(true);
                $entityManager->flush();
                $this->addFlash('success', 'Your account is now active and ready!');
            } else {
                $this->addFlash('info', 'Your account is already verified.');
            }

        } else {
            foreach ($URLValidator->getErrors() as $error) {
                $this->addFlash('danger', $error);
            }
        }

        return $this->redirectToRoute('app_login');
    }

}
