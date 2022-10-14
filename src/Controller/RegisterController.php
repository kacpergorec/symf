<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\RegisterUserType;
use App\Repository\UserRepository;
use App\Service\VerificationLinkMailerHelper;
use App\Service\VerificationURLValidator;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\ORM\EntityManagerInterface;
use http\Exception\InvalidArgumentException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Message;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

class RegisterController extends AbstractController
{


    #[Route('/register', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer, UserPasswordHasherInterface $passwordHasher, VerifyEmailHelperInterface $verifyEmailHelper): Response
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

            $signatureComponents = $verifyEmailHelper->generateSignature(
                'app_verify_email',
                (string)$user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()]
            );

            $activationEmail = (new TemplatedEmail())
                ->from($this->getParameter('app.mail.from'))
                ->to($user->getEmail())
                ->subject('Confirmation Email - Symf')
                ->htmlTemplate('emails/verification.html.twig')
                ->context([
                    'user' => $user,
                    'activationLink' => $signatureComponents->getSignedUrl()
                ]);

            try {
                $mailer->send($activationEmail);
                $this->addFlash(
                    'info',
                    "Congratulations {$user}! You are now a part of growing <b>Symf</b> community! <br>
                An activation link to <b>{$user->getEmail()}</b> was sent."
                );

            } catch (TransportException $e) {
                $this->addFlash('warning', 'An activation link was not sent. Please contact with the Administrator.');
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
            dd('send mail here');
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
