<?php

namespace App\Controller;

use App\Entity\Url;
use App\Form\Type\Url\UrlSubmitType;
use App\Form\Type\User\UserDeleteType;
use App\Form\Type\User\UserEditType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;

class ProfileController extends AbstractController
{

    #[Route('/profile', name: 'app_profile')]
    public function index(PaginatorInterface $paginator, Security $security, Request $request): Response
    {


        $urlForm = $this->createForm(UrlSubmitType::class, new Url(), [
            'action' => $this->generateUrl('app_url_shorten')
        ]);

        if ($user = $security->getUser()) {
            $pagination = $paginator->paginate(
                $user->getUrls(),
                $request->query->getInt('page', 1),
                3
            );
        }

        return $this->renderForm('profile/index.html.twig', [
            'urlForm' => $urlForm,
            'pagination' => $pagination
        ]);
    }

    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function edit(EntityManagerInterface $entityManager, Request $request, Security $security): Response
    {
        $user = $security->getUser();

        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'profile.edit.success');

            return $this->redirectToRoute('app_profile');
        }


        return $this->renderForm('profile/edit.html.twig', [
            'profileForm' => $form,
        ]);
    }

    #[Route('/profile/delete', name: 'app_profile_delete')]
    public function delete(Session $session, TokenStorageInterface $tokenStorage, Request $request, Security $security, UserRepository $userRepository): Response
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
                $userRepository->remove($user, true);
                $this->addFlash('success', 'profile.delete.success');
            } catch (Exception $e) {
                $this->addFlash('danger', 'profile.delete.error');
                throw new $e;
            }

            return $this->redirectToRoute('app_home');
        }

        return $this->renderForm('profile/edit.html.twig', [
            'profileForm' => $form,
        ]);
    }

}
