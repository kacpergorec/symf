<?php

namespace App\Controller;

use App\Entity\Url;
use App\Form\Type\Url\UrlProfileSubmitType;
use App\Repository\UrlRepository;
use App\Service\UniqueTokenGenerator;
use App\Service\UrlsSessionHandler;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class UrlController extends AbstractController
{

    #[Route('/shorten', name: 'app_url_shorten')]
    public function shorten(Security $security, Request $request, UniqueTokenGenerator $generator, UrlRepository $urlRepository, UrlsSessionHandler $urlsSessionHandler, PaginatorInterface $paginator): Response
    {
        $form = $this->createForm(UrlProfileSubmitType::class);

        $form->handleRequest($request);

        if ($user = $security->getUser()) {
            $urls = $user->getUrls();
        } else {
            $urls = $urlsSessionHandler->get();
        }

        $shortedUrl = '';

        /**
         * @var Url $url
         */
        if ($form->isSubmitted() && $form->isValid()) {
            $url = $form->getData();

            if ($user) {
                $url->setUser($user);
                $url->updateExpirationDate('P1M');
            }


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

                if (!$urlRepository->findOneBy(['shortKey' => $uniqueKey])) {
                    $url->setShortKey($uniqueKey);
                }

                $i++;
            }

            $urlRepository->save($url, true);

            if (!$user) {
                $urlsSessionHandler->add($url);
            }

            $shortedUrl = $request->getHttpHost() . '/' . $url->getShortKey();

            $this->addFlash('success', "URL has been shorted to {$shortedUrl}");

        }

        $pagination = $paginator->paginate(
            $urls,
            $request->query->getInt('page', 1),
            8
        );

        return $this->renderForm('shorten/index.html.twig', [
            'urlForm' => $form,
            'shortedUrl' => $shortedUrl,
            'urls' => $pagination
        ]);
    }

    #[Route('/url/delete/{id}', name: 'app_url_delete', requirements: ['id' => '\d+'])]
    public function delete($id, UrlRepository $urlRepository, Security $security): Response
    {
        $user = $security->getUser();

        if ($user && ($url = $urlRepository->find($id)) && $url->validateUser($user)) {
            $urlRepository->remove($url, true);
            $this->addFlash('success', 'url.removed');
        }

        return $this->redirectToRoute('app_profile');
    }

    #[Route('/url/refresh/{id}', name: 'app_url_refresh', requirements: ['id' => '\d+'])]
    public function refresh($id, UrlRepository $urlRepository, Security $security)
    {
        $user = $security->getUser();

        if ($user && ($url = $urlRepository->find($id)) && $url->validateUser($user)) {
            $url->updateExpirationDate('P1M1D');
            $urlRepository->save($url, true);
            $this->addFlash('success', 'url.refreshed');
        }

        return $this->redirectToRoute('app_profile');
    }

    #[Route('/{key}', name: 'app_url_redirect')]
    public function urlRedirect($key, UrlRepository $urlRepository): Response
    {
        if (($url = $urlRepository->findOneBy(['shortKey' => $key]))) {
//            return $this->redirect($url->getLongUrl(), '301');
            $this->addFlash('success', "found {$url->getLongUrl()}");
        }

        //if no link was found
        return $this->forward(PageController::class . ':index', [
            'slug' => $key
        ]);
    }

}
