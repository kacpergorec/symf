<?php

namespace App\Controller;

use App\Entity\Url;
use App\Form\Type\Url\UrlSubmitType;
use App\Repository\UrlRepository;
use App\Service\UniqueTokenGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class UrlController extends AbstractController
{
    #[Route('/url/shorten', name: 'app_url_shorten')]
    public function shorten(Security $security, Request $request, UniqueTokenGenerator $generator, UrlRepository $urlRepository): Response
    {
        $form = $this->createForm(UrlSubmitType::class);

        $form->handleRequest($request);

        $user = $security->getUser();

        /**
         * @var Url $url
         */
        if ($form->isSubmitted() && $form->isValid()) {
            $url = $form->getData();
            $url->setUser($user);


            while (!$url->hasShortKey()) {

                $uniqueKey = $generator->generate('4');

                //TODO: index shortKey column in the database
                //TODO  : auto increment key length after hitting the limit

                if (!$urlRepository->findOneBy(['shortKey' => $uniqueKey])) {
                    $url->setShortKey($uniqueKey);
                }

            }

            $urlRepository->save($url, true);

            $this->addFlash('success', "URL has been shorted to <kbd>{$request->getHttpHost()}/{$url->getShortKey()}</kbd>");
        }

        if ($user) {
            return $this->redirectToRoute('app_profile');
        }

        return $this->renderForm('shorten/index.html.twig', [
            'urlForm' => $form
        ]);
    }

    #[Route('/url/delete/{id}', name: 'app_url_delete', requirements: ['id' => '\d+'])]
    public function delete($id, UrlRepository $urlRepository, Security $security): Response
    {
        $user = $security->getUser();

        if ($user && ($url = $urlRepository->find($id)) && $url->validateUser($user)) {
            $urlRepository->remove($url, true);
            $this->addFlash('success', 'URL was removed successfully!');
        }

        return $this->redirectToRoute('app_profile');
    }

    #[Route('/{key}', name: 'app_url_redirect')]
    public function urlRedirect($key, UrlRepository $urlRepository): Response
    {
        if (($url = $urlRepository->findOneBy(['shortKey' => $key]))) {
            return $this->redirect($url->getLongUrl(), '301');
        }

        //if no link was found
        return $this->forward(PageController::class . ':index', [
            'slug' => $key
        ]);
    }

}
