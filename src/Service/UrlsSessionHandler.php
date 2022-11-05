<?php
declare (strict_types=1);

namespace App\Service;


use App\Entity\Url;
use App\Repository\UrlRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class UrlsSessionHandler
{
    private RequestStack $requestStack;
    private SessionInterface $session;
    private UrlRepository $urlRepository;

    public function __construct(RequestStack $requestStack, UrlRepository $urlRepository)
    {
        $this->requestStack = $requestStack;
        $this->urlRepository = $urlRepository;
        $this->session = $this->requestStack->getSession();
    }

    public function add(Url $url): self
    {
        $urls = $this->session->get('urls');
        $urls[$url->getId()] = $url;
        $this->session->set('urls', $urls);

        return $this;
    }

    /**
     * @return Url[]
     */
    public function get($reverseOrder = true): array
    {
        $this->filterDeletedUrls();

        $urls = $this->session->get('urls');

        return $reverseOrder ? array_reverse($urls) : $urls;
    }

    public function getKeys(): array
    {
        return array_map(
            static fn($url) => $url->getShortKey(),
            $this->get()
        );
    }

    private function filterDeletedUrls(): self
    {
        $urls = (array)$this->session->get('urls');

        foreach ($urls as $url) {
            if (!$this->urlRepository->find($url->getId())) {
                unset($urls[$url->getId()]);
            }
        }

        $this->session->set('urls', $urls);

        return $this;
    }
}