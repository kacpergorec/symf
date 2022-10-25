<?php

namespace App\Twig;

use DateTime;
use DateTimeInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class DateDifferenceExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('diff_in_days', [$this, 'diffInDays']),
            new TwigFilter('diff_level', [$this, 'difflevel']),
            new TwigFilter('diff_error_code', [$this, 'diffError']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('diff_in_days', [$this, 'diffInDays']),
            new TwigFunction('diff_level', [$this, 'difflevel']),
            new TwigFunction('diff_error_code', [$this, 'diffError']),
        ];
    }

    public function diffInDays(DateTimeInterface $since): int
    {
        $today = new DateTime();

        return $since->diff($today)->format("%a");
    }

    public function difflevel(DateTimeInterface $since): string
    {
        $days = $this->diffInDays($since);

        if ($days <= 5) {
            return 'danger';
        }

        if ($days <= 30) {
            return 'warning';
        }

        return '';
    }

}
