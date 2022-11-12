<?php
declare (strict_types=1);

namespace App\Menu;

use App\Repository\PageRepository;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class MenuBuilder
{

    public function __construct(
       private FactoryInterface $factory,
       private PageRepository $repository
    )
    {
    }

    public function createMainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('Home', ['route' => 'app_home']);

        foreach ($this->repository->findAll() as $page) {
            if ($page->isInMenu() && $page->isPublished()) {
                $menu->addChild($page->getTitle(), [
                    'route' => 'app_page',
                    'routeParameters' => ['slug' => $page->getSlug()]
                ]);
            }
        }

        return $menu;
    }

}