<?php
declare (strict_types=1);

namespace App\Form\Type\User;

use App\Form\Model\UserDeleteModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserDeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', PasswordType::class, [
                    'label' => 'user.password',
                    'help' => 'profile.delete.type_current_password',
                ]
            )
            ->add('save', SubmitType::class, [
                    'attr' => ['class' => 'btn btn-outline-danger'],
                    'label' => 'profile.delete.delete_account'
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserDeleteModel::class,
        ]);
    }
}