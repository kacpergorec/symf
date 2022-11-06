<?php

namespace App\Controller\Admin;

use App\Entity\Url;
use App\Service\EntityUniqueTokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UrlCrudController extends AbstractCrudController
{

    public function __construct(
        private EntityUniqueTokenGenerator $generator,
        private ManagerRegistry $doctrine
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
            DateTimeField::new('expirationDate')
                ->hideOnForm()
                ->setLabel('Expiration')
                ->setTemplatePath('admin/crud/field/formatted_datetime.html.twig'),
            AssociationField::new('User'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $refresh = Action::new('refresh_expiration')
            ->linkToCrudAction('refreshExpiration');

        $actions
            ->disable(Action::EDIT)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $refresh)
            ->add(Crud::PAGE_DETAIL, $refresh)
            ->addBatchAction(
                Action::new('batch_refresh_expiration', 'Refresh Expiration')
                    ->linkToCrudAction('batchRefreshExpiration')
            );
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


    public function refreshExpiration(AdminContext $context): RedirectResponse
    {
        $className = $context->getEntity()->getFqcn();
        if ($entityManager = $this->doctrine->getManagerForClass($className)) {
            /**
             * @var Url $url
             */
            $url = $context->getEntity()->getInstance();
            $url->updateExpirationDate(Url::ONE_MONTH);

            $entityManager->persist($url);
            $entityManager->flush();
        }

        $this->addFlash('success', 'url.refreshed');

        return $this->redirect($context->getReferrer());
    }

    public function batchRefreshExpiration(BatchActionDto $batchActionDto)
    {
        $className = $batchActionDto->getEntityFqcn();
        if ($entityManager = $this->doctrine->getManagerForClass($className)) {

            foreach ($batchActionDto->getEntityIds() as $id) {
                if ($url = $entityManager->find($className, $id)) {
                    $url->updateExpirationDate(Url::ONE_MINUTE);
                }
            }

            $entityManager->flush();
        }

        return $this->redirect($batchActionDto->getReferrerUrl());
    }

}
