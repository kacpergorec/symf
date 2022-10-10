<?php

namespace App\Twig;

use App\Service\BubbleRenderer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;


class BubbleExtension extends AbstractExtension
{
    private BubbleRenderer $bubbleRenderer;

    public function __construct(BubbleRenderer $bubbleRenderer)
    {
        $this->bubbleRenderer = $bubbleRenderer;
    }

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
        return $this->bubbleRenderer->renderBubble($value, $color);
    }
}
