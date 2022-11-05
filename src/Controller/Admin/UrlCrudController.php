<?php

namespace App\Controller\Admin;

use App\Entity\Url;
use App\Service\EntityUniqueTokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

class UrlCrudController extends AbstractCrudController
{

    public function __construct(
        private EntityUniqueTokenGenerator $generator
    )
    {
    }

    public static function getEntityFqcn(): string
    {
        return Url::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            UrlField::new('shortUrl')
                ->hideOnForm(),
            TextField::new('shortKey')
                ->onlyOnDetail(),
            UrlField::new('longUrl', 'Url'),
            AssociationField::new('User'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions
            ->remove(Crud::PAGE_INDEX,Action::EDIT)
        ;
        return parent::configureActions($actions);
    }

    /**
     * @param Url $url
     */
    public function persistEntity(EntityManagerInterface $entityManager, $url): void
    {
        $this->generateToken($entityManager, $url);
        parent::persistEntity($entityManager, $url);
    }

    private function generateToken(EntityManagerInterface $entityManager, Url $url): void
    {
        $repository = $entityManager->getRepository($url::class);
        $shortKey = $this->generator->generateUniqueToken(4, $repository);

        $url->setShortKey($shortKey);
    }
}
