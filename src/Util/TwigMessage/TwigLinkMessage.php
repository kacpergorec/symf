<?php
declare (strict_types=1);

namespace App\Util\TwigMessage;


use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class TwigLinkMessage extends TwigMessage
{

    protected string $linkMessage;
    protected string $link;

    public function __construct(string $message, array $messageExtra, string $linkMessage, string $link)
    {
        parent::__construct($message, $messageExtra);

        $this->setTemplate('components/message_link.html.twig');
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