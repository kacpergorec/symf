<?php
declare (strict_types=1);

namespace App\Factory\User;


use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class AbstractUserFactory
{


    public function __construct(private UserPasswordHasherInterface $passwordHasher){}

    abstract public function createNew(string $username, string $email, string $plainPassword): User;

    abstract public function createNewFromEntity(User $user): User;

    protected function hashPassword(User $user, string $plainPassword): string
    {
        return $this->passwordHasher->hashPassword(
            $user,
            $plainPassword,
        );
    }
}