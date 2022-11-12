<?php
declare (strict_types=1);

namespace App\Factory\Page;

use App\Entity\Page;
use Symfony\Component\String\Slugger\AsciiSlugger;

class PageFactory
{
    public function createPublishedInMenu(string $title, string $content): Page
    {
        $page = new Page();

        $slugger = new AsciiSlugger();

        $page->setTitle($title);
        $page->setSlug((string)$slugger->slug($title));
        $page->setContent($content);
        $page->setInMenu(true);
        $page->setPublished(true);

        return $page;
    }


}