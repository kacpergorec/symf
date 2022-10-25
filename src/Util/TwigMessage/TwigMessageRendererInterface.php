<?php
declare (strict_types=1);

namespace App\Util\TwigMessage;


interface TwigMessageRendererInterface
{

    public function render(TwigMessage $message): string;
}