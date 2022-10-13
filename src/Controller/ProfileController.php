<?php

namespace App\Controller;

use App\Form\Type\UserEditType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class ProfileController extends AbstractController
{

    private Security $security;
    private ManagerRegistry $doctrine;

    public function __construct(Security $security, ManagerRegistry $doctrine)
    {
        $this->security = $security;
        $this->doctrine = $doctrine;
    }

    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig');
    }

    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function edit(Request $request): Response
    {
        $entityManager = $this->doctrine->getManager();

        $user = $this->security->getUser();

        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success',
                "Account info saved!"
            );

            return $this->redirectToRoute('app_profile');
        }


        return $this->renderForm('profile/edit.html.twig', [
            'profileForm' => $form,
        ]);
    }

    #[Route('/profile/delete', name: 'app_profile_delete')]
    public function delete(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->security->getUser();

        $entityManager = $this->doctrine->getManager();

        $form = $this->createFormBuilder($user)
            ->add('password', PasswordType::class, ['help' => 'Type in your current password to delete your account.',
                'constraints' => [new UserPassword()]])
            ->add('save', SubmitType::class, ['attr' => ['class' => 'btn btn-outline-danger'], 'label' => 'Delete account'])
            ->getForm();

        return $this->renderForm('profile/edit.html.twig', [
            'profileForm' => $form,
        ]);
    }

}
