<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actionDelete = Action::new('delete')
            ->displayIf(function ($entity) {
                return $this->getUser() !== $entity;
            })
            ->setCssClass('text-danger')
            ->linkToCrudAction(Action::DELETE);

        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->add(Crud::PAGE_INDEX, $actionDelete)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->hideOnForm();
        yield TextField::new('email')->setFormType(EmailType::class);
        yield TextField::new('email')->onlyWhenUpdating()->setDisabled(true);
        yield TextField::new('username');
        yield TextField::new('firstname')->onlyWhenUpdating();
        yield TextField::new('lastname')->onlyWhenUpdating();
        yield TextField::new('password')
            ->onlyWhenCreating()
            ->setFormType(PasswordType::class);
        yield BooleanField::new('verified');
    }


    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $user = $this->hashUserPassword($entityInstance);

        parent::persistEntity($entityManager, $user);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $user = $this->hashUserPassword($entityInstance);

        parent::updateEntity($entityManager, $user);
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::deleteEntity($entityManager, $entityInstance);
    }


    private function hashUserPassword(User $user): User
    {
        $hashedPassword = $this->userPasswordHasher->hashPassword($user, $user->getPassword());

        $user->setPassword($hashedPassword);

        return $user;
    }
}
