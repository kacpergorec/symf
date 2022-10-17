<?php
declare (strict_types=1);

namespace App\Util\TwigMessage;

class TwigMessage
{

    private const AFFIX = '%';
    private string $twigMessageTemplatePath = 'components/message.html.twig';
    protected string $message;
    protected array $messageExtra;

    public function __construct(string $message, array $messageExtra)
    {
        $this->setMessage($message);
        $this->setExtra($messageExtra);
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getMessageExtra(): array
    {
        return $this->messageExtra;
    }

    /**
     * Safely adds prefix and suffix to the key array for
     * Twig to pass it further to the translation file.
     *
     * username => %username% , %%%username%%% => %username%
     */
    public function setExtra(array $extra): void
    {
        $extra = array_combine(
            array_map(fn($k) => self::AFFIX . trim($k, self::AFFIX) . self::AFFIX, array_keys($extra)),
            $extra
        );

        $this->messageExtra = $extra;
    }

    public function getTemplatePath(): string
    {
        return $this->twigMessageTemplatePath;
    }

    public function setTemplate(string $templatePath)
    {
        $this->twigMessageTemplatePath = $templatePath;
    }

    public function getVariables(): array
    {
        return get_object_vars($this);
    }
}