<?php

namespace App\Controller;

use App\Entity\Url;
use App\Form\Type\Url\UrlSubmitType;
use App\Repository\UrlRepository;
use App\Service\UniqueTokenGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Exception;
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


            /**
             * This is a temporary solution.
             *
             * The problem here is that when the record limit for given length is peaked,
             * Every new request, counting will start from here and check a LOT of records.
             *
             * The incremented tokenLength value should be stored in the database and incremented once
             * every time the limit is peaked.
             */
            $generator->setTokenLength(3);

            $i = 0;
            while (!$url->hasShortKey()) {

                if ($generator->getOutcomesCount() === $i) {
                    $generator->incrementTokenLength();
                }

                $uniqueKey = $generator->generate();

                //TODO: index shortKey column in the database
                if (!$urlRepository->findOneBy(['shortKey' => $uniqueKey])) {
                    $url->setShortKey($uniqueKey);
                }

                $i++;
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