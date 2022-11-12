<?php

namespace App\Controller;

use App\Entity\Url;
use App\Entity\User;
use App\Form\Type\Url\UrlProfileSubmitType;
use App\Repository\UrlRepository;
use App\Service\EntityUniqueTokenGenerator;
use App\Service\UrlsSessionHandler;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class UrlController extends AbstractController
{

    #[Route('/shorten', name: 'app_url_shorten')]
    public function shorten(
        UrlHelper $urlHelper,
        Security $security,
        Request $request,
        EntityUniqueTokenGenerator $tokenGenerator,
        UrlRepository $urlRepository,
        UrlsSessionHandler $urlsSessionHandler,
        PaginatorInterface $paginator
    ): Response
    {
        $form = $this->createForm(UrlProfileSubmitType::class);

        $form->handleRequest($request);

        /**
         * @var $user User
         */
        $user = $security->getUser();

        $shortedUrl = '';

        /**
         * @var Url $url
         */
        if ($form->isSubmitted() && $form->isValid()) {
            $url = $form->getData();

            if ($user) {
                $url->setUser($user);
                $url->updateExpirationDate(Url::THREE_MONTHS);
            }

            $shortKey = $tokenGenerator->generateUniqueToken(4, $urlRepository);

            $url->setShortKey($shortKey);

            $urlRepository->save($url, true);

            if (!$user) {
                $urlsSessionHandler->add($url);
            }

            $shortedUrl = $urlHelper->getAbsoluteUrl($url->getShortKey());

            $this->addFlash('success', "URL has been shorted to <a href='{$shortedUrl}'>{$shortedUrl}</a>");

        }

        if ($user) {
            $urls = array_reverse($user->getUrls()->toArray());
        } else {
            $urls = $urlsSessionHandler->get();
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
            $url->updateExpirationDate(Url::ONE_MONTH);
            $urlRepository->save($url, true);
            $this->addFlash('success', 'url.refreshed');
        }

        return $this->redirectToRoute('app_profile');
    }

    #[Route('/{key}', name: 'app_url_redirect')]
    public function urlRedirect($key, UrlRepository $urlRepository): Response
    {

        if ($url = $urlRepository->findOneBy(['shortKey' => $key])) {

            if ($url->isExpired()) {
                $this->addFlash('warning', 'messages.url_expired');
                throw $this->createNotFoundException();
            }

            //301 redirect is apparently healthiest for SEO
            //source: https://blog.rebrandly.com/301-redirect/
            return $this->redirect($url->getLongUrl(), '301');
        }

        //if no link was found
        return $this->forward(PageController::class . ':index', [
            'slug' => $key
        ]);
    }

}
