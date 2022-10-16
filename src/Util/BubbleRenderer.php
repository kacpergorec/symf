<?php
declare (strict_types=1);

namespace App\Util;


class BubbleRenderer
{

    public function renderBubble(string $value, string $color): string
    {
        return "<span class='fw-normal badge bg-opacity-10 border border-opacity-10 border-{$color} bg-{$color} text-{$color}'>{$value}</span>";
    }

    public function renderBubbles(array $items): string
    {
        $html = '';

        foreach ($items as $item) {
            //for now only one color
            $html .= $this->renderBubble($item, 'primary');
        }

        return $html;
    }
}