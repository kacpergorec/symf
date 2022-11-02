<?php
declare (strict_types=1);

namespace App\Factory\User;


use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminUserFactory extends VerifiedUserFactory
{
    protected const ROLE_ADMIN = 'ROLE_ADMIN';

    public function __construct(UserPasswordHasherInterface $passwordHasher, private $defaultAdminUsername, private $defaultAdminPassword)
    {
        parent::__construct($passwordHasher);
    }

    public function createNew(string $username, string $email, string $plainPassword): User
    {
        $user = parent::createNew($username, $email, $plainPassword);

        $user->addRole(self::ROLE_ADMIN);

        $user->updateFieldsIfEmpty([
            'username' => $this->defaultAdminUsername,
            'email' => $this->defaultAdminUsername,
            'password' => $this->hashPassword($user, $this->defaultAdminPassword),
        ]);

        return $user;
    }

    public function createNewFromEntity(User $user): User
    {
        $user = parent::createNewFromEntity($user);

        $user->updateFieldsIfEmpty([
            'username' => $this->defaultAdminUsername,
            'password' => $this->hashPassword($user, $this->defaultAdminPassword),
        ]);

        $user->addRole(self::ROLE_ADMIN);

        return $user;
    }
}