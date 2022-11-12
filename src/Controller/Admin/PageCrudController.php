<?php

namespace App\Controller\Admin;

use App\Entity\Page;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use RuntimeException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\AsciiSlugger;

class PageCrudController extends AbstractCrudController
{
    public const IMAGES_UPLOAD_MAIN_DIR = 'public/';
    public const IMAGES_UPLOAD_PATH = 'uploads/images/';

    public function __construct(
        private Security $security
    )
    {
    }

    public static function getEntityFqcn(): string
    {
        return Page::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            ImageField::new('image')
                ->setUploadDir(self::IMAGES_UPLOAD_MAIN_DIR . self::IMAGES_UPLOAD_PATH)
                ->setBasePath(self::IMAGES_UPLOAD_PATH)
                ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')
            ,
            TextField::new('title'),
            TextField::new('slug')
                ->onlyOnDetail()
            ,
            UrlField::new('redirectUrl')
                ->hideOnIndex()
                ->setLabel('Redirect to')
                ->setHelp('Leave empty for no redirect'),
            TextEditorField::new('content')
                ->setTemplatePath('admin/crud/field/text_editor.html.twig')
            ,
            AssociationField::new('author')
                ->hideOnForm(),
            BooleanField::new('inMenu'),
            BooleanField::new('published'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
        return parent::configureActions($actions);
    }

    /**
     * @param Page $page
     */
    public function persistEntity(EntityManagerInterface $entityManager, $page): void
    {
        if (!$user = $this->security->getUser()) {
            throw new RuntimeException('You have to be logged in to add pages.');
        }

        $this->setSlug($entityManager, $page);

        $page->setAuthor($user);
        $page->setUpdatedAt(new DateTimeImmutable());

        parent::persistEntity($entityManager, $page);
    }


    public function deleteEntity(EntityManagerInterface $entityManager, $page): void
    {
        parent::deleteEntity($entityManager, $page);

        //delete page's image
        $imgPath = self::IMAGES_UPLOAD_PATH . $page->getImage();
        if (is_file($imgPath)) {
            unlink($imgPath);
        }
    }

    private function setSlug(EntityManagerInterface $entityManager, Page $page): void
    {
        $slugger = new AsciiSlugger();

        $slug = $maybeSlug = $slugger->slug(
            mb_strtolower($page->getTitle())
        );

        $repository = $entityManager->getRepository(Page::class);

        //if there is a existing slug add a number to a slug to preserve uniqueness
        //Counting starts from 2. It is more readable to user this way.
        $i = 2;
        while ($repository->findBy(['slug' => $slug])) {
            $slug = "{$maybeSlug}-{$i}";
            $i++;
        }

        $page->setSlug($slug);
    }

}
