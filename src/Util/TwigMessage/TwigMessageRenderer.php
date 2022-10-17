<?php
declare (strict_types=1);

namespace App\Util\TwigMessage;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class TwigMessageRenderer
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function render(TwigMessage $message): string
    {

        return $this->twig->render(
            $message->getTemplatePath(),
            $message->getVariables()
        );
    }

}