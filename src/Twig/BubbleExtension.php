<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class BubbleExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('bubble', [$this, 'renderBubble'], ['is_safe' => ['html']]),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('bubble', [$this, 'renderBubble'], ['is_safe' => ['html']]),
        ];
    }

    public function renderBubble($value, $color): string
    {
        return "<span class='fw-normal badge bg-opacity-10 border border-opacity-10 border-{$color} bg-{$color} text-{$color}'>{$value}</span>";
    }
}
