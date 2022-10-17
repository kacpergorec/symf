<?php

namespace App\Controller;

use App\Form\Type\UserDeleteType;
use App\Form\Type\UserEditType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;

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
    public function delete(Session $session, TokenStorageInterface $tokenStorage, Request $request): Response
    {
        $user = $this->security->getUser();

        if (!$user) {
            throw $this->createNotFoundException();
        }

        $entityManager = $this->doctrine->getManager();

        $form = $this->createForm(UserDeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            ///log out current user
            $tokenStorage->setToken(null);
            $session->invalidate();

            try {
                $entityManager->remove($user);
                $entityManager->flush();
                $this->addFlash('success', 'Your account has been deleted.');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Your account was not deleted due to an error.');
                throw new $e;
            }

            return $this->redirectToRoute('app_home');
        }

        return $this->renderForm('profile/edit.html.twig', [
            'profileForm' => $form,
        ]);
    }

}
