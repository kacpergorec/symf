<?php
declare (strict_types=1);

namespace App\Util\TwigMessage;

/**
 * Class TwigMessage
 *
 * Properties are linked to twig variables.
 * Every property must be used in a twig view.
 *
 * @package App\Util\TwigMessage
 */
class TwigMessage
{

    private const AFFIX = '%';
    protected const TWIG_TEMPLATE_PATH = 'components/message.html.twig';
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
        return static::TWIG_TEMPLATE_PATH;
    }


    public function getVariables(): array
    {
        return get_object_vars($this);
    }
}