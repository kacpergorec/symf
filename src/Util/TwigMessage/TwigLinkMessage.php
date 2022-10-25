<?php
declare (strict_types=1);

namespace App\Util\TwigMessage;


class TwigLinkMessage extends TwigMessage
{

    protected const TWIG_TEMPLATE_PATH = 'components/message_link.html.twig';
    protected string $linkMessage;
    protected string $link;

    public function __construct(string $message, array $messageExtra, string $linkMessage, string $link)
    {
        parent::__construct($message, $messageExtra);

        $this->setLinkMessage($linkMessage);
        $this->setLink($link);
    }

    public function getLinkMessage(): string
    {
        return $this->linkMessage;
    }

    public function setLinkMessage(string $linkMessage): void
    {
        $this->linkMessage = $linkMessage;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link): void
    {
        $this->link = $link;
    }

}