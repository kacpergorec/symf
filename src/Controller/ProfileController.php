<?php

namespace App\Controller;

use App\Entity\Url;
use App\Form\Type\Url\UrlSubmitType;
use App\Form\Type\User\UserDeleteType;
use App\Form\Type\User\UserEditType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProfileController extends AbstractController
{

    #[Route('/profile', name: 'app_profile')]
    public function index(Request $request): Response
    {
        $urlForm = $this->createForm(UrlSubmitType::class, new Url(),[
            'action' => $this->generateUrl('app_shorten')
        ]);

        return $this->renderForm('profile/index.html.twig', [
            'urlForm' => $urlForm,
        ]);
    }

    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function edit(EntityManagerInterface $entityManager, Request $request, TranslatorInterface $translator, Security $security): Response
    {
        $user = $security->getUser();

        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success',
                $translator->trans('profile.edit.success')
            );

            return $this->redirectToRoute('app_profile');
        }


        return $this->renderForm('profile/edit.html.twig', [
            'profileForm' => $form,
        ]);
    }

    #[Route('/profile/delete', name: 'app_profile_delete')]
    public function delete(Session $session, TokenStorageInterface $tokenStorage, Request $request, Security $security, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        $user = $security->getUser();

        if (!$user) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(UserDeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            ///log out current user
            $tokenStorage->setToken(null);
            $session->invalidate();

            try {
                $entityManager->remove($user);
                $entityManager->flush();
                $this->addFlash('success', $translator->trans('profile.delete.success'));
            } catch (\Exception $e) {
                $this->addFlash('danger', $translator->trans('profile.delete.error'));
                throw new $e;
            }

            return $this->redirectToRoute('app_home');
        }

        return $this->renderForm('profile/edit.html.twig', [
            'profileForm' => $form,
        ]);
    }

}
