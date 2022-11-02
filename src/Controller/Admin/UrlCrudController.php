<?php

namespace App\Controller\Admin;

use App\Entity\Url;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

class UrlCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Url::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            UrlField::new('shortUrl')
                ->hideOnForm(),
            TextField::new('shortKey')
                ->hideOnIndex()
                ->hideOnDetail(),
            UrlField::new('longUrl'),
            AssociationField::new('User'),
        ];
    }
}
