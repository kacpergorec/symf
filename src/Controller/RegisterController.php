<?php

namespace App\Controller;

use App\Entity\User;
use App\Factory\User\AdminUserFactory;
use App\Factory\User\UserFactory;
use App\Form\Type\User\UserRegisterType;
use App\Repository\UserRepository;
use App\Service\VerificationLinkMailerHelper;
use App\Service\VerificationURLValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserRepository $userRepository, UserFactory $userFactory, VerificationLinkMailerHelper $verificationLinkMailerHelper, TranslatorInterface $translator): Response
    {
        $user = new User();

        $form = $this->createForm(UserRegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $userFactory->createNewFromEntity($user);

            $userRepository->save($user, true);

            if ($verificationLinkMailerHelper->send($user)) {
                $this->addFlash(
                    'success',
                    $translator->trans('register.activation.sent', [
                        '%user%' => $user->getUsername(),
                        '%email%' => $user->getEmail()
                    ])
                );
            } else {
                $this->addFlash('warning', 'register.activation.not_sent');
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
                $this->addFlash('success', 'register.activation.sent_again');
            } else {
                $this->addFlash('warning', 'register.activation.not_sent');
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
                $this->addFlash('success', 'account.verified');
            } else {
                $this->addFlash('info', 'account.already_verified');
            }

        } else {
            foreach ($URLValidator->getErrors() as $error) {
                $this->addFlash('danger', $error);
            }
        }

        return $this->redirectToRoute('app_login');
    }

}
